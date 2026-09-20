<?php

declare(strict_types=1);

/**
 * Render real Blade screens with wholly fictional fixtures.
 * No .env loading, application kernel, database provider, route dispatch or HTTP call.
 * Run: php -d allow_url_fopen=0 output/latex/smartrecruit-memoire-kanban-uml/evidence/interfaces/render.php
 * Then serve only the site/ directory created next to this script.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$projectRoot = dirname(__DIR__, 5);
$previewRoot = __DIR__;
$siteRoot = $previewRoot.'/site';
$origin = 'http://smartrecruit-preview.invalid';

foreach ([$siteRoot, $previewRoot.'/compiled', $previewRoot.'/storage'] as $directory) {
    if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
        throw new RuntimeException('Cannot create preview directory: '.$directory);
    }
}

require $projectRoot.'/vendor/autoload.php';

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\View\ViewServiceProvider;

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (! (error_reporting() & $severity)) {
        return false;
    }
    if (in_array($severity, [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE], true)) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});

// Deliberately do not require bootstrap/app.php or bootstrap the console/HTTP kernel.
// Application's base providers are framework event/log/routing/context providers only.
$app = new Application($projectRoot);
$app->useStoragePath($previewRoot.'/storage');
$app->instance('config', new Repository([
    'app' => ['name' => 'SmartRecruit Preview', 'url' => $origin, 'locale' => 'fr', 'fallback_locale' => 'fr', 'timezone' => 'UTC', 'debug' => false],
    'view' => ['paths' => [$projectRoot.'/resources/views'], 'compiled' => $previewRoot.'/compiled', 'cache' => false],
    'cache' => ['default' => 'array', 'stores' => ['array' => ['driver' => 'array']]],
    'session' => ['driver' => 'array', 'files' => $previewRoot.'/storage/sessions'],
    'logging' => ['default' => 'stderr', 'channels' => ['stderr' => ['driver' => 'monolog', 'handler' => Monolog\Handler\StreamHandler::class, 'with' => ['stream' => 'php://stderr']]]],
    'smart_recruit' => [
        'application_status_labels' => ['submitted' => 'Reçue', 'shortlisted' => 'Présélectionnée', 'interview' => 'Acceptée', 'rejected' => 'Refusée'],
        'offer_status_labels' => ['draft' => 'Brouillon', 'published' => 'Publiée', 'closed' => 'Fermée'],
        'role_labels' => ['admin' => 'Administrateur', 'recruiter' => 'Recruteur', 'student' => 'Étudiant'],
    ],
]));
$app->instance('files', new Filesystem());
$app->instance('translator', new Translator(new ArrayLoader(), 'fr'));
$app->bind('db', static fn () => throw new RuntimeException('Database access is forbidden in static preview.'));
$app->bind('db.schema', static fn () => throw new RuntimeException('Schema access is forbidden in static preview.'));
$app->bind('Illuminate\Http\Client\Factory', static fn () => throw new RuntimeException('HTTP calls are forbidden in static preview.'));
Facade::setFacadeApplication($app);
$app->register(ViewServiceProvider::class);

// Register route definitions only. The route closures and middleware are never run.
require $projectRoot.'/routes/web.php';
$routes = $app['router']->getRoutes();
$routes->refreshNameLookups();
$request = Request::create($origin.'/');
$app->instance('request', $request);
$url = new UrlGenerator($routes, $request);
$url->forceRootUrl($origin);
$app->instance('url', $url);
$session = new Store('static-preview', new ArraySessionHandler(120));
$session->start();
$session->put('_token', str_repeat('preview', 6));
$app->instance('session', $session);
$app->instance('session.store', $session);
$request->setLaravelSession($session);
$view = $app['view'];

$stamp = '2026-09-10 10:00:00';
$baseUser = ['student_id' => null, 'company_name' => '', 'position' => '', 'website' => '', 'created_at' => $stamp, 'deleted' => false];
$admin = array_replace($baseUser, ['id' => 'usr_preview_admin', 'name' => 'Administration Démo', 'email' => 'admin@example.test', 'role' => 'admin']);
$recruiter = array_replace($baseUser, ['id' => 'usr_preview_recruiter', 'name' => 'Recruteur Exemple', 'email' => 'recruteur@example.test', 'role' => 'recruiter', 'company_name' => 'Atelier Démo', 'position' => 'Responsable des talents', 'website' => 'https://example.test']);
$guest = array_replace($baseUser, ['id' => null, 'name' => 'Visiteur Démo', 'email' => '', 'role' => 'guest']);
$students = [];
$studentNames = ['Lina Exemple', 'Sami Exemple', 'Maya Exemple', 'Adam Exemple', 'Nour Exemple', 'Yanis Exemple'];
$headlines = ['Développeuse web — Laravel et React', 'Développeur Java et Spring Boot', 'Analyse de données et Python', 'Développeur mobile Flutter', 'Design UI et intégration front-end', 'Infrastructure, cloud et DevOps'];
$skillSets = [['Laravel', 'PHP', 'React', 'SQL', 'Git', 'Tests'], ['Java', 'Spring Boot', 'SQL', 'Docker'], ['Python', 'Pandas', 'SQL', 'Power BI'], ['Flutter', 'Dart', 'Firebase'], ['Figma', 'HTML', 'CSS', 'JavaScript'], ['Docker', 'Linux', 'CI/CD', 'AWS']];
foreach ($studentNames as $index => $name) {
    $students[] = [
        'id' => 'stu_preview_'.($index + 1), 'user_id' => 'usr_preview_student_'.($index + 1),
        'name' => $name, 'email' => 'profil'.($index + 1).'@example.test',
        'headline' => $headlines[$index], 'location' => ['Tunis', 'Ariana', 'Sfax'][$index % 3],
        'education' => $index % 2 === 0 ? 'Master Développement Logiciel' : 'Licence en informatique',
        'experience_years' => $index % 3, 'skills' => $skillSets[$index],
        'links' => ['github' => 'https://example.test/projet-fictif', 'linkedin' => '', 'portfolio' => ''],
        'cv_text' => "Profil entièrement fictif créé pour vérifier la présentation.\nProjet académique : conception d'une plateforme web, travail en équipe et tests.\nCompétences : ".implode(', ', $skillSets[$index]).'.',
        'file_name' => $index === 3 ? null : 'cv-fictif-'.($index + 1).'.pdf',
        'metadata' => ['emails' => ['profil'.($index + 1).'@example.test'], 'phones' => [], 'detected_skills' => $skillSets[$index]],
        'created_at' => $stamp, 'deleted' => false,
    ];
}
$studentUser = array_replace($baseUser, ['id' => 'usr_preview_student_1', 'name' => $students[0]['name'], 'email' => $students[0]['email'], 'role' => 'student', 'student_id' => $students[0]['id']]);
$users = [$admin, $recruiter, $studentUser];
foreach (array_slice($students, 1) as $studentRecord) {
    $users[] = array_replace($baseUser, ['id' => $studentRecord['user_id'], 'name' => $studentRecord['name'], 'email' => $studentRecord['email'], 'role' => 'student', 'student_id' => $studentRecord['id']]);
}
$offers = [];
$offerTitles = ['Stage PFE — Développement full-stack Laravel & React', 'Développeur Java / Spring Boot', 'Stage Data Analyst — tableaux de bord et qualité des données', 'Stage mobile Flutter'];
foreach ($offerTitles as $index => $title) {
    $offers[] = [
        'id' => 'off_preview_'.($index + 1), 'title' => $title, 'company' => 'Atelier Démo',
        'location' => $index % 2 === 0 ? 'Tunis · Hybride' : 'Ariana', 'type' => 'Stage PFE · 4 à 6 mois',
        'description' => "Offre entièrement fictive pour l'aperçu visuel. Rejoignez une équipe de démonstration pour concevoir des interfaces claires, développer des fonctionnalités utiles et améliorer la qualité du logiciel. Les missions comprennent la modélisation, l'intégration et les tests.",
        'required_skills' => $skillSets[$index], 'owner_id' => $recruiter['id'],
        'status' => ['published', 'published', 'draft', 'closed'][$index], 'created_at' => $stamp, 'deleted' => false,
    ];
}
$scores = [94, 87, 76, 64, 52, 38];
$rankings = [];
foreach ($students as $index => $studentRecord) {
    $rankings[] = ['student' => $studentRecord, 'match' => [
        'score' => $scores[$index], 'matched_skills' => array_slice($studentRecord['skills'], 0, 3),
        'missing_skills' => $index === 0 ? [] : ['React'], 'text_similarity' => max(20, $scores[$index] - 6),
        'semantic_coverage' => $index < 3 ? 100 : 50, 'algorithm' => 'tf-idf-cosine', 'source' => 'aperçu-fictif',
        'explanation' => 'Indicateur fictif de présentation : les compétences principales correspondent à cette offre.',
    ]];
}
$applications = [];
foreach ($students as $index => $studentRecord) {
    $offerRecord = $offers[$index % 2];
    $applications[] = [
        'id' => 'app_preview_'.($index + 1), 'student_id' => $studentRecord['id'], 'offer_id' => $offerRecord['id'],
        'student_name' => $studentRecord['name'], 'student_headline' => $studentRecord['headline'],
        'offer_title' => $offerRecord['title'], 'offer_company' => $offerRecord['company'],
        'status' => ['submitted', 'shortlisted', 'interview', 'rejected'][$index % 4], 'match_score' => $index === 5 ? null : $scores[$index],
        'applied_at' => $stamp, 'created_at' => $stamp, 'deleted' => false,
    ];
}
$offerSummaries = [];
foreach ($offers as $index => $offerRecord) {
    $offerSummaries[] = ['offer' => $offerRecord, 'top' => $index === 3 ? null : $rankings[$index], 'applications' => count(array_filter($applications, fn ($record) => $record['offer_id'] === $offerRecord['id']))];
}
$bestMatches = [];
foreach ($rankings as $ranking) {
    $bestMatches[$ranking['student']['id']] = ['offer' => $offers[0], 'match' => $ranking['match']];
}
$data = ['users' => $users, 'students' => $students, 'offers' => $offers, 'applications' => $applications];
$userCounts = ['admin' => 1, 'recruiter' => 1, 'student' => count($students), 'trashed' => 0];
$applicationCounts = ['active' => count($applications), 'trashed' => 0, 'submitted' => 2, 'shortlisted' => 2, 'interview' => 1, 'rejected' => 1];
$common = [
    'data' => $data, 'user' => $admin, 'users' => $users, 'students' => $students, 'student' => $students[0], 'profile' => $students[0],
    'offers' => $offers, 'offer' => $offers[0], 'applications' => $applications, 'rankings' => $rankings,
    'offerSummaries' => $offerSummaries, 'bestMatches' => $bestMatches,
    'myApplications' => [['offer' => $offers[0], 'application' => $applications[0]]],
    'matchScores' => array_combine(array_column($offers, 'id'), [94, 87, 76, 64]),
    'aiHealth' => ['status' => 'ok', 'message' => 'État fictif pour aperçu'],
    'offerCount' => count($offers), 'applicationCount' => count($applications),
    'statusFilter' => null, 'statusCounts' => ['published' => 2, 'draft' => 1, 'closed' => 1],
    'withApplicationsOnly' => false, 'withApplicationsCount' => 2, 'ownerFilter' => null,
    'offerOwners' => [$recruiter], 'ownerCounts' => [$recruiter['id'] => count($offers), '' => 0],
    'showTrashed' => false, 'trashed' => false, 'trashedCount' => 0, 'roleFilter' => null,
    'userCounts' => $userCounts, 'counts' => $userCounts, 'account' => null, 'defaultRole' => 'recruiter', 'role' => null,
    'title' => 'Supprimer un compte fictif', 'entity' => 'Compte de démonstration',
    'impact' => ['Un profil fictif', 'Deux candidatures de démonstration'],
    'cancel' => $url->route('admin.users.index'), 'action' => $url->route('admin.users.destroy', $studentUser['id']),
];

$cases = [];
$add = static function (string $name, string $template, string $routeName, array $identity, array $overrides = [], array $query = []) use (&$cases): void {
    $cases[] = compact('name', 'template', 'routeName', 'identity', 'overrides', 'query');
};
foreach (['admin' => $admin, 'student' => $studentUser, 'recruiter' => $recruiter, 'guest' => $guest] as $roleName => $identity) {
    $add('dashboard-'.$roleName, 'dashboard.'.$roleName, 'dashboard', $identity);
}
$add('login', 'auth.login', 'login', $guest);
$add('register', 'auth.register', 'register', $guest);
$add('register-student', 'auth.register', 'register', $guest, ['role' => 'student'], ['role' => 'student']);
$add('register-recruiter', 'auth.register', 'register', $guest, ['role' => 'recruiter'], ['role' => 'recruiter']);
foreach (['admin' => $admin, 'recruiter' => $recruiter, 'student' => $studentUser] as $roleName => $identity) {
    $add('offers-index-'.$roleName, 'offers.index', 'offers.index', $identity, ['offers' => $roleName === 'student' ? array_slice($offers, 0, 2) : $offers]);
    $add('offers-show-'.$roleName, 'offers.show', 'offers.show', $identity);
    $add('students-show-'.$roleName, 'students.show', 'students.show', $identity);
}
$add('offers-create', 'offers.create', 'offers.create', $recruiter);
$add('offers-edit', 'offers.edit', 'offers.edit', $recruiter);
$add('students-index-admin', 'students.index', 'students.index', $admin);
$add('students-index-recruiter', 'students.index', 'students.index', $recruiter);
$add('students-create', 'students.create', 'students.create', $admin);
$add('students-edit', 'students.edit', 'students.edit', $studentUser);
$add('admin-index', 'admin.index', 'admin.index', $admin);
$add('admin-applications', 'admin.applications', 'admin.applications.index', $admin, ['counts' => $applicationCounts]);
$add('admin-users-index', 'admin.users.index', 'admin.users.index', $admin);
$add('admin-users-create', 'admin.users.form', 'admin.users.create', $admin);
$add('admin-users-edit', 'admin.users.form', 'admin.users.edit', $admin, ['account' => $recruiter]);
$add('admin-confirm', 'admin.confirm', 'admin.users.confirm-delete', $admin);
$add('offers-empty', 'offers.index', 'offers.index', $studentUser, ['offers' => [], 'applications' => []]);
$add('admin-applications-empty', 'admin.applications', 'admin.applications.index', $admin, ['applications' => [], 'counts' => array_fill_keys(array_keys($applicationCounts), 0)]);
$add('login-errors', 'auth.login', 'login', $guest, ['_preview_errors' => ['email' => ['Cette adresse fictive illustre une erreur de validation.']]]);

// Copy presentation assets only, so the site can be served with no project root access.
$assetCount = 0;
if (is_dir($projectRoot.'/assets')) {
    $assetFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projectRoot.'/assets', FilesystemIterator::SKIP_DOTS));
    foreach ($assetFiles as $asset) {
        if (! $asset->isFile() || ! in_array(strtolower($asset->getExtension()), ['css', 'svg', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'ico', 'woff', 'woff2', 'ttf', 'otf', 'js'], true)) {
            continue;
        }
        $relative = str_replace('\\', '/', substr($asset->getPathname(), strlen($projectRoot.'/assets') + 1));
        $destination = $siteRoot.'/assets/'.$relative;
        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0775, true);
        }
        copy($asset->getPathname(), $destination);
        $assetCount++;
    }
}

$destinations = [];
foreach ($cases as $case) {
    $roleName = $case['identity']['role'];
    $destinations[$roleName][$case['routeName']] ??= $case['name'].'.html';
    $destinations['default'][$case['routeName']] ??= $case['name'].'.html';
}

$report = ['fixture_notice' => 'All names, documents, companies, accounts and scores are fictional preview fixtures.', 'environment' => ['dotenv_loaded' => false, 'database_provider_registered' => false, 'route_handlers_dispatched' => 0, 'network_calls' => 0, 'php' => PHP_VERSION], 'asset_files_copied' => $assetCount, 'pages' => [], 'errors' => []];
foreach ($cases as $case) {
    try {
        $roleName = $case['identity']['role'];
        $activeRoute = $routes->getByName($case['routeName']);
        if (! $activeRoute) {
            throw new RuntimeException('Missing named route: '.$case['routeName']);
        }
        $request = Request::create($origin.'/preview', 'GET', $case['query']);
        $request->setRouteResolver(static fn () => $activeRoute);
        $request->setLaravelSession($session);
        $app->instance('request', $request);
        $url->setRequest($request);
        Facade::clearResolvedInstance('request');
        $session->put('smart_recruit_user', $case['identity']);
        $session->forget(['success', 'warning', 'error', '_old_input']);
        $values = array_replace($common, ['user' => $case['identity']], $case['overrides']);
        $values['errors'] = (new ViewErrorBag())->put('default', new MessageBag($values['_preview_errors'] ?? []));
        $html = $view->make($case['template'], $values)->render();
        $html = preg_replace_callback('/\b(href|src|action)="([^"]*)"/u', static function (array $match) use ($origin, $routes, $destinations, $roleName): string {
            [$full, $attribute, $encoded] = $match;
            $target = html_entity_decode($encoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($attribute === 'action') {
                return 'action="#preview-static" data-preview-action="disabled"';
            }
            if (str_starts_with($target, $origin.'/assets/')) {
                return $attribute.'="'.substr($target, strlen($origin) + 1).'"';
            }
            if (str_starts_with($target, '/assets/')) {
                return $attribute.'="'.ltrim($target, '/').'"';
            }
            if ($attribute === 'href' && str_starts_with($target, $origin)) {
                try {
                    $linkedRequest = Request::create($target, 'GET');
                    $name = $routes->match($linkedRequest)->getName();
                    if ($name === 'register' && in_array($linkedRequest->query('role'), ['student', 'recruiter'], true)) {
                        $destination = 'register-'.$linkedRequest->query('role').'.html';
                    } else {
                        $destination = $destinations[$roleName][$name] ?? $destinations['default'][$name] ?? '#preview-static';
                    }
                    return 'href="'.$destination.'"';
                } catch (Throwable) {
                    return 'href="#preview-static"';
                }
            }
            if (preg_match('~^(?:https?:)?//~i', $target)) {
                return $attribute.'="#preview-static"';
            }
            return $full;
        }, $html);
        $html = str_replace('</head>', '<meta name="smartrecruit-preview" content="Données entièrement fictives. Aperçu statique sans base ni actions métier."></head>', $html);
        $html = str_replace('</body>', '<script>document.addEventListener("submit",function(event){event.preventDefault();},true);</script></body>', $html);
        $file = $case['name'].'.html';
        file_put_contents($siteRoot.'/'.$file, $html);
        $report['pages'][] = ['file' => $file, 'view' => $case['template'], 'role' => $roleName, 'bytes' => strlen($html)];
    } catch (Throwable $error) {
        $report['errors'][] = ['case' => $case['name'], 'view' => $case['template'], 'message' => $error->getMessage()];
        fwrite(STDERR, $case['name'].': '.$error->getMessage().PHP_EOL);
    } finally {
        $view->flushState();
    }
}

$screenViews = [];
$viewFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projectRoot.'/resources/views', FilesystemIterator::SKIP_DOTS));
foreach ($viewFiles as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }
    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($projectRoot.'/resources/views') + 1));
    if ($relative === 'layout.blade.php' || str_starts_with($relative, 'partials/')) {
        continue;
    }
    $screenViews[] = str_replace('/', '.', substr($relative, 0, -10));
}
sort($screenViews);
$renderedViews = array_values(array_unique(array_column($report['pages'], 'view')));
$report['screen_view_count'] = count($screenViews);
$report['rendered_view_count'] = count($renderedViews);
$report['uncovered_views'] = array_values(array_diff($screenViews, $renderedViews));
$report['page_count'] = count($report['pages']);
$report['limits'] = ['Static rendering is not a controller, authorization, persistence or service test.', 'Filters and actions are visual only; submissions are blocked and business URLs are replaced.', 'CV filenames are fictional; there is no document download.', 'Re-run to copy updated CSS/assets and render updated Blade files.'];
$links = '';
foreach ($report['pages'] as $page) {
    $links .= '<a href="'.htmlspecialchars($page['file'], ENT_QUOTES).'">'.htmlspecialchars($page['file'], ENT_QUOTES).'<small>'.htmlspecialchars($page['view'].' · '.$page['role'], ENT_QUOTES).'</small></a>';
}
$indexHtml = '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>SmartRecruit — Aperçus fictifs</title><style>body{font:16px system-ui;margin:40px;background:#f4f5f2;color:#17201b}main{max-width:1100px;margin:auto}h1{font-size:28px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px}a{display:block;padding:16px;border:1px solid #dfe6df;background:white;border-radius:12px;color:#126b5b;text-decoration:none}small{display:block;color:#68736f;margin-top:6px}p{line-height:1.6}</style><main><h1>SmartRecruit — vérification visuelle</h1><p>Données entièrement fictives. Vrais templates Blade et composants partagés. Aucune base, aucun service ni action métier. Les formulaires sont inactifs.</p><p>'.$report['rendered_view_count'].' templates couverts · '.$report['page_count'].' variantes · '.count($report['errors']).' erreur(s).</p><div class="grid">'.$links.'</div></main></html>';
file_put_contents($siteRoot.'/index.html', $indexHtml);
file_put_contents($previewRoot.'/report.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo json_encode(['screen_views' => count($screenViews), 'rendered_views' => count($renderedViews), 'pages' => count($report['pages']), 'errors' => count($report['errors']), 'uncovered_views' => $report['uncovered_views'], 'site' => $siteRoot], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
exit($report['errors'] || $report['uncovered_views'] ? 1 : 0);
