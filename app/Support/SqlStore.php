<?php

namespace SmartRecruit\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class SqlStore
{
    private static function defaultPasswordHash(): string
    {
        return password_hash(config('smart_recruit.default_password'), PASSWORD_BCRYPT);
    }

    /*
     * Constructeurs de requêtes de lecture.
     *
     * Toute lecture applicative passe par ces méthodes : elles excluent les
     * lignes soft-deleted. Les tests d'existence de syncDemoData() utilisent
     * volontairement DB::table() directement, afin de « voir » les lignes
     * supprimées et de ne pas les recréer.
     */

    private function activeUsers()
    {
        return DB::table('sr_users')->whereNull('sr_users.deleted_at');
    }

    private function activeStudentProfiles()
    {
        return DB::table('sr_student_profiles')->whereNull('sr_student_profiles.deleted_at');
    }

    private function activeRecruiterProfiles()
    {
        return DB::table('sr_recruiter_profiles')->whereNull('sr_recruiter_profiles.deleted_at');
    }

    private function activeOffers()
    {
        return DB::table('sr_offers')->whereNull('sr_offers.deleted_at');
    }

    private function activeApplications()
    {
        return DB::table('sr_applications')->whereNull('sr_applications.deleted_at');
    }

    /**
     * Horodatage de suppression, à la microseconde.
     *
     * La restauration est symétrique : elle relève les lignes dont le
     * `deleted_at` est identique à celui du parent, c'est-à-dire celles
     * supprimées lors de la même opération. now() n'écrit que la seconde :
     * deux suppressions rapprochées deviendraient indiscernables et une ligne
     * supprimée individuellement serait relevée à tort.
     */
    private function deletionStamp(): string
    {
        return now()->format('Y-m-d H:i:s.u');
    }

    public static function isAvailable(): bool
    {
        try {
            DB::connection()->getPdo();

            return Schema::hasTable('sr_users')
                && Schema::hasTable('sr_student_profiles')
                && Schema::hasTable('sr_offers')
                && Schema::hasTable('sr_applications');
        } catch (Throwable) {
            return false;
        }
    }

    public function all(): array
    {
        $this->seedIfEmpty();

        return [
            'users' => $this->users(),
            'students' => $this->students(),
            'offers' => $this->offers(),
            'applications' => $this->applications(),
        ];
    }

    public function reset(): void
    {
        DB::transaction(function (): void {
            DB::table('sr_match_scores')->delete();
            DB::table('sr_applications')->delete();
            DB::table('sr_cv_documents')->delete();
            DB::table('sr_offers')->delete();
            DB::table('sr_recruiter_profiles')->delete();
            DB::table('sr_student_profiles')->delete();
            DB::table('sr_users')->delete();
        });

        $this->syncDemoData();
    }

    public function ensureDefaultAccounts(): array
    {
        $this->syncDemoData();

        $this->activeUsers()->update([
            'password' => self::defaultPasswordHash(),
            'updated_at' => now(),
        ]);

        $counts = $this->activeUsers()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        return [
            'admin' => (int) ($counts['admin'] ?? 0),
            'recruiter' => (int) ($counts['recruiter'] ?? 0),
            'student' => (int) ($counts['student'] ?? 0),
        ];
    }

