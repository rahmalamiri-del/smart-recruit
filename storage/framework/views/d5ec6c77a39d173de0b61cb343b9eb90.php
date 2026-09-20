<?php $__env->startSection('title', $offer['title']); ?>

<?php $__env->startSection('content'); ?>
<?php
    $role = $user['role'] ?? 'guest';
    $isManager = in_array($role, ['admin', 'recruiter'], true);
    $applicationsByStudent = collect($applications)->keyBy('student_id');
    $myApplication = $role === 'student' ? $applicationsByStudent->get($user['student_id'] ?? null) : null;
    $statusLabels = config('smart_recruit.application_status_labels');
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Offre de stage</p>
        <h1><?php echo e($offer['title']); ?></h1>
        <p class="lead"><?php echo e($offer['company']); ?> · <?php echo e($offer['location']); ?> · <?php echo e($offer['type']); ?></p>
    </div>
    <div class="actions">
        <?php if($isManager): ?>
            <a class="button secondary" href="<?php echo e(route('offers.edit', $offer['id'])); ?>">Modifier l'offre</a>
        <?php endif; ?>
        <?php if($role === 'admin'): ?>
            <a class="button danger" href="<?php echo e(route('offers.confirm-delete', $offer['id'])); ?>">Supprimer</a>
        <?php endif; ?>
        <?php if($role === 'student' && ! $myApplication): ?>
            <form method="post" action="<?php echo e(route('offers.apply', $offer['id'])); ?>">
                <?php echo csrf_field(); ?>
                <button class="button" type="submit">Postuler en un clic</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<section class="panel offer-overview">
    <p class="eyebrow">L’opportunité</p>
    <h2>À propos de l’offre</h2>
    <p class="offer-description"><?php echo e($offer['description']); ?></p>
    <?php if(count($offer['required_skills'])): ?>
        <h3>Compétences recherchées</h3>
        <div class="chips">
            <?php $__currentLoopData = $offer['required_skills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span><?php echo e($skill); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</section>

<?php if($role === 'admin' && count($offerOwners)): ?>
    <section class="panel">
        <h2>Propriétaire de l'offre</h2>
        <p class="muted">Réassigner cette offre à un autre recruteur (utile si son propriétaire a été supprimé).</p>
        <form class="form-card compact-form" method="post" action="<?php echo e(route('offers.reassign', $offer['id'])); ?>">
            <?php echo csrf_field(); ?>
            <label>
                Recruteur
                <select name="owner_id" required>
                    <?php $__currentLoopData = $offerOwners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($owner['id']); ?>" <?php if(($offer['owner_id'] ?? null) === $owner['id']): echo 'selected'; endif; ?>>
                            <?php echo e($owner['name']); ?><?php echo e($owner['company_name'] ? ' — '.$owner['company_name'] : ''); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
            <button class="button secondary" type="submit">Réassigner</button>
        </form>
    </section>
<?php endif; ?>

<?php if($role === 'student'): ?>
    <?php if($myApplication): ?>
        <section class="panel">
            <h2>Ma candidature</h2>
            <span class="status-badge status-<?php echo e($myApplication['status']); ?>"><?php echo e($statusLabels[$myApplication['status']] ?? $myApplication['status']); ?></span>
        </section>
    <?php else: ?>
        <section class="panel">
            <h2>Postuler en un clic</h2>
            <p class="muted">
                Votre candidature utilise automatiquement le CV et les compétences de votre profil.
                Cliquez sur « Postuler en un clic » ci-dessus pour envoyer votre candidature.
            </p>
            <?php if($user['student_id'] ?? null): ?>
                <a class="button secondary" href="<?php echo e(route('students.edit', $user['student_id'])); ?>">Mettre à jour mon CV avant de postuler</a>
            <?php endif; ?>
        </section>
    <?php endif; ?>
<?php endif; ?>

<?php if($isManager): ?>
<section>
    <div class="section-title">
        <h2>Classement automatique</h2>
        <span><?php echo e(count($applications)); ?> candidature(s) enregistrée(s)</span>
    </div>

    <div class="ranking">
        <?php $__empty_1 = true; $__currentLoopData = $rankings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $application = $applicationsByStudent->get($row['student']['id']);
                $status = $application['status'] ?? null;
            ?>

            <article class="candidate">
                <div class="rank">#<?php echo e($index + 1); ?></div>
                <div class="candidate-main">
                    <div class="candidate-head">
                        <div>
                            <h3><a href="<?php echo e(route('students.show', $row['student']['id'])); ?>"><?php echo e($row['student']['name']); ?></a></h3>
                            <p><?php echo e($row['student']['headline']); ?></p>
                        </div>
                        <strong><?php echo e($row['match']['score']); ?>%</strong>
                    </div>
                    <div class="bar"><span style="width: <?php echo e($row['match']['score']); ?>%"></span></div>
                    <p class="muted"><?php echo e($row['match']['explanation']); ?></p>
                    <div class="match-meta">
                        <span>Similarité du texte : <?php echo e($row['match']['text_similarity']); ?>%</span>
                        <span>Correspondance des compétences : <?php echo e($row['match']['semantic_coverage']); ?>%</span>
                    </div>
                    <div class="chips matched">
                        <?php $__currentLoopData = $row['match']['matched_skills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span><?php echo e($skill); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if(count($row['match']['missing_skills'])): ?>
                        <p class="missing">Compétences à vérifier : <?php echo e(implode(', ', $row['match']['missing_skills'])); ?></p>
                    <?php endif; ?>

                    <?php if($application): ?>
                        <div class="decision-row">
                            <span class="status-badge status-<?php echo e($status); ?>"><?php echo e($statusLabels[$status] ?? $status); ?></span>
                            <?php if($application['match_score'] !== null): ?>
                                <span class="decision-score">Score enregistré : <?php echo e($application['match_score']); ?>%</span>
                            <?php endif; ?>
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
                        </div>
                    <?php else: ?>
                        <p class="muted small-note">Ce profil n’a pas encore candidaté à cette offre.</p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state"><h3>Aucun profil à comparer</h3><p>Le classement apparaîtra lorsque des profils seront disponibles.</p></div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\smart-recruit\resources\views/offers/show.blade.php ENDPATH**/ ?>