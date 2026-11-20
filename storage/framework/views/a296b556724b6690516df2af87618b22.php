<?php ($home = $home ?? url(app()->getLocale())); ?>
<footer class="bg-sidebar text-sidebar-foreground">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div>
            <a dir="ltr" class="inline-flex flex-col items-center leading-none text-sidebar-accent-foreground" href="<?php echo e($home); ?>">
                <span class="font-display text-3xl">FORMA</span>
                <span class="mt-1 text-[8px] tracking-[0.45em]">INTERIORS</span>
            </a>
            <p class="mt-5 max-w-xs text-sm leading-7 opacity-80">تصميم داخلي وتأثيث متكامل للوحدات السكنية والمشاريع، من الفكرة حتى التسليم.</p>
        </div>
        <div>
            <h2 class="mb-4 text-xs font-semibold tracking-[0.2em] text-accent">روابط</h2>
            <nav class="grid gap-3">
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="<?php echo e($home); ?>#about">من نحن</a>
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="<?php echo e($home); ?>#services">خدماتنا</a>
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="<?php echo e($home); ?>/projects">المشاريع</a>
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="<?php echo e($home); ?>/packages">الباقات</a>
            </nav>
        </div>
        <div>
            <h2 class="mb-4 text-xs font-semibold tracking-[0.2em] text-accent">تواصل</h2>
            <div class="grid justify-items-start gap-3">
                <a href="tel:+966599202222" class="text-sm opacity-80 transition-opacity hover:opacity-100" dir="ltr">+966 59 920 2222</a>
                <a href="tel:+966535920450" class="text-sm opacity-80 transition-opacity hover:opacity-100" dir="ltr">+966 53 592 0450</a>
                <a href="https://wa.me/966599202222" target="_blank" rel="noreferrer" class="text-sm opacity-80 transition-opacity hover:opacity-100">واتساب</a>
            </div>
        </div>
        <div>
            <h2 class="mb-4 text-xs font-semibold tracking-[0.2em] text-accent">ساعات العمل</h2>
            <p class="text-sm opacity-80">الأحد – الخميس</p>
            <p class="mt-2 text-sm opacity-80">10:00 صباحاً – 8:00 مساءً</p>
        </div>
    </div>
    <div class="border-t border-sidebar-border px-5 py-5">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 text-xs opacity-65">
            <span>© 2026 فورما للتصميم الداخلي. جميع الحقوق محفوظة.</span>
            <span>تصميم · تأثيث · توريد · تركيب</span>
        </div>
    </div>
</footer>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/forma-footer.blade.php ENDPATH**/ ?>