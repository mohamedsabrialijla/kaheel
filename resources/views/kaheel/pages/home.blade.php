@php($home = $home ?? url(app()->getLocale()))
<section class="relative isolate flex min-h-[80vh] items-end overflow-hidden bg-sidebar text-sidebar-accent-foreground">
    <img src="{{ asset('kaheel/forma/living-room-open.png') }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" style="animation:hero-zoom 18s ease-out forwards">
    <div class="absolute inset-0 -z-10 bg-sidebar/60"></div>
    <div class="mx-auto w-full max-w-7xl px-5 pb-20 pt-32 md:pb-28">
        <div class="max-w-xl">
            <p class="mb-4 flex items-center gap-3 text-xs tracking-[0.2em] text-accent"><span class="h-px w-8 bg-accent"></span>تصميم داخلي وتأثيث متكامل</p>
            <h1 class="font-display text-5xl leading-tight md:text-7xl">مساحات تُصمَّم لتُعاش</h1>
            <p class="mt-5 text-sm leading-7 opacity-90 md:text-base">نتولى تأثيث وحدتك السكنية من الفكرة الأولى حتى تسليم المفتاح؛ تصميم مدروس، وتوريد منتقى، وتركيب دقيق، لتنتقل إلى منزل جاهز للسكن من اليوم الأول.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#contact" class="inline-flex h-9 items-center bg-secondary px-6 text-sm text-secondary-foreground hover:bg-secondary/80">احجز استشارة</a>
                <a href="#services" class="inline-flex h-9 items-center border border-sidebar-accent-foreground/50 px-6 text-sm text-sidebar-accent-foreground hover:bg-sidebar-accent">اكتشف خدماتنا</a>
            </div>
        </div>
    </div>
</section>

<section id="about" class="bg-background py-20 md:py-28">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 md:grid-cols-2">
        <div class="reveal">
            <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary"><span class="h-px w-8 bg-primary"></span>عن فورما</p>
            <h2 class="font-display text-4xl md:text-5xl">أناقة لا يحدّها زمن</h2>
            <p class="mt-6 text-sm leading-8 text-muted-foreground md:text-base">فورما للتصميم الداخلي علامة تجمع رقيّ الطابع الكلاسيكي بروح التصميم المعاصر. نستلهم أعمالنا من العمارة وتفاصيلها المتقنة ومن جمال الخامات الطبيعية، لنصنع مساحات تتوازن فيها الصلابة مع الدفء، وتحتفظ بأناقتها مع مرور السنوات.</p>
            <dl class="mt-10 grid grid-cols-3 gap-4 border-t border-border pt-8">
                <div><dt class="font-display text-4xl text-primary md:text-5xl">25</dt><dd class="mt-2 text-xs leading-5 text-muted-foreground">يوم عمل حتى التسليم</dd></div>
                <div><dt class="font-display text-4xl text-primary md:text-5xl">01</dt><dd class="mt-2 text-xs leading-5 text-muted-foreground">جهة تواصل واحدة طوال المشروع</dd></div>
                <div><dt class="font-display text-4xl text-primary md:text-5xl">04</dt><dd class="mt-2 text-xs leading-5 text-muted-foreground">مراحل واضحة من الفكرة إلى المفتاح</dd></div>
            </dl>
        </div>
        <div class="reveal"><img src="{{ asset('kaheel/forma/vip-detail.jpg') }}" alt="تفاصيل التصميم" loading="lazy" class="aspect-[4/5] w-full rounded-t-full object-cover"></div>
    </div>
</section>

