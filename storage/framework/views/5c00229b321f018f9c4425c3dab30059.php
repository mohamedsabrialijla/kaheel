<?php echo $__env->make('kaheel.partials.native-validation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<link href="<?php echo e(asset('kaheel/forma.css')); ?>?v=2" rel="stylesheet">
<style>
.reveal{opacity:1;transform:none}
nav.design-steps{display:flex!important;flex-direction:column!important;gap:.5rem;margin-bottom:1.5rem}
@media (min-width:768px){
nav.design-steps{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;flex-direction:unset}
}
button.design-step{display:flex!important;align-items:flex-start!important;flex-wrap:wrap;width:100%;min-width:0;min-height:3.5rem;height:auto!important;white-space:normal!important;text-align:start;line-height:1.45;padding:.85rem 1rem}
button.design-step>span:last-child{flex:1 1 0;min-width:0;overflow-wrap:anywhere;word-break:break-word;white-space:normal!important}
</style>
<?php echo $__env->make('kaheel.partials.forma-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/public-header.blade.php ENDPATH**/ ?>