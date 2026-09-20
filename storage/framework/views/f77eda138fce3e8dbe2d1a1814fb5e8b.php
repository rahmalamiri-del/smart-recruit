<?php $__env->startSection('title', 'Mon espace candidat'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $applicationStatusLabels = config('smart_recruit.application_status_labels');
?>
<section class="page-head dashboard-hero">
    <div class="hero-content">
        <p class="hero-label">Espace candidat</p>
        <h1>Faites avancer votre candidature.</h1>
        <p class="lead">Présentez vos compétences, explorez les offres et suivez chaque candidature.</p>
        <div class="actions">
            <a class="button" href="<?php echo e(route('offers.index')); ?>">Découvrir les offres</a>
            <?php if($profile): ?>
                <a class="button secondary" href="<?php echo e(route('students.edit', $profile['id'])); ?>">Compléter mon profil</a>
            <?php endif; ?>
        </div>
    </div>
    <aside class="hero-aside">
        <p class="hero-label">Votre prochaine étape</p>
        <h2>Un profil qui vous ressemble.</h2>
        <p>Gardez votre CV et vos compétences à jour pour aider les recruteurs à découvrir votre parcours.</p>
    </aside>
</section>

<?php if($profile): ?>
    <section class="split">
        <article class="panel">
            <p class="eyebrow">Mon profil</p>
            <h2><?php echo e($profile['name']); ?></h2>
            <p class="lead"><?php echo e($profile['headline']); ?></p>
            <p class="section-subtitle">Mes compétences</p>
            <div class="chips">
                <?php $__empty_1 = true; $__currentLoopData = $profile['skills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <span><?php echo e($skill); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="muted">Ajoutez vos compétences pour enrichir votre profil.</p>
                <?php endif; ?>
            </div>
            <a class="button secondary" href="<?php echo e(route('students.edit', $profile['id'])); ?>">Modifier mon profil</a>
        </article>

        <aside class="panel">
            <div class="section-title">
                <div>
                    <h2>Mes candidatures</h2>
                    <p class="section-subtitle">Retrouvez les offres auxquelles vous avez postulé et leur état d’avancement.</p>
                </div>
            </div>
            <?php if(count($myApplications)): ?>
                <div class="stack">
                    <?php $__currentLoopData = $myApplications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="item">
                            <div class="item-main">
                                <p class="muted"><?php echo e($row['offer']['company']); ?></p>
                                <h3><a href="<?php echo e(route('offers.show', $row['offer']['id'])); ?>"><?php echo e($row['offer']['title']); ?></a></h3>
                            </div>
                            <span class="status-badge status-<?php echo e($row['application']['status']); ?>"><?php echo e($applicationStatusLabels[$row['application']['status']] ?? $row['application']['status']); ?></span>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Votre prochaine opportunité vous attend.</h3>
                    <p class="muted">Vous n’avez pas encore envoyé de candidature. Explorez les offres pour trouver celle qui vous intéresse.</p>
                    <a class="button" href="<?php echo e(route('offers.index')); ?>">Parcourir les offres</a>
                </div>
            <?php endif; ?>
        </aside>
    </section>
<?php else: ?>
    <section class="panel empty-state">
        <h2>Votre profil n’est pas encore disponible.</h2>
        <p class="muted">Aucun profil candidat n’est associé à ce compte pour le moment. Vous pouvez déjà consulter les offres.</p>
        <a class="button" href="<?php echo e(route('offers.index')); ?>">Découvrir les offres</a>
    </section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\smart-recruit\resources\views/dashboard/student.blade.php ENDPATH**/ ?>