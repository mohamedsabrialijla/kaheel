<?php
    $home = url(app()->getLocale());
    $compounds = $customer ? ($customer->units ?? []) : [];
    $displayName = $customer ? ($customer->name ?: $customer->username) : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>وحداتي | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/home.css')); ?>?v=11" rel="stylesheet">
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

    <main class="account-page">
        <h1 class="auth-title" data-ar="وحداتي" data-en="My units">وحداتي</h1>
        <p class="auth-lead" data-ar="<?php echo e($displayName !== '' ? 'الوحدات المسجلة باسم '.$displayName : 'وحدات العميل تظهر بعد تسجيل الدخول.'); ?>" data-en="<?php echo e($displayName !== '' ? 'Units registered to '.$displayName : 'Customer units appear after sign-in.'); ?>">
            <?php echo e($displayName !== '' ? 'الوحدات المسجلة باسم '.$displayName : 'وحدات العميل تظهر بعد تسجيل الدخول.'); ?>

        </p>

        <?php $__empty_1 = true; $__currentLoopData = $compounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <section class="account-compound">
                <h2><?php echo e($compound['display_name'] ?: '—'); ?></h2>
                <p class="account-meta">
                    <span data-ar="رقم المجمع" data-en="Compound">رقم المجمع</span>
                    <?php echo e($compound['compound_number']); ?>

                    · <?php echo e($compound['owned_units_count']); ?>

                    <span data-ar="وحدة" data-en="units">وحدة</span>
                </p>
                <ul class="account-units">
                    <?php $__currentLoopData = $compound['units']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <strong><?php echo e($unit['unit_no']); ?></strong>
                            <span>
                                <?php if($unit['bedrooms_number']): ?>
                                    <?php echo e($unit['bedrooms_number']); ?> <span data-ar="غرف" data-en="beds">غرف</span>
                                <?php endif; ?>
                                <?php if($unit['squar_meters']): ?>
                                    · <?php echo e($unit['squar_meters']); ?> <span data-ar="م²" data-en="m²">م²</span>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <section class="account-compound">
                <p class="account-meta" data-ar="لا توجد وحدات مرتبطة بهذا الحساب." data-en="No units are linked to this account.">لا توجد وحدات مرتبطة بهذا الحساب.</p>
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
        if (localStorage.getItem("forma-lang") === "en") {
            document.getElementById("lang-toggle").click();
        }
    </script>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/account.blade.php ENDPATH**/ ?>