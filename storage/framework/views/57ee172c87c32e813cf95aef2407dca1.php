<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'SmartRecruit'); ?></title>
    <link rel="icon" type="image/svg+xml" href="<?php echo e(url('/assets/brand/smartrecruit-mark.svg')); ?>">
    <meta name="theme-color" content="#126b5b">
    <link rel="stylesheet" href="<?php echo e(url('/assets/app.css')); ?>?v=20260911">
</head>
<?php
    $role = $user['role'] ?? 'guest';

    $roleLabel = config('smart_recruit.role_labels')[$role] ?? 'Bienvenue';
    // Les versions mobile et bureau partagent les mêmes destinations et droits.
    $navItems = [
        ['label' => $role === 'guest' ? 'Accueil' : 'Tableau de bord', 'icon' => 'dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
    ];

    if (in_array($role, ['admin', 'recruiter'], true)) {
        $navItems[] = ['label' => 'Profils candidats', 'icon' => 'students', 'url' => route('students.index'), 'active' => request()->routeIs('students.*')];
    }

    if (in_array($role, ['admin', 'recruiter', 'student'], true)) {
        $navItems[] = ['label' => 'Offres de stage', 'icon' => 'offers', 'url' => route('offers.index'), 'active' => request()->routeIs('offers.*')];
    }

    if ($role === 'student' && ($user['student_id'] ?? null)) {
        $navItems[] = ['label' => 'Mon profil', 'icon' => 'profile', 'url' => route('students.show', $user['student_id']), 'active' => request()->routeIs('students.*')];
    }

    if ($role === 'admin') {
        $navItems[] = ['label' => 'Administration', 'icon' => 'admin', 'url' => route('admin.index'), 'active' => request()->routeIs('admin.index')];
        $navItems[] = ['label' => 'Comptes', 'icon' => 'profile', 'url' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')];
        $navItems[] = ['label' => 'Candidatures', 'icon' => 'applications', 'url' => route('admin.applications.index'), 'active' => request()->routeIs('admin.applications.*')];
    }

    $useSidebar = in_array($role, ['admin', 'recruiter', 'student'], true);
?>
<body class="<?php echo e($useSidebar ? 'workspace' : 'public-site'); ?>">
<a class="skip-link" href="#main-content">Aller au contenu</a>
<?php if($useSidebar): ?>
    <div class="app-shell">
        <aside class="sidebar">
            <?php echo $__env->make('partials.logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="sidebar-menu">
                <p class="nav-caption">Votre espace</p>
                <?php echo $__env->make('partials.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <details class="mobile-menu">
                <summary><?php echo $__env->make('partials.icon', ['name' => 'menu'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <span>Menu</span></summary>
                <?php echo $__env->make('partials.navigation', ['navClass' => 'mobile-links'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </details>

            <div class="sidebar-foot">
                <span class="user-avatar" aria-hidden="true"><?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user['name'] ?? $roleLabel, 0, 1))); ?></span>
                <div class="user-meta"><strong><?php echo e($user['name'] ?? $roleLabel); ?></strong><span><?php echo e($roleLabel); ?></span></div>
                <?php echo $__env->make('partials.logout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </aside>

        <div class="workspace-content">
            <header class="workspace-bar">
                <span>Espace <?php echo e(mb_strtolower($roleLabel)); ?> <span class="breadcrumb-divider" aria-hidden="true">/</span> <strong><?php echo $__env->yieldContent('title', 'SmartRecruit'); ?></strong></span>
                <span class="workspace-signature">Candidats &amp; entreprises</span>
            </header>
            <?php echo $__env->make('partials.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
<?php else: ?>
    <header class="topbar">
        <?php echo $__env->make('partials.logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('partials.navigation', ['navClass' => 'nav'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="session">
            <?php if($role === 'guest'): ?>
                <a class="button ghost" href="<?php echo e(route('login')); ?>">Se connecter</a>
                <a class="button" href="<?php echo e(route('register')); ?>">Créer un compte</a>
            <?php else: ?>
                <span class="role"><?php echo e($role); ?></span>
                <?php echo $__env->make('partials.logout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        </div>
    </header>

    <?php echo $__env->make('partials.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH D:\smart-recruit\resources\views/layout.blade.php ENDPATH**/ ?>