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
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/forma.css')); ?>?v=2" rel="stylesheet">
    <script src="<?php echo e(asset('kaheel/forma-i18n.js')); ?>?v=1"></script>
    <script src="<?php echo e(asset('kaheel/forma-lang.js')); ?>?v=1"></script>
    <style>
        /* forma.css hides .reveal until JS adds .in — keep content visible if JS is late or missing */
        .reveal { opacity: 1; transform: none; }
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
        const menuBtn = document.querySelector('[aria-controls="public-mobile-menu"]');
        if (menuBtn) {
            let panel = document.getElementById("public-mobile-menu");
            if (!panel) {
                panel = document.createElement("nav");
                panel.id = "public-mobile-menu";
                panel.className = "hidden border-t border-border bg-card px-4 py-3 lg:hidden";
                panel.innerHTML = document.querySelector('nav[aria-label="التنقل الرئيسي"]')?.innerHTML || "";
                menuBtn.closest("header")?.append(panel);
            }
            menuBtn.addEventListener("click", () => {
                const open = panel.classList.toggle("hidden") === false;
                menuBtn.setAttribute("aria-expanded", open ? "true" : "false");
            });
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