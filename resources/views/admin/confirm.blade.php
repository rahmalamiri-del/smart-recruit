@extends('layout')

@section('title', $title)

@section('content')
<section class="form-shell">
    <div class="section-title">
        <h1>{{ $title }}</h1>
        <a href="{{ $cancel }}">Annuler</a>
    </div>

    <div class="panel">
        <h2>{{ $entity }}</h2>

        <p class="lead">Cet élément sera retiré des listes actives. Vous pourrez le retrouver dans les éléments supprimés et le restaurer.</p>

        @if(count($impact))
            <p class="muted">Seront également masqués :</p>
            <ul class="impact-list">
                @foreach($impact as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        @endif

        <form method="post" action="{{ $action }}">
            @csrf
            <div class="actions">
                <button class="button danger" type="submit">Confirmer la suppression</button>
                <a class="button secondary" href="{{ $cancel }}">Annuler</a>
            </div>
        </form>
    </div>
</section>
@endsection
