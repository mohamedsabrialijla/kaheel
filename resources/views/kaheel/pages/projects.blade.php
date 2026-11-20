@php
    $home = $home ?? url(app()->getLocale());
    $tabOn = 'inline-flex h-8 items-center rounded-full bg-primary px-5 text-xs font-medium text-primary-foreground shadow hover:bg-primary/90';
    $tabOff = 'inline-flex h-8 items-center rounded-full border border-input bg-background px-5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground';
    $shots = [
        ['file' => 'living-room-open.png', 'cat' => 'vip', 'ar' => 'غرفة الطعام والمعيشة', 'en' => 'Dining and living', 'span' => 'row-span-2'],
        ['file' => 'vip-kitchen.jpg', 'cat' => 'vip', 'ar' => 'المطبخ ومنطقة الطعام', 'en' => 'Kitchen and dining', 'span' => ''],
        ['file' => 'vip-master.jpg', 'cat' => 'vip', 'ar' => 'غرفة النوم الرئيسية', 'en' => 'Master bedroom', 'span' => ''],
        ['file' => 'vip-wardrobes.jpg', 'cat' => 'vip', 'ar' => 'تفاصيل غرفة النوم والدواليب', 'en' => 'Wardrobe details', 'span' => ''],
        ['file' => 'vip-bedroom.jpg', 'cat' => 'vip', 'ar' => 'غرفة النوم الثانية', 'en' => 'Second bedroom', 'span' => ''],
        ['file' => 'vip-detail.jpg', 'cat' => 'vip', 'ar' => 'تفاصيل جانب السرير', 'en' => 'Bedside details', 'span' => 'row-span-2'],
        ['file' => 'living-room-sofa.png', 'cat' => 'classic', 'ar' => 'غرفة المعيشة والطعام', 'en' => 'Living and dining', 'span' => ''],
        ['file' => 'classic-kitchen.jpg', 'cat' => 'classic', 'ar' => 'المطبخ', 'en' => 'Kitchen', 'span' => ''],
        ['file' => 'classic-bedroom.jpg', 'cat' => 'classic', 'ar' => 'غرفة النوم', 'en' => 'Bedroom', 'span' => ''],
        ['file' => 'classic-kitchen-detail.jpg', 'cat' => 'classic', 'ar' => 'تفاصيل المطبخ', 'en' => 'Kitchen details', 'span' => ''],
        ['file' => 'classic-living-detail-2.jpg', 'cat' => 'classic', 'ar' => 'تفاصيل غرفة المعيشة', 'en' => 'Living room details', 'span' => 'row-span-2'],
    ];
@endphp
<section class="relative isolate overflow-hidden bg-sidebar text-sidebar-accent-foreground">
    <img src="{{ asset('kaheel/forma/living-room-open.png') }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-sidebar/70"></div>
    <div class="mx-auto max-w-7xl px-5 pb-16 pt-24 md:pb-20 md:pt-32">
        <nav aria-label="breadcrumb" class="mb-4 text-xs opacity-80"><a class="hover:underline" href="{{ $home }}">الرئيسية</a><span class="mx-2">/</span><span>مشاريعنا السابقة</span></nav>
        <h1 class="font-display text-4xl md:text-6xl">مشروع كحيل</h1>
        <p class="mt-4 max-w-xl text-sm leading-7 opacity-85">شريك التأثيث لمشروع كحيل السكني؛ وحدات تُسلَّم مؤثثة بالكامل وجاهزة للسكن.</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-5 py-14 md:py-20">
    <div class="reveal mb-8">
        <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary"><span class="h-px w-8 bg-primary"></span>عن المشروع</p>
        <h2 class="font-display text-4xl md:text-5xl">معرض المشروع</h2>
        <p class="mt-4 max-w-2xl text-sm leading-7 text-muted-foreground">نؤثث وحدات المشروع السكنية بباقات متكاملة تُسلَّم جاهزة للمالك، من الأثاث والمطابخ إلى الأجهزة والمفروشات.</p>
    </div>
    <div class="mt-8 flex gap-2" role="tablist" data-project-filters>
        <button type="button" class="{{ $tabOn }}" data-filter="all" role="tab" aria-selected="true">الكل</button>
        <button type="button" class="{{ $tabOff }}" data-filter="vip" role="tab" aria-selected="false">VIP</button>
        <button type="button" class="{{ $tabOff }}" data-filter="classic" role="tab" aria-selected="false">كلاسيك</button>
    </div>
    <div class="mt-8 grid auto-rows-[220px] grid-cols-2 gap-3 md:grid-cols-3" data-project-grid>
        @foreach ($shots as $shot)
            @php $badge = $shot['cat'] === 'vip' ? 'VIP' : 'كلاسيك'; @endphp
            <button type="button" class="group relative overflow-hidden text-start {{ $shot['span'] }}" data-project-item data-category="{{ $shot['cat'] }}" data-src="{{ asset('kaheel/forma/'.$shot['file']) }}" data-ar="{{ $shot['ar'] }}" data-en="{{ $shot['en'] }}" data-badge="{{ $badge }}" aria-label="{{ $shot['ar'] }}">
                <img src="{{ asset('kaheel/forma/'.$shot['file']) }}" alt="{{ $shot['ar'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                <span class="absolute inset-x-0 bottom-0 translate-y-0 bg-sidebar/80 p-3 text-sm text-sidebar-accent-foreground transition-transform duration-300 md:translate-y-full md:p-4 group-hover:translate-y-0 group-focus-visible:translate-y-0">{{ $shot['ar'] }}<span class="ms-2 text-xs text-accent">· {{ $badge }}</span></span>
            </button>
        @endforeach
    </div>
