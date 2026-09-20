@extends('layout')

@section('title', $account ? 'Modifier le compte' : 'Nouveau compte')

@section('content')
@php
    $isEdit = $account !== null;
    $currentRole = old('role', $isEdit ? $account['role'] : $defaultRole);
    $roleLabels = config('smart_recruit.role_labels');
    $isOwnAccount = $isEdit && $account['id'] === $user['id'];
@endphp

<section class="form-shell wide">
    <div class="section-title">
        <h1>{{ $isEdit ? 'Modifier le compte' : 'Créer un compte' }}</h1>
        <a href="{{ route('admin.users.index') }}">Retour à la liste</a>
    </div>
    <p class="muted">{{ $isEdit ? 'Actualisez les informations du compte et vérifiez ses accès.' : 'Renseignez les informations de la personne et choisissez son rôle dans SmartRecruit.' }}</p>

    <form class="form-card" method="post" action="{{ $isEdit ? route('admin.users.update', $account['id']) : route('admin.users.store') }}">
        @csrf
        <div class="form-section">
            <h2>Informations du compte</h2>
            <div class="grid-2">
                <label>
                    Nom complet
                    <input name="name" value="{{ old('name', $isEdit ? $account['name'] : '') }}" required>
                </label>
                <label>
                    Adresse e-mail
                    <input type="email" name="email" value="{{ old('email', $isEdit ? $account['email'] : '') }}" required>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h2>Accès</h2>
            <label>
                Rôle
                @if($isOwnAccount)
                    <input value="{{ $roleLabels[$currentRole] ?? $currentRole }}" disabled>
                    <input type="hidden" name="role" value="{{ $currentRole }}">
                @else
                    <select name="role" required>
                        @foreach($roleLabels as $value => $label)
                            <option value="{{ $value }}" @selected($currentRole === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
            </label>
            <p class="field-help">Le rôle détermine les espaces et les actions accessibles à ce compte.</p>
        </div>

        <div class="form-section">
            <h2>Informations du recruteur</h2>
            <p class="field-help">Complétez ces champs pour un compte recruteur.</p>
            <div class="grid-2">
                <label>
                    Raison sociale <span>(recruteur)</span>
                    <input name="company_name" value="{{ old('company_name', $isEdit ? $account['company_name'] : '') }}">
                </label>
                <label>
                    Poste occupé <span>(recruteur)</span>
                    <input name="position" value="{{ old('position', $isEdit ? $account['position'] : '') }}">
                </label>
                <label>
                    Site web <span>(recruteur)</span>
                    <input type="url" name="website" value="{{ old('website', $isEdit ? $account['website'] : '') }}">
                </label>
            </div>
        </div>

        @if($isEdit)
            @if($account['student_id'])
                <div class="form-section">
                    <h2>Profil candidat</h2>
                    <p class="field-help">
                        Le parcours et le CV se modifient depuis le profil lié à ce compte.
                        <a href="{{ route('students.edit', $account['student_id']) }}">Modifier le profil candidat</a>
                    </p>
                </div>
            @endif
        @else
            <div class="form-section">
                <h2>Profil candidat</h2>
                <label>
                    Titre du profil <span>(candidat)</span>
                    <input name="headline" value="{{ old('headline') }}" placeholder="Développeur Full-Stack, Data Scientist...">
                </label>
                <p class="field-help">Pour un compte candidat, indiquez le domaine de formation ou le métier recherché.</p>
            </div>
        @endif

        @unless($isEdit)
            <div class="form-section">
                <h2>Mot de passe</h2>
                <div class="grid-2">
                    <label>
                        Mot de passe
                        <input type="password" name="password" required minlength="8">
                    </label>
                    <label>
                        Confirmer le mot de passe
                        <input type="password" name="password_confirmation" required minlength="8">
                    </label>
                </div>
                <p class="field-help">Utilisez au moins 8 caractères et confirmez le mot de passe à l'identique.</p>
            </div>
        @endunless

        <button class="button" type="submit">{{ $isEdit ? 'Enregistrer les modifications' : 'Créer le compte' }}</button>
    </form>

    <p class="field-help">
        Le champ « raison sociale » n'est requis que pour un recruteur, « titre du profil » que pour un candidat.
        Le profil correspondant est créé automatiquement selon le rôle choisi.
        @if($isOwnAccount)
            Votre propre rôle n'est pas modifiable, afin d'éviter de vous retirer vos droits.
        @endif
    </p>

    @if($isEdit)
        <div class="panel">
            <form class="form-card" method="post" action="{{ route('admin.users.password', $account['id']) }}">
                @csrf
                <div class="form-section">
                    <h2>Réinitialiser le mot de passe</h2>
                    <p class="field-help">Renseignez un nouveau mot de passe d'au moins 8 caractères pour ce compte.</p>
                    <div class="grid-2">
                        <label>
                            Nouveau mot de passe
                            <input type="password" name="password" required minlength="8">
                        </label>
                        <label>
                            Confirmer le mot de passe
                            <input type="password" name="password_confirmation" required minlength="8">
                        </label>
                    </div>
                </div>
                <button class="button secondary" type="submit">Réinitialiser le mot de passe</button>
            </form>
        </div>
    @endif
</section>
@endsection
