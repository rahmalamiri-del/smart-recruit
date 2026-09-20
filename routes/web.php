<?php

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use SmartRecruit\Support\AiClient;
use SmartRecruit\Support\CvParser;
use SmartRecruit\Support\DemoStore;
use SmartRecruit\Support\SemanticMatcher;
use SmartRecruit\Support\SqlStore;

if (! function_exists('sr_store')) {
    /**
     * MySQL est l'unique source de vérité. En cas d'indisponibilité on échoue
     * explicitement plutôt que de servir silencieusement des données
     * différentes issues d'un stockage de secours.
     */
    function sr_store(): SqlStore
    {
        abort_unless(
            SqlStore::isAvailable(),
            503,
            "Base de données MySQL indisponible. Vérifiez la configuration DB_* et exécutez 'php artisan migrate'."
        );

        return new SqlStore();
    }
}

if (! function_exists('sr_matcher')) {
    function sr_matcher(): SemanticMatcher
    {
        return new SemanticMatcher(
            config('smart_recruit.skill_aliases', []),
            config('smart_recruit.skill_taxonomy', [])
        );
    }
}

if (! function_exists('sr_cv_parser')) {
    function sr_cv_parser(): CvParser
    {
        return new CvParser(sr_matcher(), new AiClient(config('smart_recruit.ai_service_url')));
    }
}

if (! function_exists('sr_user')) {
    /**
     * Utilisateur de la session, revalidé contre la base une fois par requête.
     *
     * Un compte supprimé perd immédiatement sa session ; un rôle modifié par
     * l'administrateur est appliqué sans attendre une reconnexion. Sans cela,
     * un utilisateur supprimé conserverait ses droits tant que son cookie vit.
     */
    function sr_user(): array
    {
        static $revalidated = null;

        if ($revalidated !== null) {
            return $revalidated;
        }

        $guest = DemoStore::guestUser();
        $session = session('smart_recruit_user');

        if (! is_array($session) || ($session['role'] ?? 'guest') === 'guest') {
            return $revalidated = $guest;
        }

        if (! SqlStore::isAvailable()) {
            // Base injoignable : on ne dégrade pas la session ici, la route
            // concernée échouera explicitement via sr_store().
            return $revalidated = $session;
        }

        $account = (new SqlStore())->findUser($session['id'] ?? '');

        if (! $account) {
            session()->forget('smart_recruit_user');

            return $revalidated = $guest;
        }

        $fresh = [
            'id' => $account['id'],
            'name' => $account['name'],
            'email' => $account['email'],
            'role' => $account['role'],
            'student_id' => $account['student_id'],
        ];

        if ($fresh !== $session) {
            session(['smart_recruit_user' => $fresh]);
        }

        return $revalidated = $fresh;
    }
}

if (! function_exists('sr_user_delete_impact')) {
    /**
     * Décompte, pour la page de confirmation, ce qui sera marqué supprimé
     * en même temps que le compte. Rien n'est détruit : tout reste restaurable.
     */
    function sr_user_delete_impact(SqlStore $store, array $account): array
    {
        $data = $store->all();
        $impact = [];

        if ($account['student_id']) {
            $applications = array_filter(
                $data['applications'],
                fn (array $application): bool => $application['student_id'] === $account['student_id']
            );
            $impact[] = 'Profil candidat « '.$account['student_id'].' »';
            $impact[] = count($applications).' candidature(s) de ce candidat';
        }

        if ($account['recruiter_id']) {
            $offers = array_filter(
                $data['offers'],
                fn (array $offer): bool => ($offer['owner_id'] ?? null) === $account['id']
            );
            $offerIds = array_column($offers, 'id');
            $applications = array_filter(
                $data['applications'],
                fn (array $application): bool => in_array($application['offer_id'], $offerIds, true)
            );
            $impact[] = 'Profil recruteur « '.$account['recruiter_id'].' »';
            $impact[] = count($offers).' offre(s) publiée(s)';
            $impact[] = count($applications).' candidature(s) reçue(s) sur ces offres';
        }

        return $impact;
    }
}

if (! function_exists('sr_offer_owner_matches')) {
    function sr_offer_owner_matches(array $offer, array $user): bool
    {
        return ($offer['owner_id'] ?? null) !== null && $offer['owner_id'] === ($user['id'] ?? null);
    }
}

if (! function_exists('sr_match')) {
    function sr_match(array $offer, array $student, AiClient $aiClient, bool $aiIsOnline, $store = null): array
    {
        $match = sr_matcher()->score($offer, $student);

        if ($aiIsOnline) {
            $aiMatch = $aiClient->match(
                sr_offer_text($offer),
                sr_student_text($student),
                $offer['required_skills'] ?? [],
                $student['skills'] ?? []
            );

            if (is_array($aiMatch) && isset($aiMatch['score'])) {
                $match = array_merge($match, [
                    'score' => (int) $aiMatch['score'],
                    'matched_skills' => $aiMatch['matched_skills'] ?? $match['matched_skills'],
                    'missing_skills' => $aiMatch['missing_skills'] ?? $match['missing_skills'],
                    'semantic_coverage' => (int) ($aiMatch['semantic_coverage'] ?? $match['semantic_coverage']),
                    'text_similarity' => (int) ($aiMatch['text_similarity'] ?? $match['text_similarity']),
                    'algorithm' => $aiMatch['algorithm'] ?? $match['algorithm'],
                    'source' => $aiMatch['source'] ?? 'python',
                ]);
            }
        }

        if ($store && method_exists($store, 'saveMatchScore')) {
            $store->saveMatchScore($offer['id'], $student['id'], $match);
        }

        return $match;
    }
}

if (! function_exists('sr_rankings')) {
    function sr_rankings(array $offer, array $students, $store = null): array
    {
        $aiClient = new AiClient(config('smart_recruit.ai_service_url'));
        $aiIsOnline = ($aiClient->health()['status'] ?? null) === 'ok';
        $rankings = [];

        foreach ($students as $student) {
            $rankings[] = [
                'student' => $student,
                'match' => sr_match($offer, $student, $aiClient, $aiIsOnline, $store),
            ];
        }

        usort($rankings, fn (array $a, array $b): int => $b['match']['score'] <=> $a['match']['score']);

        return $rankings;
    }
}