</section>
<section class="bg-muted py-16 md:py-20">
    <div class="reveal mx-auto max-w-3xl px-5 text-center">
        <h2 class="font-display text-4xl md:text-5xl">اختر الباقة التي تناسب وحدتك</h2>
        <p class="mt-4 text-sm text-muted-foreground">تعرّف على ما تشمله كل باقة والفرق بينها.</p>
        <a class="mt-8 inline-flex h-11 items-center bg-primary px-8 text-sm text-primary-foreground" href="{{ $home }}/packages">استعرض الباقات</a>
    </div>
</section>
@include('kaheel.partials.visit-cta')
<div id="project-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-sidebar/95 p-4" role="dialog" aria-modal="true" hidden>
    <button type="button" class="absolute end-4 top-4 inline-flex h-9 w-9 items-center justify-center text-sidebar-foreground hover:bg-sidebar-accent" data-lb="close" aria-label="إغلاق">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
    </button>
    <button type="button" class="absolute start-4 inline-flex h-9 w-9 items-center justify-center text-sidebar-foreground hover:bg-sidebar-accent" data-lb="prev" aria-label="prev">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ltr:rotate-180" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
    </button>
    <figure class="max-h-full max-w-5xl">
        <img alt="" class="max-h-[80vh] w-auto object-contain" data-lb="img">
        <figcaption class="mt-3 text-center text-sm text-sidebar-foreground" data-lb="cap"></figcaption>
    </figure>
    <button type="button" class="absolute end-4 inline-flex h-9 w-9 items-center justify-center text-sidebar-foreground hover:bg-sidebar-accent" data-lb="next" aria-label="next">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ltr:rotate-180" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
    </button>
</div>
<script>
    (() => {
        const bar = document.querySelector("[data-project-filters]");
        const lb = document.getElementById("project-lightbox");
        if (!bar || !lb) return;
        const on = "inline-flex h-8 items-center rounded-full bg-primary px-5 text-xs font-medium text-primary-foreground shadow hover:bg-primary/90";
        const off = "inline-flex h-8 items-center rounded-full border border-input bg-background px-5 text-xs font-medium shadow-sm hover:bg-accent hover:text-accent-foreground";
        const buttons = [...bar.querySelectorAll("button[data-filter]")];
        const items = [...document.querySelectorAll("[data-project-item]")];
        const img = lb.querySelector("[data-lb=img]");
        const cap = lb.querySelector("[data-lb=cap]");
        let index = 0;
        const visible = () => items.filter((el) => !el.hidden);
        const apply = (cat) => {
            buttons.forEach((btn) => {
                const active = btn.dataset.filter === cat;
                btn.className = active ? on : off;
                btn.setAttribute("aria-selected", active ? "true" : "false");
            });
            items.forEach((el) => {
                el.hidden = cat !== "all" && el.dataset.category !== cat;
            });
        };
        const isEn = () => document.documentElement.lang === "en";
        const show = (i) => {
            const list = visible();
            if (!list.length) return;
            index = (i + list.length) % list.length;
            const el = list[index];
            const title = isEn() ? (el.dataset.en || el.dataset.ar) : el.dataset.ar;
            img.src = el.dataset.src;
            img.alt = title;
            cap.textContent = title + " · " + el.dataset.badge + " (" + (index + 1) + "/" + list.length + ")";
            lb.hidden = false;
            lb.classList.remove("hidden");
            lb.classList.add("flex");
        };
        const close = () => {
            lb.hidden = true;
            lb.classList.add("hidden");
            lb.classList.remove("flex");
        };
        buttons.forEach((btn) => btn.addEventListener("click", () => apply(btn.dataset.filter)));
        items.forEach((el, i) => el.addEventListener("click", () => show(visible().indexOf(el))));
        lb.querySelector("[data-lb=close]").addEventListener("click", close);
        lb.querySelector("[data-lb=prev]").addEventListener("click", (e) => { e.stopPropagation(); show(index - 1); });
        lb.querySelector("[data-lb=next]").addEventListener("click", (e) => { e.stopPropagation(); show(index + 1); });
        lb.addEventListener("click", (e) => { if (e.target === lb) close(); });
        document.addEventListener("keydown", (e) => {
            if (lb.hidden) return;
            if (e.key === "Escape") close();
            if (e.key === "ArrowLeft") show(index + (document.documentElement.dir === "rtl" ? -1 : 1));
            if (e.key === "ArrowRight") show(index + (document.documentElement.dir === "rtl" ? 1 : -1));
        });
    })();
</script>
