@extends('layout')

@section('title', 'Gestion des candidatures')

@section('content')
@php
    $statusLabels = config('smart_recruit.application_status_labels');
@endphp

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h1>Candidatures</h1>
        <p class="lead">Toutes les candidatures de la plateforme, tous recruteurs confondus.</p>
    </div>
</section>

<section class="actions">
    <a class="button {{ $statusFilter === null && ! $trashed ? '' : 'secondary' }} tiny" href="{{ route('admin.applications.index') }}">
        Toutes ({{ $counts['active'] }})
    </a>
    @foreach($statusLabels as $value => $label)
        <a class="button {{ $statusFilter === $value && ! $trashed ? '' : 'secondary' }} tiny" href="{{ route('admin.applications.index', ['status' => $value]) }}">
            {{ $label }} ({{ $counts[$value] ?? 0 }})
        </a>
    @endforeach
    <a class="button {{ $trashed ? '' : 'secondary' }} tiny" href="{{ route('admin.applications.index', ['trashed' => 1]) }}">
        Supprimées ({{ $counts['trashed'] }})
    </a>
</section>

<section class="table-wrap">
    <table>
        <thead>
            <tr>
                <th scope="col">Candidat</th>
                <th scope="col">Offre</th>
                <th scope="col">Statut</th>
                <th scope="col">Score</th>
                <th scope="col">Date</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $application)
                <tr>
                    <td>
                        <strong>{{ $application['student_name'] }}</strong>
                        <span>{{ $application['student_headline'] }}</span>
                    </td>
                    <td>
                        @if($application['deleted'])
                            {{ $application['offer_title'] }}
                        @else
                            <a href="{{ route('offers.show', $application['offer_id']) }}">{{ $application['offer_title'] }}</a>
                        @endif
                        <span>{{ $application['offer_company'] }}</span>
                    </td>
                    <td><span class="status-badge status-{{ $application['status'] }}">{{ $statusLabels[$application['status']] ?? $application['status'] }}</span></td>
                    <td>{{ $application['match_score'] !== null ? $application['match_score'].'%' : '—' }}</td>
                    <td class="table-date">{{ \Illuminate\Support\Str::before($application['applied_at'], ' ') }}</td>
                    <td>
                        <div class="actions">
                            @if($application['deleted'])
                                <form class="inline-form" method="post" action="{{ route('admin.applications.restore', $application['id']) }}">
                                    @csrf
                                    <button class="button tiny" type="submit">Restaurer</button>
                                </form>
                            @else
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
                                <form class="inline-form" method="post" action="{{ route('admin.applications.destroy', $application['id']) }}">
                                    @csrf
                                    <button class="button secondary tiny" type="submit">Supprimer</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"><span>Aucune candidature ne correspond à ce filtre.</span></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
