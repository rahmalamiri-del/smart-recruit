@extends('layout')

@section('title', $student['name'])

@section('content')
@php
    $role = $user['role'] ?? 'guest';
    $isOwnProfile = $role === 'student' && $student['id'] === ($user['student_id'] ?? null);
    // Le CV se gère par son propriétaire ou par l'administrateur ; le recruteur
    // ne fait que le consulter.
    $canManageCv = $role === 'admin' || $isOwnProfile;
@endphp
<section class="page-head profile-header">
    <div class="profile-identity">
        <span class="profile-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($student['name'], 0, 1)) }}</span>
        <div>
        <p class="eyebrow">{{ $student['location'] }} · {{ $student['experience_years'] }} an(s)</p>
        <h1>{{ $student['name'] }}</h1>
        <p class="lead">{{ $student['headline'] }}</p>
        </div>
    </div>
    <div class="actions">
        @if(in_array($user['role'] ?? 'guest', ['admin', 'recruiter']))
            <a class="button secondary" href="{{ route('students.index') }}">Tous les profils</a>
        @endif
        @if((($user['role'] ?? 'guest') === 'admin') || (($user['role'] ?? 'guest') === 'student' && $student['id'] === ($user['student_id'] ?? null)))
            <a class="button secondary" href="{{ route('students.edit', $student['id']) }}">Modifier</a>
        @endif
        @if(($user['role'] ?? 'guest') === 'admin')
            <a class="button danger" href="{{ route('students.confirm-delete', $student['id']) }}">Supprimer</a>
        @endif
    </div>
</section>

<section class="split">
    <article class="panel">
        <h2>Parcours et compétences</h2>
        <dl class="definition">
            <dt>E-mail</dt>
            <dd>{{ $student['email'] }}</dd>
            <dt>Formation</dt>
            <dd>{{ $student['education'] }}</dd>
            <dt>Fichier CV</dt>
            <dd>
                @if($student['file_name'])
                    <a href="{{ route('students.cv', $student['id']) }}" target="_blank" rel="noopener">{{ $student['file_name'] }}</a>
                @else
                    Non chargé
                @endif
            </dd>
        </dl>

        @if($student['file_name'])
            <div class="actions">
                <a class="button secondary tiny" href="{{ route('students.cv', $student['id']) }}" target="_blank" rel="noopener">Ouvrir le CV</a>
                @if($canManageCv)
                    <a class="button secondary tiny" href="{{ route('students.edit', $student['id']) }}">Remplacer</a>
                    <form class="inline-form" method="post" action="{{ route('students.cv.destroy', $student['id']) }}">
                        @csrf
                        <button class="button tiny danger" type="submit">Retirer le CV</button>
                    </form>
                @endif
            </div>
        @endif
        <div class="chips">
            @foreach($student['skills'] as $skill)
                <span>{{ $skill }}</span>
            @endforeach
        </div>
    </article>

    <article class="panel">
        <h2>Coordonnées du CV</h2>
        <dl class="definition">
            <dt>E-mails détectés</dt>
            <dd>{{ implode(', ', $student['metadata']['emails'] ?? []) ?: 'Aucun' }}</dd>
            <dt>Téléphones détectés</dt>
            <dd>{{ implode(', ', $student['metadata']['phones'] ?? []) ?: 'Aucun' }}</dd>
        </dl>
    </article>
</section>

<section class="panel full">
    <h2>Contenu du CV</h2>
    <p class="cv-text">{{ $student['cv_text'] ?: 'Aucun texte CV disponible.' }}</p>
</section>
@endsection
