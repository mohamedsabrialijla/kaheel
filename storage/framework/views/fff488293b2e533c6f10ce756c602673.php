<?php
    $home = $home ?? url(app()->getLocale());
    $packages = $packages ?? collect();
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $canSeePackages = kaheel_customer() || auth('admin')->check();
?>
<?php if($canSeePackages && $packages->isNotEmpty()): ?>
<section class="mx-auto max-w-6xl px-5 py-16 md:py-20" id="kaheel-packages">
    <div class="reveal text-center">
        <p class="mb-3 text-[0.65rem] font-semibold tracking-[0.32em] text-primary">PACKAGES</p>
        <h2 class="font-display text-4xl md:text-5xl" data-ar="باقات التأثيث" data-en="Furnishing packages">باقات التأثيث</h2>
        <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-muted-foreground" data-ar="نفس الباقات المعتمدة في اللوحة — اختر باقتك ثم اطلب." data-en="The same packages from the dashboard — pick one, then order.">نفس الباقات المعتمدة في اللوحة — اختر باقتك ثم اطلب.</p>
    </div>
    <div class="mt-10 grid gap-6 md:grid-cols-2">
        <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $cover = $package->images->first();
                $coverUrl = $cover ? $cover->publicUrl() : asset('kaheel/forma/living-room-open.png');
                $from = $package->tiers->min('price_incl_vat');
            ?>
            <article class="reveal flex flex-col overflow-hidden border border-border bg-card">
                <a class="aspect-[16/10] overflow-hidden" href="<?php echo e(url(app()->getLocale().'/packages/'.$package->code)); ?>">
                    <img src="<?php echo e($coverUrl); ?>" alt="<?php echo e($package->name_ar); ?>" class="h-full w-full object-cover transition duration-700 hover:scale-105">
                </a>
                <div class="flex flex-1 flex-col p-7">
                    <p class="text-[0.65rem] font-semibold tracking-[0.28em] text-primary"><?php echo e($package->code); ?></p>
                    <h3 class="mt-2 font-display text-3xl" data-ar="<?php echo e($package->name_ar); ?>" data-en="<?php echo e($package->name_en); ?>"><?php echo e($package->name_ar); ?></h3>
                    <?php if($package->tagline_ar || $package->tagline_en): ?>
                        <p class="mt-3 text-sm leading-7 text-muted-foreground" data-ar="<?php echo e($package->tagline_ar); ?>" data-en="<?php echo e($package->tagline_en); ?>"><?php echo e($package->tagline_ar); ?></p>
                    <?php endif; ?>
                    <?php if($from !== null): ?>
                        <p class="mt-5 text-sm text-muted-foreground" data-ar="يبدأ من" data-en="From">يبدأ من</p>
                        <p class="font-display text-3xl text-primary"><?php echo e($money($from)); ?> <span class="text-base">SAR</span></p>
                    <?php endif; ?>
                    <div class="mt-auto flex flex-wrap gap-3 pt-6">
                        <a class="inline-flex h-11 items-center bg-primary px-6 text-sm text-primary-foreground" href="<?php echo e(url(app()->getLocale().'/packages/'.$package->code)); ?>" data-ar="تفاصيل الباقة" data-en="Package details">تفاصيل الباقة</a>
                        <a class="inline-flex h-11 items-center border border-border px-6 text-sm" href="<?php echo e(kaheel_order_start_url($package->code)); ?>" data-ar="اطلب الآن" data-en="Order now">اطلب الآن</a>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/catalog-packages.blade.php ENDPATH**/ ?>