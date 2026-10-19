@php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $mode = $mode ?? ($order->status === 'final_payment' ? 'final' : 'deposit');
    $amount = $amount ?? ($mode === 'final'
        ? max(0, (float) $order->price_incl + (float) $order->addons_total - (float) $order->deposit_amount)
        : (float) $order->deposit_amount);
    $done = $mode === 'final'
        ? in_array($order->final_payment_status, ['pending', 'approved'], true)
        : ($order->status !== 'deposit_pending');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $mode === 'final' ? 'السداد النهائي' : 'دفع العربون' }} | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=32" rel="stylesheet">
</head>
<body>
    @include('kaheel.partials.public-header')
    <main class="order-page">
        <p class="order-lead">{{ $order->number }}</p>
        <h1 class="auth-title" data-ar="{{ $mode === 'final' ? 'ادفع المبلغ النهائي' : 'دفع العربون' }}" data-en="{{ $mode === 'final' ? 'Pay final amount' : 'Deposit payment' }}">{{ $mode === 'final' ? 'ادفع المبلغ النهائي' : 'دفع العربون' }}</h1>
        <p class="order-lead">{{ $money($amount) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>

        @if ($done)
            <section class="order-card">
                @if ($mode === 'final')
                    <p class="auth-alert is-success pay-success" data-ar="{{ $order->final_payment_status === 'approved' ? 'تم تأكيد السداد النهائي.' : 'تم إرسال السداد النهائي. انتظر موافقة من الإدارة.' }}" data-en="{{ $order->final_payment_status === 'approved' ? 'The final payment is confirmed.' : 'The final payment was sent. Please wait for admin approval.' }}">{{ $order->final_payment_status === 'approved' ? 'تم تأكيد السداد النهائي.' : 'تم إرسال السداد النهائي. انتظر موافقة من الإدارة.' }}</p>
                @else
                    <p class="auth-alert is-success pay-success" data-ar="{{ $order->payment_method === 'visa' ? 'تم تسجيل الدفع بالفيزا. انتظر موافقة من الإدارة.' : 'تم استلام الإيصال. انتظر موافقة من الإدارة.' }}" data-en="{{ $order->payment_method === 'visa' ? 'The visa payment was recorded. Please wait for admin approval.' : 'The receipt was received. Please wait for admin approval.' }}">{{ $order->payment_method === 'visa' ? 'تم تسجيل الدفع بالفيزا. انتظر موافقة من الإدارة.' : 'تم استلام الإيصال. انتظر موافقة من الإدارة.' }}</p>
                @endif
                <a class="btn btn-lg btn-primary auth-submit" href="{{ url(app()->getLocale().'/orders/'.$order->id) }}" data-ar="عرض الطلب" data-en="View order">عرض الطلب</a>
            </section>
        @else
            <section class="order-card">
                @if ($mode !== 'final' && $order->payment_status === 'rejected' && $order->payment_note)
                    <p class="auth-alert is-error">{{ $order->payment_note }}</p>
                @endif
                <h2 data-ar="اختر طريقة الدفع" data-en="Choose a payment method">اختر طريقة الدفع</h2>
                <p class="order-lead" data-ar="يمكنك التبديل بين الطريقتين في أي وقت." data-en="You can switch between the two methods at any time.">يمكنك التبديل بين الطريقتين في أي وقت.</p>
                <p class="order-pay-note" data-ar="الدفع هنا تجريبي لأغراض العرض، ويتم تسجيل رقم عملية وإيصال كما في التشغيل الفعلي." data-en="Payment here is a sandbox simulation; a transaction number and receipt are recorded as in production.">الدفع هنا تجريبي لأغراض العرض، ويتم تسجيل رقم عملية وإيصال كما في التشغيل الفعلي.</p>
                @if (session('pay_error'))
                    <p class="auth-alert is-error">{{ session('pay_error') }}</p>
                @endif
                <p class="auth-alert is-error" id="pay-file-error" hidden></p>
                <div class="pay-columns">
                    <div class="pay-column">
                        <button class="order-choice" type="button" id="choose-receipt">
                            <strong data-ar="رفع إيصال" data-en="Upload receipt">رفع إيصال</strong>
                            <span data-ar="صورة أو ملف PDF" data-en="Image or PDF">صورة أو ملف PDF</span>
                        </button>
                        <form class="pay-panel is-off" id="panel-receipt" method="post" action="{{ url(app()->getLocale().'/orders/'.$order->id.'/pay') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="lang" value="ar">
                            <input type="hidden" name="method" value="receipt">
                            <p class="order-lead" data-ar="أرفق الإيصال ثم اضغط موافق. الحد الأقصى 20 ميغابايت." data-en="Attach the receipt, then press OK. Maximum size is 20 MB.">أرفق الإيصال ثم اضغط موافق. الحد الأقصى 20 ميغابايت.</p>
                            <input class="order-file" id="receipt" name="receipt" type="file" accept="image/*,application/pdf" disabled>
                            <button class="btn btn-lg btn-primary auth-submit" id="receipt-ok" type="submit" disabled data-ar="موافق" data-en="OK">موافق</button>
                        </form>
                    </div>
                    <div class="pay-column">
                        <button class="order-choice" type="button" id="choose-visa">
                            <strong data-ar="فيزا" data-en="Visa">فيزا</strong>
                            <span data-ar="بطاقة بنكية" data-en="Bank card">بطاقة بنكية</span>
                        </button>
                        <form class="pay-panel is-off" id="panel-visa" method="post" action="{{ url(app()->getLocale().'/orders/'.$order->id.'/pay') }}">
                            @csrf
                            <input type="hidden" name="lang" value="ar">
                            <input type="hidden" name="method" value="visa">
                            <p class="order-lead" data-ar="أدخل بيانات البطاقة ثم اضغط موافق." data-en="Enter the card details, then press OK.">أدخل بيانات البطاقة ثم اضغط موافق.</p>
                            <div class="auth-form">
                                <label class="auth-field">
                                    <span data-ar="الاسم على البطاقة" data-en="Name on card">الاسم على البطاقة</span>
                                    <input id="visa-name" type="text" autocomplete="off" disabled>
                                </label>
                                <label class="auth-field">
                                    <span data-ar="رقم البطاقة" data-en="Card number">رقم البطاقة</span>
                                    <input id="visa-number" type="text" inputmode="numeric" maxlength="19" autocomplete="off" disabled>
                                </label>
                                <label class="auth-field">
                                    <span data-ar="تاريخ الانتهاء" data-en="Expiry">تاريخ الانتهاء</span>
                                    <input id="visa-expiry" type="text" inputmode="numeric" maxlength="5" autocomplete="off" disabled>
                                </label>
                                <label class="auth-field">
                                    <span>CVV</span>
                                    <input id="visa-cvv" type="text" inputmode="numeric" maxlength="4" autocomplete="off" disabled>
                                </label>
                                <button class="btn btn-lg btn-primary auth-submit" id="visa-ok" type="submit" disabled data-ar="موافق" data-en="OK">موافق</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        @endif
    </main>
    <script>
        const root = document.documentElement;
        let lang = "ar";

        function applyLang(next) {
            lang = next;
            root.lang = lang;
            root.dir = lang === "ar" ? "rtl" : "ltr";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                node.textContent = lang === "ar" ? node.dataset.ar : node.dataset.en;
            });
            document.querySelectorAll('input[name="lang"]').forEach((node) => { node.value = lang; });
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            localStorage.setItem("forma-lang", lang);
        }

        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(lang === "ar" ? "en" : "ar");
        });

        const chooseReceipt = document.getElementById("choose-receipt");
        const chooseVisa = document.getElementById("choose-visa");
        if (chooseReceipt && chooseVisa) {
            const panelReceipt = document.getElementById("panel-receipt");
            const panelVisa = document.getElementById("panel-visa");
            const receipt = document.getElementById("receipt");
            const receiptOk = document.getElementById("receipt-ok");
            const visaFields = ["visa-name", "visa-number", "visa-expiry", "visa-cvv"].map((id) => document.getElementById(id));
            const visaOk = document.getElementById("visa-ok");

            function setMode(mode) {
                const receiptOn = mode === "receipt";
                chooseReceipt.classList.toggle("is-on", receiptOn);
                chooseVisa.classList.toggle("is-on", !receiptOn);
                panelReceipt.classList.toggle("is-off", !receiptOn);
                panelVisa.classList.toggle("is-off", receiptOn);
                receipt.disabled = !receiptOn;
                receiptOk.disabled = !receiptOn || !receipt.files.length;
                visaFields.forEach((field) => { field.disabled = receiptOn; });
                visaOk.disabled = receiptOn;
            }

            chooseReceipt.addEventListener("click", () => setMode("receipt"));
            chooseVisa.addEventListener("click", () => setMode("visa"));
            receipt.addEventListener("change", () => {
                receiptOk.disabled = !receipt.files.length;
            });
            setMode("receipt");
        }

        applyLang(localStorage.getItem("forma-lang") === "en" ? "en" : "ar");
    </script>
</body>
</html>
