<?php
    $step = $order->progressStep();
    $percent = $order->progressPercent();
?>
<div class="order-progress">
    <div class="order-progress-top">
        <span data-ar="التقدم" data-en="Progress">التقدم</span>
        <strong><?php echo e($percent); ?>%</strong>
    </div>
    <div class="order-track">
        <div class="order-track-fill" style="width: <?php echo e($percent); ?>%"></div>
        <div class="order-track-dots">
            <?php for($i = 0; $i < 8; $i++): ?>
                <span class="order-dot <?php echo e($i < $step ? 'is-on' : ''); ?>"></span>
            <?php endfor; ?>
        </div>
    </div>
</div>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/order-progress.blade.php ENDPATH**/ ?>