<?php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $done = $order->status !== 'deposit_pending';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>دفع العربون | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/home.css')); ?>?v=17" rel="stylesheet">
</head>
<body>
    <?php echo $__env->make('kaheel.partials.public-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="order-page">
        <p class="order-lead"><?php echo e($order->number); ?></p>
        <h1 class="auth-title" data-ar="دفع العربون" data-en="Deposit payment">دفع العربون</h1>
        <p class="order-lead"><?php echo e($money($order->deposit_amount)); ?> <span data-ar="ريال" data-en="SAR">ريال</span></p>

        <?php if($done): ?>
            <section class="order-card">
                <p class="auth-alert is-success pay-success" data-ar="<?php echo e($order->payment_method === 'visa' ? 'تم تسجيل الدفع بالفيزا. انتظر موافقة من الإدارة.' : 'تم استلام الإيصال. انتظر موافقة من الإدارة.'); ?>" data-en="<?php echo e($order->payment_method === 'visa' ? 'The visa payment was recorded. Please wait for admin approval.' : 'The receipt was received. Please wait for admin approval.'); ?>"><?php echo e($order->payment_method === 'visa' ? 'تم تسجيل الدفع بالفيزا. انتظر موافقة من الإدارة.' : 'تم استلام الإيصال. انتظر موافقة من الإدارة.'); ?></p>
                <a class="btn btn-lg btn-primary auth-submit" href="<?php echo e(url(app()->getLocale().'/orders')); ?>" data-ar="عرض الطلبات" data-en="View orders">عرض الطلبات</a>
            </section>
        <?php else: ?>
            <section class="order-card">
                <?php if($order->payment_status === 'rejected' && $order->payment_note): ?>
                    <p class="auth-alert is-error"><?php echo e($order->payment_note); ?></p>
                <?php endif; ?>
                <h2 data-ar="اختر طريقة الدفع" data-en="Choose a payment method">اختر طريقة الدفع</h2>
                <p class="order-lead" data-ar="يمكنك التبديل بين الطريقتين في أي وقت." data-en="You can switch between the two methods at any time.">يمكنك التبديل بين الطريقتين في أي وقت.</p>
                <?php if(session('pay_error')): ?>
                    <p class="auth-alert is-error"><?php echo e(session('pay_error')); ?></p>
                <?php endif; ?>
                <p class="auth-alert is-error" id="pay-file-error" hidden></p>
                <div class="pay-columns">
                    <div class="pay-column">
                        <button class="order-choice" type="button" id="choose-receipt">
                            <strong data-ar="رفع إيصال" data-en="Upload receipt">رفع إيصال</strong>
                            <span data-ar="صورة أو ملف PDF" data-en="Image or PDF">صورة أو ملف PDF</span>
                        </button>
                        <form class="pay-panel is-off" id="panel-receipt" method="post" action="<?php echo e(url(app()->getLocale().'/orders/'.$order->id.'/pay')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="lang" value="ar">
                            <input type="hidden" name="method" value="receipt">
                            <p class="order-lead" data-ar="أرفق إيصال العربون ثم اضغط موافق. الحد الأقصى 20 ميغابايت." data-en="Attach the deposit receipt, then press OK. Maximum size is 20 MB.">أرفق إيصال العربون ثم اضغط موافق. الحد الأقصى 20 ميغابايت.</p>
                            <input class="order-file" id="receipt" name="receipt" type="file" accept="image/*,application/pdf" disabled>
                            <button class="btn btn-lg btn-primary auth-submit" id="receipt-ok" type="submit" disabled data-ar="موافق" data-en="OK">موافق</button>
                        </form>
                    </div>
                    <div class="pay-column">
                        <button class="order-choice" type="button" id="choose-visa">
                            <strong data-ar="فيزا" data-en="Visa">فيزا</strong>
                            <span data-ar="بطاقة بنكية" data-en="Bank card">بطاقة بنكية</span>
                        </button>
                        <form class="pay-panel is-off" id="panel-visa" method="post" action="<?php echo e(url(app()->getLocale().'/orders/'.$order->id.'/pay')); ?>">
                            <?php echo csrf_field(); ?>
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
        <?php endif; ?>
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
            const label = document.getElementById("lang-label");
            if (label) label.textContent = lang === "ar" ? "EN" : "ع";
            document.querySelectorAll('input[name="lang"]').forEach((node) => {
                node.value = lang;
            });
            localStorage.setItem("forma-lang", lang);
        }

        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(lang === "ar" ? "en" : "ar");
        });

        const receiptChoice = document.getElementById("choose-receipt");
        const visaChoice = document.getElementById("choose-visa");
        const receiptPanel = document.getElementById("panel-receipt");
        const visaPanel = document.getElementById("panel-visa");
        const receiptInput = document.getElementById("receipt");
        const receiptOk = document.getElementById("receipt-ok");
        const visaInputs = ["visa-name", "visa-number", "visa-expiry", "visa-cvv"].map((id) => document.getElementById(id));
        const visaOk = document.getElementById("visa-ok");

        function visaReady() {
            return visaInputs.every((input) => input && input.value.trim() !== "");
        }

        function selectMethod(method) {
            const receipt = method === "receipt";
            receiptChoice.classList.toggle("is-on", receipt);
            visaChoice.classList.toggle("is-on", !receipt);
            receiptPanel.classList.toggle("is-off", !receipt);
            visaPanel.classList.toggle("is-off", receipt);
            receiptInput.disabled = !receipt;
            receiptOk.disabled = !receipt || !receiptInput.files.length;
            visaInputs.forEach((input) => {
                if (input) input.disabled = receipt;
            });
            if (visaOk) visaOk.disabled = receipt || !visaReady();
        }

        if (receiptChoice) {
            receiptChoice.addEventListener("click", () => selectMethod("receipt"));
            visaChoice.addEventListener("click", () => selectMethod("visa"));
            receiptInput.addEventListener("change", () => {
                receiptOk.disabled = receiptInput.disabled || !receiptInput.files.length;
            });
            receiptPanel.addEventListener("submit", (event) => {
                const file = receiptInput.files[0];
                if (file && file.size > 20 * 1024 * 1024) {
                    event.preventDefault();
                    const alertBox = document.getElementById("pay-file-error");
                    if (alertBox) {
                        alertBox.hidden = false;
                        alertBox.textContent = lang === "ar"
                            ? "الإيصال أكبر من 20 ميغابايت. الطلب محفوظ، اختر ملفاً أصغر."
                            : "The receipt is larger than 20 MB. The order is saved; choose a smaller file.";
                    }
                }
            });
            visaInputs.forEach((input) => {
                if (!input) return;
                input.addEventListener("input", () => {
                    if (visaOk) visaOk.disabled = input.disabled || !visaReady();
                });
            });
        }

        applyLang(localStorage.getItem("forma-lang") === "en" ? "en" : "ar");
    </script>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/order-pay.blade.php ENDPATH**/ ?>