@extends('layout')

@section('title', $offer['title'])

@section('content')
@php
    $role = $user['role'] ?? 'guest';
    $isManager = in_array($role, ['admin', 'recruiter'], true);
    $applicationsByStudent = collect($applications)->keyBy('student_id');
    $myApplication = $role === 'student' ? $applicationsByStudent->get($user['student_id'] ?? null) : null;
    $statusLabels = config('smart_recruit.application_status_labels');
@endphp

<section class="page-head">
    <div>
        <p class="eyebrow">Offre de stage</p>
        <h1>{{ $offer['title'] }}</h1>
        <p class="lead">{{ $offer['company'] }} · {{ $offer['location'] }} · {{ $offer['type'] }}</p>
    </div>
    <div class="actions">
        @if($isManager)
            <a class="button secondary" href="{{ route('offers.edit', $offer['id']) }}">Modifier l'offre</a>
        @endif
        @if($role === 'admin')
            <a class="button danger" href="{{ route('offers.confirm-delete', $offer['id']) }}">Supprimer</a>
        @endif
        @if($role === 'student' && ! $myApplication)
            <form method="post" action="{{ route('offers.apply', $offer['id']) }}">
                @csrf
                <button class="button" type="submit">Postuler en un clic</button>
            </form>
        @endif
    </div>
</section>

<section class="panel offer-overview">
    <p class="eyebrow">L’opportunité</p>
    <h2>À propos de l’offre</h2>
    <p class="offer-description">{{ $offer['description'] }}</p>
    @if(count($offer['required_skills']))
        <h3>Compétences recherchées</h3>
        <div class="chips">
            @foreach($offer['required_skills'] as $skill)
                <span>{{ $skill }}</span>
            @endforeach
        </div>
    @endif
</section>

@if($role === 'admin' && count($offerOwners))
    <section class="panel">
        <h2>Propriétaire de l'offre</h2>
        <p class="muted">Réassigner cette offre à un autre recruteur (utile si son propriétaire a été supprimé).</p>
        <form class="form-card compact-form" method="post" action="{{ route('offers.reassign', $offer['id']) }}">
            @csrf
            <label>
                Recruteur
                <select name="owner_id" required>
                    @foreach($offerOwners as $owner)
                        <option value="{{ $owner['id'] }}" @selected(($offer['owner_id'] ?? null) === $owner['id'])>
                            {{ $owner['name'] }}{{ $owner['company_name'] ? ' — '.$owner['company_name'] : '' }}
                        </option>
                    @endforeach
                </select>
            </label>
            <button class="button secondary" type="submit">Réassigner</button>
        </form>
    </section>
@endif

@if($role === 'student')
    @if($myApplication)
        <section class="panel">
            <h2>Ma candidature</h2>
            <span class="status-badge status-{{ $myApplication['status'] }}">{{ $statusLabels[$myApplication['status']] ?? $myApplication['status'] }}</span>
        </section>
    @else
        <section class="panel">
            <h2>Postuler en un clic</h2>
            <p class="muted">
                Votre candidature utilise automatiquement le CV et les compétences de votre profil.
                Cliquez sur « Postuler en un clic » ci-dessus pour envoyer votre candidature.
            </p>
            @if($user['student_id'] ?? null)
                <a class="button secondary" href="{{ route('students.edit', $user['student_id']) }}">Mettre à jour mon CV avant de postuler</a>
            @endif
        </section>
    @endif
@endif

@if($isManager)
<section>
    <div class="section-title">
        <h2>Classement automatique</h2>
        <span>{{ count($applications) }} candidature(s) enregistrée(s)</span>
    </div>

    <div class="ranking">
        @forelse($rankings as $index => $row)
            @php
                $application = $applicationsByStudent->get($row['student']['id']);
                $status = $application['status'] ?? null;
            @endphp

            <article class="candidate">
                <div class="rank">#{{ $index + 1 }}</div>
                <div class="candidate-main">
                    <div class="candidate-head">
                        <div>
                            <h3><a href="{{ route('students.show', $row['student']['id']) }}">{{ $row['student']['name'] }}</a></h3>
                            <p>{{ $row['student']['headline'] }}</p>
                        </div>
                        <strong>{{ $row['match']['score'] }}%</strong>
                    </div>
                    <div class="bar"><span style="width: {{ $row['match']['score'] }}%"></span></div>
                    <p class="muted">{{ $row['match']['explanation'] }}</p>
                    <div class="match-meta">
                        <span>Similarité du texte : {{ $row['match']['text_similarity'] }}%</span>
                        <span>Correspondance des compétences : {{ $row['match']['semantic_coverage'] }}%</span>
                    </div>
                    <div class="chips matched">
                        @foreach($row['match']['matched_skills'] as $skill)
                            <span>{{ $skill }}</span>
                        @endforeach
                    </div>
                    @if(count($row['match']['missing_skills']))
                        <p class="missing">Compétences à vérifier : {{ implode(', ', $row['match']['missing_skills']) }}</p>
                    @endif

                    @if($application)
                        <div class="decision-row">
                            <span class="status-badge status-{{ $status }}">{{ $statusLabels[$status] ?? $status }}</span>
                            @if($application['match_score'] !== null)
                                <span class="decision-score">Score enregistré : {{ $application['match_score'] }}%</span>
                            @endif
                            <form class="inline-form" method="post" action="{{ route('applications.status', $application['id']) }}">
                                @csrf
                                <input type="hidden" name="status" value="interview">
                                <button class="button tiny" type="submit">Accepter</button>
                            </form>
                            <form class="inline-form" method="post" action="{{ route('applications.status', $application['id']) }}">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button class="button tiny danger" type="submit">Refuser</button>
                            </form>
                        </div>
                    @else
                        <p class="muted small-note">Ce profil n’a pas encore candidaté à cette offre.</p>
                    @endif
                </div>
            </article>
        @empty
            <div class="empty-state"><h3>Aucun profil à comparer</h3><p>Le classement apparaîtra lorsque des profils seront disponibles.</p></div>
        @endforelse
    </div>
</section>
@endif
@endsection