if (! function_exists('sr_recommend_offers')) {
    /**
     * Offres triées par pertinence pour un étudiant donné (le plus proche de
     * son profil/CV en premier) — utilisé pour la liste d'offres côté étudiant.
     */
    function sr_recommend_offers(array $student, array $offers, $store = null): array
    {
        $aiClient = new AiClient(config('smart_recruit.ai_service_url'));
        $aiIsOnline = ($aiClient->health()['status'] ?? null) === 'ok';
        $recommendations = [];

        foreach ($offers as $offer) {
            $recommendations[] = [
                'offer' => $offer,
                'match' => sr_match($offer, $student, $aiClient, $aiIsOnline, $store),
            ];
        }

        usort($recommendations, fn (array $a, array $b): int => $b['match']['score'] <=> $a['match']['score']);

        return $recommendations;
    }
}

if (! function_exists('sr_recommend_students')) {
    /**
     * Étudiants triés par leur meilleure compatibilité avec un ensemble
     * d'offres — utilisé pour mettre en avant les profils pertinents pour
     * un recruteur sur la liste des étudiants.
     */
    function sr_recommend_students(array $offers, array $students, $store = null): array
    {
        if (! $offers) {
            return array_map(fn (array $student): array => ['student' => $student, 'best' => null], $students);
        }

        $aiClient = new AiClient(config('smart_recruit.ai_service_url'));
        $aiIsOnline = ($aiClient->health()['status'] ?? null) === 'ok';
        $recommendations = [];

        foreach ($students as $student) {
            $best = null;

            foreach ($offers as $offer) {
                $match = sr_match($offer, $student, $aiClient, $aiIsOnline, $store);

                if ($best === null || $match['score'] > $best['match']['score']) {
                    $best = ['offer' => $offer, 'match' => $match];
                }
            }

            $recommendations[] = ['student' => $student, 'best' => $best];
        }

        usort($recommendations, fn (array $a, array $b): int => ($b['best']['match']['score'] ?? 0) <=> ($a['best']['match']['score'] ?? 0));

        return $recommendations;
    }
}

if (! function_exists('sr_offer_text')) {
    function sr_offer_text(array $offer): string
    {
        return trim(($offer['title'] ?? '').' '.($offer['description'] ?? '').' '.implode(' ', $offer['required_skills'] ?? []));
    }
}

if (! function_exists('sr_student_text')) {
    function sr_student_text(array $student): string
    {
        return trim(($student['headline'] ?? '').' '.($student['education'] ?? '').' '.($student['cv_text'] ?? '').' '.implode(' ', $student['skills'] ?? []));
    }
}

if (! function_exists('sr_uploads_path')) {
    function sr_uploads_path(): string
    {
        $path = storage_path('app/uploads');

        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }

        return $path;
    }
}

Route::get('/', function (Request $request) {
    $store = sr_store();
    $data = $store->all();
    $user = sr_user();
    $role = $user['role'] ?? 'guest';
    $aiHealth = (new AiClient(config('smart_recruit.ai_service_url')))->health();

    if ($role === 'recruiter') {
        $ownOffers = array_values(array_filter(
            $data['offers'],
            fn (array $offer): bool => sr_offer_owner_matches($offer, $user)
        ));

        $applicationCounts = [];
        foreach ($data['applications'] as $application) {
            $applicationCounts[$application['offer_id']] = ($applicationCounts[$application['offer_id']] ?? 0) + 1;
        }

        $statusCounts = ['draft' => 0, 'published' => 0, 'closed' => 0];
        foreach ($ownOffers as $offer) {
            $statusCounts[$offer['status'] ?? 'published']++;
        }

        $requestedStatus = $request->query('status');
        $statusFilter = in_array($requestedStatus, ['draft', 'published', 'closed'], true) ? $requestedStatus : null;

        $filteredOffers = $statusFilter
            ? array_values(array_filter($ownOffers, fn (array $offer): bool => ($offer['status'] ?? 'published') === $statusFilter))
            : $ownOffers;

        $withApplicationsCount = count(array_filter(
            $filteredOffers,
            fn (array $offer): bool => ($applicationCounts[$offer['id']] ?? 0) > 0
        ));

        $withApplicationsOnly = $request->boolean('with_applications');

        if ($withApplicationsOnly) {
            $filteredOffers = array_values(array_filter(
                $filteredOffers,
                fn (array $offer): bool => ($applicationCounts[$offer['id']] ?? 0) > 0
            ));
        }

        $offerSummaries = [];
        $totalApplications = 0;

        foreach ($ownOffers as $offer) {
            $totalApplications += $applicationCounts[$offer['id']] ?? 0;
        }

        foreach ($filteredOffers as $offer) {
            $rankings = sr_rankings($offer, $data['students'], $store);
            $offerSummaries[] = [
                'offer' => $offer,
                'top' => $rankings[0] ?? null,
                'applications' => $applicationCounts[$offer['id']] ?? 0,
            ];
        }

        usort($offerSummaries, fn (array $a, array $b): int => ($b['top']['match']['score'] ?? 0) <=> ($a['top']['match']['score'] ?? 0));

        return view('dashboard.recruiter', [
            'offerSummaries' => $offerSummaries,
            'offerCount' => count($ownOffers),
            'applicationCount' => $totalApplications,
            'statusFilter' => $statusFilter,
            'statusCounts' => $statusCounts,
            'withApplicationsOnly' => $withApplicationsOnly,
            'withApplicationsCount' => $withApplicationsCount,
            'aiHealth' => $aiHealth,
            'user' => $user,
        ]);
    }

    if ($role === 'student') {
        $profile = $user['student_id'] ? $store->findStudent($user['student_id']) : null;
        $myApplications = [];

        if ($profile) {
            foreach ($data['applications'] as $application) {
                if ($application['student_id'] !== $profile['id']) {
                    continue;
                }

                $offer = collect($data['offers'])->firstWhere('id', $application['offer_id']);

                if ($offer) {
                    $myApplications[] = ['application' => $application, 'offer' => $offer];
                }
            }
        }

        return view('dashboard.student', [
            'profile' => $profile,
            'myApplications' => $myApplications,
            'user' => $user,
        ]);
    }

    if ($role === 'admin') {
        $offerSummaries = [];

        foreach ($data['offers'] as $offer) {
            $rankings = sr_rankings($offer, $data['students'], $store);
            $offerSummaries[] = [
                'offer' => $offer,
                'top' => $rankings[0] ?? null,
                'applications' => count($store->applicationsForOffer($offer['id'])),
            ];
        }

        return view('dashboard.admin', [
            'data' => $data,
            'offerSummaries' => $offerSummaries,
            'aiHealth' => $aiHealth,
            'user' => $user,
        ]);
    }

    return view('dashboard.guest', [
        'data' => $data,
        'aiHealth' => $aiHealth,
        'user' => $user,
    ]);
})->name('dashboard');

