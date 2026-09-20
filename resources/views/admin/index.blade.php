@extends('layout')

@section('title', 'Administration')

@section('content')
@php
    $statusLabels = config('smart_recruit.application_status_labels');
    $recentApplications = array_slice($data['applications'], 0, 10);
@endphp

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h1>Pilotez votre plateforme</h1>
        <p class="lead">Gestion complète des comptes, des offres, des profils candidats et des candidatures.</p>
    </div>
    <div class="actions">
        <a class="button" href="{{ route('admin.users.create') }}">Nouveau compte</a>
        <a class="button secondary" href="{{ route('offers.create') }}">Nouvelle offre</a>
    </div>
</section>

<section class="stats-grid">
    <article class="metric">
        <span>{{ $userCounts['recruiter'] ?? 0 }}</span>
        <p>Recruteurs</p>
    </article>
    <article class="metric">
        <span>{{ count($data['students']) }}</span>
        <p>Profils candidats</p>
    </article>
    <article class="metric">
        <span>{{ count($data['offers']) }}</span>
        <p>Offres</p>
    </article>
    <article class="metric">
        <span>{{ count($data['applications']) }}</span>
        <p>Candidatures</p>
    </article>
</section>

<section class="split">
    <div>
        <div class="section-title">
            <h2>Gérer</h2>
        </div>
        <div class="stack">
            <article class="item">
                <div class="item-main">
                    <h3><a href="{{ route('admin.users.index') }}">Comptes et recruteurs</a></h3>
                    <p class="muted">Créer, modifier, changer un rôle, réinitialiser un mot de passe, supprimer ou restaurer.</p>
                </div>
                <a class="button secondary" href="{{ route('admin.users.index') }}">Ouvrir</a>
            </article>
            <article class="item">
                <div class="item-main">
                    <h3><a href="{{ route('offers.index') }}">Offres</a></h3>
                    <p class="muted">Toutes les offres : modifier, changer le statut, réassigner à un recruteur, supprimer.</p>
                </div>
                <a class="button secondary" href="{{ route('offers.index') }}">Ouvrir</a>
            </article>
            <article class="item">
                <div class="item-main">
                    <h3><a href="{{ route('students.index') }}">Profils candidats</a></h3>
                    <p class="muted">Profils candidats : ajouter un CV, modifier, supprimer ou restaurer.</p>
                </div>
                <a class="button secondary" href="{{ route('students.index') }}">Ouvrir</a>
            </article>
            <article class="item">
                <div class="item-main">
                    <h3><a href="{{ route('admin.applications.index') }}">Candidatures</a></h3>
                    <p class="muted">Suivre, filtrer, accepter, refuser ou retirer une candidature.</p>
                </div>
                <a class="button secondary" href="{{ route('admin.applications.index') }}">Ouvrir</a>
            </article>
        </div>
    </div>

    <div class="panel">
        <p class="eyebrow">Repères de gestion</p>
        <h2>Un suivi à chaque étape</h2>
        <dl class="definition">
            <dt>Suppression</dt>
            <dd>Les éléments supprimés peuvent être restaurés depuis les listes.</dd>
            <dt>Comparaison</dt>
            <dd>Les profils sont rapprochés des offres selon leur contenu et leurs compétences.</dd>
            <dt>Candidatures</dt>
            <dd>Suivez les candidatures reçues, présélectionnées, acceptées ou refusées.</dd>
        </dl>
    </div>
</section>

<section class="panel full">
    <div class="section-title">
        <h2>Dernières candidatures</h2>
        <a href="{{ route('admin.applications.index') }}">Tout voir ({{ count($data['applications']) }})</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th scope="col">Offre</th>
                    <th scope="col">Candidat</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentApplications as $application)
                    @php
                        $offer = collect($data['offers'])->firstWhere('id', $application['offer_id']);
                        $student = collect($data['students'])->firstWhere('id', $application['student_id']);
                    @endphp
                    <tr>
                        <td>{{ $offer['title'] ?? $application['offer_id'] }}</td>
                        <td>{{ $student['name'] ?? $application['student_id'] }}</td>
                        <td><span class="status-badge status-{{ $application['status'] }}">{{ $statusLabels[$application['status']] ?? $application['status'] }}</span></td>
                        <td>{{ $application['match_score'] !== null ? $application['match_score'].'%' : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
