<?php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $mode = $mode ?? ($order->status === 'final_payment' ? 'final' : 'deposit');
    $amount = $amount ?? ($mode === 'final'
        ? max(0, (float) $order->price_incl + (float) $order->addons_total - (float) $order->deposit_amount)
        : (float) $order->deposit_amount);
    $done = $mode === 'final'
        ? in_array($order->final_payment_status, ['pending', 'approved'], true)
        : ($order->status !== 'deposit_pending');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($mode === 'final' ? 'السداد النهائي' : 'دفع العربون'); ?> | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/home.css')); ?>?v=40" rel="stylesheet">
</head>
<body>
    <?php echo $__env->make('kaheel.partials.public-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="order-page">
        <p class="order-lead"><?php echo e($order->number); ?></p>
        <h1 class="auth-title" data-ar="<?php echo e($mode === 'final' ? 'ادفع المبلغ النهائي' : 'دفع العربون'); ?>" data-en="<?php echo e($mode === 'final' ? 'Pay final amount' : 'Deposit payment'); ?>"><?php echo e($mode === 'final' ? 'ادفع المبلغ النهائي' : 'دفع العربون'); ?></h1>
        <p class="order-lead"><?php echo e($money($amount)); ?> <span data-ar="ريال" data-en="SAR">ريال</span></p>

        <?php if($done): ?>
            <section class="order-card">
                <?php if($mode === 'final'): ?>
                    <p class="auth-alert is-success pay-success" data-ar="<?php echo e($order->final_payment_status === 'approved' ? 'تم تأكيد السداد النهائي.' : 'تم إرسال السداد النهائي. انتظر موافقة من الإدارة.'); ?>" data-en="<?php echo e($order->final_payment_status === 'approved' ? 'The final payment is confirmed.' : 'The final payment was sent. Please wait for admin approval.'); ?>"><?php echo e($order->final_payment_status === 'approved' ? 'تم تأكيد السداد النهائي.' : 'تم إرسال السداد النهائي. انتظر موافقة من الإدارة.'); ?></p>
                <?php else: ?>
                    <p class="auth-alert is-success pay-success" data-ar="<?php echo e($order->payment_method === 'visa' ? 'تم تسجيل الدفع بالفيزا. انتظر موافقة من الإدارة.' : 'تم استلام الإيصال. انتظر موافقة من الإدارة.'); ?>" data-en="<?php echo e($order->payment_method === 'visa' ? 'The visa payment was recorded. Please wait for admin approval.' : 'The receipt was received. Please wait for admin approval.'); ?>"><?php echo e($order->payment_method === 'visa' ? 'تم تسجيل الدفع بالفيزا. انتظر موافقة من الإدارة.' : 'تم استلام الإيصال. انتظر موافقة من الإدارة.'); ?></p>
                <?php endif; ?>
                <a class="btn btn-lg btn-primary auth-submit" href="<?php echo e(url(app()->getLocale().'/orders/'.$order->id)); ?>" data-ar="عرض الطلب" data-en="View order">عرض الطلب</a>
            </section>
        <?php else: ?>
            <section class="order-card">
                <?php if($mode !== 'final' && $order->payment_status === 'rejected' && $order->payment_note): ?>
                    <p class="auth-alert is-error"><?php echo e($order->payment_note); ?></p>
                <?php endif; ?>
                <?php if($mode === 'final' && $order->final_payment_status === 'rejected' && $order->final_payment_note): ?>
                    <p class="auth-alert is-error"><?php echo e($order->final_payment_note); ?></p>
                <?php endif; ?>
                <?php echo $__env->make('kaheel.partials.payment-methods', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
            document.querySelectorAll('input[name="lang"]').forEach((node) => { node.value = lang; });
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            localStorage.setItem("forma-lang", lang);
        }

        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(lang === "ar" ? "en" : "ar");
        });

        applyLang(localStorage.getItem("forma-lang") === "en" ? "en" : "ar");
    </script>
    <?php echo $__env->make('kaheel.partials.payment-methods-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/order-pay.blade.php ENDPATH**/ ?>