Route::get('/test-connection', function () {
    $client = new AiClient(config('smart_recruit.ai_service_url'));
    $health = $client->health();

    return response()->json([
        'test_status' => ($health['status'] ?? null) === 'ok' ? 'SUCCÈS' : 'ÉCHEC',
        'laravel_message' => 'Laravel a contacté le microservice IA.',
        'ai_service_url' => config('smart_recruit.ai_service_url'),
        'python_response' => $health,
        'storage' => SqlStore::isAvailable() ? 'mysql' : 'json-fallback',
    ], ($health['status'] ?? null) === 'ok' ? 200 : 503);
})->name('test-connection');

Route::get('/login', function () {
    return view('auth.login', [
        'user' => sr_user(),
    ]);
})->name('login');

Route::post('/login', function (Request $request) {
    abort_unless(SqlStore::isAvailable(), 503, 'Connexion à la base de données requise pour se connecter.');

    $validated = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $account = (new SqlStore())->findUserByEmail($validated['email']);

    if (! $account || ! password_verify($validated['password'], $account['password_hash'])) {
        return back()->withInput(['email' => $validated['email']])->with('error', 'Identifiants invalides.');
    }

    session(['smart_recruit_user' => Arr::except($account, 'password_hash')]);

    return redirect()->route('dashboard')->with('success', 'Session active : '.$account['name']);
})->name('login.store')->middleware('throttle:10,1');

Route::get('/register', function (Request $request) {
    $role = $request->query('role');

    return view('auth.register', [
        'role' => in_array($role, ['recruiter', 'student'], true) ? $role : null,
        'user' => sr_user(),
    ]);
})->name('register');

Route::post('/register', function (Request $request) {
    abort_unless(SqlStore::isAvailable(), 503, 'Connexion à la base de données requise pour créer un compte.');

    $validated = $request->validate([
        'role' => ['required', 'in:recruiter,student'],
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:160', 'unique:sr_users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'company_name' => ['required_if:role,recruiter', 'nullable', 'string', 'max:160'],
        'position' => ['nullable', 'string', 'max:120'],
        'headline' => ['required_if:role,student', 'nullable', 'string', 'max:180'],
    ]);

    $account = (new SqlStore())->registerUser($validated);
    session(['smart_recruit_user' => $account]);

    return redirect()->route('dashboard')->with('success', 'Compte créé, bienvenue '.$account['name'].' !');
})->name('register.store')->middleware('throttle:10,1');

Route::post('/logout', function () {
    session()->forget('smart_recruit_user');

    return redirect()->route('dashboard')->with('success', 'Session fermée.');
})->name('logout');

Route::get('/admin', function () {
    $store = sr_store();
    $data = $store->all();

    return view('admin.index', [
        'data' => $data,
        'userCounts' => array_count_values(array_column($data['users'], 'role')),
        'user' => sr_user(),
    ]);
})->name('admin.index')->middleware('role:admin');

/*
|--------------------------------------------------------------------------
| Administration : gestion des comptes (recruteurs, étudiants, admins)
|--------------------------------------------------------------------------
| Toutes les mutations sont en POST + @csrf (le projet n'utilise ni PUT/PATCH
| ni JavaScript). Les suppressions sont logiques et passent par une page de
| confirmation server-rendered.
*/

Route::get('/admin/users', function (Request $request) {
    $store = sr_store();
    $role = $request->query('role');
    $roleFilter = in_array($role, ['admin', 'recruiter', 'student'], true) ? $role : null;
    $trashed = $request->boolean('trashed');

    $all = $store->listUsers(['trashed' => true]);
    $counts = [
        'admin' => 0,
        'recruiter' => 0,
        'student' => 0,
        'trashed' => 0,
    ];

    foreach ($all as $account) {
        if ($account['deleted']) {
            $counts['trashed']++;

            continue;
        }

        $counts[$account['role']] = ($counts[$account['role']] ?? 0) + 1;
    }

    $users = array_values(array_filter($all, function (array $account) use ($roleFilter, $trashed): bool {
        if ($trashed) {
            return $account['deleted'];
        }

        if ($account['deleted']) {
            return false;
        }

        return $roleFilter === null || $account['role'] === $roleFilter;
    }));

    return view('admin.users.index', [
        'users' => $users,
        'roleFilter' => $roleFilter,
        'trashed' => $trashed,
        'counts' => $counts,
        'user' => sr_user(),
    ]);
})->name('admin.users.index')->middleware('role:admin');

Route::get('/admin/users/create', function (Request $request) {
    $role = $request->query('role');

    return view('admin.users.form', [
        'account' => null,
        'defaultRole' => in_array($role, ['admin', 'recruiter', 'student'], true) ? $role : 'recruiter',
        'user' => sr_user(),
    ]);
})->name('admin.users.create')->middleware('role:admin');

