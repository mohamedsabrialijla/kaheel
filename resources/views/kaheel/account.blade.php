@php
    $home = url(app()->getLocale());
    $compounds = $customer ? ($customer->units ?? []) : [];
    $displayName = $customer ? ($customer->name ?: $customer->username) : '';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>وحداتي | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=30" rel="stylesheet">
</head>
<body>
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

    <main class="account-page">
        <h1 class="auth-title" data-ar="وحداتي" data-en="My units">وحداتي</h1>
        <p class="auth-lead" data-ar="{{ $displayName !== '' ? 'الوحدات المسجلة باسم '.$displayName : 'وحدات العميل تظهر بعد تسجيل الدخول.' }}" data-en="{{ $displayName !== '' ? 'Units registered to '.$displayName : 'Customer units appear after sign-in.' }}">
            {{ $displayName !== '' ? 'الوحدات المسجلة باسم '.$displayName : 'وحدات العميل تظهر بعد تسجيل الدخول.' }}
        </p>

        @forelse ($compounds as $compound)
            <section class="account-compound">
                <h2>{{ $compound['display_name'] ?: '—' }}</h2>
                <p class="account-meta">
                    <span data-ar="رقم المجمع" data-en="Compound">رقم المجمع</span>
                    {{ $compound['compound_number'] }}
                    · {{ $compound['owned_units_count'] }}
                    <span data-ar="وحدة" data-en="units">وحدة</span>
                </p>
                <ul class="account-units">
                    @foreach ($compound['units'] as $unit)
                        <li>
                            <strong>{{ $unit['unit_no'] }}</strong>
                            <span>
                                @if ($unit['bedrooms_number'])
                                    {{ $unit['bedrooms_number'] }} <span data-ar="غرف" data-en="beds">غرف</span>
                                @endif
                                @if ($unit['squar_meters'])
                                    · {{ $unit['squar_meters'] }} <span data-ar="م²" data-en="m²">م²</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <section class="account-compound">
                <p class="account-meta" data-ar="لا توجد وحدات مرتبطة بهذا الحساب." data-en="No units are linked to this account.">لا توجد وحدات مرتبطة بهذا الحساب.</p>
            </section>
        @endforelse
    </main>
    <script>
        const root = document.documentElement;
        document.getElementById("lang-toggle").addEventListener("click", () => {
            const en = root.lang !== "en";
            root.lang = en ? "en" : "ar";
            root.dir = en ? "ltr" : "rtl";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                node.textContent = en ? node.dataset.en : node.dataset.ar;
            });
            document.getElementById("lang-label").textContent = en ? "ع" : "EN";
            localStorage.setItem("forma-lang", en ? "en" : "ar");
        });
        if (localStorage.getItem("forma-lang") === "en") {
            document.getElementById("lang-toggle").click();
        }
    </script>
</body>
</html>
