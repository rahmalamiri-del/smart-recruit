@extends('layout')

@section('title', 'Tableau de bord administrateur')

@section('content')
<section class="page-head dashboard-hero">
    <div class="hero-content">
        <p class="hero-label">Espace administrateur</p>
        <h1>Le recrutement, en un coup d’œil.</h1>
        <p class="lead">Suivez les offres, les profils et les candidatures depuis un même espace.</p>
        <div class="actions">
            <a class="button" href="{{ route('offers.create') }}">Créer une offre</a>
            <a class="button secondary" href="{{ route('students.create') }}">Ajouter un profil</a>
            <a class="button secondary" href="{{ route('admin.index') }}">Gérer la plateforme</a>
        </div>
    </div>
    <aside class="hero-aside">
        <p class="hero-label">Votre vue d’ensemble</p>
        <h2>Des profils aux opportunités.</h2>
        <p>Explorez les correspondances et retrouvez les candidatures associées à chaque offre.</p>
    </aside>
</section>

<section class="stats-grid" aria-label="Vue d’ensemble de la plateforme">
    <article class="metric">
        <p class="metric-label">Profils candidats</p>
        <span>{{ count($data['students']) }}</span>
        <p class="metric-caption">Dans votre vivier de talents</p>
    </article>
    <article class="metric">
        <p class="metric-label">Offres enregistrées</p>
        <span>{{ count($data['offers']) }}</span>
        <p class="metric-caption">Tous statuts confondus</p>
    </article>
    <article class="metric">
        <p class="metric-label">Candidatures</p>
        <span>{{ count($data['applications']) }}</span>
        <p class="metric-caption">Sur l’ensemble des offres</p>
    </article>
    <article class="metric">
        <p class="metric-label">Moteur d’analyse</p>
        <span class="metric-state">{{ ($aiHealth['status'] ?? 'offline') === 'ok' ? 'Connecté' : 'Local' }}</span>
        <p class="metric-caption">{{ ($aiHealth['status'] ?? 'offline') === 'ok' ? 'Service d’analyse disponible' : 'Analyse locale disponible' }}</p>
    </article>
</section>

<section>
    <div class="section-title">
        <div>
            <h2>Les profils, offre par offre</h2>
            <p class="section-subtitle">Retrouvez le profil le mieux classé pour chaque opportunité.</p>
        </div>
        <a href="{{ route('offers.index') }}">Voir toutes les offres</a>
    </div>
    <div class="stack">
        @forelse($offerSummaries as $summary)
            <article class="item">
                <div class="item-main">
                    <p class="muted">{{ $summary['offer']['company'] }} · {{ $summary['applications'] }} {{ $summary['applications'] > 1 ? 'candidatures' : 'candidature' }}</p>
                    <h3><a href="{{ route('offers.show', $summary['offer']['id']) }}">{{ $summary['offer']['title'] }}</a></h3>
                    @if($summary['top'])
                        <p>Profil le mieux classé : <strong>{{ $summary['top']['student']['name'] }}</strong></p>
                    @else
                        <p class="muted">Aucun profil à comparer pour le moment.</p>
                    @endif
                </div>
                @if($summary['top'])
                    <div class="score-ring" aria-label="Score de compatibilité : {{ $summary['top']['match']['score'] }} %">{{ $summary['top']['match']['score'] }}%</div>
                @endif
            </article>
        @empty
            <div class="panel empty-state">
                <h3>Tout commence par une offre.</h3>
                <p class="muted">Créez une première opportunité pour découvrir les profils qui lui correspondent.</p>
                <a class="button" href="{{ route('offers.create') }}">Créer une offre</a>
            </div>
        @endforelse
    </div>
</section>
@endsection