Route::post('/admin/users', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:160', 'unique:sr_users,email'],
        'role' => ['required', 'in:admin,recruiter,student'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'company_name' => ['required_if:role,recruiter', 'nullable', 'string', 'max:160'],
        'position' => ['nullable', 'string', 'max:120'],
        'website' => ['nullable', 'url', 'max:255'],
        'headline' => ['required_if:role,student', 'nullable', 'string', 'max:180'],
    ]);

    $account = sr_store()->createUser($validated);

    return redirect()->route('admin.users.index')
        ->with('success', 'Compte créé : '.$account['name'].' ('.$account['role'].').');
})->name('admin.users.store')->middleware('role:admin');

Route::get('/admin/users/{account}/edit', function (string $account) {
    abort_unless($record = sr_store()->findUser($account, true), 404);

    return view('admin.users.form', [
        'account' => $record,
        'defaultRole' => $record['role'],
        'user' => sr_user(),
    ]);
})->name('admin.users.edit')->middleware('role:admin');

Route::post('/admin/users/{account}', function (Request $request, string $account) {
    $store = sr_store();
    $user = sr_user();
    abort_unless($record = $store->findUser($account), 404);

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:160', Rule::unique('sr_users', 'email')->ignore($account, 'public_id')],
        'role' => ['required', 'in:admin,recruiter,student'],
        'company_name' => ['required_if:role,recruiter', 'nullable', 'string', 'max:160'],
        'position' => ['nullable', 'string', 'max:120'],
        'website' => ['nullable', 'url', 'max:255'],
        'headline' => ['nullable', 'string', 'max:180'],
    ]);

    if ($validated['role'] !== $record['role']) {
        // Ne jamais se rétrograder soi-même, ni retirer le dernier admin.
        if ($record['id'] === $user['id']) {
            return back()->withInput()->with('error', "Vous ne pouvez pas modifier votre propre rôle.");
        }

        if ($record['role'] === 'admin' && $store->countAdmins() <= 1) {
            return back()->withInput()->with('error', "Impossible : c'est le dernier compte administrateur.");
        }
    }

    $store->updateUser($account, $validated);

    return redirect()->route('admin.users.index')->with('success', 'Compte mis à jour : '.$validated['name'].'.');
})->name('admin.users.update')->middleware('role:admin');

Route::post('/admin/users/{account}/password', function (Request $request, string $account) {
    $store = sr_store();
    abort_unless($store->findUser($account), 404);

    $validated = $request->validate([
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $store->updateUserPassword($account, $validated['password']);

    return back()->with('success', 'Mot de passe réinitialisé.');
})->name('admin.users.password')->middleware('role:admin');

Route::get('/admin/users/{account}/delete', function (string $account) {
    $store = sr_store();
    abort_unless($record = $store->findUser($account), 404);

    return view('admin.confirm', [
        'title' => 'Supprimer le compte',
        'entity' => $record['name'].' ('.$record['email'].')',
        'impact' => sr_user_delete_impact($store, $record),
        'action' => route('admin.users.destroy', $record['id']),
        'cancel' => route('admin.users.index'),
        'user' => sr_user(),
    ]);
})->name('admin.users.confirm-delete')->middleware('role:admin');

Route::post('/admin/users/{account}/delete', function (string $account) {
    $store = sr_store();
    $user = sr_user();
    abort_unless($record = $store->findUser($account), 404);

    if ($record['id'] === $user['id']) {
        return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
    }

    if ($record['role'] === 'admin' && $store->countAdmins() <= 1) {
        return back()->with('error', "Impossible : c'est le dernier compte administrateur.");
    }

    $store->softDeleteUser($account);

    return redirect()->route('admin.users.index')
        ->with('success', 'Compte supprimé : '.$record['name'].'. Il reste restaurable.');
})->name('admin.users.destroy')->middleware('role:admin');

Route::post('/admin/users/{account}/restore', function (string $account) {
    $store = sr_store();
    abort_unless($record = $store->findUser($account, true), 404);

    $store->restoreUser($account);

    return back()->with('success', 'Compte restauré : '.$record['name'].'.');
})->name('admin.users.restore')->middleware('role:admin');

Route::get('/students', function () {
    $store = sr_store();
    $data = $store->all();
    $user = sr_user();
    $students = $data['students'];
    $bestMatches = [];

    if (($user['role'] ?? 'guest') === 'recruiter') {
        $ownOffers = array_values(array_filter(
            $data['offers'],
            fn (array $offer): bool => sr_offer_owner_matches($offer, $user)
        ));

        $recommendations = sr_recommend_students($ownOffers, $students, $store);
        $students = array_map(fn (array $row): array => $row['student'], $recommendations);

        foreach ($recommendations as $row) {
            $bestMatches[$row['student']['id']] = $row['best'];
        }
    }

    // Onglet de restauration : sans lui la suppression serait un aller simple.
    $trashedStudents = ($user['role'] ?? 'guest') === 'admin' ? $store->listTrashedStudents() : [];
    $showTrashed = ($user['role'] ?? 'guest') === 'admin' && request()->boolean('trashed');

    if ($showTrashed) {
        $students = $trashedStudents;
        $bestMatches = [];
    }

    return view('students.index', [
        'students' => $students,
        'bestMatches' => $bestMatches,
        'trashedCount' => count($trashedStudents),
        'showTrashed' => $showTrashed,
        'user' => $user,
    ]);
})->name('students.index')->middleware('role:admin,recruiter');

Route::get('/students/create', function () {
    return view('students.create', [
        'user' => sr_user(),
    ]);
})->name('students.create')->middleware('role:admin');

Route::post('/students', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:160'],
        'headline' => ['required', 'string', 'max:180'],
        'location' => ['nullable', 'string', 'max:120'],
        'education' => ['nullable', 'string', 'max:180'],
        'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
        'skills' => ['nullable', 'string', 'max:500'],
        'cv_text' => ['nullable', 'string', 'max:10000'],
        'github' => ['nullable', 'url', 'max:255'],
        'linkedin' => ['nullable', 'url', 'max:255'],
        'portfolio' => ['nullable', 'url', 'max:255'],
        'cv_file' => ['nullable', 'file', 'mimes:pdf,txt,md', 'max:4096'],
    ]);

    $parser = sr_cv_parser();
    $file = $request->file('cv_file');
    $parsed = $parser->parse($file, $validated['cv_text'] ?? '');
    $fileName = $parsed['file_name'];

    if ($file && $fileName) {
        $file->move(sr_uploads_path(), $fileName);
    }

    $manualSkills = array_filter(array_map('trim', explode(',', $validated['skills'] ?? '')));
    $student = sr_store()->addStudent([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'headline' => $validated['headline'],
        'location' => $validated['location'] ?? 'Tunisie',
        'education' => ($validated['education'] ?? '') !== '' ? $validated['education'] : $parsed['education'],
        'experience_years' => $validated['experience_years'] ?? $parsed['experience_years'],
        'skills' => array_values(array_unique(array_merge($manualSkills, $parsed['skills']))),
        'links' => [
            'github' => $validated['github'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'portfolio' => $validated['portfolio'] ?? null,
        ],
        'cv_text' => $parsed['text'],
        'file_name' => $fileName,
        'metadata' => $parsed['metadata'],
    ]);

    $redirect = redirect()->route('students.show', $student['id']);

    if ($parsed['extraction_unreliable']) {
        return $redirect->with('warning', "Le texte du CV n'a pas pu être extrait automatiquement (fichier scanné/image ou format inhabituel). Complétez le champ texte CV manuellement pour un meilleur score de matching.");
    }

    return $redirect->with('success', 'Profil candidat créé et CV analysé.');
})->name('students.store')->middleware('role:admin');

