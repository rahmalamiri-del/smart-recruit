<?php $__env->startSection('title', $title); ?>

<?php $__env->startSection('content'); ?>
<section class="form-shell">
    <div class="section-title">
        <h1><?php echo e($title); ?></h1>
        <a href="<?php echo e($cancel); ?>">Annuler</a>
    </div>

    <div class="panel">
        <h2><?php echo e($entity); ?></h2>

        <p class="lead">Cet élément sera retiré des listes actives. Vous pourrez le retrouver dans les éléments supprimés et le restaurer.</p>

        <?php if(count($impact)): ?>
            <p class="muted">Seront également masqués :</p>
            <ul class="impact-list">
                <?php $__currentLoopData = $impact; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($line); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="<?php echo e($action); ?>">
            <?php echo csrf_field(); ?>
            <div class="actions">
                <button class="button danger" type="submit">Confirmer la suppression</button>
                <a class="button secondary" href="<?php echo e($cancel); ?>">Annuler</a>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/admin/confirm.blade.php ENDPATH**/ ?>