<?php
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $home = url(app()->getLocale());
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($package->name_ar); ?> | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/home.css')); ?>?v=35" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="<?php echo e($home); ?>" aria-label="FORMA BY AZURE — KAHEEL">
                <span class="brand-mark">
                    <span class="brand-forma">FORMA</span>
                    <span class="brand-azure">BY AZURE</span>
                </span>
                <span class="brand-name">KAHEEL</span>
            </a>
            <nav class="nav" aria-label="main">
                <a class="btn btn-sm nav-home" href="<?php echo e($home); ?>" data-ar="الرئيسية" data-en="Home">الرئيسية</a>
                <?php echo $__env->make('kaheel.partials.role-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('kaheel.partials.notice-bell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <button class="btn btn-sm" type="button" id="lang-toggle" aria-label="language">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m5 8 6 6"/>
                        <path d="m4 14 6-6 2-3"/>
                        <path d="M2 5h12"/>
                        <path d="M7 2h1"/>
                        <path d="m22 22-5-10-5 10"/>
                        <path d="M14 18h6"/>
                    </svg>
                    <span id="lang-label">EN</span>
                </button>
                <?php echo $__env->make('kaheel.partials.account-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </nav>
        </div>
    </header>

    <main class="pkg-page">
        <p class="pkg-kicker">FORMA BY AZURE · KAHEEL</p>
        <h1><?php echo e($package->code); ?></h1>
        <p class="pkg-sub" data-ar="وحدة نموذجية — تم التسليم" data-en="Show unit — delivered">وحدة نموذجية — تم التسليم</p>

        <section class="pkg-gallery" aria-label="<?php echo e($package->code); ?>">
            <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <figure class="pkg-shot">
                    <img src="<?php echo e(asset('kaheel/'.$shot['file'])); ?>" alt="<?php echo e($shot['ar']); ?>" data-alt-ar="<?php echo e($shot['ar']); ?>" data-alt-en="<?php echo e($shot['en']); ?>">
                    <figcaption data-ar="<?php echo e($shot['ar']); ?>" data-en="<?php echo e($shot['en']); ?>"><?php echo e($shot['ar']); ?></figcaption>
                </figure>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>

        <section class="pkg-prices">
            <h2 data-ar="جدول الأسعار" data-en="Price list">جدول الأسعار</h2>
            <div class="tier-grid" style="--tier-count: <?php echo e(max(1, $package->tiers->count())); ?>">
                <?php $__currentLoopData = $package->tiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="tier-card">
                        <p class="tier-name" data-ar="<?php echo e($tier->name_ar); ?>" data-en="<?php echo e($tier->name_en); ?>"><?php echo e($tier->name_ar); ?></p>
                        <p class="tier-desc" data-ar="<?php echo e($tier->description_ar); ?>" data-en="<?php echo e($tier->description_en); ?>"><?php echo e($tier->description_ar); ?></p>
                        <p class="tier-label" data-ar="قبل الضريبة" data-en="Excl. VAT">قبل الضريبة</p>
                        <p class="tier-amount"><?php echo e($money($tier->price_excl_vat)); ?></p>
                        <div class="tier-rule"></div>
                        <p class="tier-label is-primary" data-ar="شامل الضريبة" data-en="Incl. VAT">شامل الضريبة</p>
                        <p class="tier-amount is-primary"><?php echo e($money($tier->price_incl_vat)); ?></p>
                        <p class="tier-label" data-ar="محتويات الباقة" data-en="Package contents">محتويات الباقة</p>
                        <ul class="tier-list">
                            <?php $__currentLoopData = $tier->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                    <span>
                                        <span class="item-title" data-ar="<?php echo e($item->name_ar); ?>" data-en="<?php echo e($item->name_en); ?>"><?php echo e($item->name_ar); ?></span>
                                        <span class="item-spec" data-ar="<?php echo e($item->product_name_ar); ?>" data-en="<?php echo e($item->product_name_en); ?>"><?php echo e($item->product_name_ar); ?></span>
                                        <?php if($item->detail_ar || $item->detail_en): ?>
                                            <span class="item-detail" data-ar="<?php echo e($item->detail_ar); ?>" data-en="<?php echo e($item->detail_en); ?>"><?php echo e($item->detail_ar); ?></span>
                                        <?php endif; ?>
                                        <span class="item-qty" data-ar="<?php echo e(rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.')); ?> <?php echo e($item->unit_label_ar); ?>" data-en="<?php echo e(rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.')); ?> <?php echo e($item->unit_label_en); ?>"><?php echo e(rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.')); ?> <?php echo e($item->unit_label_ar); ?></span>
                                    </span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <p class="pkg-note" data-ar="<?php echo e($deliveryAr); ?>" data-en="<?php echo e($deliveryEn); ?>"><?php echo e($deliveryAr); ?></p>
            <a class="btn btn-lg btn-primary pkg-order" href="<?php echo e(kaheel_order_start_url($package->code)); ?>" data-ar="اطلب الآن" data-en="Order now">اطلب الآن</a>
        </section>
    </main>

    <script>
        const root = document.documentElement;

        function applyLang(lang) {
            root.lang = lang;
            root.dir = lang === "ar" ? "rtl" : "ltr";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                node.textContent = lang === "ar" ? node.dataset.ar : node.dataset.en;
            });
            document.querySelectorAll("[data-alt-ar]").forEach((node) => {
                node.alt = lang === "ar" ? node.dataset.altAr : node.dataset.altEn;
            });
            document.title = (lang === "ar" ? <?php echo json_encode($package->name_ar, 15, 512) ?> : <?php echo json_encode($package->name_en, 15, 512) ?>) + " | FORMA BY AZURE – KAHEEL";
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            localStorage.setItem("forma-lang", lang);
        }

        const stored = localStorage.getItem("forma-lang");
        applyLang(stored === "en" ? "en" : "ar");
        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(root.lang === "ar" ? "en" : "ar");
        });
    </script>
    <?php echo $__env->make('kaheel.partials.native-validation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/package.blade.php ENDPATH**/ ?>