Route::get('/students/{student}', function (string $student) {
    $user = sr_user();
    abort_unless($profile = sr_store()->findStudent($student), 404);

    if (($user['role'] ?? 'guest') === 'student') {
        abort_unless($profile['id'] === ($user['student_id'] ?? null), 403);
    }

    return view('students.show', [
        'student' => $profile,
        'offers' => sr_store()->all()['offers'],
        'user' => $user,
    ]);
})->name('students.show')->middleware('role:admin,recruiter,student');

/*
|--------------------------------------------------------------------------
| CV : consultation et gestion du fichier
|--------------------------------------------------------------------------
| Les CV sont stockés hors du dossier web (storage/app/uploads) : ils ne sont
| donc accessibles que par cette route, qui contrôle les droits. L'admin et le
| recruteur consultent tous les CV ; l'étudiant uniquement le sien.
*/

Route::get('/students/{student}/cv', function (string $student) {
    $store = sr_store();
    $user = sr_user();
    $role = $user['role'] ?? 'guest';

    abort_unless($profile = $store->findStudent($student), 404);

    if ($role === 'student') {
        abort_unless($profile['id'] === ($user['student_id'] ?? null), 403);
    }

    abort_unless($document = $store->findCvDocument($student), 404);

    // Le nom est re-nettoyé ici : seul un fichier du dossier d'upload est servi.
    $path = sr_uploads_path().DIRECTORY_SEPARATOR.basename($document['file_name']);

    abort_unless(is_file($path), 404);

    return response()->file($path, [
        'Content-Disposition' => 'inline; filename="'.basename($document['file_name']).'"',
    ]);
})->name('students.cv')->middleware('role:admin,recruiter,student');

Route::post('/students/{student}/cv/delete', function (string $student) {
    $store = sr_store();
    $user = sr_user();
    abort_unless($profile = $store->findStudent($student), 404);

    // Un étudiant ne retire que son propre CV ; l'administrateur, n'importe lequel.
    if (($user['role'] ?? 'guest') === 'student') {
        abort_unless($profile['id'] === ($user['student_id'] ?? null), 403);
    }

    abort_unless($store->deleteCvDocument($student), 404);

    return back()->with('success', ($profile['id'] === ($user['student_id'] ?? null))
        ? 'CV retiré de votre profil.'
        : 'CV retiré du profil de '.$profile['name'].'.');
})->name('students.cv.destroy')->middleware('role:admin,student');

Route::get('/students/{student}/edit', function (string $student) {
    $user = sr_user();
    abort_unless($profile = sr_store()->findStudent($student), 404);

    if (($user['role'] ?? 'guest') === 'student') {
        abort_unless($profile['id'] === ($user['student_id'] ?? null), 403);
    }

    return view('students.edit', [
        'student' => $profile,
        'user' => $user,
    ]);
})->name('students.edit')->middleware('role:admin,student');

