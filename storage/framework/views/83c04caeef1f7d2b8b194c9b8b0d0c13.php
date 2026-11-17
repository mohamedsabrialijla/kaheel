<?php
    $home = $home ?? url(app()->getLocale());
    $on = 'border px-5 py-2 text-sm transition border-primary bg-primary text-primary-foreground';
    $off = 'border px-5 py-2 text-sm transition border-border bg-card text-muted-foreground hover:border-primary hover:text-foreground';
    $shots = [
        ['file' => 'living-room-open.png', 'cat' => 'living', 'ar' => 'غرفة الطعام والمعيشة', 'en' => 'Dining and living', 'aspect' => 'aspect-[3/4]'],
        ['file' => 'vip-kitchen.jpg', 'cat' => 'kitchen', 'ar' => 'المطبخ ومنطقة الطعام', 'en' => 'Kitchen and dining', 'aspect' => 'aspect-square'],
        ['file' => 'vip-master.jpg', 'cat' => 'bedroom', 'ar' => 'غرفة النوم الرئيسية', 'en' => 'Master bedroom', 'aspect' => 'aspect-[3/4]'],
        ['file' => 'vip-wardrobes.jpg', 'cat' => 'bedroom', 'ar' => 'تفاصيل الدواليب', 'en' => 'Wardrobe details', 'aspect' => 'aspect-square'],
        ['file' => 'vip-bedroom.jpg', 'cat' => 'bedroom', 'ar' => 'غرفة النوم الثانية', 'en' => 'Second bedroom', 'aspect' => 'aspect-square'],
        ['file' => 'vip-detail.jpg', 'cat' => 'bedroom', 'ar' => 'تفاصيل جانب السرير', 'en' => 'Bedside details', 'aspect' => 'aspect-square'],
        ['file' => 'living-room-sofa.png', 'cat' => 'living', 'ar' => 'غرفة المعيشة والطعام', 'en' => 'Living and dining', 'aspect' => 'aspect-[3/4]'],
        ['file' => 'classic-kitchen.jpg', 'cat' => 'kitchen', 'ar' => 'المطبخ', 'en' => 'Kitchen', 'aspect' => 'aspect-square'],
        ['file' => 'classic-bedroom.jpg', 'cat' => 'bedroom', 'ar' => 'غرفة النوم', 'en' => 'Bedroom', 'aspect' => 'aspect-square'],
        ['file' => 'classic-kitchen-detail.jpg', 'cat' => 'kitchen', 'ar' => 'تفاصيل المطبخ', 'en' => 'Kitchen details', 'aspect' => 'aspect-square'],
        ['file' => 'classic-living-detail-2.jpg', 'cat' => 'living', 'ar' => 'تفاصيل غرفة المعيشة', 'en' => 'Living room details', 'aspect' => 'aspect-square'],
    ];
?>
<section class="bg-muted pt-16 pb-14 text-center md:pt-24">
    <div class="mx-auto max-w-3xl px-5">
        <div class="reveal" style="transition-delay:0ms">
            <p class="mb-4 text-[0.65rem] font-semibold tracking-[0.32em] text-primary">PORTFOLIO</p>
            <h1 class="font-display text-4xl md:text-6xl" data-ar="مشاريعنا" data-en="Our projects">مشاريعنا</h1>
            <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-muted-foreground" data-ar="وحدات سلّمناها مؤثثة بالكامل — كل صورة خلفها مخطط وتصميم وتوريد وتركيب وتسليم." data-en="Fully furnished units we delivered — every image is backed by a plan, design, supply, installation and handover.">وحدات سلّمناها مؤثثة بالكامل — كل صورة خلفها مخطط وتصميم وتوريد وتركيب وتسليم.</p>
        </div>
    </div>
</section>
<section class="mx-auto max-w-6xl px-5 py-14 md:py-20">
    <div class="reveal mb-10 flex flex-wrap justify-center gap-2" data-project-filters style="transition-delay:0ms">
        <button type="button" class="<?php echo e($on); ?>" data-filter="all" aria-pressed="true" data-ar="الكل" data-en="All">الكل</button>
        <button type="button" class="<?php echo e($off); ?>" data-filter="living" aria-pressed="false" data-ar="غرف معيشة" data-en="Living rooms">غرف معيشة</button>
        <button type="button" class="<?php echo e($off); ?>" data-filter="kitchen" aria-pressed="false" data-ar="مطابخ" data-en="Kitchens">مطابخ</button>
        <button type="button" class="<?php echo e($off); ?>" data-filter="bedroom" aria-pressed="false" data-ar="غرف نوم" data-en="Bedrooms">غرف نوم</button>
    </div>
    <div class="columns-2 gap-3 md:columns-3 [&>*]:mb-3" data-project-grid>
        <?php $__currentLoopData = $shots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $shot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="reveal group relative block overflow-hidden break-inside-avoid" data-project-item data-category="<?php echo e($shot['cat']); ?>" style="transition-delay:<?php echo e(($i % 3) * 90); ?>ms">
                <img src="<?php echo e(asset('kaheel/forma/'.$shot['file'])); ?>" alt="<?php echo e($shot['ar']); ?>" class="w-full object-cover transition duration-700 group-hover:scale-105 <?php echo e($shot['aspect']); ?>">
                <span class="absolute inset-x-0 bottom-0 bg-sidebar/80 px-4 py-3 text-xs text-sidebar-foreground opacity-0 backdrop-blur transition duration-500 group-hover:opacity-100" data-ar="<?php echo e($shot['ar']); ?>" data-en="<?php echo e($shot['en']); ?>"><?php echo e($shot['ar']); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<section class="bg-primary py-16 text-primary-foreground md:py-20">
    <div class="reveal mx-auto max-w-3xl px-5 text-center" style="transition-delay:0ms">
        <h2 class="font-display text-4xl md:text-5xl" data-ar="وحدتك التالية هنا" data-en="Your next unit starts here">وحدتك التالية هنا</h2>
        <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80 mt-8 h-12 rounded-none px-8" href="<?php echo e($home); ?>/auth">
            <span data-ar="ابدأ طلبك" data-en="Start your order">ابدأ طلبك</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left size-4 ltr:rotate-180" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
        </a>
    </div>
</section>
<script>
    (() => {
        const bar = document.querySelector("[data-project-filters]");
        if (!bar) return;
        const on = "border px-5 py-2 text-sm transition border-primary bg-primary text-primary-foreground";
        const off = "border px-5 py-2 text-sm transition border-border bg-card text-muted-foreground hover:border-primary hover:text-foreground";
        const buttons = [...bar.querySelectorAll("button[data-filter]")];
        const items = [...document.querySelectorAll("[data-project-item]")];
        const apply = (cat) => {
            buttons.forEach((btn) => {
                const active = btn.dataset.filter === cat;
                btn.className = active ? on : off;
                btn.setAttribute("aria-pressed", active ? "true" : "false");
            });
            items.forEach((el) => {
                el.hidden = cat !== "all" && el.dataset.category !== cat;
            });
        };
        buttons.forEach((btn) => btn.addEventListener("click", () => apply(btn.dataset.filter)));
    })();
</script>
<?php /**PATH E:\kaheel\resources\views/kaheel/pages/projects.blade.php ENDPATH**/ ?>