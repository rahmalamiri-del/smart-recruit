

<?php $__env->startSection('title', 'Gestion des candidatures'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusLabels = config('smart_recruit.application_status_labels');
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h1>Candidatures</h1>
        <p class="lead">Toutes les candidatures de la plateforme, tous recruteurs confondus.</p>
    </div>
</section>

<section class="actions">
    <a class="button <?php echo e($statusFilter === null && ! $trashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('admin.applications.index')); ?>">
        Toutes (<?php echo e($counts['active']); ?>)
    </a>
    <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a class="button <?php echo e($statusFilter === $value && ! $trashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('admin.applications.index', ['status' => $value])); ?>">
            <?php echo e($label); ?> (<?php echo e($counts[$value] ?? 0); ?>)
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <a class="button <?php echo e($trashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('admin.applications.index', ['trashed' => 1])); ?>">
        Supprimées (<?php echo e($counts['trashed']); ?>)
    </a>
</section>

<section class="table-wrap">
    <table>
        <thead>
            <tr>
                <th scope="col">Candidat</th>
                <th scope="col">Offre</th>
                <th scope="col">Statut</th>
                <th scope="col">Score</th>
                <th scope="col">Date</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e($application['student_name']); ?></strong>
                        <span><?php echo e($application['student_headline']); ?></span>
                    </td>
                    <td>
                        <?php if($application['deleted']): ?>
                            <?php echo e($application['offer_title']); ?>

                        <?php else: ?>
                            <a href="<?php echo e(route('offers.show', $application['offer_id'])); ?>"><?php echo e($application['offer_title']); ?></a>
                        <?php endif; ?>
                        <span><?php echo e($application['offer_company']); ?></span>
                    </td>
                    <td><span class="status-badge status-<?php echo e($application['status']); ?>"><?php echo e($statusLabels[$application['status']] ?? $application['status']); ?></span></td>
                    <td><?php echo e($application['match_score'] !== null ? $application['match_score'].'%' : '—'); ?></td>
                    <td class="table-date"><?php echo e(\Illuminate\Support\Str::before($application['applied_at'], ' ')); ?></td>
                    <td>
                        <div class="actions">
                            <?php if($application['deleted']): ?>
                                <form class="inline-form" method="post" action="<?php echo e(route('admin.applications.restore', $application['id'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="button tiny" type="submit">Restaurer</button>
                                </form>
                            <?php else: ?>
                                <form class="inline-form" method="post" action="<?php echo e(route('applications.status', $application['id'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="interview">
                                    <button class="button tiny" type="submit">Accepter</button>
                                </form>
                                <form class="inline-form" method="post" action="<?php echo e(route('applications.status', $application['id'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="button tiny danger" type="submit">Refuser</button>
                                </form>
                                <form class="inline-form" method="post" action="<?php echo e(route('admin.applications.destroy', $application['id'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="button secondary tiny" type="submit">Supprimer</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6"><span>Aucune candidature ne correspond à ce filtre.</span></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/admin/applications.blade.php ENDPATH**/ ?>