Route::post('/students/{student}', function (Request $request, string $student) {
    $user = sr_user();
    abort_unless($profile = sr_store()->findStudent($student), 404);

    if (($user['role'] ?? 'guest') === 'student') {
        abort_unless($profile['id'] === ($user['student_id'] ?? null), 403);
    }

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:160'],
        'headline' => ['required', 'string', 'max:180'],
        'location' => ['nullable', 'string', 'max:120'],
        'education' => ['nullable', 'string', 'max:180'],
        'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
        'skills' => ['nullable', 'string', 'max:500'],
        'cv_text' => ['nullable', 'string', 'max:10000'],
        'cv_file' => ['nullable', 'file', 'mimes:pdf,txt,md', 'max:4096'],
        'emails' => ['nullable', 'string', 'max:500'],
        'phones' => ['nullable', 'string', 'max:500'],
        'github' => ['nullable', 'url', 'max:255'],
        'linkedin' => ['nullable', 'url', 'max:255'],
        'portfolio' => ['nullable', 'url', 'max:255'],
    ]);

    $manualSkills = array_filter(array_map('trim', explode(',', $validated['skills'] ?? '')));
    $manualEmails = array_values(array_filter(array_map('trim', explode(',', $validated['emails'] ?? ''))));
    $manualPhones = array_values(array_filter(array_map('trim', explode(',', $validated['phones'] ?? ''))));

    $update = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'headline' => $validated['headline'],
        'location' => $validated['location'] ?? $profile['location'],
        'education' => $validated['education'] ?? $profile['education'],
        'experience_years' => $validated['experience_years'] ?? $profile['experience_years'],
        'skills' => array_values(array_unique($manualSkills)),
        'links' => [
            'github' => $validated['github'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'portfolio' => $validated['portfolio'] ?? null,
        ],
        'cv_text' => $validated['cv_text'] ?? $profile['cv_text'],
        'metadata' => array_merge($profile['metadata'] ?? [], [
            'emails' => $manualEmails,
            'phones' => $manualPhones,
        ]),
    ];

    $extractionUnreliable = false;
    $file = $request->file('cv_file');

    if ($file) {
        $parsed = sr_cv_parser()->parse($file, $validated['cv_text'] ?? '');
        $fileName = $parsed['file_name'];

        if ($fileName) {
            $file->move(sr_uploads_path(), $fileName);
        }

        $update['cv_text'] = $parsed['text'];
        $update['skills'] = array_values(array_unique(array_merge($manualSkills, $parsed['skills'])));
        $update['metadata'] = [
            'emails' => array_values(array_unique(array_merge($manualEmails, $parsed['metadata']['emails'] ?? []))),
            'phones' => array_values(array_unique(array_merge($manualPhones, $parsed['metadata']['phones'] ?? []))),
            'detected_skills' => $parsed['metadata']['detected_skills'] ?? [],
        ];
        $update['file_name'] = $fileName;

        if (($validated['education'] ?? '') === '' && $parsed['education'] !== 'Non renseigné') {
            $update['education'] = $parsed['education'];
        }

        if (! isset($validated['experience_years']) && $parsed['experience_years'] > 0) {
            $update['experience_years'] = $parsed['experience_years'];
        }

        $extractionUnreliable = $parsed['extraction_unreliable'];
    }

    sr_store()->updateStudent($profile['id'], $update);

    $redirect = redirect()->route('students.show', $profile['id']);

    if ($extractionUnreliable) {
        return $redirect->with('warning', "Le texte du CV n'a pas pu être extrait automatiquement (fichier scanné/image ou format inhabituel). Complétez le champ texte CV manuellement pour un meilleur score de matching.");
    }

    return $redirect->with('success', 'Profil mis à jour.');
})->name('students.update')->middleware('role:admin,student');

Route::get('/offers', function (Request $request) {
    $store = sr_store();
    $data = $store->all();
    $user = sr_user();
    $role = $user['role'] ?? 'guest';
    $offers = $data['offers'];
    $matchScores = [];
    $statusFilter = null;
    $statusCounts = [];
    $withApplicationsOnly = false;
    $withApplicationsCount = 0;
    $ownerFilter = null;
    $ownerCounts = [];

    // L'administrateur et le recruteur partagent la meme gestion : filtres et
    // tri par score du meilleur candidat. Seul le perimetre differe (toutes les
    // offres pour l'admin, les siennes pour le recruteur), auquel s'ajoute pour
    // l'admin un filtre par proprietaire.
    if (in_array($role, ['admin', 'recruiter'], true)) {
        $managedOffers = $role === 'admin'
            ? $offers
            : array_values(array_filter($offers, fn (array $offer): bool => sr_offer_owner_matches($offer, $user)));

        $statusCounts = ['draft' => 0, 'published' => 0, 'closed' => 0];

        foreach ($managedOffers as $offer) {
            $statusCounts[$offer['status'] ?? 'published']++;
        }

        if ($role === 'admin') {
            foreach ($managedOffers as $offer) {
                $key = $offer['owner_id'] ?? '';
                $ownerCounts[$key] = ($ownerCounts[$key] ?? 0) + 1;
            }

            $requestedOwner = $request->query('owner');

            if ($requestedOwner === 'none') {
                $ownerFilter = 'none';
                $managedOffers = array_values(array_filter(
                    $managedOffers,
                    fn (array $offer): bool => ($offer['owner_id'] ?? null) === null
                ));
            } elseif (is_string($requestedOwner) && $requestedOwner !== '') {
                $ownerFilter = $requestedOwner;
                $managedOffers = array_values(array_filter(
                    $managedOffers,
                    fn (array $offer): bool => ($offer['owner_id'] ?? null) === $requestedOwner
                ));
            }
        }

        $requestedStatus = $request->query('status');
        $statusFilter = in_array($requestedStatus, ['draft', 'published', 'closed'], true) ? $requestedStatus : null;

        $offers = $statusFilter
            ? array_values(array_filter($managedOffers, fn (array $offer): bool => ($offer['status'] ?? 'published') === $statusFilter))
            : $managedOffers;

        $applicationCounts = [];

        foreach ($data['applications'] as $application) {
            $applicationCounts[$application['offer_id']] = ($applicationCounts[$application['offer_id']] ?? 0) + 1;
        }

        $withApplicationsCount = count(array_filter(
            $offers,
            fn (array $offer): bool => ($applicationCounts[$offer['id']] ?? 0) > 0
        ));

        $withApplicationsOnly = $request->boolean('with_applications');

        if ($withApplicationsOnly) {
            $offers = array_values(array_filter(
                $offers,
                fn (array $offer): bool => ($applicationCounts[$offer['id']] ?? 0) > 0
            ));
        }

        $offerScores = [];

        foreach ($offers as $offer) {
            $rankings = sr_rankings($offer, $data['students'], $store);
            $topScore = $rankings[0]['match']['score'] ?? 0;
            $offerScores[$offer['id']] = $topScore;
            $matchScores[$offer['id']] = $topScore;
        }

        usort($offers, fn (array $a, array $b): int => $offerScores[$b['id']] <=> $offerScores[$a['id']]);
    } elseif ($role === 'student') {
        $published = array_values(array_filter(
            $offers,
            fn (array $offer): bool => ($offer['status'] ?? 'published') === 'published'
        ));

        $profile = ($user['student_id'] ?? null) ? $store->findStudent($user['student_id']) : null;

        if ($profile) {
            $recommendations = sr_recommend_offers($profile, $published, $store);
            $offers = array_map(fn (array $row): array => $row['offer'], $recommendations);

            foreach ($recommendations as $row) {
                $matchScores[$row['offer']['id']] = $row['match']['score'];
            }
        } else {
            $offers = $published;
        }
    }

    // Onglet de restauration des offres supprimees (administrateur).
    $trashedOffers = $role === 'admin' ? $store->listTrashedOffers() : [];
    $showTrashed = $role === 'admin' && $request->boolean('trashed');

    if ($showTrashed) {
        $offers = $trashedOffers;
        $matchScores = [];
    }

    return view('offers.index', [
        'offers' => $offers,
        'applications' => $data['applications'],
        'matchScores' => $matchScores,
        'statusFilter' => $statusFilter,
        'statusCounts' => $statusCounts,
        'withApplicationsOnly' => $withApplicationsOnly,
        'withApplicationsCount' => $withApplicationsCount,
        'ownerFilter' => $ownerFilter,
        'ownerCounts' => $ownerCounts,
        'offerOwners' => $role === 'admin' ? $store->listOfferOwners() : [],
        'trashedCount' => count($trashedOffers),
        'showTrashed' => $showTrashed,
        'user' => $user,
    ]);
})->name('offers.index')->middleware('role:admin,recruiter,student');

