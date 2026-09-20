<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('smart-recruit:ensure-accounts', function (): int {
    if (! SmartRecruit\Support\SqlStore::isAvailable()) {
        $this->error('Base de données indisponible : impossible de vérifier/mettre à jour les comptes.');

        return self::FAILURE;
    }

    $counts = (new SmartRecruit\Support\SqlStore())->ensureDefaultAccounts();

    $this->info('Mot de passe par défaut appliqué à tous les utilisateurs : '.config('smart_recruit.default_password'));

    foreach ($counts as $role => $total) {
        $this->line(($total > 0 ? '<info>[ok]</info> ' : '<error>[manquant]</error> ').$role.' : '.$total.' compte(s)');
    }

    return self::SUCCESS;
})->purpose('Applique le mot de passe par défaut à tous les comptes et garantit au moins un compte par rôle.');

Artisan::command('smart-recruit:seed', function (): int {
    if (! SmartRecruit\Support\SqlStore::isAvailable()) {
        $this->error('Base de données MySQL indisponible : impossible de réinitialiser les données.');

        return self::FAILURE;
    }

    (new SmartRecruit\Support\SqlStore())->reset();

    $this->info('Données Smart-Recruit réinitialisées.');

    return self::SUCCESS;
})->purpose('Réinitialise les données MVP Smart-Recruit.');
