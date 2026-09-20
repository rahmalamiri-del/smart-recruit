@extends('layout')

@section('title', 'Profils candidats')

@section('content')
@php
    $role = $user['role'] ?? 'guest';
    $isRecruiter = $role === 'recruiter';
    $isAdmin = $role === 'admin';
    $highMatchThreshold = 70;
@endphp
<section class="page-head">
    <div>
        <p class="eyebrow">{{ $isRecruiter ? 'Compatibilité avec vos offres' : 'Votre vivier de talents' }}</p>
        <h1>Profils candidats</h1>
        @if($isRecruiter)
            <p class="lead">Triés par compatibilité avec vos offres — les profils les plus proches en premier.</p>
        @endif
    </div>
    @if($role === 'admin')
        <a class="button" href="{{ route('students.create') }}">Ajouter un CV</a>
    @endif
</section>

@if($isAdmin)
    <section class="actions">
        <a class="button {{ $showTrashed ? 'secondary' : '' }} tiny" href="{{ route('students.index') }}">Actifs</a>
        <a class="button {{ $showTrashed ? '' : 'secondary' }} tiny" href="{{ route('students.index', ['trashed' => 1]) }}">
            Supprimés ({{ $trashedCount }})
        </a>
    </section>
@endif

<section class="table-wrap">
    <table>
        <thead>
            <tr>
                <th scope="col">Candidat</th>
                <th scope="col">Formation</th>
                <th scope="col">Compétences détectées</th>
                <th scope="col">Exp.</th>
                @if($isRecruiter)
                    <th scope="col">Compatibilité</th>
                @endif
                <th scope="col">CV</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                @php
                    $best = $bestMatches[$student['id']] ?? null;
                    $bestScore = $best['match']['score'] ?? null;
                    $isHighMatch = $bestScore !== null && $bestScore >= $highMatchThreshold;
                @endphp
                <tr class="{{ $isHighMatch ? 'high-match' : '' }}">
                    <td>
                        <strong>{{ $student['name'] }}</strong>
                        <span>{{ $student['headline'] }}</span>
                    </td>
                    <td>{{ $student['education'] }}</td>
                    <td>
                        <div class="chips">
                            @foreach(array_slice($student['skills'], 0, 6) as $skill)
                                <span>{{ $skill }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>{{ $student['experience_years'] }} an(s)</td>
                    @if($isRecruiter)
                        <td>
                            @if($bestScore !== null)
                                <span class="match-chip {{ $isHighMatch ? 'high' : '' }}">{{ $bestScore }}%</span>
                                <span>pour « {{ $best['offer']['title'] }} »</span>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                    @endif
                    <td>
                        @if($student['file_name'])
                            <a href="{{ route('students.cv', $student['id']) }}" target="_blank" rel="noopener">Ouvrir</a>
                        @else
                            <span>—</span>
                        @endif
                    </td>
                    <td>
                        @if($showTrashed ?? false)
                            <form class="inline-form" method="post" action="{{ route('students.restore', $student['id']) }}">
                                @csrf
                                <button class="button tiny" type="submit">Restaurer</button>
                            </form>
                        @else
                            <a class="button secondary tiny" href="{{ route('students.show', $student['id']) }}">Voir le profil</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ $isRecruiter ? 7 : 6 }}"><div class="empty-state">Aucun profil à afficher pour cette sélection.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
