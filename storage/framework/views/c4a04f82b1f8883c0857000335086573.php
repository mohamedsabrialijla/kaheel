<?php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلباتي | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/home.css')); ?>?v=13" rel="stylesheet">
</head>
<body>
    <?php echo $__env->make('kaheel.partials.public-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="order-page">
        <h1 class="auth-title" data-ar="طلباتي" data-en="My orders">طلباتي</h1>
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a class="order-list-card" href="<?php echo e(url(app()->getLocale().'/orders/'.$order->id)); ?>">
                <strong><?php echo e($order->number); ?></strong>
                <span class="order-muted"><?php echo e($order->compound_name); ?> · <?php echo e($order->unit_no); ?> · <span data-ar="<?php echo e($order->package_name_ar); ?>" data-en="<?php echo e($order->package_name_en); ?>"><?php echo e($order->package_name_ar); ?></span></span>
                <span class="order-price"><?php echo e($money($order->price_incl)); ?> <span data-ar="ريال" data-en="SAR">ريال</span></span>
                <span class="order-muted" data-ar="بانتظار العربون" data-en="Awaiting deposit">بانتظار العربون</span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <section class="order-card">
                <p class="order-lead" data-ar="لا توجد طلبات بعد" data-en="No orders yet">لا توجد طلبات بعد</p>
                <a class="btn btn-lg btn-primary" href="<?php echo e(url(app()->getLocale().'/order')); ?>" data-ar="إنشاء طلب" data-en="Create order">إنشاء طلب</a>
            </section>
        <?php endif; ?>
    </main>
    <script>
        const root = document.documentElement;
        document.getElementById("lang-toggle").addEventListener("click", () => {
            const en = root.lang !== "en";
            root.lang = en ? "en" : "ar";
            root.dir = en ? "ltr" : "rtl";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                node.textContent = en ? node.dataset.en : node.dataset.ar;
            });
            document.getElementById("lang-label").textContent = en ? "ع" : "EN";
            localStorage.setItem("forma-lang", en ? "en" : "ar");
        });
        if (localStorage.getItem("forma-lang") === "en") document.getElementById("lang-toggle").click();
    </script>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/orders.blade.php ENDPATH**/ ?>