<section id="services" class="bg-muted py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-5">
        <div class="reveal">
            <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary"><span class="h-px w-8 bg-primary"></span>خدماتنا</p>
            <h2 class="max-w-md font-display text-4xl md:text-5xl">خدمة متكاملة تحت سقف واحد</h2>
        </div>
        <div class="mt-12 grid gap-px bg-border sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['n' => '01', 't' => 'التصميم الداخلي', 'd' => 'تخطيط المساحات واختيار الخامات والإضاءة والمفروشات ضمن رؤية واحدة تجمع الجمال والوظيفة.', 'icon' => 'pen-tool'],
                ['n' => '02', 't' => 'التوريد والتصنيع', 'd' => 'أثاث وأعمال نجارة وأجهزة ومنسوجات، منتقاة بعناية لتنسجم مع التصميم.', 'icon' => 'armchair'],
                ['n' => '03', 't' => 'التركيب والتنفيذ', 'd' => 'فريق متخصص يتولى التوريد والتركيب بدقة، وفق جدول زمني محدد.', 'icon' => 'wrench'],
                ['n' => '04', 't' => 'التسليم الجاهز', 'd' => 'نسلّمك المساحة مكتملة التفاصيل وجاهزة للسكن فوراً.', 'icon' => 'key-round'],
            ] as $svc)
                <div class="reveal h-full">
                    <div class="group h-full bg-muted p-8 transition-colors hover:bg-card">
                        <div class="flex items-center justify-between">
                            @if ($svc['icon'] === 'pen-tool')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pen-tool size-7 text-primary transition-transform group-hover:-translate-y-1" aria-hidden="true"><path d="M15.707 21.293a1 1 0 0 1-1.414 0l-1.586-1.586a1 1 0 0 1 0-1.414l5.586-5.586a1 1 0 0 1 1.414 0l1.586 1.586a1 1 0 0 1 0 1.414z"></path><path d="m18 13-1.375-6.874a1 1 0 0 0-.746-.776L3.235 2.028a1 1 0 0 0-1.207 1.207L5.35 15.879a1 1 0 0 0 .776.746L13 18"></path><path d="m2.3 2.3 7.286 7.286"></path><circle cx="11" cy="11" r="2"></circle></svg>
                            @elseif ($svc['icon'] === 'armchair')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-armchair size-7 text-primary transition-transform group-hover:-translate-y-1" aria-hidden="true"><path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"></path><path d="M3 16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V11a2 2 0 0 0-4 0z"></path><path d="M5 18v2"></path><path d="M19 18v2"></path></svg>
                            @elseif ($svc['icon'] === 'wrench')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wrench size-7 text-primary transition-transform group-hover:-translate-y-1" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"></path></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key-round size-7 text-primary transition-transform group-hover:-translate-y-1" aria-hidden="true"><path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path><circle cx="16.5" cy="7.5" r=".5" fill="currentColor"></circle></svg>
                            @endif
                            <span class="font-display text-2xl text-muted-foreground">{{ $svc['n'] }}</span>
                        </div>
                        <h3 class="mt-8 text-lg font-semibold">{{ $svc['t'] }}</h3>
                        <p class="mt-3 text-sm leading-7 text-muted-foreground">{{ $svc['d'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="process" class="bg-sidebar py-20 text-sidebar-accent-foreground md:py-28">
    <div class="mx-auto max-w-7xl px-5">
        <div class="reveal">
            <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-accent"><span class="h-px w-8 bg-accent"></span>آلية العمل</p>
            <h2 class="font-display text-4xl md:text-5xl">من الفكرة إلى المفتاح</h2>
            <p class="mt-4 max-w-xl text-sm leading-7 opacity-80">رحلة واضحة من أربع مراحل، تعرف فيها ما الذي يحدث ومتى، ومن تتواصل معه.</p>
        </div>
        <div class="relative mt-14 grid gap-10 md:grid-cols-4">
            <span aria-hidden="true" class="absolute inset-x-0 top-5 hidden h-px bg-sidebar-border md:block"></span>
            @foreach ([
                ['n' => '01', 't' => 'الاستشارة', 'd' => 'نتعرّف على مساحتك واحتياجاتك وذوقك وميزانيتك.'],
                ['n' => '02', 't' => 'التصميم والاختيار', 'd' => 'نعرض عليك التصميم والاختيارات لتراجعها وتخصّصها قبل البدء بالتنفيذ.'],
                ['n' => '03', 't' => 'التوريد والتركيب', 'd' => 'نورّد كل العناصر ونركّبها بإشراف فريقنا، مع جهة تواصل واحدة طوال المشروع.'],
                ['n' => '04', 't' => 'التسليم', 'd' => 'نسلّمك وحدتك جاهزة للسكن خلال 25 يوم عمل من تاريخ الدفع.'],
            ] as $step)
                <div class="reveal">
                    <span class="relative grid size-10 place-items-center rounded-full border border-accent bg-sidebar font-display text-sm text-accent">{{ $step['n'] }}</span>
                    <h3 class="mt-6 text-lg font-semibold">{{ $step['t'] }}</h3>
                    <p class="mt-3 text-sm leading-7 opacity-75">{{ $step['d'] }}</p>
                </div>
            @endforeach
        </div>
        <p class="mt-12 text-xs opacity-60">* أيام العمل لا تشمل الجمعة والسبت.</p>
    </div>
</section>

<section class="bg-background py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-5">
        <div class="reveal">
            <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary"><span class="h-px w-8 bg-primary"></span>نهجنا</p>
            <h2 class="font-display text-4xl md:text-5xl">التفاصيل تصنع الفرق</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-muted-foreground">نؤمن أن جمال المساحة يكمن في تفاصيلها: الخامة المختارة، واللون المدروس، واللمسة التي تجمع الجمال بالوظيفة.</p>
        </div>
        <div class="mt-12 grid gap-4 md:grid-cols-3 md:grid-rows-2">
            <figure class="reveal group relative min-h-64 overflow-hidden">
                <img src="{{ asset('kaheel/forma/vip-wardrobes.jpg') }}" alt="وظيفة بلمسة أنيقة" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                <figcaption class="absolute inset-x-0 bottom-0 bg-sidebar/70 p-5 text-sidebar-accent-foreground"><p class="font-semibold">وظيفة بلمسة أنيقة</p><p class="mt-1 text-xs opacity-85">حلول تخزين ذكية تندمج مع التصميم</p></figcaption>
            </figure>
            <figure class="reveal group relative min-h-64 overflow-hidden">
                <img src="{{ asset('kaheel/forma/vip-kitchen.jpg') }}" alt="مساحات تجمعكم" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                <figcaption class="absolute inset-x-0 bottom-0 bg-sidebar/70 p-5 text-sidebar-accent-foreground"><p class="font-semibold">مساحات تجمعكم</p><p class="mt-1 text-xs opacity-85">تصميم يراعي طريقة عيشكم واستقبالكم لضيوفكم</p></figcaption>
            </figure>
            <figure class="reveal group relative min-h-80 overflow-hidden rounded-t-full md:col-start-3 md:row-span-2 md:row-start-1">
                <img src="{{ asset('kaheel/forma/vip-master.jpg') }}" alt="عناية بكل تفصيل" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                <figcaption class="absolute inset-x-0 bottom-0 bg-sidebar/70 p-5 text-sidebar-accent-foreground"><p class="font-semibold">عناية بكل تفصيل</p><p class="mt-1 text-xs opacity-85">من اختيار الأقمشة إلى توزيع الإضاءة، لا نترك شيئاً للصدفة</p></figcaption>
            </figure>
            <figure class="reveal group relative min-h-64 overflow-hidden md:col-span-2">
                <img src="{{ asset('kaheel/forma/classic-living-detail-2.jpg') }}" alt="خامات منتقاة بعناية" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                <figcaption class="absolute inset-x-0 bottom-0 bg-sidebar/70 p-5 text-sidebar-accent-foreground"><p class="font-semibold">خامات منتقاة بعناية</p><p class="mt-1 text-xs opacity-85">أقمشة وأخشاب وأحجار ومعادن نختارها لجودة تدوم وأناقة لا تبهت مع الوقت</p></figcaption>
            </figure>
        </div>
    </div>
</section>

<section class="bg-muted py-20 md:py-28">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 md:grid-cols-[1fr_1.4fr]">
        <div class="reveal">
            <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary"><span class="h-px w-8 bg-primary"></span>عملاؤنا</p>
            <h2 class="font-display text-4xl md:text-5xl">لمن نعمل</h2>
            <p class="mt-4 text-sm leading-7 text-muted-foreground">نعمل مع الأفراد والمستثمرين والمطوّرين، بنفس المعايير ونفس الاهتمام بالتفاصيل.</p>
        </div>
        <div class="divide-y divide-border border-y border-border">
            <div class="reveal flex gap-6 py-6"><span class="font-display text-2xl text-primary">01</span><div><h3 class="text-lg font-semibold">الأفراد</h3><p class="mt-2 text-sm leading-7 text-muted-foreground">لمن يريد منزلاً مكتملاً دون عناء التنسيق بين أكثر من جهة.</p></div></div>
            <div class="reveal flex gap-6 py-6"><span class="font-display text-2xl text-primary">02</span><div><h3 class="text-lg font-semibold">مُلّاك الوحدات</h3><p class="mt-2 text-sm leading-7 text-muted-foreground">تجهيز وحدات جاهزة للسكن أو التأجير بجودة ثابتة ووقت محدد.</p></div></div>
            <div class="reveal flex gap-6 py-6"><span class="font-display text-2xl text-primary">03</span><div><h3 class="text-lg font-semibold">المطوّرون العقاريون</h3><p class="mt-2 text-sm leading-7 text-muted-foreground">تأثيث مشاريع كاملة بمعايير موحدة ومتابعة واضحة من التصميم حتى التسليم.</p></div></div>
        </div>
    </div>
</section>

<section id="contact" class="relative isolate overflow-hidden bg-sidebar text-sidebar-accent-foreground">
    <img src="{{ asset('kaheel/forma/living-room-sofa.png') }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-sidebar/75"></div>
    <div class="mx-auto max-w-3xl px-5 py-24 text-center md:py-32">
        <div class="reveal">
            <div class="flex justify-center"><p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-accent"><span class="h-px w-8 bg-accent"></span>تواصل معنا</p></div>
            <h2 class="font-display text-4xl md:text-5xl">لنبدأ بتصميم مساحتك</h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 opacity-85">تحدّث مع فريقنا اليوم، ودعنا نحوّل مساحتك إلى منزل يشبهك.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="https://wa.me/966599202222" target="_blank" rel="noreferrer" class="inline-flex h-9 items-center gap-2 bg-secondary px-6 text-sm text-secondary-foreground hover:bg-secondary/80">تواصل عبر واتساب</a>
                <a href="tel:+966599202222" class="inline-flex h-9 items-center gap-2 border border-sidebar-accent-foreground/50 px-6 text-sm text-sidebar-accent-foreground hover:bg-sidebar-accent">اتصل بنا <span dir="ltr">+966 59 920 2222</span></a>
            </div>
        </div>
    </div>
</section>
