<form method="post" action="<?php echo e(route('logout')); ?>">
    <?php echo csrf_field(); ?>
    <button class="icon-button" type="submit" title="Déconnexion" aria-label="Déconnexion">
        
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M14.2 7.8V5.9A1.9 1.9 0 0 0 12.3 4H6.4A1.9 1.9 0 0 0 4.5 5.9v12.2A1.9 1.9 0 0 0 6.4 20h5.9a1.9 1.9 0 0 0 1.9-1.9v-1.9"
                  stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10.4 12h9.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M16.8 9.3 19.5 12l-2.7 2.7" stroke="currentColor" stroke-width="1.7"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</form>
<?php /**PATH H:\smart-recruit\resources\views/partials/logout.blade.php ENDPATH**/ ?>