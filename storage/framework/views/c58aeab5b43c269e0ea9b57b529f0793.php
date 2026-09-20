

<?php $__env->startSection('title', 'Mon espace recruteur'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $offerStatusLabels = config('smart_recruit.offer_status_labels');
    $withApplicationsParam = $withApplicationsOnly ? 1 : null;
?>
<section class="page-head dashboard-hero">
    <div class="hero-content">
        <p class="hero-label">Espace recruteur</p>
        <h1>Vos offres. Vos prochains talents.</h1>
        <p class="lead">Publiez vos opportunités, comparez les profils et suivez les candidatures reçues.</p>
        <div class="actions">
            <a class="button" href="<?php echo e(route('offers.create')); ?>">Créer une offre</a>
            <a class="button secondary" href="<?php echo e(route('offers.index')); ?>">Gérer mes offres</a>
        </div>
    </div>
    <aside class="hero-aside">
        <p class="hero-label">Pour chaque opportunité</p>
        <h2>Repérez les compétences qui comptent.</h2>
        <p>Retrouvez les profils les mieux classés, puis ouvrez une offre pour examiner les candidatures.</p>
    </aside>
</section>

<section class="stats-grid" aria-label="Vue d’ensemble de votre recrutement">
    <article class="metric">
        <p class="metric-label">Mes offres</p>
        <span><?php echo e($offerCount); ?></span>
        <p class="metric-caption">Tous statuts confondus</p>
    </article>
    <article class="metric">
        <p class="metric-label">Candidatures reçues</p>
        <span><?php echo e($applicationCount); ?></span>
        <p class="metric-caption">Sur l’ensemble de vos offres</p>
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
            <h2>Mes offres en un coup d’œil</h2>
            <p class="section-subtitle">Filtrez vos offres pour retrouver celles qui vous intéressent.</p>
        </div>
        <a href="<?php echo e(route('offers.index')); ?>">Voir toutes mes offres</a>
    </div>

    <div class="actions filter-bar" role="group" aria-label="Filtrer les offres">
        <a class="button <?php echo e($statusFilter === null ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('dashboard', array_filter(['with_applications' => $withApplicationsParam]))); ?>" <?php if($statusFilter === null): ?> aria-current="true" <?php endif; ?>>
            Toutes (<?php echo e(array_sum($statusCounts)); ?>)
        </a>
        <?php $__currentLoopData = $offerStatusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a class="button <?php echo e($statusFilter === $value ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('dashboard', array_filter(['status' => $value, 'with_applications' => $withApplicationsParam]))); ?>" <?php if($statusFilter === $value): ?> aria-current="true" <?php endif; ?>>
                <?php echo e($label); ?> (<?php echo e($statusCounts[$value] ?? 0); ?>)
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <a class="button <?php echo e($withApplicationsOnly ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('dashboard', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? null : 1]))); ?>" <?php if($withApplicationsOnly): ?> aria-current="true" <?php endif; ?>>
            Avec candidatures (<?php echo e($withApplicationsCount); ?>)
        </a>
    </div>

    <?php if(count($offerSummaries)): ?>
        <div class="stack">
            <?php $__currentLoopData = $offerSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="item">
                    <div class="item-main">
                        <p class="muted">
                            <?php echo e($summary['offer']['company']); ?> · <?php echo e($summary['applications']); ?> <?php echo e($summary['applications'] > 1 ? 'candidatures' : 'candidature'); ?>

                            · <span class="status-badge status-<?php echo e($summary['offer']['status'] ?? 'published'); ?>"><?php echo e($offerStatusLabels[$summary['offer']['status'] ?? 'published'] ?? $summary['offer']['status']); ?></span>
                        </p>
                        <h3><a href="<?php echo e(route('offers.show', $summary['offer']['id'])); ?>"><?php echo e($summary['offer']['title']); ?></a></h3>
                        <?php if($summary['top']): ?>
                            <p>Profil le mieux classé : <strong><?php echo e($summary['top']['student']['name']); ?></strong></p>
                        <?php else: ?>
                            <p class="muted">Aucun profil à comparer pour le moment.</p>
                        <?php endif; ?>
                    </div>
                    <div class="actions">
                        <?php if($summary['top']): ?>
                            <div class="score-ring" aria-label="Score de compatibilité : <?php echo e($summary['top']['match']['score']); ?> %"><?php echo e($summary['top']['match']['score']); ?>%</div>
                        <?php endif; ?>
                        <a class="button secondary" href="<?php echo e(route('offers.edit', $summary['offer']['id'])); ?>">Modifier l’offre</a>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php elseif($offerCount > 0): ?>
        <div class="panel empty-state">
            <h3>Aucune offre dans cette sélection.</h3>
            <p class="muted">Essayez un autre filtre pour retrouver vos offres.</p>
            <a class="button secondary" href="<?php echo e(route('dashboard')); ?>">Afficher toutes mes offres</a>
        </div>
    <?php else: ?>
        <div class="panel empty-state">
            <h3>Présentez votre première opportunité.</h3>
            <p class="muted">Décrivez votre besoin et les compétences recherchées pour découvrir les profils correspondants.</p>
            <a class="button" href="<?php echo e(route('offers.create')); ?>">Créer ma première offre</a>
        </div>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\smart-recruit\resources\views/dashboard/recruiter.blade.php ENDPATH**/ ?>