@php($home = $home ?? url(app()->getLocale()))
<footer class="bg-sidebar text-sidebar-foreground">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div>
            <a dir="ltr" class="inline-flex flex-col items-center leading-none text-sidebar-accent-foreground" href="{{ $home }}">
                <span class="font-display text-3xl">FORMA</span>
                <span class="mt-1 text-[8px] tracking-[0.45em]">INTERIORS</span>
            </a>
            <p class="mt-5 max-w-xs text-sm leading-7 opacity-80">تصميم داخلي وتأثيث متكامل للوحدات السكنية والمشاريع، من الفكرة حتى التسليم.</p>
        </div>
        <div>
            <h2 class="mb-4 text-xs font-semibold tracking-[0.2em] text-accent">روابط</h2>
            <nav class="grid gap-3">
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="{{ $home }}#about">من نحن</a>
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="{{ $home }}#services">خدماتنا</a>
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="{{ $home }}/projects">المشاريع</a>
                <a class="text-sm opacity-80 transition-opacity hover:opacity-100" href="{{ $home }}/packages">الباقات</a>
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
    <div class="border-t border-sidebar-border px-5 py-10">
        <div class="mx-auto flex max-w-7xl flex-col items-center gap-4 text-center">
            <p class="text-sm tracking-wide text-sidebar-foreground/70">إحدى شركات أزور</p>
            <a href="https://azure.sa" target="_blank" rel="noreferrer" class="inline-flex items-center text-sidebar-accent-foreground" aria-label="AZURE">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 520 88" class="h-10 w-auto" fill="none" aria-hidden="true">
                    <g fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 72 V28 c0-12 10-22 24-22 s24 10 24 22 v44"/>
                        <path d="M86 22 h78 M86 66 h78 M92 22 L158 66"/>
                        <path d="M188 22 v32 c0 16 12 28 32 28 s32-12 32-28 V22"/>
                        <path d="M276 72 V22 h18 c18 0 32 12 32 28 s-14 28-32 28 h-18 M308 50 h18"/>
                        <path d="M400 22 h-48 v50 h48 M352 44 h40"/>
                    </g>
                </svg>
            </a>
        </div>
        <div class="mx-auto mt-8 flex max-w-7xl flex-wrap items-center justify-between gap-3 text-xs opacity-65">
            <span>© 2026 فورما للتصميم الداخلي. جميع الحقوق محفوظة.</span>
            <span>تصميم · تأثيث · توريد · تركيب</span>
        </div>
    </div>
</footer>
