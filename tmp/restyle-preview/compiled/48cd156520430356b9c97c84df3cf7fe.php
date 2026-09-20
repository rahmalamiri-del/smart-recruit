<main class="page" id="main-content" tabindex="-1">
    <?php if(session('success')): ?>
        <div class="alert success" role="status"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="alert warning" role="status"><?php echo e(session('warning')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert danger" role="alert"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert danger" role="alert">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div><?php echo e($error); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</main>
<?php /**PATH H:\smart-recruit\resources\views/partials/main.blade.php ENDPATH**/ ?>