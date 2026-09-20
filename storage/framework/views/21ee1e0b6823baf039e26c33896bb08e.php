<?php $__env->startSection('title', 'Créer un compte · SmartRecruit'); ?>

<?php $__env->startSection('content'); ?>
<?php if(! $role): ?>
<section class="auth-shell" aria-labelledby="register-title">
    <aside class="auth-aside" aria-labelledby="register-welcome">
        <p class="auth-kicker">Bienvenue sur SmartRecruit</p>
        <h2 id="register-welcome">Deux parcours.<br>Une même rencontre.</h2>
        <p>Choisissez l'espace qui correspond à votre projet et faites le premier pas.</p>

        <ul class="auth-benefits">
            <li>
                <strong>Donnez de la visibilité à votre parcours</strong>
                <span>Un profil pour présenter votre formation, vos projets et vos compétences.</span>
            </li>
            <li>
                <strong>Faites connaître vos besoins</strong>
                <span>Des offres pour rencontrer des candidats dont le parcours rejoint vos projets.</span>
            </li>
        </ul>
    </aside>

    <div class="auth-content">
        <div class="section-title">
            <h1 id="register-title">Créer un compte</h1>
        </div>
        <p class="muted">Quel espace vous correspond ?</p>

        <div class="form-card">
            <div class="form-section">
                <h2>Je suis candidat</h2>
                <p class="field-help">Je souhaite présenter mon profil et candidater à des offres.</p>
                <a class="button" href="<?php echo e(route('register', ['role' => 'student'])); ?>">Créer mon compte candidat</a>
            </div>
            <div class="form-section">
                <h2>Je suis recruteur</h2>
                <p class="field-help">Je souhaite publier des offres et découvrir des profils pour mon entreprise.</p>
                <a class="button secondary" href="<?php echo e(route('register', ['role' => 'recruiter'])); ?>">Créer mon compte recruteur</a>
            </div>
        </div>

        <p class="auth-footer">
            Déjà un compte ? <a href="<?php echo e(route('login')); ?>">Se connecter</a>
        </p>
    </div>
</section>
<?php else: ?>
<section class="auth-shell" aria-labelledby="register-title">
    <aside class="auth-aside" aria-labelledby="register-welcome">
        <p class="auth-kicker">Espace <?php echo e($role === 'recruiter' ? 'recruteur' : 'candidat'); ?></p>
        <?php if($role === 'recruiter'): ?>
            <h2 id="register-welcome">Vos projets ont besoin de talents.</h2>
            <p>Créez votre espace pour présenter votre entreprise et les opportunités que vous proposez.</p>
            <ul class="auth-benefits">
                <li>
                    <strong>Présentez vos offres</strong>
                    <span>Décrivez les missions et les compétences recherchées.</span>
                </li>
                <li>
                    <strong>Découvrez les profils</strong>
                    <span>Consultez les parcours des candidats et suivez les candidatures à vos offres.</span>
                </li>
            </ul>
        <?php else: ?>
            <h2 id="register-welcome">Votre parcours mérite d'être découvert.</h2>
            <p>Créez votre espace pour faire connaître vos compétences et découvrir des opportunités.</p>
            <ul class="auth-benefits">
                <li>
                    <strong>Commencez par l'essentiel</strong>
                    <span>Choisissez un titre qui décrit votre formation ou votre domaine.</span>
                </li>
                <li>
                    <strong>Complétez ensuite votre profil</strong>
                    <span>Ajoutez votre CV, vos compétences et vos liens professionnels depuis votre espace.</span>
                </li>
            </ul>
        <?php endif; ?>
    </aside>

    <div class="auth-content">
        <div class="section-title">
            <h1 id="register-title">Inscription <?php echo e($role === 'recruiter' ? 'recruteur' : 'candidat'); ?></h1>
            <a href="<?php echo e(route('register')); ?>">Changer de rôle</a>
        </div>
        <p class="muted">Renseignez vos informations pour créer votre espace.</p>

        <form class="form-card" method="post" action="<?php echo e(route('register.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="role" value="<?php echo e($role); ?>">

            <div class="form-section">
                <h2>Vos informations</h2>
                <label for="register-name">
                    Nom complet
                    <input id="register-name" name="name" value="<?php echo e(old('name')); ?>" autocomplete="name" required autofocus>
                </label>
                <label for="register-email">
                    Adresse e-mail
                    <input id="register-email" type="email" name="email" value="<?php echo e(old('email')); ?>" autocomplete="username" required>
                </label>

                <?php if($role === 'recruiter'): ?>
                    <label for="register-company">
                        Raison sociale
                        <input id="register-company" name="company_name" value="<?php echo e(old('company_name')); ?>" autocomplete="organization" required>
                    </label>
                    <label for="register-position">
                        Poste occupé (facultatif)
                        <input id="register-position" name="position" value="<?php echo e(old('position')); ?>" autocomplete="organization-title">
                    </label>
                <?php else: ?>
                    <label for="register-headline">
                        Titre du profil
                        <input id="register-headline" name="headline" value="<?php echo e(old('headline')); ?>" placeholder="Développeur Full-Stack, Data Scientist..." required>
                    </label>
                <?php endif; ?>
            </div>

            <div class="form-section">
                <h2>Votre mot de passe</h2>
                <label for="register-password">
                    Mot de passe
                    <input id="register-password" type="password" name="password" autocomplete="new-password" aria-describedby="password-help" required minlength="8">
                </label>
                <p class="field-help" id="password-help">Utilisez au moins 8 caractères.</p>
                <label for="register-password-confirmation">
                    Confirmer le mot de passe
                    <input id="register-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required minlength="8">
                </label>
            </div>

            <button class="button" type="submit">Créer mon compte</button>
        </form>

        <p class="auth-footer">
            Déjà un compte ? <a href="<?php echo e(route('login')); ?>">Se connecter</a>
        </p>
    </div>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/auth/register.blade.php ENDPATH**/ ?>