    public function syncDemoData(): void
    {
        $seed = DemoStore::seedData();

        DB::transaction(function () use ($seed): void {
            foreach ($seed['users'] as $user) {
                $userId = DB::table('sr_users')
                    ->where('public_id', $user['id'])
                    ->orWhere('email', $user['email'])
                    ->value('id');

                if (! $userId) {
                    $userId = DB::table('sr_users')->insertGetId([
                        'public_id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'password' => self::defaultPasswordHash(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if ($user['role'] === 'recruiter' && ! DB::table('sr_recruiter_profiles')->where('public_id', 'rec_demo')->exists()) {
                    DB::table('sr_recruiter_profiles')->insert([
                        'public_id' => 'rec_demo',
                        'user_id' => $userId,
                        'company_name' => 'Smart Business Solutions',
                        'position' => 'Talent Manager',
                        'website' => 'https://smartbs.tn',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $recruiterProfileId = DB::table('sr_recruiter_profiles')->where('public_id', 'rec_demo')->value('id')
                ?: $this->defaultRecruiterProfileId();

            foreach ($seed['students'] as $student) {
                if (DB::table('sr_student_profiles')->where('public_id', $student['id'])->exists()) {
                    continue;
                }

                $userId = DB::table('sr_users')->where('email', $student['email'])->value('id');

                if (! $userId) {
                    $userId = DB::table('sr_users')->insertGetId([
                        'public_id' => 'usr_'.Str::after($student['id'], 'stu_'),
                        'name' => $student['name'],
                        'email' => $student['email'],
                        'role' => 'student',
                        'password' => self::defaultPasswordHash(),
                        'created_at' => $student['created_at'],
                        'updated_at' => $student['created_at'],
                    ]);
                }

                DB::table('sr_student_profiles')->insert([
                    'public_id' => $student['id'],
                    'user_id' => $userId,
                    'headline' => $student['headline'],
                    'location' => $student['location'],
                    'education' => $student['education'],
                    'experience_years' => $student['experience_years'],
                    'skills' => $this->toJson($student['skills']),
                    'links' => $this->toJson($student['links']),
                    'cv_text' => $student['cv_text'],
                    'cv_metadata' => $this->toJson($student['metadata']),
                    'created_at' => $student['created_at'],
                    'updated_at' => $student['created_at'],
                ]);
            }

            foreach ($seed['offers'] as $offer) {
                if (DB::table('sr_offers')->where('public_id', $offer['id'])->exists()) {
                    continue;
                }

                DB::table('sr_offers')->insert([
                    'public_id' => $offer['id'],
                    'recruiter_profile_id' => $recruiterProfileId,
                    'title' => $offer['title'],
                    'company' => $offer['company'],
                    'location' => $offer['location'],
                    'type' => $offer['type'],
                    'description' => $offer['description'],
                    'required_skills' => $this->toJson($offer['required_skills']),
                    'status' => $offer['status'],
                    'created_at' => $offer['created_at'],
                    'updated_at' => $offer['created_at'],
                ]);
            }

            foreach ($seed['applications'] as $application) {
                if (DB::table('sr_applications')->where('public_id', $application['id'])->exists()) {
                    continue;
                }

                $offerId = DB::table('sr_offers')->where('public_id', $application['offer_id'])->value('id');
                $studentProfileId = DB::table('sr_student_profiles')->where('public_id', $application['student_id'])->value('id');

                if (! $offerId || ! $studentProfileId) {
                    continue;
                }

                $exists = DB::table('sr_applications')
                    ->where('offer_id', $offerId)
                    ->where('student_profile_id', $studentProfileId)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('sr_applications')->insert([
                    'public_id' => $application['id'],
                    'offer_id' => $offerId,
                    'student_profile_id' => $studentProfileId,
                    'status' => $application['status'],
                    'applied_at' => $application['applied_at'],
                    'created_at' => $application['applied_at'],
                    'updated_at' => $application['applied_at'],
                ]);
            }
        });
    }

    public function addStudent(array $payload): array
    {
        return DB::transaction(function () use ($payload): array {
            $publicId = 'stu_'.Str::lower(Str::random(8));
            $userId = DB::table('sr_users')->insertGetId([
                'public_id' => 'usr_'.Str::lower(Str::random(8)),
                'name' => $payload['name'],
                'email' => $payload['email'],
                'role' => 'student',
                'password' => self::defaultPasswordHash(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $profileId = DB::table('sr_student_profiles')->insertGetId([
                'public_id' => $publicId,
                'user_id' => $userId,
                'headline' => $payload['headline'] ?? null,
                'location' => $payload['location'] ?? null,
                'education' => $payload['education'] ?? null,
                'experience_years' => (int) ($payload['experience_years'] ?? 0),
                'skills' => $this->toJson($payload['skills'] ?? []),
                'links' => $this->toJson(array_filter($payload['links'] ?? [])),
                'cv_text' => $payload['cv_text'] ?? null,
                'cv_metadata' => $this->toJson($payload['metadata'] ?? []),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! empty($payload['file_name'])) {
                DB::table('sr_cv_documents')->insert([
                    'student_profile_id' => $profileId,
                    'original_name' => $payload['file_name'],
                    'stored_path' => 'storage/app/uploads/'.$payload['file_name'],
                    'mime_type' => null,
                    'size' => 0,
                    'parser_result' => $this->toJson($payload['metadata'] ?? []),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $this->findStudent($publicId);
        });
    }

    public function addOffer(array $payload): array
    {
        return DB::transaction(function () use ($payload): array {
            $publicId = 'off_'.Str::lower(Str::random(8));

            $recruiterProfileId = isset($payload['owner_id'])
                ? $this->recruiterProfileIdForUser($payload['owner_id'])
                : $this->defaultRecruiterProfileId();

            DB::table('sr_offers')->insert([
                'public_id' => $publicId,
                'recruiter_profile_id' => $recruiterProfileId,
                'title' => $payload['title'],
                'company' => $payload['company'],
                'location' => $payload['location'] ?? null,
                'type' => $payload['type'] ?? 'Stage',
                'description' => $payload['description'],
                'required_skills' => $this->toJson($payload['required_skills'] ?? []),
                'status' => $payload['status'] ?? 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $this->findOffer($publicId);
        });
    }

    public function updateStudent(string $id, array $payload): ?array
    {
        $profile = $this->activeStudentProfiles()->where('public_id', $id)->first();

        if (! $profile) {
            return null;
        }

        $userUpdate = [];
        foreach (['name', 'email'] as $field) {
            if (array_key_exists($field, $payload)) {
                $userUpdate[$field] = $payload[$field];
            }
        }

        if ($userUpdate) {
            $userUpdate['updated_at'] = now();
            DB::table('sr_users')->where('id', $profile->user_id)->update($userUpdate);
        }

        $profileUpdate = ['updated_at' => now()];
        foreach (['headline', 'location', 'education', 'cv_text'] as $field) {
            if (array_key_exists($field, $payload)) {
                $profileUpdate[$field] = $payload[$field];
            }
        }

        if (array_key_exists('experience_years', $payload)) {
            $profileUpdate['experience_years'] = (int) $payload['experience_years'];
        }

        if (array_key_exists('skills', $payload)) {
            $profileUpdate['skills'] = $this->toJson($payload['skills']);
        }

        if (array_key_exists('links', $payload)) {
            $profileUpdate['links'] = $this->toJson(array_filter($payload['links']));
        }

        if (array_key_exists('metadata', $payload)) {
            $profileUpdate['cv_metadata'] = $this->toJson($payload['metadata']);
        }

        DB::table('sr_student_profiles')->where('public_id', $id)->update($profileUpdate);

        if (! empty($payload['file_name'])) {
            DB::table('sr_cv_documents')->insert([
                'student_profile_id' => $profile->id,
                'original_name' => $payload['file_name'],
                'stored_path' => 'storage/app/uploads/'.$payload['file_name'],
                'mime_type' => null,
                'size' => 0,
                'parser_result' => $this->toJson($payload['metadata'] ?? []),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $this->findStudent($id);
    }

    public function updateOffer(string $id, array $payload): ?array
    {
        $exists = $this->activeOffers()->where('public_id', $id)->exists();

        if (! $exists) {
            return null;
        }

        $update = ['updated_at' => now()];

        foreach (['title', 'company', 'location', 'type', 'description', 'status'] as $field) {
            if (array_key_exists($field, $payload)) {
                $update[$field] = $payload[$field];
            }
        }

        if (array_key_exists('required_skills', $payload)) {
            $update['required_skills'] = $this->toJson($payload['required_skills']);
        }

        DB::table('sr_offers')->where('public_id', $id)->update($update);

        return $this->findOffer($id);
    }

    public function findApplication(string $id): ?array
    {
        $row = $this->activeApplications()
            ->join('sr_student_profiles', 'sr_student_profiles.id', '=', 'sr_applications.student_profile_id')
            ->join('sr_offers', 'sr_offers.id', '=', 'sr_applications.offer_id')
            ->where('sr_applications.public_id', $id)
            ->select(
                'sr_applications.*',
                'sr_student_profiles.public_id as student_public_id',
                'sr_offers.public_id as offer_public_id'
            )
            ->first();

        return $row ? $this->formatApplication($row) : null;
    }

    public function apply(string $offerId, string $studentId): void
    {
        $offer = $this->activeOffers()->where('public_id', $offerId)->first();
        $student = $this->activeStudentProfiles()->where('public_id', $studentId)->first();

        if (! $offer || ! $student) {
            return;
        }

        // L'index unique (offer_id, student_profile_id) couvre aussi les lignes
        // soft-deleted : une candidature retirée est restaurée plutôt que
        // réinsérée, sinon l'étudiant ne pourrait jamais re-postuler.
        $existing = DB::table('sr_applications')
            ->where('offer_id', $offer->id)
            ->where('student_profile_id', $student->id)
            ->first();

        if ($existing) {
            if ($existing->deleted_at !== null) {
                DB::table('sr_applications')
                    ->where('id', $existing->id)
                    ->update([
                        'deleted_at' => null,
                        'status' => 'submitted',
                        'applied_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            return;
        }

        DB::table('sr_applications')->insert([
            'public_id' => 'app_'.Str::lower(Str::random(8)),
            'offer_id' => $offer->id,
            'student_profile_id' => $student->id,
            'status' => 'submitted',
            'applied_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateApplicationStatus(string $applicationId, string $status): ?array
    {
        if (! in_array($status, ['submitted', 'shortlisted', 'interview', 'rejected'], true)) {
            return null;
        }

        $exists = $this->activeApplications()
            ->where('public_id', $applicationId)
            ->exists();

        if (! $exists) {
            return null;
        }

        DB::table('sr_applications')
            ->where('public_id', $applicationId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);

        $row = $this->activeApplications()
            ->join('sr_student_profiles', 'sr_student_profiles.id', '=', 'sr_applications.student_profile_id')
            ->join('sr_offers', 'sr_offers.id', '=', 'sr_applications.offer_id')
            ->where('sr_applications.public_id', $applicationId)
            ->select(
                'sr_applications.*',
                'sr_student_profiles.public_id as student_public_id',
                'sr_offers.public_id as offer_public_id'
            )
            ->first();

        return $row ? $this->formatApplication($row) : null;
    }

    public function findStudent(string $id): ?array
    {
        $row = $this->activeStudentProfiles()
            ->join('sr_users', 'sr_users.id', '=', 'sr_student_profiles.user_id')
            ->whereNull('sr_users.deleted_at')
            ->select('sr_student_profiles.*', 'sr_users.name', 'sr_users.email')
            ->where('sr_student_profiles.public_id', $id)
            ->first();

        return $row ? $this->formatStudent($row) : null;
    }

    public function findUserByEmail(string $email): ?array
    {
        $row = $this->activeUsers()
            ->leftJoin('sr_student_profiles', function ($join): void {
                $join->on('sr_student_profiles.user_id', '=', 'sr_users.id')
                    ->whereNull('sr_student_profiles.deleted_at');
            })
            ->select('sr_users.public_id', 'sr_users.name', 'sr_users.email', 'sr_users.role', 'sr_users.password', 'sr_student_profiles.public_id as student_public_id')
            ->where('sr_users.email', $email)
            ->first();

        if (! $row) {
            return null;
        }

        return [
            'id' => $row->public_id,
            'name' => $row->name,
            'email' => $row->email,
            'role' => $row->role,
            'student_id' => $row->student_public_id,
            'password_hash' => $row->password,
        ];
    }

    /*
     * ------------------------------------------------------------------
     * Administration : comptes, suppression logique et restauration.
     * ------------------------------------------------------------------
     */

    /** Fiche d'un compte, sans jamais exposer le hash du mot de passe. */
    public function findUser(string $publicId, bool $withTrashed = false): ?array
    {
        $query = $withTrashed ? DB::table('sr_users') : $this->activeUsers();

        $row = $query
            ->leftJoin('sr_student_profiles', 'sr_student_profiles.user_id', '=', 'sr_users.id')
            ->leftJoin('sr_recruiter_profiles', 'sr_recruiter_profiles.user_id', '=', 'sr_users.id')
            ->where('sr_users.public_id', $publicId)
            ->select(
                'sr_users.public_id',
                'sr_users.name',
                'sr_users.email',
                'sr_users.role',
                'sr_users.created_at',
                'sr_users.deleted_at',
                'sr_student_profiles.public_id as student_public_id',
                'sr_student_profiles.deleted_at as student_deleted_at',
                'sr_recruiter_profiles.public_id as recruiter_public_id',
                'sr_recruiter_profiles.company_name',
                'sr_recruiter_profiles.position',
                'sr_recruiter_profiles.website'
            )
            ->first();

        return $row ? $this->formatUser($row) : null;
    }

    /**
     * Liste des comptes pour la console d'administration.
     * Filtres acceptés : role, trashed (bool, inclut les comptes supprimés).
     */
    public function listUsers(array $filters = []): array
    {
        $query = ! empty($filters['trashed']) ? DB::table('sr_users') : $this->activeUsers();

        $query->leftJoin('sr_student_profiles', 'sr_student_profiles.user_id', '=', 'sr_users.id')
            ->leftJoin('sr_recruiter_profiles', 'sr_recruiter_profiles.user_id', '=', 'sr_users.id')
            ->select(
                'sr_users.public_id',
                'sr_users.name',
                'sr_users.email',
                'sr_users.role',
                'sr_users.created_at',
                'sr_users.deleted_at',
                'sr_student_profiles.public_id as student_public_id',
                'sr_student_profiles.deleted_at as student_deleted_at',
                'sr_recruiter_profiles.public_id as recruiter_public_id',
                'sr_recruiter_profiles.company_name',
                'sr_recruiter_profiles.position',
                'sr_recruiter_profiles.website'
            );

        if (! empty($filters['role'])) {
            $query->where('sr_users.role', $filters['role']);
        }

        return array_map(
            fn (object $row): array => $this->formatUser($row),
            $query->orderBy('sr_users.role')->orderBy('sr_users.name')->get()->all()
        );
    }

    public function countAdmins(): int
    {
        return (int) $this->activeUsers()->where('role', 'admin')->count();
    }

    /** Création d'un compte par l'administrateur, avec le profil métier associé. */
    public function createUser(array $payload): array
    {
        return DB::transaction(function () use ($payload): array {
            $publicId = 'usr_'.Str::lower(Str::random(8));
            $userId = DB::table('sr_users')->insertGetId([
                'public_id' => $publicId,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'role' => $payload['role'],
                'password' => password_hash(
                    $payload['password'] ?? config('smart_recruit.default_password'),
                    PASSWORD_BCRYPT
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->ensureProfileForRole($userId, $payload['role'], $payload);

            return $this->findUser($publicId);
        });
    }

    /**
     * Mise à jour d'un compte : identité, rôle et profil recruteur.
     * Un changement de rôle crée le profil manquant et conserve l'ancien
     * (soft-deleted) afin de rester réversible.
     */
    public function updateUser(string $publicId, array $payload): ?array
    {
        $user = $this->activeUsers()->where('public_id', $publicId)->first();

        if (! $user) {
            return null;
        }

        return DB::transaction(function () use ($user, $publicId, $payload): ?array {
            $update = ['updated_at' => now()];

            foreach (['name', 'email'] as $field) {
                if (array_key_exists($field, $payload)) {
                    $update[$field] = $payload[$field];
                }
            }

            $newRole = $payload['role'] ?? $user->role;

            if ($newRole !== $user->role) {
                $update['role'] = $newRole;
                $this->ensureProfileForRole($user->id, $newRole, $payload);
            }

            DB::table('sr_users')->where('id', $user->id)->update($update);

            if (array_key_exists('company_name', $payload) || array_key_exists('position', $payload) || array_key_exists('website', $payload)) {
                $this->updateRecruiterProfileForUser($user->id, $payload);
            }

            return $this->findUser($publicId);
        });
    }

    public function updateUserPassword(string $publicId, string $plainPassword): bool
    {
        $affected = $this->activeUsers()
            ->where('public_id', $publicId)
            ->update([
                'password' => password_hash($plainPassword, PASSWORD_BCRYPT),
                'updated_at' => now(),
            ]);

        return $affected > 0;
    }

    /**
     * Suppression logique d'un compte : le compte et ses profils sont marqués
     * supprimés, ainsi que les candidatures de l'étudiant concerné et les
     * offres du recruteur concerné. Rien n'est détruit physiquement.
     */
    public function softDeleteUser(string $publicId): bool
    {
        $user = $this->activeUsers()->where('public_id', $publicId)->first();

        if (! $user) {
            return false;
        }

        return DB::transaction(function () use ($user): bool {
            $now = $this->deletionStamp();

            $studentProfileIds = $this->activeStudentProfiles()->where('user_id', $user->id)->pluck('id');

            if ($studentProfileIds->isNotEmpty()) {
                DB::table('sr_applications')
                    ->whereIn('student_profile_id', $studentProfileIds)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => $now, 'updated_at' => $now]);

                DB::table('sr_student_profiles')
                    ->whereIn('id', $studentProfileIds)
                    ->update(['deleted_at' => $now, 'updated_at' => $now]);
            }

            $recruiterProfileIds = $this->activeRecruiterProfiles()->where('user_id', $user->id)->pluck('id');

            if ($recruiterProfileIds->isNotEmpty()) {
                $offerIds = $this->activeOffers()->whereIn('recruiter_profile_id', $recruiterProfileIds)->pluck('id');

                if ($offerIds->isNotEmpty()) {
                    DB::table('sr_applications')
                        ->whereIn('offer_id', $offerIds)
                        ->whereNull('deleted_at')
                        ->update(['deleted_at' => $now, 'updated_at' => $now]);

                    DB::table('sr_offers')
                        ->whereIn('id', $offerIds)
                        ->update(['deleted_at' => $now, 'updated_at' => $now]);
                }

                DB::table('sr_recruiter_profiles')
                    ->whereIn('id', $recruiterProfileIds)
                    ->update(['deleted_at' => $now, 'updated_at' => $now]);
            }

            DB::table('sr_users')->where('id', $user->id)->update(['deleted_at' => $now, 'updated_at' => $now]);

            return true;
        });
    }

    /** Restauration d'un compte et de ses profils (pas de ses offres/candidatures). */
    public function restoreUser(string $publicId): bool
    {
        $user = DB::table('sr_users')->where('public_id', $publicId)->whereNotNull('deleted_at')->first();

        if (! $user) {
            return false;
        }

        return DB::transaction(function () use ($user): bool {
            $now = now();
            $deletedAt = $user->deleted_at;

            // Restauration symétrique : on ne relève que ce qui a été supprimé
            // dans la même opération que le compte.
            $studentProfileIds = DB::table('sr_student_profiles')
                ->where('user_id', $user->id)
                ->where('deleted_at', $deletedAt)
                ->pluck('id');

            $recruiterProfileIds = DB::table('sr_recruiter_profiles')
                ->where('user_id', $user->id)
                ->where('deleted_at', $deletedAt)
                ->pluck('id');

            if ($studentProfileIds->isNotEmpty()) {
                DB::table('sr_applications')
                    ->whereIn('student_profile_id', $studentProfileIds)
                    ->where('deleted_at', $deletedAt)
                    ->update(['deleted_at' => null, 'updated_at' => $now]);

                DB::table('sr_student_profiles')
                    ->whereIn('id', $studentProfileIds)
                    ->update(['deleted_at' => null, 'updated_at' => $now]);
            }

            if ($recruiterProfileIds->isNotEmpty()) {
                $offerIds = DB::table('sr_offers')
                    ->whereIn('recruiter_profile_id', $recruiterProfileIds)
                    ->where('deleted_at', $deletedAt)
                    ->pluck('id');

                if ($offerIds->isNotEmpty()) {
                    DB::table('sr_applications')
                        ->whereIn('offer_id', $offerIds)
                        ->where('deleted_at', $deletedAt)
                        ->update(['deleted_at' => null, 'updated_at' => $now]);

                    DB::table('sr_offers')
                        ->whereIn('id', $offerIds)
                        ->update(['deleted_at' => null, 'updated_at' => $now]);
                }

                DB::table('sr_recruiter_profiles')
                    ->whereIn('id', $recruiterProfileIds)
                    ->update(['deleted_at' => null, 'updated_at' => $now]);
            }

            DB::table('sr_users')->where('id', $user->id)->update(['deleted_at' => null, 'updated_at' => $now]);

            return true;
        });
    }

    /** Suppression logique d'un profil étudiant et de ses candidatures. */
    public function softDeleteStudent(string $publicId): bool
    {
        $profile = $this->activeStudentProfiles()->where('public_id', $publicId)->first();

        if (! $profile) {
            return false;
        }

        return DB::transaction(function () use ($profile): bool {
            $now = $this->deletionStamp();

            DB::table('sr_applications')
                ->where('student_profile_id', $profile->id)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $now, 'updated_at' => $now]);

            DB::table('sr_student_profiles')->where('id', $profile->id)->update(['deleted_at' => $now, 'updated_at' => $now]);

            return true;
        });
    }

    public function restoreStudent(string $publicId): bool
    {
        $profile = DB::table('sr_student_profiles')
            ->where('public_id', $publicId)
            ->whereNotNull('deleted_at')
            ->first();

        if (! $profile) {
            return false;
        }

        return DB::transaction(function () use ($profile): bool {
            // Restauration symétrique : seules les candidatures supprimées dans
            // la même opération (même horodatage) sont restaurées. Une
            // candidature retirée isolément auparavant reste supprimée.
            DB::table('sr_applications')
                ->where('student_profile_id', $profile->id)
                ->where('deleted_at', $profile->deleted_at)
                ->update(['deleted_at' => null, 'updated_at' => now()]);

            DB::table('sr_student_profiles')
                ->where('id', $profile->id)
                ->update(['deleted_at' => null, 'updated_at' => now()]);

            return true;
        });
    }

    /** Suppression logique d'une offre et de ses candidatures. */
    public function softDeleteOffer(string $publicId): bool
    {
        $offer = $this->activeOffers()->where('public_id', $publicId)->first();

        if (! $offer) {
            return false;
        }

        return DB::transaction(function () use ($offer): bool {
            $now = $this->deletionStamp();

            DB::table('sr_applications')
                ->where('offer_id', $offer->id)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $now, 'updated_at' => $now]);

            DB::table('sr_offers')->where('id', $offer->id)->update(['deleted_at' => $now, 'updated_at' => $now]);

            return true;
        });
    }

    public function restoreOffer(string $publicId): bool
    {
        $offer = DB::table('sr_offers')
            ->where('public_id', $publicId)
            ->whereNotNull('deleted_at')
            ->first();

        if (! $offer) {
            return false;
        }

        return DB::transaction(function () use ($offer): bool {
            DB::table('sr_applications')
                ->where('offer_id', $offer->id)
                ->where('deleted_at', $offer->deleted_at)
                ->update(['deleted_at' => null, 'updated_at' => now()]);

            DB::table('sr_offers')
                ->where('id', $offer->id)
                ->update(['deleted_at' => null, 'updated_at' => now()]);

            return true;
        });
    }

    public function softDeleteApplication(string $publicId): bool
    {
        $affected = $this->activeApplications()
            ->where('public_id', $publicId)
            ->update(['deleted_at' => $this->deletionStamp(), 'updated_at' => now()]);

        return $affected > 0;
    }

    public function restoreApplication(string $publicId): bool
    {
        $affected = DB::table('sr_applications')
            ->where('public_id', $publicId)
            ->whereNotNull('deleted_at')
            ->update(['deleted_at' => null, 'updated_at' => now()]);

        return $affected > 0;
    }

    /**
     * Réassigne une offre à un autre recruteur.
     * Résout le cas des offres devenues sans propriétaire.
     */
    public function reassignOffer(string $offerPublicId, string $recruiterUserPublicId): ?array
    {
        $offer = $this->activeOffers()->where('public_id', $offerPublicId)->first();
        $user = $this->activeUsers()->where('public_id', $recruiterUserPublicId)->first();

        if (! $offer || ! $user || ! in_array($user->role, ['recruiter', 'admin'], true)) {
            return null;
        }

        DB::table('sr_offers')->where('id', $offer->id)->update([
            'recruiter_profile_id' => $this->recruiterProfileIdForUser($recruiterUserPublicId),
            'updated_at' => now(),
        ]);

        return $this->findOffer($offerPublicId);
    }

    /**
     * Document CV courant d'un étudiant (le plus récent), ou null.
     * Seul le nom de fichier est exploité côté application : le chemin réel est
     * reconstruit à partir du dossier d'upload, jamais depuis la base, pour
     * éviter toute traversée de répertoire.
     */
    public function findCvDocument(string $studentPublicId): ?array
    {
        $profile = $this->activeStudentProfiles()->where('public_id', $studentPublicId)->first();

        if (! $profile) {
            return null;
        }

        $document = DB::table('sr_cv_documents')
            ->where('student_profile_id', $profile->id)
            ->orderByDesc('id')
            ->first();

        if (! $document || ! $document->original_name) {
            return null;
        }

        return [
            'id' => (int) $document->id,
            'file_name' => basename((string) $document->original_name),
            'created_at' => (string) $document->created_at,
        ];
    }

    /** Retire le CV courant d'un étudiant (ligne du document uniquement). */
    public function deleteCvDocument(string $studentPublicId): bool
    {
        $document = $this->findCvDocument($studentPublicId);

        if (! $document) {
            return false;
        }

        return DB::table('sr_cv_documents')->where('id', $document['id'])->delete() > 0;
    }

    /** Offres supprimées (pour l'onglet de restauration de l'administrateur). */
    public function listTrashedOffers(): array
    {
        return array_map(
            fn (object $row): array => $this->formatOffer($row) + ['deleted_at' => (string) $row->deleted_at],
            DB::table('sr_offers')
                ->leftJoin('sr_recruiter_profiles', 'sr_recruiter_profiles.id', '=', 'sr_offers.recruiter_profile_id')
                ->leftJoin('sr_users', 'sr_users.id', '=', 'sr_recruiter_profiles.user_id')
                ->whereNotNull('sr_offers.deleted_at')
                ->select('sr_offers.*', 'sr_users.public_id as owner_public_id')
                ->orderByDesc('sr_offers.deleted_at')
                ->get()
                ->all()
        );
    }

    /** Profils étudiants supprimés (pour l'onglet de restauration). */
    public function listTrashedStudents(): array
    {
        return array_map(
            fn (object $row): array => $this->formatStudent($row) + ['deleted_at' => (string) $row->deleted_at],
            DB::table('sr_student_profiles')
                ->join('sr_users', 'sr_users.id', '=', 'sr_student_profiles.user_id')
                ->whereNotNull('sr_student_profiles.deleted_at')
                ->select('sr_student_profiles.*', 'sr_users.name', 'sr_users.email')
                ->orderByDesc('sr_student_profiles.deleted_at')
                ->get()
                ->all()
        );
    }

    /** Recruteurs sélectionnables comme propriétaire d'une offre. */
    public function listOfferOwners(): array
    {
        return array_map(
            fn (object $row): array => [
                'id' => $row->public_id,
                'name' => $row->name,
                'company_name' => $row->company_name,
            ],
            $this->activeUsers()
                ->leftJoin('sr_recruiter_profiles', function ($join): void {
                    $join->on('sr_recruiter_profiles.user_id', '=', 'sr_users.id')
                        ->whereNull('sr_recruiter_profiles.deleted_at');
                })
                ->whereIn('sr_users.role', ['recruiter', 'admin'])
                ->select('sr_users.public_id', 'sr_users.name', 'sr_recruiter_profiles.company_name')
                ->orderBy('sr_users.name')
                ->get()
                ->all()
        );
    }

    /** Retourne le titre du profil étudiant d'un compte, s'il en a un. */
    public function studentHeadline(string $studentPublicId): ?string
    {
        return $this->activeStudentProfiles()->where('public_id', $studentPublicId)->value('headline');
    }

    /** Liste des candidatures enrichie pour la console d'administration. */
    public function listApplications(array $filters = []): array
    {
        $query = ! empty($filters['trashed']) ? DB::table('sr_applications') : $this->activeApplications();

        $query->join('sr_student_profiles', 'sr_student_profiles.id', '=', 'sr_applications.student_profile_id')
            ->join('sr_offers', 'sr_offers.id', '=', 'sr_applications.offer_id')
            ->join('sr_users', 'sr_users.id', '=', 'sr_student_profiles.user_id')
            ->select(
                'sr_applications.*',
                'sr_student_profiles.public_id as student_public_id',
                'sr_student_profiles.headline as student_headline',
                'sr_users.name as student_name',
                'sr_offers.public_id as offer_public_id',
                'sr_offers.title as offer_title',
                'sr_offers.company as offer_company'
            );

        if (! empty($filters['status'])) {
            $query->where('sr_applications.status', $filters['status']);
        }

        if (! empty($filters['offer_id'])) {
            $query->where('sr_offers.public_id', $filters['offer_id']);
        }

        return array_map(function (object $row): array {
            $application = $this->formatApplication($row);

            return $application + [
                'student_name' => $row->student_name,
                'student_headline' => $row->student_headline,
                'offer_title' => $row->offer_title,
                'offer_company' => $row->offer_company,
                'deleted' => $row->deleted_at !== null,
            ];
        }, $query->orderByDesc('sr_applications.applied_at')->get()->all());
    }

    private function ensureProfileForRole(int $userId, string $role, array $payload): void
    {
        if ($role === 'student') {
            $existing = DB::table('sr_student_profiles')->where('user_id', $userId)->first();

            if ($existing) {
                DB::table('sr_student_profiles')->where('id', $existing->id)->update([
                    'deleted_at' => null,
                    'updated_at' => now(),
                ]);

                return;
            }

            DB::table('sr_student_profiles')->insert([
                'public_id' => 'stu_'.Str::lower(Str::random(8)),
                'user_id' => $userId,
                'headline' => $payload['headline'] ?? 'Profil à compléter',
                'location' => $payload['location'] ?? null,
                'education' => $payload['education'] ?? null,
                'experience_years' => (int) ($payload['experience_years'] ?? 0),
                'skills' => $this->toJson([]),
                'links' => $this->toJson([]),
                'cv_text' => null,
                'cv_metadata' => $this->toJson([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        if ($role === 'recruiter') {
            $existing = DB::table('sr_recruiter_profiles')->where('user_id', $userId)->first();

            if ($existing) {
                DB::table('sr_recruiter_profiles')->where('id', $existing->id)->update([
                    'deleted_at' => null,
                    'company_name' => $payload['company_name'] ?? $existing->company_name,
                    'updated_at' => now(),
                ]);

                return;
            }

            DB::table('sr_recruiter_profiles')->insert([
                'public_id' => 'rec_'.Str::lower(Str::random(8)),
                'user_id' => $userId,
                'company_name' => $payload['company_name'] ?? 'Entreprise à compléter',
                'position' => $payload['position'] ?? null,
                'website' => $payload['website'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function updateRecruiterProfileForUser(int $userId, array $payload): void
    {
        $profile = DB::table('sr_recruiter_profiles')->where('user_id', $userId)->first();

        if (! $profile) {
            return;
        }

        $update = ['updated_at' => now()];

        foreach (['company_name', 'position', 'website'] as $field) {
            if (array_key_exists($field, $payload)) {
                $update[$field] = $payload[$field];
            }
        }

        DB::table('sr_recruiter_profiles')->where('id', $profile->id)->update($update);
    }

    private function formatUser(object $row): array
    {
        return [
            'id' => $row->public_id,
            'name' => $row->name,
            'email' => $row->email,
            'role' => $row->role,
            'student_id' => ($row->student_deleted_at ?? null) === null ? ($row->student_public_id ?? null) : null,
            'recruiter_id' => $row->recruiter_public_id ?? null,
            'company_name' => $row->company_name ?? null,
            'position' => $row->position ?? null,
            'website' => $row->website ?? null,
            'created_at' => (string) ($row->created_at ?? ''),
            'deleted' => ($row->deleted_at ?? null) !== null,
        ];
    }

    public function registerUser(array $payload): array
    {
        return DB::transaction(function () use ($payload): array {
            $publicId = 'usr_'.Str::lower(Str::random(8));
            $userId = DB::table('sr_users')->insertGetId([
                'public_id' => $publicId,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'role' => $payload['role'],
                'password' => password_hash($payload['password'], PASSWORD_BCRYPT),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $studentPublicId = null;

            if ($payload['role'] === 'student') {
                $studentPublicId = 'stu_'.Str::lower(Str::random(8));

                DB::table('sr_student_profiles')->insert([
                    'public_id' => $studentPublicId,
                    'user_id' => $userId,
                    'headline' => $payload['headline'],
                    'location' => $payload['location'] ?? null,
                    'education' => $payload['education'] ?? null,
                    'experience_years' => 0,
                    'skills' => $this->toJson([]),
                    'links' => $this->toJson([]),
                    'cv_text' => null,
                    'cv_metadata' => $this->toJson([]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($payload['role'] === 'recruiter') {
                DB::table('sr_recruiter_profiles')->insert([
                    'public_id' => 'rec_'.Str::lower(Str::random(8)),
                    'user_id' => $userId,
                    'company_name' => $payload['company_name'],
                    'position' => $payload['position'] ?? null,
                    'website' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return [
                'id' => $publicId,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'role' => $payload['role'],
                'student_id' => $studentPublicId,
            ];
        });
    }

    public function findOffer(string $id): ?array
    {
        $row = $this->offerQuery()->where('sr_offers.public_id', $id)->first();

        return $row ? $this->formatOffer($row) : null;
    }

    public function applicationsForOffer(string $offerId): array
    {
        $offer = $this->activeOffers()->where('public_id', $offerId)->first();

        if (! $offer) {
            return [];
        }

        return array_map(
            fn (object $row): array => $this->formatApplication($row),
            $this->activeApplications()
                ->join('sr_student_profiles', 'sr_student_profiles.id', '=', 'sr_applications.student_profile_id')
                ->join('sr_offers', 'sr_offers.id', '=', 'sr_applications.offer_id')
                ->whereNull('sr_student_profiles.deleted_at')
                ->where('sr_applications.offer_id', $offer->id)
                ->select(
                    'sr_applications.*',
                    'sr_student_profiles.public_id as student_public_id',
                    'sr_offers.public_id as offer_public_id'
                )
                ->orderByDesc('sr_applications.applied_at')
                ->get()
                ->all()
        );
    }

    public function saveMatchScore(string $offerId, string $studentId, array $match): void
    {
        if (! Schema::hasTable('sr_match_scores')) {
            return;
        }

        $application = $this->activeApplications()
            ->join('sr_offers', 'sr_offers.id', '=', 'sr_applications.offer_id')
            ->join('sr_student_profiles', 'sr_student_profiles.id', '=', 'sr_applications.student_profile_id')
            ->where('sr_offers.public_id', $offerId)
            ->where('sr_student_profiles.public_id', $studentId)
            ->select('sr_applications.id', 'sr_applications.status')
            ->first();

        if (! $application) {
            return;
        }

        $payload = [
            'application_id' => $application->id,
            'score' => (int) ($match['score'] ?? 0),
            'text_similarity' => (int) ($match['text_similarity'] ?? 0),
            'semantic_coverage' => (int) ($match['semantic_coverage'] ?? 0),
            'matched_skills' => $this->toJson($match['matched_skills'] ?? []),
            'missing_skills' => $this->toJson($match['missing_skills'] ?? []),
            'raw_response' => $this->toJson($match),
            'updated_at' => now(),
        ];

        $existing = DB::table('sr_match_scores')
            ->where('application_id', $application->id)
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            DB::table('sr_match_scores')->where('id', $existing->id)->update($payload);
        } else {
            DB::table('sr_match_scores')->insert(array_merge($payload, [
                'created_at' => now(),
            ]));
        }

        if ($application->status === 'submitted' && (int) ($match['score'] ?? 0) >= 70) {
            DB::table('sr_applications')
                ->where('id', $application->id)
                ->update([
                    'status' => 'shortlisted',
                    'updated_at' => now(),
                ]);
        }
    }

    private function users(): array
    {
        return array_map(
            fn (object $row): array => [
                'id' => $row->public_id,
                'name' => $row->name,
                'email' => $row->email,
                'role' => $row->role,
                'student_id' => $row->student_public_id,
            ],
            $this->activeUsers()
                ->leftJoin('sr_student_profiles', function ($join): void {
                    $join->on('sr_student_profiles.user_id', '=', 'sr_users.id')
                        ->whereNull('sr_student_profiles.deleted_at');
                })
                ->select('sr_users.*', 'sr_student_profiles.public_id as student_public_id')
                ->orderBy('sr_users.id')
                ->get()
                ->all()
        );
    }

    private function students(): array
    {
        return array_map(
            fn (object $row): array => $this->formatStudent($row),
            $this->activeStudentProfiles()
                ->join('sr_users', 'sr_users.id', '=', 'sr_student_profiles.user_id')
                ->whereNull('sr_users.deleted_at')
                ->select('sr_student_profiles.*', 'sr_users.name', 'sr_users.email')
                ->orderByDesc('sr_student_profiles.created_at')
                ->get()
                ->all()
        );
    }

    private function offers(): array
    {
        return array_map(
            fn (object $row): array => $this->formatOffer($row),
            $this->offerQuery()->orderByDesc('sr_offers.created_at')->get()->all()
        );
    }

    private function offerQuery()
    {
        return $this->activeOffers()
            ->leftJoin('sr_recruiter_profiles', function ($join): void {
                $join->on('sr_recruiter_profiles.id', '=', 'sr_offers.recruiter_profile_id')
                    ->whereNull('sr_recruiter_profiles.deleted_at');
            })
            ->leftJoin('sr_users', function ($join): void {
                $join->on('sr_users.id', '=', 'sr_recruiter_profiles.user_id')
                    ->whereNull('sr_users.deleted_at');
            })
            ->select('sr_offers.*', 'sr_users.public_id as owner_public_id');
    }

    private function applications(): array
    {
        return array_map(
            fn (object $row): array => $this->formatApplication($row),
            $this->activeApplications()
                ->join('sr_student_profiles', 'sr_student_profiles.id', '=', 'sr_applications.student_profile_id')
                ->join('sr_offers', 'sr_offers.id', '=', 'sr_applications.offer_id')
                ->whereNull('sr_student_profiles.deleted_at')
                ->whereNull('sr_offers.deleted_at')
                ->select(
                    'sr_applications.*',
                    'sr_student_profiles.public_id as student_public_id',
                    'sr_offers.public_id as offer_public_id'
                )
                ->orderByDesc('sr_applications.applied_at')
                ->get()
                ->all()
        );
    }

    private function formatStudent(object $row): array
    {
        $document = DB::table('sr_cv_documents')
            ->where('student_profile_id', $row->id)
            ->orderByDesc('id')
            ->first();

        return [
            'id' => $row->public_id,
            'name' => $row->name,
            'email' => $row->email,
            'headline' => $row->headline ?? '',
            'location' => $row->location ?? '',
            'experience_years' => (int) ($row->experience_years ?? 0),
            'education' => $row->education ?? 'Non renseigné',
            'skills' => $this->fromJson($row->skills),
            'links' => $this->fromJson($row->links),
            'cv_text' => $row->cv_text ?? '',
            'file_name' => $document->original_name ?? null,
            'metadata' => $this->fromJson($row->cv_metadata),
            'created_at' => (string) $row->created_at,
        ];
    }

    private function formatOffer(object $row): array
    {
        return [
            'id' => $row->public_id,
            'title' => $row->title,
            'company' => $row->company,
            'location' => $row->location ?? '',
            'type' => $row->type ?? 'Stage',
            'description' => $row->description,
            'required_skills' => $this->fromJson($row->required_skills),
            'status' => $row->status,
            'owner_id' => $row->owner_public_id ?? null,
            'created_at' => (string) $row->created_at,
        ];
    }

    private function formatApplication(object $row): array
    {
        $matchScore = Schema::hasTable('sr_match_scores')
            ? DB::table('sr_match_scores')
                ->where('application_id', $row->id)
                ->orderByDesc('id')
                ->first()
            : null;

        return [
            'id' => $row->public_id,
            'offer_id' => $row->offer_public_id,
            'student_id' => $row->student_public_id,
            'status' => $row->status,
            'applied_at' => (string) $row->applied_at,
            'match_score' => $matchScore ? (int) $matchScore->score : null,
        ];
    }

    private function seedIfEmpty(): void
    {
        $this->syncDemoData();
    }

    private function seedDemoData(): void
    {
        // Ne seeder QUE sur une base vierge. Auparavant cette méthode appelait
        // syncDemoData() à chaque requête : coût inutile, et surtout toute
        // entité supprimée par l'administrateur aurait été recréée.
        if (DB::table('sr_users')->exists()) {
            return;
        }

        $this->syncDemoData();
    }

    private function recruiterProfileIdForUser(string $publicUserId): int
    {
        $userId = $this->activeUsers()->where('public_id', $publicUserId)->value('id');

        if (! $userId) {
            return $this->defaultRecruiterProfileId();
        }

        $profileId = $this->activeRecruiterProfiles()->where('user_id', $userId)->value('id');

        if ($profileId) {
            return (int) $profileId;
        }

        $user = $this->activeUsers()->where('id', $userId)->first();

        return (int) DB::table('sr_recruiter_profiles')->insertGetId([
            'public_id' => 'rec_'.Str::lower(Str::random(8)),
            'user_id' => $userId,
            'company_name' => $user->name ?? 'Smart-Recruit',
            'position' => 'Recruteur',
            'website' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function defaultRecruiterProfileId(): ?int
    {
        $profileId = $this->activeRecruiterProfiles()->value('id');

        if ($profileId) {
            return (int) $profileId;
        }

        $userId = DB::table('sr_users')->insertGetId([
            'public_id' => 'usr_recruiter_default',
            'name' => 'Recruteur Smart-Recruit',
            'email' => 'recruteur+'.Str::lower(Str::random(6)).'@smart-recruit.test',
            'role' => 'recruiter',
            'password' => self::defaultPasswordHash(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (int) DB::table('sr_recruiter_profiles')->insertGetId([
            'public_id' => 'rec_'.Str::lower(Str::random(8)),
            'user_id' => $userId,
            'company_name' => 'Smart-Recruit',
            'position' => 'Recruteur',
            'website' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function toJson(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function fromJson(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
