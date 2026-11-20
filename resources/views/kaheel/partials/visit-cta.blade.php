@php($home = $home ?? url(app()->getLocale()))
<section id="contact" class="relative isolate overflow-hidden bg-sidebar text-sidebar-accent-foreground">
    <img src="{{ asset('kaheel/forma/vip-master.jpg') }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-sidebar/75"></div>
    <div class="mx-auto max-w-3xl px-5 py-24 text-center md:py-32">
        <div class="reveal">
            <div class="flex justify-center">
                <p class="mb-3 flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-accent"><span class="h-px w-8 bg-accent"></span>تواصل معنا</p>
            </div>
            <h2 class="font-display text-4xl md:text-5xl">{{ $ctaTitle ?? 'لنبدأ بزيارة' }}</h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 opacity-85">{{ $ctaLead ?? 'تواصل معنا لتحديد موعد المعاينة، ونتولى الباقي.' }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="https://wa.me/966599202222" target="_blank" rel="noreferrer" class="inline-flex h-9 items-center gap-2 bg-secondary px-6 text-sm text-secondary-foreground hover:bg-secondary/80">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" aria-hidden="true"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"></path></svg>
                    تواصل عبر واتساب
                </a>
                <a href="tel:+966599202222" class="inline-flex h-9 items-center gap-2 border border-sidebar-accent-foreground/50 px-6 text-sm text-sidebar-accent-foreground hover:bg-sidebar-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
                    اتصل بنا <span dir="ltr">+966 59 920 2222</span>
                </a>
            </div>
        </div>
    </div>
</section>
