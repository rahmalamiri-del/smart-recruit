<nav class="<?php echo e($navClass ?? 'sidebar-nav'); ?>" aria-label="Navigation principale">
    <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($item['url']); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => $item['active']]); ?>" <?php if($item['active']): ?> aria-current="page" <?php endif; ?>>
            <?php echo $__env->make('partials.icon', ['name' => $item['icon']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <span><?php echo e($item['label']); ?></span>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH H:\smart-recruit\resources\views/partials/navigation.blade.php ENDPATH**/ ?>