<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SmartRecruit')</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('/assets/brand/smartrecruit-mark.svg') }}">
    <link rel="stylesheet" href="{{ url('/assets/app.css') }}">
</head>
@php
    $role = $user['role'] ?? 'guest';

    // Navigation décrite une seule fois : la barre latérale (admin) et la barre
    // horizontale (autres rôles) rendent la même liste.
    $navItems = [
        ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
    ];

    if (in_array($role, ['admin', 'recruiter'], true)) {
        $navItems[] = ['label' => 'Étudiants', 'url' => route('students.index'), 'active' => request()->routeIs('students.*')];
    }

    if (in_array($role, ['admin', 'recruiter', 'student'], true)) {
        $navItems[] = ['label' => 'Offres', 'url' => route('offers.index'), 'active' => request()->routeIs('offers.*')];
    }

    if ($role === 'student' && ($user['student_id'] ?? null)) {
        $navItems[] = ['label' => 'Mon profil', 'url' => route('students.show', $user['student_id']), 'active' => request()->routeIs('students.*')];
    }

    if ($role === 'admin') {
        $navItems[] = ['label' => 'Console', 'url' => route('admin.index'), 'active' => request()->routeIs('admin.index')];
        $navItems[] = ['label' => 'Comptes', 'url' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')];
        $navItems[] = ['label' => 'Candidatures', 'url' => route('admin.applications.index'), 'active' => request()->routeIs('admin.applications.*')];
    }

    // L'administration passe en barre latérale verticale : elle compte plus
    // d'entrées et sert d'espace de travail, pas de simple site de consultation.
    $useSidebar = $role === 'admin';
@endphp
<body>
@if($useSidebar)
    <div class="app-shell">
        <aside class="sidebar">
            @include('partials.logo')

            <nav class="sidebar-nav">
                @foreach($navItems as $item)
                    <a href="{{ $item['url'] }}" @class(['active' => $item['active']])>{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="sidebar-foot">
                <span class="role">{{ $role }}</span>
                @include('partials.logout')
            </div>
        </aside>

        @include('partials.main')
    </div>
@else
    <header class="topbar">
        @include('partials.logo')

        <nav class="nav">
            @foreach($navItems as $item)
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="session">
            @if($role === 'guest')
                <a class="button secondary" href="{{ route('register') }}">Inscription</a>
                <a class="button ghost" href="{{ route('login') }}">Connexion</a>
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
