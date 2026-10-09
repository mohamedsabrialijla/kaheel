@php
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $home = url(app()->getLocale());
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $package->name_ar }} | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=10" rel="stylesheet">
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

    <main class="pkg-page">
        <p class="pkg-kicker">FORMA BY AZURE · KAHEEL</p>
        <h1>{{ $package->code }}</h1>
        <p class="pkg-sub" data-ar="وحدة نموذجية — تم التسليم" data-en="Show unit — delivered">وحدة نموذجية — تم التسليم</p>

        <section class="pkg-gallery" aria-label="{{ $package->code }}">
            @foreach ($gallery as $shot)
                <figure class="pkg-shot">
                    <img src="{{ asset('kaheel/'.$shot['file']) }}" alt="{{ $shot['ar'] }}" data-alt-ar="{{ $shot['ar'] }}" data-alt-en="{{ $shot['en'] }}">
                    <figcaption data-ar="{{ $shot['ar'] }}" data-en="{{ $shot['en'] }}">{{ $shot['ar'] }}</figcaption>
                </figure>
            @endforeach
        </section>

        <section class="pkg-prices">
            <h2 data-ar="جدول الأسعار" data-en="Price list">جدول الأسعار</h2>
            <div class="tier-grid">
                @foreach ($package->tiers as $tier)
                    <div class="tier-card">
                        <p class="tier-name" data-ar="{{ $tier->name_ar }}" data-en="{{ $tier->name_en }}">{{ $tier->name_ar }}</p>
                        <p class="tier-desc" data-ar="{{ $tier->description_ar }}" data-en="{{ $tier->description_en }}">{{ $tier->description_ar }}</p>
                        <p class="tier-label" data-ar="قبل الضريبة" data-en="Excl. VAT">قبل الضريبة</p>
                        <p class="tier-amount">{{ $money($tier->price_excl_vat) }}</p>
                        <div class="tier-rule"></div>
                        <p class="tier-label is-primary" data-ar="شامل الضريبة" data-en="Incl. VAT">شامل الضريبة</p>
                        <p class="tier-amount is-primary">{{ $money($tier->price_incl_vat) }}</p>
                        <p class="tier-label" data-ar="محتويات الباقة" data-en="Package contents">محتويات الباقة</p>
                        <ul class="tier-list">
                            @foreach ($tier->items as $item)
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                    <span>
                                        <span class="item-title" data-ar="{{ $item->name_ar }}" data-en="{{ $item->name_en }}">{{ $item->name_ar }}</span>
                                        <span class="item-spec" data-ar="{{ $item->product_name_ar }}" data-en="{{ $item->product_name_en }}">{{ $item->product_name_ar }}</span>
                                        <span class="item-qty" data-ar="{{ rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }} {{ $item->unit_label_ar }}" data-en="{{ rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }} {{ $item->unit_label_en }}">{{ rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }} {{ $item->unit_label_ar }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
            <p class="pkg-note" data-ar="{{ $deliveryAr }}" data-en="{{ $deliveryEn }}">{{ $deliveryAr }}</p>
            <a class="btn btn-lg btn-primary pkg-order" href="{{ url(app()->getLocale().'/auth?pkg='.$package->code.'&redirect=/order') }}" data-ar="اطلب الآن" data-en="Order now">اطلب الآن</a>
        </section>
    </main>

    <script>
        const root = document.documentElement;

        function applyLang(lang) {
            root.lang = lang;
            root.dir = lang === "ar" ? "rtl" : "ltr";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                node.textContent = lang === "ar" ? node.dataset.ar : node.dataset.en;
            });
            document.querySelectorAll("[data-alt-ar]").forEach((node) => {
                node.alt = lang === "ar" ? node.dataset.altAr : node.dataset.altEn;
            });
            document.title = (lang === "ar" ? @json($package->name_ar) : @json($package->name_en)) + " | FORMA BY AZURE – KAHEEL";
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            localStorage.setItem("forma-lang", lang);
        }

        const stored = localStorage.getItem("forma-lang");
        applyLang(stored === "en" ? "en" : "ar");
        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(root.lang === "ar" ? "en" : "ar");
        });
    </script>
</body>
</html>
