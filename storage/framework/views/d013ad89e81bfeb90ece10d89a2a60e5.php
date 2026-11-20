<?php
    $home = $home ?? url(app()->getLocale());
    $iconCls = 'mx-auto size-7 text-primary transition-transform group-hover:-translate-y-1';
    $pkgIcons = [
        'armchair' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"/><path d="M3 16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V11a2 2 0 0 0-4 0z"/><path d="M5 18v2"/><path d="M19 18v2"/></svg>',
        'chef-hat' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>',
        'door-closed' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M10 12h.01"/><path d="M18 20V6a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v14"/><path d="M2 20h20"/></svg>',
        'refrigerator' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M5 6a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6Z"/><path d="M5 10h14"/><path d="M15 7v6"/></svg>',
        'blinds' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M3 3h18"/><path d="M20 7H8"/><path d="M20 11H8"/><path d="M10 19h10"/><path d="M8 15h12"/><path d="M4 3v14"/><circle cx="4" cy="19" r="2"/></svg>',
        'lamp' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M12 12v6"/><path d="M4.077 10.615A1 1 0 0 0 5 12h14a1 1 0 0 0 .923-1.385l-3.077-7.384A2 2 0 0 0 15 2H9a2 2 0 0 0-1.846 1.23Z"/><path d="M8 20a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1z"/></svg>',
        'bed-double' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M12 4v6"/><path d="M2 18h20"/></svg>',
        'package' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><polyline points="3.29 7 12 12 20.71 7"/><path d="m7.5 4.27 9 5.15"/></svg>',
        'wrench' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"/></svg>',
        'key-round' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="'.$iconCls.'" aria-hidden="true"><path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"/><circle cx="16.5" cy="7.5" r=".5" fill="currentColor"/></svg>',
    ];
?>
<section class="relative isolate overflow-hidden bg-sidebar text-sidebar-accent-foreground">
    <img src="<?php echo e(asset('kaheel/forma/living-room-sofa.png')); ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-sidebar/70"></div>
    <div class="mx-auto max-w-7xl px-5 pb-16 pt-24 md:pb-20 md:pt-32">
        <nav aria-label="breadcrumb" class="mb-4 text-xs opacity-80"><a class="hover:underline" href="<?php echo e($home); ?>">الرئيسية</a><span class="mx-2">/</span><span>الباقات</span></nav>
        <h1 class="font-display text-4xl md:text-6xl">باقات التأثيث</h1>
        <p class="mt-4 max-w-xl text-sm leading-7 opacity-85">باقات تأثيث متكاملة تُسلَّم مؤثثة بالكامل وجاهزة للسكن من اليوم الأول.</p>
    </div>
</section>

