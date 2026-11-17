<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FORMA BY AZURE – KAHEEL | باقات تأثيث الوحدات</title>
    <meta name="description" content="باقات تأثيث وتشطيبات متكاملة، صُممت خصيصاً لوحدات كحيل وتُسلّم جاهزة للسكن.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/home.css')); ?>?v=46" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="<?php echo e(url(app()->getLocale())); ?>" aria-label="FORMA BY AZURE — KAHEEL">
                <span class="brand-mark">
                    <span class="brand-forma">FORMA</span>
                    <span class="brand-azure">BY AZURE</span>
                </span>
                <span class="brand-name">KAHEEL</span>
            </a>
            <nav class="nav" aria-label="main">
                <a class="btn btn-sm nav-home" href="<?php echo e(url(app()->getLocale())); ?>" data-t="navHome">الرئيسية</a>
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

    <main>
        <section class="hero">
            <img class="hero-photo" src="<?php echo e(asset('kaheel/vip-living.jpg')); ?>" alt="وحدة كحيل مؤثثة من فورما باي أزور" data-alt-ar="وحدة كحيل مؤثثة من فورما باي أزور" data-alt-en="Kaheel furnished unit by FORMA BY AZURE">
            <div class="hero-shade"></div>
            <div class="hero-inner">
                <div class="hero-copy">
                    <p class="eyebrow">FORMA BY AZURE</p>
                    <h1>KAHEEL</h1>
                    <p class="hero-sub" data-t="heroTitle">مساحات مختارة لحياة عصرية</p>
                    <p class="hero-body" data-t="heroBody">باقات تأثيث وتشطيبات متكاملة، صُممت خصيصاً لوحدات كحيل وتُسلّم جاهزة للسكن.</p>
                    <div class="hero-actions">
                        <a class="btn btn-lg btn-primary" href="#packages" data-t="heroCta">استعرض الباقات</a>
                        <a class="btn btn-lg btn-outline" href="<?php echo e(kaheel_customer() ? url(app()->getLocale().'/orders') : (auth()->check() ? url(app()->getLocale().'/account') : (auth('admin')->check() ? url(app()->getLocale().'/dashboard') : url(app()->getLocale().'/auth')))); ?>" data-t="navOrders">طلباتي</a>
                    </div>
                    <p class="hero-note" data-ar="<?php echo e($deliveryAr); ?>" data-en="<?php echo e($deliveryEn); ?>"><?php echo e($deliveryAr); ?></p>
                </div>
            </div>
        </section>

        <section class="packages" id="packages">
            <div class="wrap">
                <div class="packages-head">
                    <p class="eyebrow">KAHEEL COLLECTIONS</p>
                    <h2 data-t="packagesTitle">باقات الأثاث</h2>
                    <p class="packages-lead" data-t="packagesBody">تصفح الباقات ومحتوياتها بدون تسجيل دخول. سعر الباقة يعتمد على نوع الوحدة وعدد غرف النوم.</p>
                </div>

                <div class="package-rail" data-count="<?php echo e($packages->count()); ?>">
                <div class="package-grid">
                    <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $code = strtoupper($package->code);
                            $cover = $package->images->first();
                            $imageUrl = $cover ? $cover->publicUrl() : asset('kaheel/'.($code === 'CLASSIC' ? 'classic-living.jpg' : 'vip-living.jpg'));
                            $from = $package->tiers->min('price_incl_vat');
                            $lines = $highlights[$package->id] ?? [];
                        ?>
                        <article class="package-card <?php echo e($code === 'VIP' ? 'is-vip' : 'is-classic'); ?>">
                            <a class="package-media" href="<?php echo e(url(app()->getLocale().'/packages/'.strtolower($code))); ?>">
                                <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($package->name_ar); ?>" data-alt-ar="<?php echo e($package->name_ar); ?>" data-alt-en="<?php echo e($package->name_en); ?>">
                                <span class="badge <?php echo e($code === 'VIP' ? 'badge-vip' : 'badge-classic'); ?>" data-t="showUnit">وحدة نموذجية تم التسليم</span>
                            </a>
                            <div class="package-body">
                                <div class="package-top">
                                    <div>
                                        <h3 data-ar="<?php echo e($package->name_ar); ?>" data-en="<?php echo e($package->name_en); ?>"><?php echo e($package->name_ar); ?></h3>
                                        <div class="accent-bar"></div>
                                    </div>
                                    <?php if($code === 'VIP'): ?>
                                        <span class="premium">PREMIUM CHOICE</span>
                                    <?php endif; ?>
                                </div>
                                <p class="tagline" data-ar="<?php echo e($package->tagline_ar); ?>" data-en="<?php echo e($package->tagline_en); ?>"><?php echo e($package->tagline_ar); ?></p>
                                <div class="highlights">
                                    <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div><span class="mark"><?php echo e($code === 'VIP' ? '✦' : '•'); ?></span><span data-ar="<?php echo e($item['ar']); ?>" data-en="<?php echo e($item['en']); ?>"><?php echo e($item['ar']); ?></span></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="price-block">
                                    <p class="price-label" data-t="startingFrom">تبدأ من</p>
                                    <p class="price"><?php echo e(number_format((float) $from, 2, '.', ',')); ?> <span data-t="sar">ريال</span></p>
                                    <p class="price-vat" data-t="inclVat">شامل الضريبة</p>
                                    <div class="package-actions">
                                        <a class="btn btn-primary btn-square" href="<?php echo e(kaheel_order_start_url($code)); ?>">
                                            <span data-t="orderNow">اطلب الآن</span>
                                            <svg class="icon icon-forward" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="m12 19-7-7 7-7"/>
                                                <path d="M19 12H5"/>
                                            </svg>
                                        </a>
                                        <a class="btn btn-square-outline" href="<?php echo e(url(app()->getLocale().'/packages/'.strtolower($code))); ?>" data-t="viewDetails">تفاصيل الباقة</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                </div>

                <p class="section-note" data-ar="<?php echo e($deliveryAr); ?>" data-en="<?php echo e($deliveryEn); ?>"><?php echo e($deliveryAr); ?></p>
            </div>
        </section>

        <section class="includes">
            <div class="includes-inner">
                <h2 data-t="includes">محتويات الباقة</h2>
                <div class="include-grid">
                    <?php $__currentLoopData = $outline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg><span data-ar="<?php echo e($item->name_ar); ?>" data-en="<?php echo e($item->name_en); ?>"><?php echo e($item->name_ar); ?></span></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div>
                <p class="footer-brand">FORMA <span>BY AZURE</span></p>
                <p class="footer-line">KAHEEL · <?php echo e($packages->pluck('code')->implode(' · ')); ?></p>
            </div>
            <div class="footer-contact">
                <p class="phones"><?php echo e($phones); ?></p>
                <p class="hours" data-t="hours">الأحد – الخميس · 10:00 صباحاً – 8:00 مساءً</p>
            </div>
        </div>
    </footer>

    <script>
        const copy = {
            navHome: { ar: "الرئيسية", en: "Home" },
            navAdmin: { ar: "الإدارة", en: "Admin" },
            signIn: { ar: "تسجيل الدخول", en: "Sign in" },
            signOut: { ar: "تسجيل الخروج", en: "Sign out" },
            navOrders: { ar: "طلباتي", en: "My Orders" },
            heroTitle: { ar: "مساحات مختارة لحياة عصرية", en: "Curated Spaces for Modern Living" },
            heroBody: { ar: "باقات تأثيث وتشطيبات متكاملة، صُممت خصيصاً لوحدات كحيل وتُسلّم جاهزة للسكن.", en: "Complete furniture and finishing packages, designed for Kaheel residences and delivered move-in ready." },
            heroCta: { ar: "استعرض الباقات", en: "Explore packages" },
            packagesTitle: { ar: "باقات الأثاث", en: "Furniture packages" },
            packagesBody: { ar: "تصفح الباقات ومحتوياتها بدون تسجيل دخول. سعر الباقة يعتمد على نوع الوحدة وعدد غرف النوم.", en: "Browse packages and contents without signing in. Pricing depends on unit type and bedroom count." },
            showUnit: { ar: "وحدة نموذجية تم التسليم", en: "Show unit delivered" },
            vipTagline: { ar: "تجهيز فاخر متكامل لوحدتك", en: "Complete premium turnkey furnishing" },
            classicTagline: { ar: "تجهيز أنيق بقيمة متوازنة", en: "Elegant furnishing with balanced value" },
            startingFrom: { ar: "تبدأ من", en: "Starting from" },
            sar: { ar: "ريال", en: "SAR" },
            inclVat: { ar: "شامل الضريبة", en: "Incl. VAT" },
            orderNow: { ar: "اطلب الآن", en: "Order now" },
            viewDetails: { ar: "تفاصيل الباقة", en: "Package details" },
            includes: { ar: "محتويات الباقة", en: "Package contents" },
            itemFurniture: { ar: "الأثاث", en: "Furniture" },
            itemKitchen: { ar: "المطبخ", en: "Kitchen" },
            itemWardrobes: { ar: "الدواليب", en: "Wardrobes" },
            itemAppliances: { ar: "الأجهزة", en: "Appliances" },
            itemCurtains: { ar: "الستائر", en: "Curtains" },
            itemAccessories: { ar: "الإكسسوارات", en: "Accessories" },
            itemSoft: { ar: "المفروشات", en: "Soft furnishings" },
            itemSupply: { ar: "التوريد", en: "Supply" },
            itemInstall: { ar: "التركيب", en: "Installation" },
            itemHandover: { ar: "التسليم", en: "Handover" },
            hours: { ar: "الأحد – الخميس · 10:00 صباحاً – 8:00 مساءً", en: "Sunday – Thursday · 10:00 AM – 8:00 PM" }
        };

        const root = document.documentElement;

        function applyLang(lang) {
            root.lang = lang;
            root.dir = lang === "ar" ? "rtl" : "ltr";
            document.querySelectorAll("[data-t]").forEach((node) => {
                const value = copy[node.dataset.t];
                if (value) node.textContent = value[lang];
            });
            document.querySelectorAll("[data-ar]").forEach((node) => {
                if (node.dataset.t) return;
                node.textContent = lang === "ar" ? node.dataset.ar : node.dataset.en;
            });
            document.querySelectorAll("[data-alt-ar]").forEach((node) => {
                node.alt = lang === "ar" ? node.dataset.altAr : node.dataset.altEn;
            });
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
<?php /**PATH E:\kaheel\resources\views/kaheel/home.blade.php ENDPATH**/ ?>