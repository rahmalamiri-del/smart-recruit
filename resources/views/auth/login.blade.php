@extends('layout')

@section('title', 'Connexion · SmartRecruit')

@section('content')
<section class="auth-shell" aria-labelledby="login-title">
    <aside class="auth-aside" aria-labelledby="login-welcome">
        <p class="auth-kicker">Candidats &amp; entreprises</p>
        <h2 id="login-welcome">Les bons profils.<br>Les bonnes opportunités.</h2>
        <p>Un espace commun pour faire se rencontrer les compétences et les projets.</p>

        <ul class="auth-benefits">
            <li>
                <strong>Vous êtes candidat</strong>
                <span>Présentez votre parcours, découvrez les offres et retrouvez vos candidatures.</span>
            </li>
            <li>
                <strong>Vous recrutez</strong>
                <span>Publiez vos offres, explorez les profils et suivez les candidatures reçues.</span>
            </li>
        </ul>
    </aside>

    <div class="auth-content">
        <div class="section-title">
            <h1 id="login-title">Connexion</h1>
            <a href="{{ route('dashboard') }}">Retour à l'accueil</a>
        </div>
        <p class="muted">Bienvenue. Connectez-vous pour retrouver votre espace.</p>

        <form class="form-card" method="post" action="{{ route('login.store') }}">
            @csrf
            <div class="form-section">
                <label for="login-email">
                    Adresse e-mail
                    <input id="login-email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                </label>
                <label for="login-password">
                    Mot de passe
                    <input id="login-password" type="password" name="password" autocomplete="current-password" required>
                </label>
            </div>

            <button class="button" type="submit">Se connecter</button>
        </form>

        <p class="auth-footer">
            Vous découvrez SmartRecruit ? <a href="{{ route('register') }}">Créer un compte</a>
        </p>
    </div>
</section>
@endsection
