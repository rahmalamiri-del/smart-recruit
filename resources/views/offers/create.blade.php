@extends('layout')

@section('title', 'Créer une offre')

@section('content')
<section class="form-shell wide">
    <div class="section-title">
        <h1>Créer une offre de stage</h1>
        <a href="{{ route('offers.index') }}">Retour aux offres</a>
    </div>
    <p class="muted">Présentez votre mission et les compétences que vous recherchez.</p>

    <form class="form-card" method="post" action="{{ route('offers.store') }}">
        @csrf
        <div class="form-section">
            <h2>Informations sur l'offre</h2>
            <div class="grid-2">
                <label>
                    Titre de l'offre
                    <input name="title" value="{{ old('title') }}" required>
                </label>
                <label>
                    Entreprise
                    <input name="company" value="{{ old('company') }}" required>
                </label>
                <label>
                    Localisation
                    <input name="location" value="{{ old('location') }}">
                </label>
                <label>
                    Type d'opportunité
                    <input name="type" value="{{ old('type', 'Stage PFE') }}">
                </label>
            </div>
        </div>

        <div class="form-section">
            <h2>Compétences recherchées</h2>
            <label>
                Compétences requises
                <input name="required_skills" value="{{ old('required_skills') }}" placeholder="python, nlp, machine learning">
            </label>
            <p class="field-help">Séparez les compétences par des virgules pour préciser les besoins de votre mission.</p>
        </div>

        <div class="form-section">
            <h2>La mission</h2>
            <label>
                Description de l'offre
                <textarea name="description" rows="10" required>{{ old('description') }}</textarea>
            </label>
            <p class="field-help">Décrivez les missions, le contexte du projet et le profil attendu.</p>
        </div>

        <button class="button" type="submit">Publier l'offre</button>
    </form>
</section>
@endsection
