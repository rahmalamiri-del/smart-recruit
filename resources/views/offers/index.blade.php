@extends('layout')

@section('title', 'Offres de stage')

@section('content')
@php
    $role = $user['role'] ?? 'guest';
    $isStudent = $role === 'student';
    $isRecruiter = $role === 'recruiter';
    $isAdmin = $role === 'admin';
    $myApplicationsByOffer = $isStudent
        ? collect($applications)->where('student_id', $user['student_id'] ?? null)->keyBy('offer_id')
        : collect();
    $statusLabels = config('smart_recruit.application_status_labels');
    $offerStatusLabels = config('smart_recruit.offer_status_labels');
@endphp
<section class="page-head">
    <div>
        <p class="eyebrow">{{ $isRecruiter ? 'Mes offres' : ($isStudent ? 'Pour vous' : 'Toutes les offres') }}</p>
        <h1>Offres de stage</h1>
        @if($isStudent)
            <p class="lead">Triées par pertinence avec votre profil et vos compétences.</p>
        @elseif($isRecruiter || $isAdmin)
            <p class="lead">Triées par score du meilleur candidat.</p>
        @endif
    </div>
    @if(in_array($role, ['admin', 'recruiter']))
        <a class="button" href="{{ route('offers.create') }}">Créer une offre</a>
    @endif
</section>

@if(($isRecruiter || $isAdmin) && ! $showTrashed)
    @php
        // Chaque filtre preserve les autres dans l'URL.
        $base = array_filter([
            'with_applications' => $withApplicationsOnly ? 1 : null,
            'owner' => $ownerFilter,
        ]);
    @endphp
    <section class="actions">
        <a class="button {{ $statusFilter === null ? '' : 'secondary' }} tiny" href="{{ route('offers.index', $base) }}">
            Toutes ({{ array_sum($statusCounts) }})
        </a>
        @foreach($offerStatusLabels as $value => $label)
            <a class="button {{ $statusFilter === $value ? '' : 'secondary' }} tiny" href="{{ route('offers.index', $base + ['status' => $value]) }}">
                {{ $label }} ({{ $statusCounts[$value] ?? 0 }})
            </a>
        @endforeach
        <a class="button {{ $withApplicationsOnly ? '' : 'secondary' }} tiny" href="{{ route('offers.index', array_filter(['status' => $statusFilter, 'owner' => $ownerFilter, 'with_applications' => $withApplicationsOnly ? null : 1])) }}">
            Avec candidatures ({{ $withApplicationsCount }})
        </a>
    </section>

    @if($isAdmin && count($offerOwners))
        <section class="actions">
            <a class="button {{ $ownerFilter === null ? '' : 'secondary' }} tiny" href="{{ route('offers.index', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? 1 : null])) }}">
                Tous les recruteurs
            </a>
            @foreach($offerOwners as $owner)
                @if(($ownerCounts[$owner['id']] ?? 0) > 0)
                    <a class="button {{ $ownerFilter === $owner['id'] ? '' : 'secondary' }} tiny" href="{{ route('offers.index', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? 1 : null, 'owner' => $owner['id']])) }}">
                        {{ $owner['name'] }} ({{ $ownerCounts[$owner['id']] }})
                    </a>
                @endif
            @endforeach
            @if(($ownerCounts[''] ?? 0) > 0)
                <a class="button {{ $ownerFilter === 'none' ? '' : 'secondary' }} tiny danger" href="{{ route('offers.index', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? 1 : null, 'owner' => 'none'])) }}">
                    Sans propriétaire ({{ $ownerCounts[''] }})
                </a>
            @endif
        </section>
    @endif
@endif

@if($isAdmin)
    <section class="actions">
        <a class="button {{ $showTrashed ? 'secondary' : '' }} tiny" href="{{ route('offers.index') }}">Actives</a>
        <a class="button {{ $showTrashed ? '' : 'secondary' }} tiny" href="{{ route('offers.index', ['trashed' => 1]) }}">
            Supprimées ({{ $trashedCount }})
        </a>
    </section>
@endif

<section class="stack">
    @forelse($offers as $offer)
        @php
            $count = collect($applications)->where('offer_id', $offer['id'])->count();
            $score = $matchScores[$offer['id']] ?? null;
            $myApplication = $myApplicationsByOffer->get($offer['id']);
        @endphp
        <article class="item offer-card">
            <span class="company-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($offer['company'], 0, 1)) }}</span>
            <div class="item-main">
                <p class="offer-meta">
                    {{ $offer['company'] }} · {{ $offer['location'] }} · {{ $count }} candidature(s)
                    @if($isRecruiter || $isAdmin)
                        · <span class="status-badge status-{{ $offer['status'] ?? 'published' }}">{{ $offerStatusLabels[$offer['status'] ?? 'published'] ?? $offer['status'] }}</span>
                    @endif
                    @if($isAdmin)
                        @php $owner = collect($offerOwners)->firstWhere('id', $offer['owner_id'] ?? null); @endphp
                        · {{ $owner ? $owner['name'] : 'Sans propriétaire' }}
                    @endif
                </p>
                <h2><a href="{{ route('offers.show', $offer['id']) }}">{{ $offer['title'] }}</a></h2>
                <div class="chips">
                    @foreach($offer['required_skills'] as $skill)
                        <span>{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
            @if($score !== null)
                <div class="score-ring" aria-label="Score de compatibilité : {{ $score }} %">{{ $score }}%</div>
            @endif
            <div class="offer-actions">
            @if($showTrashed ?? false)
                <form class="inline-form" method="post" action="{{ route('offers.restore', $offer['id']) }}">
                    @csrf
                    <button class="button tiny" type="submit">Restaurer</button>
                </form>
            @elseif($myApplication)
                <span class="status-badge status-{{ $myApplication['status'] }}">{{ $statusLabels[$myApplication['status']] ?? $myApplication['status'] }}</span>
            @elseif($isAdmin)
                <div class="actions">
                    <a class="button secondary tiny" href="{{ route('offers.show', $offer['id']) }}">Voir les profils</a>
                    <a class="button secondary tiny" href="{{ route('offers.edit', $offer['id']) }}">Modifier</a>
                    <a class="button tiny danger" href="{{ route('offers.confirm-delete', $offer['id']) }}">Supprimer</a>
                </div>
            @else
                <a class="button secondary" href="{{ route('offers.show', $offer['id']) }}">{{ $isStudent ? 'Découvrir l’offre' : 'Voir les profils' }}</a>
            @endif
            </div>
        </article>
    @empty
        <div class="empty-state"><h2>{{ ($showTrashed ?? false) ? 'Aucune offre supprimée' : 'Aucune offre à afficher' }}</h2><p>Les offres correspondant à votre sélection apparaîtront ici.</p></div>
    @endforelse
</section>
@endsection
