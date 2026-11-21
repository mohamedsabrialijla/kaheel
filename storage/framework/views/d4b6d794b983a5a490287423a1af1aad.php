<?php
    $home = $home ?? url(app()->getLocale());
    $nav = $nav ?? '';
    $title = $title ?? 'FORMA INTERIORS';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?></title>
    <script>
        (function () {
            var en = localStorage.getItem("forma-lang") === "en";
            document.documentElement.lang = en ? "en" : "ar";
            document.documentElement.dir = en ? "ltr" : "rtl";
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=El+Messiri:wght@400;500;600&family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/forma.css')); ?>?v=3" rel="stylesheet">
    <script src="<?php echo e(asset('kaheel/forma-i18n.js')); ?>?v=1"></script>
    <script src="<?php echo e(asset('kaheel/forma-lang.js')); ?>?v=3"></script>
    <style>
        /* forma.css hides .reveal until JS adds .in — keep content visible if JS is late or missing */
        .reveal { opacity: 1; transform: none; }
        @keyframes hero-zoom { from { transform: scale(1); } to { transform: scale(1.08); } }
        #public-desktop-nav { display: none !important; }
        .public-menu-btn { display: inline-flex !important; }
        .public-mobile-menu { display: none; flex-direction: column; gap: 0; border-top: 1px solid color-mix(in oklab, var(--sidebar-border) 80%, transparent); background: var(--sidebar); padding: .5rem 1.25rem 1rem; }
        .public-mobile-menu.is-open { display: flex; }
        .public-mobile-menu a { display: block; padding: .85rem 0; border-bottom: 1px solid color-mix(in oklab, var(--sidebar-border) 70%, transparent); color: var(--sidebar-foreground); font-size: .95rem; text-decoration: none; }
        .public-mobile-menu a:last-child { border-bottom: 0; }
        .public-mobile-menu .public-mobile-cta { margin-top: .5rem; display: flex; align-items: center; justify-content: center; height: 2.5rem; background: var(--secondary); color: var(--secondary-foreground); border-bottom: 0; }
        @media (min-width: 1024px) {
            #public-desktop-nav { display: flex !important; }
            .public-menu-btn, .public-mobile-menu { display: none !important; }
        }
    </style>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body>
    <div class="min-h-screen bg-background">
        <?php echo $__env->make('kaheel.partials.forma-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo $__env->make('kaheel.partials.forma-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <script>
        const menuBtn = document.getElementById("public-menu-btn");
        const panel = document.getElementById("public-mobile-menu");
        if (menuBtn && panel) {
            const setOpen = (open) => {
                panel.classList.toggle("is-open", open);
                panel.hidden = !open;
                menuBtn.setAttribute("aria-expanded", open ? "true" : "false");
            };
            menuBtn.addEventListener("click", () => setOpen(!panel.classList.contains("is-open")));
            panel.querySelectorAll("a").forEach((link) => link.addEventListener("click", () => setOpen(false)));
        }

        const slides = [...document.querySelectorAll(".surface-hero img.absolute")];
        if (slides.length > 1) {
            let i = 0;
            const dots = [...document.querySelectorAll('.surface-hero button[aria-label^="الانتقال"]')];
            const num = document.querySelector(".surface-hero .text-gold");
            const show = (n) => {
                i = (n + slides.length) % slides.length;
                slides.forEach((img, idx) => {
                    img.style.opacity = idx === i ? "1" : "0";
                    img.setAttribute("aria-hidden", idx === i ? "false" : "true");
                });
                dots.forEach((dot, idx) => {
                    dot.setAttribute("aria-current", idx === i ? "true" : "false");
                    dot.classList.toggle("w-14", idx === i);
                    dot.classList.toggle("w-8", idx !== i);
                });
                if (num) num.textContent = String(i + 1).padStart(2, "0");
            };
            dots.forEach((dot, idx) => dot.addEventListener("click", () => show(idx)));
            document.querySelector('[aria-label="الشريحة التالية"]')?.addEventListener("click", () => show(i + 1));
            document.querySelector('[aria-label="الشريحة السابقة"]')?.addEventListener("click", () => show(i - 1));
            setInterval(() => show(i + 1), 6500);
        }

        const revealEls = document.querySelectorAll('.reveal');
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
            revealEls.forEach((el) => io.observe(el));
        }
        revealEls.forEach((el) => el.classList.add('in'));

        const apply = window.applyFormaLang;
        if (apply) {
            apply(localStorage.getItem("forma-lang") === "en");
            document.getElementById("lang-toggle")?.addEventListener("click", () => {
                const en = document.documentElement.lang !== "en";
                apply(en);
                localStorage.setItem("forma-lang", en ? "en" : "ar");
            });
        }
    </script>
    <?php echo $__env->make('kaheel.partials.native-validation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/layouts/forma.blade.php ENDPATH**/ ?>