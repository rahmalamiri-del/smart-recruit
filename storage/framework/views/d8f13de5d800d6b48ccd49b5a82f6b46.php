<?php $__env->startSection('title', 'Profils candidats'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $role = $user['role'] ?? 'guest';
    $isRecruiter = $role === 'recruiter';
    $isAdmin = $role === 'admin';
    $highMatchThreshold = 70;
?>
<section class="page-head">
    <div>
        <p class="eyebrow"><?php echo e($isRecruiter ? 'Compatibilité avec vos offres' : 'Votre vivier de talents'); ?></p>
        <h1>Profils candidats</h1>
        <?php if($isRecruiter): ?>
            <p class="lead">Triés par compatibilité avec vos offres — les profils les plus proches en premier.</p>
        <?php endif; ?>
    </div>
    <?php if($role === 'admin'): ?>
        <a class="button" href="<?php echo e(route('students.create')); ?>">Ajouter un CV</a>
    <?php endif; ?>
</section>

<?php if($isAdmin): ?>
    <section class="actions">
        <a class="button <?php echo e($showTrashed ? 'secondary' : ''); ?> tiny" href="<?php echo e(route('students.index')); ?>">Actifs</a>
        <a class="button <?php echo e($showTrashed ? '' : 'secondary'); ?> tiny" href="<?php echo e(route('students.index', ['trashed' => 1])); ?>">
            Supprimés (<?php echo e($trashedCount); ?>)
        </a>
    </section>
<?php endif; ?>

<section class="table-wrap">
    <table>
        <thead>
            <tr>
                <th scope="col">Candidat</th>
                <th scope="col">Formation</th>
                <th scope="col">Compétences détectées</th>
                <th scope="col">Exp.</th>
                <?php if($isRecruiter): ?>
                    <th scope="col">Compatibilité</th>
                <?php endif; ?>
                <th scope="col">CV</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $best = $bestMatches[$student['id']] ?? null;
                    $bestScore = $best['match']['score'] ?? null;
                    $isHighMatch = $bestScore !== null && $bestScore >= $highMatchThreshold;
                ?>
                <tr class="<?php echo e($isHighMatch ? 'high-match' : ''); ?>">
                    <td>
                        <strong><?php echo e($student['name']); ?></strong>
                        <span><?php echo e($student['headline']); ?></span>
                    </td>
                    <td><?php echo e($student['education']); ?></td>
                    <td>
                        <div class="chips">
                            <?php $__currentLoopData = array_slice($student['skills'], 0, 6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span><?php echo e($skill); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>
                    <td><?php echo e($student['experience_years']); ?> an(s)</td>
                    <?php if($isRecruiter): ?>
                        <td>
                            <?php if($bestScore !== null): ?>
                                <span class="match-chip <?php echo e($isHighMatch ? 'high' : ''); ?>"><?php echo e($bestScore); ?>%</span>
                                <span>pour « <?php echo e($best['offer']['title']); ?> »</span>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if($student['file_name']): ?>
                            <a href="<?php echo e(route('students.cv', $student['id'])); ?>" target="_blank" rel="noopener">Ouvrir</a>
                        <?php else: ?>
                            <span>—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($showTrashed ?? false): ?>
                            <form class="inline-form" method="post" action="<?php echo e(route('students.restore', $student['id'])); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="button tiny" type="submit">Restaurer</button>
                            </form>
                        <?php else: ?>
                            <a class="button secondary tiny" href="<?php echo e(route('students.show', $student['id'])); ?>">Voir le profil</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="<?php echo e($isRecruiter ? 7 : 6); ?>"><div class="empty-state">Aucun profil à afficher pour cette sélection.</div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/students/index.blade.php ENDPATH**/ ?>