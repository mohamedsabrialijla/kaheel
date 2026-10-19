@php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلباتي | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=30" rel="stylesheet">
</head>
<body>
    @include('kaheel.partials.public-header')
    <main class="order-page">
        <div class="orders-head">
            <h1 class="auth-title" data-ar="طلباتي" data-en="My orders">طلباتي</h1>
            <a class="btn btn-primary orders-new" href="{{ url(app()->getLocale().'/order') }}">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                <span data-ar="طلب جديد" data-en="New order">طلب جديد</span>
            </a>
        </div>
        @forelse ($orders as $order)
            @php $status = $order->statusCopy(); @endphp
            <a class="order-list-card" href="{{ url(app()->getLocale().'/orders/'.$order->id) }}">
                <div class="min-w-0">
                    <strong>{{ $order->number }}@if ($order->unread_count > 0)<span class="order-unread">{{ $order->unread_count }}</span>@endif</strong>
                    <span class="order-muted">{{ $order->compound_name }} · {{ $order->unit_no }} · <span data-ar="{{ $order->package_name_ar }}" data-en="{{ $order->package_name_en }}">{{ $order->package_name_ar }}</span></span>
                </div>
                <div class="order-list-side">
                    <p class="order-list-amount">{{ $money($order->price_incl) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>
                    <span class="order-badge" data-ar="{{ $status['ar'] }}" data-en="{{ $status['en'] }}">{{ $status['ar'] }}</span>
                </div>
                @include('kaheel.partials.order-progress')
            </a>
        @empty
            <section class="order-card">
                <p class="order-lead" data-ar="لا توجد طلبات بعد" data-en="No orders yet">لا توجد طلبات بعد</p>
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
        if (localStorage.getItem("forma-lang") === "en") document.getElementById("lang-toggle").click();
    </script>
</body>
</html>
