@include('kaheel.partials.native-validation')
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="{{ $home }}" aria-label="FORMA BY AZURE — KAHEEL">
            <span class="brand-mark">
                <span class="brand-forma">FORMA</span>
                <span class="brand-azure">BY AZURE</span>
            </span>
            <span class="brand-name">KAHEEL</span>
        </a>
        <nav class="nav" aria-label="main">
            <a class="btn btn-sm nav-home" href="{{ $home }}" data-ar="الرئيسية" data-en="Home">الرئيسية</a>
            @include('kaheel.partials.role-links')
            @include('kaheel.partials.notice-bell')
            <button class="btn btn-sm" type="button" id="lang-toggle" aria-label="language">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m5 8 6 6"/>
                    <path d="m4 14 6-6 2-3"/>
                    <path d="M2 5h12"/>
                    <path d="M7 2h1"/>
                    <path d="m22 22-5-10-5 10"/>
                    <path d="M14 18h6"/>
                </svg>
                <span id="lang-label">EN</span>
            </button>
            @include('kaheel.partials.account-button')
        </nav>
    </div>
</header>