Route::get('/offers/create', function () {
    return view('offers.create', [
        'user' => sr_user(),
    ]);
})->name('offers.create')->middleware('role:admin,recruiter');

Route::post('/offers', function (Request $request) {
    $validated = $request->validate([
        'title' => ['required', 'string', 'max:180'],
        'company' => ['required', 'string', 'max:160'],
        'location' => ['nullable', 'string', 'max:140'],
        'type' => ['nullable', 'string', 'max:120'],
        'required_skills' => ['nullable', 'string', 'max:500'],
        'description' => ['required', 'string', 'max:12000'],
    ]);

    $user = sr_user();
    $offer = sr_store()->addOffer([
        'title' => $validated['title'],
        'company' => $validated['company'],
        'location' => $validated['location'] ?? 'Tunisie',
        'type' => $validated['type'] ?? 'Stage',
        'description' => $validated['description'],
        'required_skills' => array_values(array_filter(array_map('trim', explode(',', $validated['required_skills'] ?? '')))),
        'owner_id' => $user['id'],
    ]);

    return redirect()->route('offers.show', $offer['id'])->with('success', 'Offre publiée. Le ranking est calculé automatiquement.');
})->name('offers.store')->middleware('role:admin,recruiter');

Route::get('/offers/{offer}', function (string $offer) {
    $store = sr_store();
    $user = sr_user();
    $role = $user['role'] ?? 'guest';
    abort_unless($job = $store->findOffer($offer), 404);

    if ($role === 'recruiter') {
        abort_unless(sr_offer_owner_matches($job, $user), 403);
    } elseif ($role === 'student') {
        abort_unless(($job['status'] ?? 'published') === 'published', 404);
    }

    $data = $store->all();
    $applications = $store->applicationsForOffer($job['id']);

    if (in_array($role, ['admin', 'recruiter'], true)) {
        $rankings = sr_rankings($job, $data['students'], $store);
    } else {
        $rankings = [];
    }

    return view('offers.show', [
        'offer' => $job,
        'rankings' => $rankings,
        'applications' => $applications,
        'offerOwners' => $role === 'admin' ? $store->listOfferOwners() : [],
        'user' => $user,
    ]);
})->name('offers.show')->middleware('role:admin,recruiter,student');

Route::get('/offers/{offer}/edit', function (string $offer) {
    $user = sr_user();
    abort_unless($job = sr_store()->findOffer($offer), 404);

    if (($user['role'] ?? 'guest') === 'recruiter') {
        abort_unless(sr_offer_owner_matches($job, $user), 403);
    }

    return view('offers.edit', [
        'offer' => $job,
        'user' => $user,
    ]);
})->name('offers.edit')->middleware('role:admin,recruiter');

Route::post('/offers/{offer}', function (Request $request, string $offer) {
    $user = sr_user();
    abort_unless($job = sr_store()->findOffer($offer), 404);

    if (($user['role'] ?? 'guest') === 'recruiter') {
        abort_unless(sr_offer_owner_matches($job, $user), 403);
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:180'],
        'company' => ['required', 'string', 'max:160'],
        'location' => ['nullable', 'string', 'max:140'],
        'type' => ['nullable', 'string', 'max:120'],
        'status' => ['required', 'in:draft,published,closed'],
        'required_skills' => ['nullable', 'string', 'max:500'],
        'description' => ['required', 'string', 'max:12000'],
    ]);

    sr_store()->updateOffer($job['id'], [
        'title' => $validated['title'],
        'company' => $validated['company'],
        'location' => $validated['location'] ?? $job['location'],
        'type' => $validated['type'] ?? $job['type'],
        'status' => $validated['status'],
        'description' => $validated['description'],
        'required_skills' => array_values(array_filter(array_map('trim', explode(',', $validated['required_skills'] ?? '')))),
    ]);

    return redirect()->route('offers.show', $job['id'])->with('success', 'Offre mise à jour.');
})->name('offers.update')->middleware('role:admin,recruiter');

Route::post('/offers/{offer}/apply', function (string $offer) {
    $store = sr_store();
    abort_unless($job = $store->findOffer($offer), 404);

    $user = sr_user();
    $studentId = $user['student_id'] ?? null;
    abort_unless($studentId && $store->findStudent($studentId), 404);

    $store->apply($job['id'], $studentId);

    return back()->with('success', 'Candidature envoyée en un clic.');
})->name('offers.apply')->middleware('role:student');

