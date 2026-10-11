@php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $order->number }} | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=13" rel="stylesheet">
</head>
<body>
    @include('kaheel.partials.public-header')
    <main class="order-page">
        <p class="order-lead">{{ $order->number }}</p>
        <h1 class="auth-title" data-ar="تفاصيل الطلب" data-en="Order details">تفاصيل الطلب</h1>
        <section class="order-card">
            <dl class="order-rows">
                <div><dt data-ar="المشروع" data-en="Project">المشروع</dt><dd>{{ $order->compound_name }}</dd></div>
                <div><dt data-ar="الوحدة" data-en="Unit">الوحدة</dt><dd>{{ $order->unit_no }}</dd></div>
                <div><dt data-ar="الباقة" data-en="Package">الباقة</dt><dd data-ar="{{ $order->package_name_ar }} — {{ $order->tier_name_ar }}" data-en="{{ $order->package_name_en }} — {{ $order->tier_name_en }}">{{ $order->package_name_ar }} — {{ $order->tier_name_ar }}</dd></div>
                <div><dt data-ar="الإجمالي" data-en="Total">الإجمالي</dt><dd class="is-strong">{{ $money($order->price_incl) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                <div><dt data-ar="العربون" data-en="Deposit">العربون</dt><dd class="is-strong">{{ $money($order->deposit_amount) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                <div><dt data-ar="الحالة" data-en="Status">الحالة</dt><dd data-ar="بانتظار العربون" data-en="Awaiting deposit">بانتظار العربون</dd></div>
            </dl>
            @if ($order->bedrooms_number || $order->squar_meters)
                <p class="order-lead">
                    @if ($order->bedrooms_number){{ $order->bedrooms_number }} <span data-ar="غرف" data-en="bedrooms">غرف</span>@endif
                    @if ($order->squar_meters) · {{ $order->squar_meters }} <span data-ar="م²" data-en="m²">م²</span>@endif
                </p>
            @endif
            <h3 data-ar="العقد" data-en="Contract">العقد</h3>
            <pre class="order-contract">{{ $order->contract_body }}</pre>
            @if ($order->files->isNotEmpty())
                <h3 data-ar="مخطط الوحدة" data-en="Unit plan">مخطط الوحدة</h3>
                <ul class="order-list">
                    @foreach ($order->files as $file)
                        <li><span>{{ $file->original_name }}</span></li>
                    @endforeach
                </ul>
            @endif
        </section>
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
        if (localStorage.getItem("forma-lang") === "en") document.getElementById("lang-toggle").click();
    </script>
</body>
</html>
