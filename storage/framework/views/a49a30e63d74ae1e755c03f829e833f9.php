<?php $__env->startSection('title', 'Offres de stage'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $role = $user['role'] ?? 'guest';
    $isStudent = $role === 'student';
    $isRecruiter = $role === 'recruiter';
    $isAdmin = $role === 'admin';
    $myApplicationsByOffer = $isStudent
        ? collect($applications)->where('student_id', $user['student_id'] ?? null)->keyBy('offer_id')
        : collect();
    $statusLabels = config('smart_recruit.application_status_labels');
    $offerStatusLabels = config('smart_recruit.offer_status_labels');
?>
<section class="page-head">
    <div>
        <p class="eyebrow"><?php echo e($isRecruiter ? 'Mes offres' : ($isStudent ? 'Pour vous' : 'Toutes les offres')); ?></p>
        <h1>Offres de stage</h1>
        <?php if($isStudent): ?>
            <p class="lead">Triées par pertinence avec votre profil et vos compétences.</p>
        <?php elseif($isRecruiter || $isAdmin): ?>
            <p class="lead">Triées par score du meilleur candidat.</p>
        <?php endif; ?>
    </div>
    <?php if(in_array($role, ['admin', 'recruiter'])): ?>
        <a class="button" href="<?php echo e(route('offers.create')); ?>">Créer une offre</a>
    <?php endif; ?>
</section>

<?php if(($isRecruiter || $isAdmin) && ! $showTrashed): ?>
    <?php
        // Chaque filtre preserve les autres dans l'URL.
        $base = array_filter([
            'with_applications' => $withApplicationsOnly ? 1 : null,
            'owner' => $ownerFilter,
        ]);
    ?>
    <section class="actions">
        <a class="button <?php echo e($statusFilter === null ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('offers.index', $base)); ?>">
            Toutes (<?php echo e(array_sum($statusCounts)); ?>)
        </a>
        <?php $__currentLoopData = $offerStatusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a class="button <?php echo e($statusFilter === $value ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('offers.index', $base + ['status' => $value])); ?>">
                <?php echo e($label); ?> (<?php echo e($statusCounts[$value] ?? 0); ?>)
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <a class="button <?php echo e($withApplicationsOnly ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('offers.index', array_filter(['status' => $statusFilter, 'owner' => $ownerFilter, 'with_applications' => $withApplicationsOnly ? null : 1]))); ?>">
            Avec candidatures (<?php echo e($withApplicationsCount); ?>)
        </a>
    </section>

    <?php if($isAdmin && count($offerOwners)): ?>
        <section class="actions">
            <a class="button <?php echo e($ownerFilter === null ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('offers.index', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? 1 : null]))); ?>">
                Tous les recruteurs
            </a>
            <?php $__currentLoopData = $offerOwners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(($ownerCounts[$owner['id']] ?? 0) > 0): ?>
                    <a class="button <?php echo e($ownerFilter === $owner['id'] ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('offers.index', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? 1 : null, 'owner' => $owner['id']]))); ?>">
                        <?php echo e($owner['name']); ?> (<?php echo e($ownerCounts[$owner['id']]); ?>)
                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(($ownerCounts[''] ?? 0) > 0): ?>
                <a class="button <?php echo e($ownerFilter === 'none' ? '' : 'secondary'); ?> tiny danger" href="<?php echo e(route('offers.index', array_filter(['status' => $statusFilter, 'with_applications' => $withApplicationsOnly ? 1 : null, 'owner' => 'none']))); ?>">
                    Sans propriétaire (<?php echo e($ownerCounts['']); ?>)
                </a>
            <?php endif; ?>
        </section>
    <?php endif; ?>
<?php endif; ?>

<?php if($isAdmin): ?>
    <section class="actions">
        <a class="button <?php echo e($showTrashed ? 'secondary' : ''); ?> tiny" href="<?php echo e(route('offers.index')); ?>">Actives</a>
        <a class="button <?php echo e($showTrashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('offers.index', ['trashed' => 1])); ?>">
            Supprimées (<?php echo e($trashedCount); ?>)
        </a>
    </section>
<?php endif; ?>

<section class="stack">
    <?php $__empty_1 = true; $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $count = collect($applications)->where('offer_id', $offer['id'])->count();
            $score = $matchScores[$offer['id']] ?? null;
            $myApplication = $myApplicationsByOffer->get($offer['id']);
        ?>
        <article class="item offer-card">
            <span class="company-mark" aria-hidden="true"><?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($offer['company'], 0, 1))); ?></span>
            <div class="item-main">
                <p class="offer-meta">
                    <?php echo e($offer['company']); ?> · <?php echo e($offer['location']); ?> · <?php echo e($count); ?> candidature(s)
                    <?php if($isRecruiter || $isAdmin): ?>
                        · <span class="status-badge status-<?php echo e($offer['status'] ?? 'published'); ?>"><?php echo e($offerStatusLabels[$offer['status'] ?? 'published'] ?? $offer['status']); ?></span>
                    <?php endif; ?>
                    <?php if($isAdmin): ?>
                        <?php $owner = collect($offerOwners)->firstWhere('id', $offer['owner_id'] ?? null); ?>
                        · <?php echo e($owner ? $owner['name'] : 'Sans propriétaire'); ?>

                    <?php endif; ?>
                </p>
                <h2><a href="<?php echo e(route('offers.show', $offer['id'])); ?>"><?php echo e($offer['title']); ?></a></h2>
                <div class="chips">
                    <?php $__currentLoopData = $offer['required_skills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span><?php echo e($skill); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php if($score !== null): ?>
                <div class="score-ring" aria-label="Score de compatibilité : <?php echo e($score); ?> %"><?php echo e($score); ?>%</div>
            <?php endif; ?>
            <div class="offer-actions">
            <?php if($showTrashed ?? false): ?>
                <form class="inline-form" method="post" action="<?php echo e(route('offers.restore', $offer['id'])); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="button tiny" type="submit">Restaurer</button>
                </form>
            <?php elseif($myApplication): ?>
                <span class="status-badge status-<?php echo e($myApplication['status']); ?>"><?php echo e($statusLabels[$myApplication['status']] ?? $myApplication['status']); ?></span>
            <?php elseif($isAdmin): ?>
                <div class="actions">
                    <a class="button secondary tiny" href="<?php echo e(route('offers.show', $offer['id'])); ?>">Voir les profils</a>
                    <a class="button secondary tiny" href="<?php echo e(route('offers.edit', $offer['id'])); ?>">Modifier</a>
                    <a class="button tiny danger" href="<?php echo e(route('offers.confirm-delete', $offer['id'])); ?>">Supprimer</a>
                </div>
            <?php else: ?>
                <a class="button secondary" href="<?php echo e(route('offers.show', $offer['id'])); ?>"><?php echo e($isStudent ? 'Découvrir l’offre' : 'Voir les profils'); ?></a>
            <?php endif; ?>
            </div>
        </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="empty-state"><h2><?php echo e(($showTrashed ?? false) ? 'Aucune offre supprimée' : 'Aucune offre à afficher'); ?></h2><p>Les offres correspondant à votre sélection apparaîtront ici.</p></div>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/offers/index.blade.php ENDPATH**/ ?>