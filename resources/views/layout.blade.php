<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SmartRecruit')</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('/assets/brand/smartrecruit-mark.svg') }}">
    <meta name="theme-color" content="#126b5b">
    <link rel="stylesheet" href="{{ url('/assets/app.css') }}?v=20260911">
</head>
@php
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
@endphp
<body class="{{ $useSidebar ? 'workspace' : 'public-site' }}">
<a class="skip-link" href="#main-content">Aller au contenu</a>
@if($useSidebar)
    <div class="app-shell">
        <aside class="sidebar">
            @include('partials.logo')

            <div class="sidebar-menu">
                <p class="nav-caption">Votre espace</p>
                @include('partials.navigation')
            </div>

            <details class="mobile-menu">
                <summary>@include('partials.icon', ['name' => 'menu']) <span>Menu</span></summary>
                @include('partials.navigation', ['navClass' => 'mobile-links'])
            </details>

            <div class="sidebar-foot">
                <span class="user-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user['name'] ?? $roleLabel, 0, 1)) }}</span>
                <div class="user-meta"><strong>{{ $user['name'] ?? $roleLabel }}</strong><span>{{ $roleLabel }}</span></div>
                @include('partials.logout')
            </div>
        </aside>

        <div class="workspace-content">
            <header class="workspace-bar">
                <span>Espace {{ mb_strtolower($roleLabel) }} <span class="breadcrumb-divider" aria-hidden="true">/</span> <strong>@yield('title', 'SmartRecruit')</strong></span>
                <span class="workspace-signature">Candidats &amp; entreprises</span>
            </header>
            @include('partials.main')
        </div>
    </div>
@else
    <header class="topbar">
        @include('partials.logo')

        @include('partials.navigation', ['navClass' => 'nav'])

        <div class="session">
            @if($role === 'guest')
                <a class="button ghost" href="{{ route('login') }}">Se connecter</a>
                <a class="button" href="{{ route('register') }}">Créer un compte</a>
            @else
                <span class="role">{{ $role }}</span>
                @include('partials.logout')
            @endif
        </div>
    </header>

    @include('partials.main')
@endif
</body>
</html>
