@extends('layout')

@section('title', 'Mon espace candidat')

@section('content')
@php
    $applicationStatusLabels = config('smart_recruit.application_status_labels');
@endphp
<section class="page-head dashboard-hero">
    <div class="hero-content">
        <p class="hero-label">Espace candidat</p>
        <h1>Faites avancer votre candidature.</h1>
        <p class="lead">Présentez vos compétences, explorez les offres et suivez chaque candidature.</p>
        <div class="actions">
            <a class="button" href="{{ route('offers.index') }}">Découvrir les offres</a>
            @if($profile)
                <a class="button secondary" href="{{ route('students.edit', $profile['id']) }}">Compléter mon profil</a>
            @endif
        </div>
    </div>
    <aside class="hero-aside">
        <p class="hero-label">Votre prochaine étape</p>
        <h2>Un profil qui vous ressemble.</h2>
        <p>Gardez votre CV et vos compétences à jour pour aider les recruteurs à découvrir votre parcours.</p>
    </aside>
</section>

@if($profile)
    <section class="split">
        <article class="panel">
            <p class="eyebrow">Mon profil</p>
            <h2>{{ $profile['name'] }}</h2>
            <p class="lead">{{ $profile['headline'] }}</p>
            <p class="section-subtitle">Mes compétences</p>
            <div class="chips">
                @forelse($profile['skills'] as $skill)
                    <span>{{ $skill }}</span>
                @empty
                    <p class="muted">Ajoutez vos compétences pour enrichir votre profil.</p>
                @endforelse
            </div>
            <a class="button secondary" href="{{ route('students.edit', $profile['id']) }}">Modifier mon profil</a>
        </article>

        <aside class="panel">
            <div class="section-title">
                <div>
                    <h2>Mes candidatures</h2>
                    <p class="section-subtitle">Retrouvez les offres auxquelles vous avez postulé et leur état d’avancement.</p>
                </div>
            </div>
            @if(count($myApplications))
                <div class="stack">
                    @foreach($myApplications as $row)
                        <article class="item">
                            <div class="item-main">
                                <p class="muted">{{ $row['offer']['company'] }}</p>
                                <h3><a href="{{ route('offers.show', $row['offer']['id']) }}">{{ $row['offer']['title'] }}</a></h3>
                            </div>
                            <span class="status-badge status-{{ $row['application']['status'] }}">{{ $applicationStatusLabels[$row['application']['status']] ?? $row['application']['status'] }}</span>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <h3>Votre prochaine opportunité vous attend.</h3>
                    <p class="muted">Vous n’avez pas encore envoyé de candidature. Explorez les offres pour trouver celle qui vous intéresse.</p>
                    <a class="button" href="{{ route('offers.index') }}">Parcourir les offres</a>
                </div>
            @endif
        </aside>
    </section>
@else
    <section class="panel empty-state">
        <h2>Votre profil n’est pas encore disponible.</h2>
        <p class="muted">Aucun profil candidat n’est associé à ce compte pour le moment. Vous pouvez déjà consulter les offres.</p>
        <a class="button" href="{{ route('offers.index') }}">Découvrir les offres</a>
    </section>
@endif
@endsection
