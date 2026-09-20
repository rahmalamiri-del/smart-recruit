<?php $__env->startSection('title', 'SmartRecruit — Candidats et entreprises'); ?>

<?php $__env->startSection('content'); ?>
<section class="page-head dashboard-hero">
    <div class="hero-content">
        <p class="hero-label">Candidats &amp; entreprises</p>
        <h1>Les bonnes compétences.<br>De nouvelles opportunités.</h1>
        <p class="lead">Un espace pour présenter votre parcours, découvrir des offres et faire avancer vos recrutements.</p>
        <div class="actions">
            <a class="button" href="<?php echo e(route('register')); ?>">Créer mon compte</a>
            <a class="button secondary" href="<?php echo e(route('login')); ?>">Se connecter</a>
        </div>
    </div>
    <aside class="hero-aside">
        <p class="hero-label">Faites le premier pas</p>
        <h2>Votre parcours mérite d’être découvert.</h2>
        <p>Candidat, mettez vos compétences en lumière. Recruteur, présentez vos besoins et explorez les profils.</p>
    </aside>
</section>

<section class="stats-grid" aria-label="SmartRecruit en un coup d’œil">
    <article class="metric">
        <p class="metric-label">Profils candidats</p>
        <span><?php echo e(count($data['students'])); ?></span>
        <p class="metric-caption">Des parcours à découvrir</p>
    </article>
    <article class="metric">
        <p class="metric-label">Offres enregistrées</p>
        <span><?php echo e(count($data['offers'])); ?></span>
        <p class="metric-caption">Sur la plateforme, tous statuts confondus</p>
    </article>
    <article class="metric">
        <p class="metric-label">Moteur d’analyse</p>
        <span class="metric-state"><?php echo e(($aiHealth['status'] ?? 'offline') === 'ok' ? 'Connecté' : 'Local'); ?></span>
        <p class="metric-caption">Pour rapprocher les profils et les offres</p>
    </article>
</section>

<section class="panel">
    <div class="section-title">
        <div>
            <p class="eyebrow">Simple, du premier pas au suivi</p>
            <h2>Votre parcours en trois étapes</h2>
            <p class="section-subtitle">Un même espace, adapté à votre rôle.</p>
        </div>
    </div>
    <ol class="timeline welcome-steps">
        <li>
            <p class="hero-label">01 · Votre espace</p>
            <h3>Créez votre compte</h3>
            <p>Choisissez votre rôle, candidat ou recruteur, pour retrouver les outils qui vous concernent.</p>
        </li>
        <li>
            <p class="hero-label">02 · Vos compétences</p>
            <h3>Présentez votre profil ou votre offre</h3>
            <p>Ajoutez votre CV et vos compétences, ou publiez une opportunité en précisant vos besoins.</p>
        </li>
        <li>
            <p class="hero-label">03 · La prochaine étape</p>
            <h3>Faites avancer les candidatures</h3>
            <p>Candidat, envoyez et suivez vos candidatures. Recruteur, examinez les profils et mettez à jour leur suivi.</p>
        </li>
    </ol>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/dashboard/guest.blade.php ENDPATH**/ ?>