Route::post('/applications/{application}/status', function (Request $request, string $application) {
    $store = sr_store();
    $user = sr_user();
    abort_unless($record = $store->findApplication($application), 404);

    if (($user['role'] ?? 'guest') === 'recruiter') {
        $offer = $store->findOffer($record['offer_id']);
        abort_unless($offer && sr_offer_owner_matches($offer, $user), 403);
    }

    $validated = $request->validate([
        'status' => ['required', 'in:submitted,shortlisted,interview,rejected'],
    ]);

    abort_unless($store->updateApplicationStatus($application, $validated['status']), 404);

    return back()->with('success', 'Statut de candidature mis a jour.');
})->name('applications.status')->middleware('role:admin,recruiter');

/*
|--------------------------------------------------------------------------
| Administration : offres, étudiants et candidatures
|--------------------------------------------------------------------------
*/

Route::get('/admin/applications', function (Request $request) {
    $store = sr_store();
    $status = $request->query('status');
    $statusFilter = in_array($status, ['submitted', 'shortlisted', 'interview', 'rejected'], true) ? $status : null;
    $trashed = $request->boolean('trashed');

    $all = $store->listApplications(['trashed' => true]);
    $counts = ['submitted' => 0, 'shortlisted' => 0, 'interview' => 0, 'rejected' => 0, 'trashed' => 0, 'active' => 0];

    foreach ($all as $application) {
        if ($application['deleted']) {
            $counts['trashed']++;

            continue;
        }

        $counts['active']++;
        $counts[$application['status']] = ($counts[$application['status']] ?? 0) + 1;
    }

    $applications = array_values(array_filter($all, function (array $application) use ($statusFilter, $trashed): bool {
        if ($trashed) {
            return $application['deleted'];
        }

        if ($application['deleted']) {
            return false;
        }

        return $statusFilter === null || $application['status'] === $statusFilter;
    }));

    return view('admin.applications', [
        'applications' => $applications,
        'statusFilter' => $statusFilter,
        'trashed' => $trashed,
        'counts' => $counts,
        'user' => sr_user(),
    ]);
})->name('admin.applications.index')->middleware('role:admin');

Route::post('/admin/applications/{application}/delete', function (string $application) {
    $store = sr_store();
    abort_unless($store->findApplication($application), 404);

    $store->softDeleteApplication($application);

    return back()->with('success', 'Candidature supprimée. Elle reste restaurable.');
})->name('admin.applications.destroy')->middleware('role:admin');

Route::post('/admin/applications/{application}/restore', function (string $application) {
    abort_unless(sr_store()->restoreApplication($application), 404);

    return back()->with('success', 'Candidature restaurée.');
})->name('admin.applications.restore')->middleware('role:admin');

Route::get('/offers/{offer}/delete', function (string $offer) {
    $store = sr_store();
    abort_unless($job = $store->findOffer($offer), 404);

    return view('admin.confirm', [
        'title' => "Supprimer l'offre",
        'entity' => $job['title'].' — '.$job['company'],
        'impact' => [count($store->applicationsForOffer($job['id'])).' candidature(s) reçue(s) sur cette offre'],
        'action' => route('offers.destroy', $job['id']),
        'cancel' => route('offers.show', $job['id']),
        'user' => sr_user(),
    ]);
})->name('offers.confirm-delete')->middleware('role:admin');

Route::post('/offers/{offer}/delete', function (string $offer) {
    $store = sr_store();
    abort_unless($job = $store->findOffer($offer), 404);

    $store->softDeleteOffer($offer);

    return redirect()->route('offers.index')
        ->with('success', 'Offre supprimée : '.$job['title'].'. Elle reste restaurable.');
})->name('offers.destroy')->middleware('role:admin');

Route::post('/offers/{offer}/restore', function (string $offer) {
    abort_unless(sr_store()->restoreOffer($offer), 404);

    return back()->with('success', 'Offre restaurée avec ses candidatures.');
})->name('offers.restore')->middleware('role:admin');

Route::post('/offers/{offer}/reassign', function (Request $request, string $offer) {
    $store = sr_store();
    abort_unless($store->findOffer($offer), 404);

    $validated = $request->validate([
        'owner_id' => ['required', 'string', 'max:64'],
    ]);

    abort_unless($store->reassignOffer($offer, $validated['owner_id']), 422);

    return back()->with('success', 'Offre réassignée à un autre recruteur.');
})->name('offers.reassign')->middleware('role:admin');

Route::get('/students/{student}/delete', function (string $student) {
    $store = sr_store();
    abort_unless($profile = $store->findStudent($student), 404);

    $applications = array_filter(
        $store->all()['applications'],
        fn (array $application): bool => $application['student_id'] === $profile['id']
    );

    return view('admin.confirm', [
        'title' => 'Supprimer le profil candidat',
        'entity' => $profile['name'].' — '.$profile['headline'],
        'impact' => [count($applications).' candidature(s) envoyée(s) par ce candidat'],
        'action' => route('students.destroy', $profile['id']),
        'cancel' => route('students.show', $profile['id']),
        'user' => sr_user(),
    ]);
})->name('students.confirm-delete')->middleware('role:admin');

Route::post('/students/{student}/delete', function (string $student) {
    $store = sr_store();
    abort_unless($profile = $store->findStudent($student), 404);

    $store->softDeleteStudent($student);

    return redirect()->route('students.index')
        ->with('success', 'Profil supprimé : '.$profile['name'].'. Il reste restaurable.');
})->name('students.destroy')->middleware('role:admin');

Route::post('/students/{student}/restore', function (string $student) {
    abort_unless(sr_store()->restoreStudent($student), 404);

    return back()->with('success', 'Profil candidat restauré.');
})->name('students.restore')->middleware('role:admin');

Route::get('/api/offers/{offer}/ranking', function (string $offer) {
    $store = sr_store();
    $user = sr_user();
    abort_unless($job = $store->findOffer($offer), 404);

    if (($user['role'] ?? 'guest') === 'recruiter') {
        abort_unless(sr_offer_owner_matches($job, $user), 403);
    }

    return response()->json([
        'offer' => $job,
        'rankings' => sr_rankings($job, $store->all()['students'], $store),
    ]);
})->middleware('role:admin,recruiter');
