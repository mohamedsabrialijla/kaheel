@php
    $home = $home ?? url(app()->getLocale());
    $customer = kaheel_customer();
    $isAdmin = auth('admin')->check();
    $loggedIn = (bool) ($customer || auth()->check());
    $ordersUrl = url(app()->getLocale().'/orders');
    $adminUrl = url(app()->getLocale().'/dashboard');
    $nav = $nav ?? '';
    $link = 'border-b-2 pb-1 text-[13px] tracking-wide transition-colors hover:text-accent';
    $on = 'border-accent text-sidebar-accent-foreground';
    $off = 'border-transparent text-sidebar-foreground';
    $iconBtn = 'inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium cursor-pointer transition-colors h-8 rounded-md text-xs px-2 text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground';
@endphp
<header class="sticky top-0 z-40 bg-sidebar text-sidebar-foreground shadow-soft">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-5">
        <a dir="ltr" class="flex flex-col items-center leading-none text-sidebar-accent-foreground" aria-label="FORMA INTERIORS" href="{{ $home }}">
            <span class="font-display text-3xl font-medium">FORMA</span>
            <span class="mt-1 text-[8px] tracking-[0.45em]">INTERIORS</span>
        </a>
        <nav id="public-desktop-nav" aria-label="التنقل الرئيسي" class="hidden items-center gap-6 lg:flex">
            <a class="{{ $link }} {{ $nav === 'home' ? $on : $off }}" href="{{ $home }}">الرئيسية</a>
            <a class="{{ $link }} {{ $nav === 'about' ? $on : $off }}" href="{{ $home }}#about">من نحن</a>
            <a class="{{ $link }} {{ $nav === 'home' ? $off : $off }}" href="{{ $home }}#services">خدماتنا</a>
            <a class="{{ $link }} {{ $nav === 'process' ? $on : $off }}" href="{{ $home }}#process">آلية العمل</a>
            <a class="{{ $link }} {{ $nav === 'projects' ? $on : $off }}" href="{{ $home }}/projects">المشاريع</a>
            <a class="{{ $link }} {{ $nav === 'packages' ? $on : $off }}" href="{{ $home }}/packages">الباقات</a>
            <a class="{{ $link }} {{ $nav === 'contact' ? $on : $off }}" href="{{ $nav === 'home' || $nav === 'packages' || $nav === 'projects' ? '#contact' : $home.'#contact' }}">تواصل معنا</a>
        </nav>
        <div class="flex items-center gap-1">
            <button id="lang-toggle" class="{{ $iconBtn }}" aria-label="language">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                <span id="lang-label">EN</span>
            </button>
            @if($loggedIn)
                <a class="{{ $iconBtn }}" href="{{ $ordersUrl }}" aria-label="طلباتي">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span class="hidden sm:inline" data-ar="طلباتي" data-en="My orders">طلباتي</span>
                </a>
            @endif
            @if($isAdmin)
                <a class="{{ $iconBtn }}" href="{{ $adminUrl }}" aria-label="الإدارة">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" aria-hidden="true"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    <span class="hidden sm:inline" data-ar="الإدارة" data-en="Admin">الإدارة</span>
                </a>
            @endif
            @if($loggedIn || $isAdmin)
                <form method="POST" action="{{ url(app()->getLocale().'/auth/logout') }}" class="m-0" onsubmit="localStorage.removeItem('authToken');localStorage.removeItem('token');localStorage.removeItem('user');">
                    @csrf
                    <button type="submit" class="{{ $iconBtn }} border border-sidebar-border" aria-label="تسجيل الخروج">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" aria-hidden="true"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                        <span class="hidden sm:inline" data-ar="تسجيل الخروج" data-en="Sign out">تسجيل الخروج</span>
                    </button>
                </form>
            @else
                <a class="{{ $iconBtn }}" href="{{ $home }}/auth" aria-label="تسجيل الدخول">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span class="hidden sm:inline">تسجيل الدخول</span>
                </a>
            @endif
            <a href="{{ $nav === 'home' || $nav === 'packages' || $nav === 'projects' ? '#contact' : $home.'#contact' }}" class="ms-1 hidden h-8 items-center justify-center bg-secondary px-3 text-xs text-secondary-foreground hover:bg-secondary/80 sm:inline-flex">احجز استشارة</a>
            <button type="button" id="public-menu-btn" class="public-menu-btn inline-flex h-9 w-9 items-center justify-center text-sidebar-foreground hover:bg-sidebar-accent lg:hidden" aria-label="القائمة" aria-expanded="false" aria-controls="public-mobile-menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="lucide lucide-menu" aria-hidden="true"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>
            </button>
        </div>
    </div>
    <nav id="public-mobile-menu" class="public-mobile-menu" hidden>
        <a href="{{ $home }}">الرئيسية</a>
        <a href="{{ $home }}#about">من نحن</a>
        <a href="{{ $home }}#services">خدماتنا</a>
        <a href="{{ $home }}#process">آلية العمل</a>
        <a href="{{ $home }}/projects">المشاريع</a>
        <a href="{{ $home }}/packages">الباقات</a>
        <a href="{{ $nav === 'home' || $nav === 'packages' || $nav === 'projects' ? '#contact' : $home.'#contact' }}">تواصل معنا</a>
        <a href="{{ $nav === 'home' || $nav === 'packages' || $nav === 'projects' ? '#contact' : $home.'#contact' }}" class="public-mobile-cta">احجز استشارة</a>
    </nav>
</header>
