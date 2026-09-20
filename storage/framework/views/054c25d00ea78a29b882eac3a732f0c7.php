<?php $__env->startSection('title', $student['name']); ?>

<?php $__env->startSection('content'); ?>
<?php
    $role = $user['role'] ?? 'guest';
    $isOwnProfile = $role === 'student' && $student['id'] === ($user['student_id'] ?? null);
    // Le CV se gère par son propriétaire ou par l'administrateur ; le recruteur
    // ne fait que le consulter.
    $canManageCv = $role === 'admin' || $isOwnProfile;
?>
<section class="page-head profile-header">
    <div class="profile-identity">
        <span class="profile-avatar" aria-hidden="true"><?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($student['name'], 0, 1))); ?></span>
        <div>
        <p class="eyebrow"><?php echo e($student['location']); ?> · <?php echo e($student['experience_years']); ?> an(s)</p>
        <h1><?php echo e($student['name']); ?></h1>
        <p class="lead"><?php echo e($student['headline']); ?></p>
        </div>
    </div>
    <div class="actions">
        <?php if(in_array($user['role'] ?? 'guest', ['admin', 'recruiter'])): ?>
            <a class="button secondary" href="<?php echo e(route('students.index')); ?>">Tous les profils</a>
        <?php endif; ?>
        <?php if((($user['role'] ?? 'guest') === 'admin') || (($user['role'] ?? 'guest') === 'student' && $student['id'] === ($user['student_id'] ?? null))): ?>
            <a class="button secondary" href="<?php echo e(route('students.edit', $student['id'])); ?>">Modifier</a>
        <?php endif; ?>
        <?php if(($user['role'] ?? 'guest') === 'admin'): ?>
            <a class="button danger" href="<?php echo e(route('students.confirm-delete', $student['id'])); ?>">Supprimer</a>
        <?php endif; ?>
    </div>
</section>

<section class="split">
    <article class="panel">
        <h2>Parcours et compétences</h2>
        <dl class="definition">
            <dt>E-mail</dt>
            <dd><?php echo e($student['email']); ?></dd>
            <dt>Formation</dt>
            <dd><?php echo e($student['education']); ?></dd>
            <dt>Fichier CV</dt>
            <dd>
                <?php if($student['file_name']): ?>
                    <a href="<?php echo e(route('students.cv', $student['id'])); ?>" target="_blank" rel="noopener"><?php echo e($student['file_name']); ?></a>
                <?php else: ?>
                    Non chargé
                <?php endif; ?>
            </dd>
        </dl>

        <?php if($student['file_name']): ?>
            <div class="actions">
                <a class="button secondary tiny" href="<?php echo e(route('students.cv', $student['id'])); ?>" target="_blank" rel="noopener">Ouvrir le CV</a>
                <?php if($canManageCv): ?>
                    <a class="button secondary tiny" href="<?php echo e(route('students.edit', $student['id'])); ?>">Remplacer</a>
                    <form class="inline-form" method="post" action="<?php echo e(route('students.cv.destroy', $student['id'])); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="button tiny danger" type="submit">Retirer le CV</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="chips">
            <?php $__currentLoopData = $student['skills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span><?php echo e($skill); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </article>

    <article class="panel">
        <h2>Coordonnées du CV</h2>
        <dl class="definition">
            <dt>E-mails détectés</dt>
            <dd><?php echo e(implode(', ', $student['metadata']['emails'] ?? []) ?: 'Aucun'); ?></dd>
            <dt>Téléphones détectés</dt>
            <dd><?php echo e(implode(', ', $student['metadata']['phones'] ?? []) ?: 'Aucun'); ?></dd>
        </dl>
    </article>
</section>

<section class="panel full">
    <h2>Contenu du CV</h2>
    <p class="cv-text"><?php echo e($student['cv_text'] ?: 'Aucun texte CV disponible.'); ?></p>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\smart-recruit\resources\views/students/show.blade.php ENDPATH**/ ?>