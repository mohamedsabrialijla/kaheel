@php
    $payAction = $payAction ?? url(app()->getLocale().'/orders/'.$order->id.'/pay');
@endphp
<h2 data-ar="اختر طريقة الدفع" data-en="Choose a payment method">اختر طريقة الدفع</h2>
<p class="order-lead" data-ar="يمكنك التبديل بين الطريقتين في أي وقت." data-en="You can switch between the two methods at any time.">يمكنك التبديل بين الطريقتين في أي وقت.</p>
<p class="order-pay-note" data-ar="الدفع هنا تجريبي لأغراض العرض، ويتم تسجيل رقم عملية وإيصال كما في التشغيل الفعلي." data-en="Payment here is a sandbox simulation; a transaction number and receipt are recorded as in production.">الدفع هنا تجريبي لأغراض العرض، ويتم تسجيل رقم عملية وإيصال كما في التشغيل الفعلي.</p>
@if (session('pay_error'))
    <p class="auth-alert is-error">{{ session('pay_error') }}</p>
@endif
<p class="auth-alert is-error" id="pay-file-error" hidden></p>
<div class="pay-columns">
    <div class="pay-column">
        <button class="order-choice is-on" type="button" id="choose-receipt">
            <strong data-ar="رفع إيصال" data-en="Upload receipt">رفع إيصال</strong>
            <span data-ar="صورة أو ملف PDF" data-en="Image or PDF">صورة أو ملف PDF</span>
        </button>
        <form class="pay-panel" id="panel-receipt" method="post" action="{{ $payAction }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="lang" value="ar">
            <input type="hidden" name="method" value="receipt">
            <p class="order-lead" data-ar="أرفق الإيصال ثم اضغط موافق. الحد الأقصى 20 ميغابايت." data-en="Attach the receipt, then press OK. Maximum size is 20 MB.">أرفق الإيصال ثم اضغط موافق. الحد الأقصى 20 ميغابايت.</p>
            <div class="ka-drop" data-ka-drop data-max="1" data-max-mb="20">
                <div class="ka-drop-zone" data-browse>
                    <strong data-ar="اسحب الإيصال هنا أو اضغط للاختيار" data-en="Drop the receipt here or click to browse">اسحب الإيصال هنا أو اضغط للاختيار</strong>
                    <span data-ar="صورة أو PDF" data-en="Image or PDF">صورة أو PDF</span>
                </div>
                <div class="ka-drop-list" data-list></div>
                <input class="order-file" id="receipt" name="receipt" type="file" accept="image/*,.jpg,.jpeg,.jfif,.png,.webp,.gif,.bmp,.tif,.tiff,.heic,.heif,.avif,.pdf,application/pdf">
            </div>
            <p class="order-lead" data-ar="الصيغ المقبولة: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF. الحد الأقصى 20 ميغابايت." data-en="Accepted formats: JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF, and PDF. Maximum size is 20 MB.">الصيغ المقبولة: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF. الحد الأقصى 20 ميغابايت.</p>
            <button class="btn btn-lg btn-primary auth-submit" id="receipt-ok" type="submit" disabled data-ar="موافق" data-en="OK">موافق</button>
        </form>
    </div>
    <div class="pay-column">
        <button class="order-choice" type="button" id="choose-visa">
            <strong data-ar="فيزا" data-en="Visa">فيزا</strong>
            <span data-ar="بطاقة بنكية" data-en="Bank card">بطاقة بنكية</span>
        </button>
        <form class="pay-panel is-off" id="panel-visa" method="post" action="{{ $payAction }}">
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
