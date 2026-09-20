@extends('layout')

@section('title', 'Mon espace recruteur')

@section('content')
@php
    $offerStatusLabels = config('smart_recruit.offer_status_labels');
    $withApplicationsParam = $withApplicationsOnly ? 1 : null;
@endphp
<section class="page-head dashboard-hero">
    <div class="hero-content">
        <p class="hero-label">Espace recruteur</p>
        <h1>Vos offres. Vos prochains talents.</h1>
        <p class="lead">Publiez vos opportunités, comparez les profils et suivez les candidatures reçues.</p>
        <div class="actions">
            <a class="button" href="{{ route('offers.create') }}">Créer une offre</a>
            <a class="button secondary" href="{{ route('offers.index') }}">Gérer mes offres</a>
        </div>
    </div>
    <aside class="hero-aside">
        <p class="hero-label">Pour chaque opportunité</p>
        <h2>Repérez les compétences qui comptent.</h2>
        <p>Retrouvez les profils les mieux classés, puis ouvrez une offre pour examiner les candidatures.</p>
    </aside>
</section>

<section class="stats-grid" aria-label="Vue d’ensemble de votre recrutement">
    <article class="metric">
        <p class="metric-label">Mes offres</p>
        <span>{{ $offerCount }}</span>
        <p class="metric-caption">Tous statuts confondus</p>
    </article>
    <article class="metric">
        <p class="metric-label">Candidatures reçues</p>
        <span>{{ $applicationCount }}</span>
        <p class="metric-caption">Sur l’ensemble de vos offres</p>
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
            <h2>Mes offres en un coup d’œil</h2>
            <p class="section-subtitle">Filtrez vos offres pour retrouver celles qui vous intéressent.</p>
        </div>
        <a href="{{ route('offers.index') }}">Voir toutes mes offres</a>
    </div>

    <div class="actions filter-bar" role="group" aria-label="Filtrer les offres">
        <a class="button {{ $statusFilter === null ? '' : 'secondary' }} tiny" href="{{ route('dashboard', array_filter(['with_applications' => $withApplicationsParam])) }}" @if($statusFilter === null) aria-current="true" @endif>
            Toutes ({{ array_sum($statusCounts) }})
        </a>
        @foreach($offerStatusLabels as $value => $label)
            <a class="button {{ $statusFilter === $value ? '' : 'secondary' }} tiny" href="{{ route('dashboard', array_filter(['status' => $value, 'with_applications' => $withApplicationsParam])) }}" @if($statusFilter === $value) aria-current="true" @endif>
                {{ $label }} ({{ $statusCounts[$value] ?? 0 }})
            </a>
        @endforeach
        <a class="button {{ $withApplicationsOnly ? '' : 'secondary' }} tiny" href="{{ route('dashboard', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? null : 1])) }}" @if($withApplicationsOnly) aria-current="true" @endif>
            Avec candidatures ({{ $withApplicationsCount }})
        </a>
    </div>

    @if(count($offerSummaries))
        <div class="stack">
            @foreach($offerSummaries as $summary)
                <article class="item">
                    <div class="item-main">
                        <p class="muted">
                            {{ $summary['offer']['company'] }} · {{ $summary['applications'] }} {{ $summary['applications'] > 1 ? 'candidatures' : 'candidature' }}
                            · <span class="status-badge status-{{ $summary['offer']['status'] ?? 'published' }}">{{ $offerStatusLabels[$summary['offer']['status'] ?? 'published'] ?? $summary['offer']['status'] }}</span>
                        </p>
                        <h3><a href="{{ route('offers.show', $summary['offer']['id']) }}">{{ $summary['offer']['title'] }}</a></h3>
                        @if($summary['top'])
                            <p>Profil le mieux classé : <strong>{{ $summary['top']['student']['name'] }}</strong></p>
                        @else
                            <p class="muted">Aucun profil à comparer pour le moment.</p>
                        @endif
                    </div>
                    <div class="actions">
                        @if($summary['top'])
                            <div class="score-ring" aria-label="Score de compatibilité : {{ $summary['top']['match']['score'] }} %">{{ $summary['top']['match']['score'] }}%</div>
                        @endif
                        <a class="button secondary" href="{{ route('offers.edit', $summary['offer']['id']) }}">Modifier l’offre</a>
                    </div>
                </article>
            @endforeach
        </div>
    @elseif($offerCount > 0)
        <div class="panel empty-state">
            <h3>Aucune offre dans cette sélection.</h3>
            <p class="muted">Essayez un autre filtre pour retrouver vos offres.</p>
            <a class="button secondary" href="{{ route('dashboard') }}">Afficher toutes mes offres</a>
        </div>
    @else
        <div class="panel empty-state">
            <h3>Présentez votre première opportunité.</h3>
            <p class="muted">Décrivez votre besoin et les compétences recherchées pour découvrir les profils correspondants.</p>
            <a class="button" href="{{ route('offers.create') }}">Créer ma première offre</a>
        </div>
    @endif
</section>
@endsection
