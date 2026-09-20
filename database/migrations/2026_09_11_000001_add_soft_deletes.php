<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Active le soft delete sur les entités gérées par l'administrateur.
 *
 * Rien n'est jamais supprimé physiquement : les lignes restent en base avec
 * un `deleted_at` renseigné. Cela neutralise trois risques d'un coup :
 *  - les cascades SQL destructrices (ON DELETE CASCADE ne se déclenche jamais) ;
 *  - la résurrection des données par syncDemoData(), qui teste l'existence des
 *    lignes par public_id sans filtrer deleted_at ;
 *  - la mise à NULL de sr_offers.recruiter_profile_id (ON DELETE SET NULL),
 *    qui rendrait des offres orphelines et invisibles pour tout recruteur.
 */
return new class extends Migration
{
    /**
     * Tables recevant le soft delete. sr_match_scores et sr_cv_documents en sont
     * exclues volontairement : ce sont des données subordonnées, toujours lues au
     * travers de leur parent (candidature / profil étudiant).
     */
    private const TABLES = [
        'sr_users',
        'sr_student_profiles',
        'sr_recruiter_profiles',
        'sr_offers',
        'sr_applications',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->softDeletes();
                $blueprint->index('deleted_at', $table.'_deleted_at_index');
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropIndex($table.'_deleted_at_index');
                $blueprint->dropSoftDeletes();
            });
        }
    }
};
