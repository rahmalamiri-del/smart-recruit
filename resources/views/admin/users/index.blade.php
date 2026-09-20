@extends('layout')

@section('title', 'Gestion des comptes')

@section('content')
@php
    $roleLabels = [
        'admin' => 'Administrateur',
        'recruiter' => 'Recruteur',
        'student' => 'Candidat',
    ];
@endphp

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h1>Comptes</h1>
        <p class="lead">Recruteurs, candidats et administrateurs. Les suppressions sont réversibles.</p>
    </div>
    <a class="button" href="{{ route('admin.users.create') }}">Nouveau compte</a>
</section>

<section class="actions">
    <a class="button {{ $roleFilter === null && ! $trashed ? '' : 'secondary' }} tiny" href="{{ route('admin.users.index') }}">
        Tous ({{ $counts['admin'] + $counts['recruiter'] + $counts['student'] }})
    </a>
    @foreach($roleLabels as $value => $label)
        <a class="button {{ $roleFilter === $value && ! $trashed ? '' : 'secondary' }} tiny" href="{{ route('admin.users.index', ['role' => $value]) }}">
            {{ $label }} ({{ $counts[$value] ?? 0 }})
        </a>
    @endforeach
    <a class="button {{ $trashed ? '' : 'secondary' }} tiny" href="{{ route('admin.users.index', ['trashed' => 1]) }}">
        Supprimés ({{ $counts['trashed'] }})
    </a>
</section>

<section class="table-wrap">
    <table>
        <thead>
            <tr>
                <th scope="col">Compte</th>
                <th scope="col">Rôle</th>
                <th scope="col">Profil</th>
                <th scope="col">Créé le</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $account)
                <tr>
                    <td>
                        <strong>{{ $account['name'] }}</strong>
                        <span>{{ $account['email'] }}</span>
                    </td>
                    <td><span class="status-badge status-role-{{ $account['role'] }}">{{ $roleLabels[$account['role']] ?? $account['role'] }}</span></td>
                    <td>
                        @if($account['role'] === 'recruiter')
                            {{ $account['company_name'] ?: 'Entreprise non renseignée' }}
                            <span>{{ $account['position'] ?: '—' }}</span>
                        @elseif($account['student_id'])
                            <a href="{{ route('students.show', $account['student_id']) }}">Voir le profil candidat</a>
                        @else
                            <span>—</span>
                        @endif
                    </td>
                    <td class="table-date">{{ \Illuminate\Support\Str::before($account['created_at'], ' ') }}</td>
                    <td>
                        <div class="actions">
                            @if($account['deleted'])
                                <form class="inline-form" method="post" action="{{ route('admin.users.restore', $account['id']) }}">
                                    @csrf
                                    <button class="button tiny" type="submit">Restaurer</button>
                                </form>
                            @else
                                <a class="button secondary tiny" href="{{ route('admin.users.edit', $account['id']) }}">Modifier</a>
                                @if($account['id'] !== $user['id'])
                                    <a class="button tiny danger" href="{{ route('admin.users.confirm-delete', $account['id']) }}">Supprimer</a>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"><span>Aucun compte ne correspond à ce filtre.</span></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