<section class="py-20 md:py-24">
    <div class="mx-auto max-w-6xl px-5">
        <div class="reveal">
            <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary"><span class="h-px w-8 bg-primary"></span>ما تشمله الباقة</p>
            <h2 class="font-display text-4xl md:text-5xl">تفاصيل الباقة</h2>
            <p class="mt-4 text-sm leading-7 text-muted-foreground">تُسلَّم كل وحدة كاملة: الأثاث، والمطبخ والخزائن، والأجهزة، والستائر، والإكسسوارات، والمفروشات.</p>
        </div>
        <div class="mt-12 grid grid-cols-2 border-s border-t border-border sm:grid-cols-3 lg:grid-cols-5">
            <?php $__currentLoopData = [
                ['t' => 'الأثاث', 'd' => 'كنب وطاولات وطقم طعام', 'icon' => 'armchair'],
                ['t' => 'المطبخ', 'd' => 'مطبخ متكامل بسطح رخامي', 'icon' => 'chef-hat'],
                ['t' => 'الخزائن', 'd' => 'دواليب نجارة مخصّصة', 'icon' => 'door-closed'],
                ['t' => 'الأجهزة', 'd' => 'أجهزة المطبخ والتلفزيون', 'icon' => 'refrigerator'],
                ['t' => 'الستائر', 'd' => 'للمعيشة وغرف النوم', 'icon' => 'blinds'],
                ['t' => 'الإكسسوارات', 'd' => 'لوحات وقطع ديكور منتقاة', 'icon' => 'lamp'],
                ['t' => 'المفروشات', 'd' => 'سجاد وطقم مفروشات كامل', 'icon' => 'bed-double'],
                ['t' => 'التوريد', 'd' => 'نورّد كل العناصر', 'icon' => 'package'],
                ['t' => 'التركيب', 'd' => 'فريق متخصص للتركيب', 'icon' => 'wrench'],
                ['t' => 'التسليم', 'd' => 'وحدة جاهزة للسكن', 'icon' => 'key-round'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="group border-b border-e border-border p-6 text-center transition-colors hover:bg-card">
                    <?php echo $pkgIcons[$item['icon']]; ?>

                    <h3 class="mt-4 text-sm font-semibold"><?php echo e($item['t']); ?></h3>
                    <p class="mt-2 text-xs leading-5 text-muted-foreground"><?php echo e($item['d']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="bg-sidebar py-20 text-sidebar-accent-foreground md:py-28">
    <div class="mx-auto max-w-5xl px-5 text-center">
        <div class="reveal">
            <div class="flex justify-center"><p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-accent"><span class="h-px w-8 bg-accent"></span>باقاتنا</p></div>
            <h2 class="font-display text-4xl md:text-5xl">VIP أم كلاسيك؟</h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 opacity-80">تشمل الباقتان نطاق التأثيث نفسه كاملاً، ويكمن الفرق في مستوى التشطيبات ومواصفات كل عنصر.</p>
        </div>
        <div class="mx-auto mt-14 grid max-w-3xl gap-8 sm:grid-cols-2">
            <article class="reveal flex h-full flex-col items-center rounded-t-full bg-primary px-8 pb-10 pt-20 text-primary-foreground transition-transform duration-500 hover:-translate-y-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" class="size-8" aria-hidden="true"><path d="M2.7 10.3a2.41 2.41 0 0 0 0 3.41l7.59 7.59a2.41 2.41 0 0 0 3.41 0l7.59-7.59a2.41 2.41 0 0 0 0-3.41l-7.59-7.59a2.41 2.41 0 0 0-3.41 0Z"/></svg>
                <h3 class="mt-4 font-display text-3xl tracking-[0.2em]" dir="ltr">VIP</h3>
                <p class="mt-3 text-sm leading-6 opacity-90">لمن يبحث عن تشطيبات أغنى وتفاصيل مصمّمة بعناية</p>
                <ul class="mt-6 grid gap-3 text-sm">
                    <li>أسطح رخامية وتشطيبات خشبية داكنة</li>
                    <li>نجارة مخصّصة مع إضاءة LED مخفية</li>
                    <li>إضاءة نحتية ولمسات فنية مختارة</li>
                </ul>
                <a href="#contact" class="mt-8 inline-flex h-9 items-center bg-secondary px-6 text-sm text-secondary-foreground">اطلب عرض سعر</a>
            </article>
            <article class="reveal flex h-full flex-col items-center rounded-t-full bg-accent px-8 pb-10 pt-20 text-accent-foreground transition-transform duration-500 hover:-translate-y-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" class="size-8" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/></svg>
                <h3 class="mt-4 font-display text-3xl tracking-[0.2em]" dir="ltr">CLASSIC</h3>
                <p class="mt-3 text-sm leading-6 opacity-90">لمن يريد وحدة متكاملة بتصميم هادئ وعملي وقيمة أفضل</p>
                <ul class="mt-6 grid gap-3 text-sm">
                    <li>خطوط نظيفة وألوان فاتحة</li>
                    <li>نطاق التأثيث الكامل نفسه</li>
                    <li>خيار عملي، قيمة أفضل</li>
                </ul>
                <a href="#contact" class="mt-8 inline-flex h-9 items-center bg-primary px-6 text-sm text-primary-foreground">اطلب عرض سعر</a>
            </article>
        </div>
        <a class="mt-10 inline-flex items-center text-sm text-sidebar-accent-foreground underline-offset-4 hover:underline" href="<?php echo e($home); ?>/projects">شاهد مشاريعنا السابقة</a>
    </div>
</section>
<?php /**PATH E:\kaheel\resources\views/kaheel/pages/furniture.blade.php ENDPATH**/ ?>