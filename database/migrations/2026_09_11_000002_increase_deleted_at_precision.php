<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Porte `deleted_at` à la précision microseconde.
 *
 * La restauration est symétrique : restaurer une entité relève uniquement les
 * lignes supprimées lors de la MÊME opération, identifiées par un `deleted_at`
 * identique. Avec une précision à la seconde, deux suppressions rapprochées
 * partagent le même horodatage : restaurer une offre pourrait alors relever une
 * candidature que l'administrateur avait retirée individuellement juste avant.
 * La précision microseconde rend chaque opération distinguable.
 */
return new class extends Migration
{
    private const TABLES = [
        'sr_users',
        'sr_student_profiles',
        'sr_recruiter_profiles',
        'sr_offers',
        'sr_applications',
    ];

    public function up(): void
    {
        $this->setPrecision(6);
    }

    public function down(): void
    {
        $this->setPrecision(0);
    }

    private function setPrecision(int $precision): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $type = $precision > 0 ? "TIMESTAMP({$precision})" : 'TIMESTAMP';

        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY `deleted_at` {$type} NULL DEFAULT NULL");
        }
    }
};
