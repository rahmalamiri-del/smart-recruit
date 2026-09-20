@extends('layout')

@section('title', 'Modifier l\'offre')

@section('content')
<section class="form-shell wide">
    <div class="section-title">
        <h1>Modifier l'offre de stage</h1>
        <a href="{{ route('offers.show', $offer['id']) }}">Retour à l'offre</a>
    </div>
    <p class="muted">Actualisez les informations de votre offre et son état de publication.</p>

    <form class="form-card" method="post" action="{{ route('offers.update', $offer['id']) }}">
        @csrf
        <div class="form-section">
            <h2>Informations sur l'offre</h2>
            <div class="grid-2">
                <label>
                    Titre de l'offre
                    <input name="title" value="{{ old('title', $offer['title']) }}" required>
                </label>
                <label>
                    Entreprise
                    <input name="company" value="{{ old('company', $offer['company']) }}" required>
                </label>
                <label>
                    Localisation
                    <input name="location" value="{{ old('location', $offer['location']) }}">
                </label>
                <label>
                    Type d'opportunité
                    <input name="type" value="{{ old('type', $offer['type']) }}">
                </label>
                <label>
                    État de publication
                    <select name="status">
                        @foreach(['published' => 'Publiée', 'draft' => 'Brouillon', 'closed' => 'Fermée'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $offer['status']) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h2>Compétences recherchées</h2>
            <label>
                Compétences requises
                <input name="required_skills" value="{{ old('required_skills', implode(', ', $offer['required_skills'])) }}" placeholder="python, nlp, machine learning">
            </label>
            <p class="field-help">Séparez les compétences par des virgules pour préciser les besoins de votre mission.</p>
        </div>

        <div class="form-section">
            <h2>La mission</h2>
            <label>
                Description de l'offre
                <textarea name="description" rows="10" required>{{ old('description', $offer['description']) }}</textarea>
            </label>
            <p class="field-help">Décrivez les missions, le contexte du projet et le profil attendu.</p>
        </div>

        <button class="button" type="submit">Enregistrer les modifications</button>
    </form>
</section>
@endsection
