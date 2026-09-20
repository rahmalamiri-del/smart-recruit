<?php $__env->startSection('title', 'Tableau de bord administrateur'); ?>

<?php $__env->startSection('content'); ?>
<section class="page-head dashboard-hero">
    <div class="hero-content">
        <p class="hero-label">Espace administrateur</p>
        <h1>Le recrutement, en un coup d’œil.</h1>
        <p class="lead">Suivez les offres, les profils et les candidatures depuis un même espace.</p>
        <div class="actions">
            <a class="button" href="<?php echo e(route('offers.create')); ?>">Créer une offre</a>
            <a class="button secondary" href="<?php echo e(route('students.create')); ?>">Ajouter un profil</a>
            <a class="button secondary" href="<?php echo e(route('admin.index')); ?>">Gérer la plateforme</a>
        </div>
    </div>
    <aside class="hero-aside">
        <p class="hero-label">Votre vue d’ensemble</p>
        <h2>Des profils aux opportunités.</h2>
        <p>Explorez les correspondances et retrouvez les candidatures associées à chaque offre.</p>
    </aside>
</section>

<section class="stats-grid" aria-label="Vue d’ensemble de la plateforme">
    <article class="metric">
        <p class="metric-label">Profils candidats</p>
        <span><?php echo e(count($data['students'])); ?></span>
        <p class="metric-caption">Dans votre vivier de talents</p>
    </article>
    <article class="metric">
        <p class="metric-label">Offres enregistrées</p>
        <span><?php echo e(count($data['offers'])); ?></span>
        <p class="metric-caption">Tous statuts confondus</p>
    </article>
    <article class="metric">
        <p class="metric-label">Candidatures</p>
        <span><?php echo e(count($data['applications'])); ?></span>
        <p class="metric-caption">Sur l’ensemble des offres</p>
    </article>
    <article class="metric">
        <p class="metric-label">Moteur d’analyse</p>
        <span class="metric-state"><?php echo e(($aiHealth['status'] ?? 'offline') === 'ok' ? 'Connecté' : 'Local'); ?></span>
        <p class="metric-caption"><?php echo e(($aiHealth['status'] ?? 'offline') === 'ok' ? 'Service d’analyse disponible' : 'Analyse locale disponible'); ?></p>
    </article>
</section>

<section>
    <div class="section-title">
        <div>
            <h2>Les profils, offre par offre</h2>
            <p class="section-subtitle">Retrouvez le profil le mieux classé pour chaque opportunité.</p>
        </div>
        <a href="<?php echo e(route('offers.index')); ?>">Voir toutes les offres</a>
    </div>
    <div class="stack">
        <?php $__empty_1 = true; $__currentLoopData = $offerSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="item">
                <div class="item-main">
                    <p class="muted"><?php echo e($summary['offer']['company']); ?> · <?php echo e($summary['applications']); ?> <?php echo e($summary['applications'] > 1 ? 'candidatures' : 'candidature'); ?></p>
                    <h3><a href="<?php echo e(route('offers.show', $summary['offer']['id'])); ?>"><?php echo e($summary['offer']['title']); ?></a></h3>
                    <?php if($summary['top']): ?>
                        <p>Profil le mieux classé : <strong><?php echo e($summary['top']['student']['name']); ?></strong></p>
                    <?php else: ?>
                        <p class="muted">Aucun profil à comparer pour le moment.</p>
                    <?php endif; ?>
                </div>
                <?php if($summary['top']): ?>
                    <div class="score-ring" aria-label="Score de compatibilité : <?php echo e($summary['top']['match']['score']); ?> %"><?php echo e($summary['top']['match']['score']); ?>%</div>
                <?php endif; ?>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="panel empty-state">
                <h3>Tout commence par une offre.</h3>
                <p class="muted">Créez une première opportunité pour découvrir les profils qui lui correspondent.</p>
                <a class="button" href="<?php echo e(route('offers.create')); ?>">Créer une offre</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>