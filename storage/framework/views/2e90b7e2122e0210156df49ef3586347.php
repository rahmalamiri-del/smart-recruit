<?php $__env->startSection('title', 'Gestion des comptes'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $roleLabels = [
        'admin' => 'Administrateur',
        'recruiter' => 'Recruteur',
        'student' => 'Candidat',
    ];
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h1>Comptes</h1>
        <p class="lead">Recruteurs, candidats et administrateurs. Les suppressions sont réversibles.</p>
    </div>
    <a class="button" href="<?php echo e(route('admin.users.create')); ?>">Nouveau compte</a>
</section>

<section class="actions">
    <a class="button <?php echo e($roleFilter === null && ! $trashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('admin.users.index')); ?>">
        Tous (<?php echo e($counts['admin'] + $counts['recruiter'] + $counts['student']); ?>)
    </a>
    <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a class="button <?php echo e($roleFilter === $value && ! $trashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('admin.users.index', ['role' => $value])); ?>">
            <?php echo e($label); ?> (<?php echo e($counts[$value] ?? 0); ?>)
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <a class="button <?php echo e($trashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('admin.users.index', ['trashed' => 1])); ?>">
        Supprimés (<?php echo e($counts['trashed']); ?>)
    </a>
</section>

<section class="table-wrap">
    <table>
        <thead>
            <tr>
                <th scope="col">Compte</th>
                <th scope="col">Rôle</th>
                <th scope="col">Profil</th>
                <th scope="col">Créé le</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e($account['name']); ?></strong>
                        <span><?php echo e($account['email']); ?></span>
                    </td>
                    <td><span class="status-badge status-role-<?php echo e($account['role']); ?>"><?php echo e($roleLabels[$account['role']] ?? $account['role']); ?></span></td>
                    <td>
                        <?php if($account['role'] === 'recruiter'): ?>
                            <?php echo e($account['company_name'] ?: 'Entreprise non renseignée'); ?>

                            <span><?php echo e($account['position'] ?: '—'); ?></span>
                        <?php elseif($account['student_id']): ?>
                            <a href="<?php echo e(route('students.show', $account['student_id'])); ?>">Voir le profil candidat</a>
                        <?php else: ?>
                            <span>—</span>
                        <?php endif; ?>
                    </td>
                    <td class="table-date"><?php echo e(\Illuminate\Support\Str::before($account['created_at'], ' ')); ?></td>
                    <td>
                        <div class="actions">
                            <?php if($account['deleted']): ?>
                                <form class="inline-form" method="post" action="<?php echo e(route('admin.users.restore', $account['id'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="button tiny" type="submit">Restaurer</button>
                                </form>
                            <?php else: ?>
                                <a class="button secondary tiny" href="<?php echo e(route('admin.users.edit', $account['id'])); ?>">Modifier</a>
                                <?php if($account['id'] !== $user['id']): ?>
                                    <a class="button tiny danger" href="<?php echo e(route('admin.users.confirm-delete', $account['id'])); ?>">Supprimer</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5"><span>Aucun compte ne correspond à ce filtre.</span></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\smart-recruit\resources\views/admin/users/index.blade.php ENDPATH**/ ?>