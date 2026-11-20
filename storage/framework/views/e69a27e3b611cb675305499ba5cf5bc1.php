<?php $title = 'الباقات | FORMA INTERIORS'; $nav = 'packages'; ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('kaheel.pages.furniture', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('kaheel.partials.catalog-packages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('kaheel.partials.visit-cta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('kaheel.layouts.forma', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\kaheel\resources\views/kaheel/furniture.blade.php ENDPATH**/ ?>