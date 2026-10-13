@php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $status = $order->statusCopy();
    $step = $order->progressStep();
    $vat = (float) $order->price_incl - (float) $order->price_excl;
    $receipt = $order->files->firstWhere('kind', 'receipt');
    $plans = $order->files->filter(fn ($file) => $file->kind === 'plan' || $file->kind === null);
    $designs = $order->files->where('kind', 'design');
    $stageDateAr = $order->updated_at->locale('ar')->isoFormat('D MMMM YYYY');
    $stageDateEn = $order->updated_at->locale('en')->isoFormat('D MMMM YYYY');
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
    <link href="{{ asset('kaheel/home.css') }}?v=20" rel="stylesheet">
</head>
<body>
    @include('kaheel.partials.public-header')
    <main class="order-page">
        <div class="order-hero">
            <div>
                <h1 class="auth-title">{{ $order->number }}</h1>
                <p class="order-lead">{{ $order->compound_name }} · {{ $order->unit_no }}</p>
            </div>
            <span class="order-badge" data-ar="{{ $status['ar'] }}" data-en="{{ $status['en'] }}">{{ $status['ar'] }}</span>
        </div>

        <section class="order-card">
            @include('kaheel.partials.order-progress')
        </section>

        <section class="order-card">
            <h2 data-ar="تفاصيل الطلب" data-en="Order details">تفاصيل الطلب</h2>
            <dl class="order-rows">
                <div><dt data-ar="المشروع" data-en="Project">المشروع</dt><dd>{{ $order->compound_name }}</dd></div>
                <div><dt data-ar="الوحدة" data-en="Unit">الوحدة</dt><dd>{{ $order->unit_no }}</dd></div>
                <div><dt data-ar="الباقة" data-en="Package">الباقة</dt><dd class="is-primary" data-ar="{{ $order->package_name_ar }} — {{ $order->tier_name_ar }}" data-en="{{ $order->package_name_en }} — {{ $order->tier_name_en }}">{{ $order->package_name_ar }} — {{ $order->tier_name_ar }}</dd></div>
                <div><dt data-ar="السعر قبل الضريبة" data-en="Price before VAT">السعر قبل الضريبة</dt><dd>{{ $money($order->price_excl) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                <div><dt data-ar="ضريبة القيمة المضافة" data-en="VAT">ضريبة القيمة المضافة</dt><dd>{{ $money($vat) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                <div><dt data-ar="الإجمالي" data-en="Total">الإجمالي</dt><dd>{{ $money($order->price_incl) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                <div><dt data-ar="العربون" data-en="Deposit">العربون</dt><dd>{{ $money($order->deposit_amount) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
            </dl>
        </section>

        @if ($order->payment_status === 'pending')
            <section class="order-card">
                <h2 data-ar="بانتظار تأكيد الدفع" data-en="Payment under review">بانتظار تأكيد الدفع</h2>
                <p class="order-lead" data-ar="تم استلام الدفع وبانتظار تأكيد الإدارة." data-en="Your payment was received and is waiting for admin confirmation.">تم استلام الدفع وبانتظار تأكيد الإدارة.</p>
                <p class="order-doc-meta">{{ $order->payment_method === 'visa' ? 'Visa' : ($receipt?->original_name ?: $order->number) }} · {{ $money($order->deposit_amount) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>
            </section>
        @elseif ($step === 0)
            <section class="order-card">
                <h2><svg class="order-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg><span data-ar="ادفع العربون" data-en="Pay deposit">ادفع العربون</span></h2>
                @if ($order->payment_status === 'rejected' && $order->payment_note)
                    <p class="auth-alert is-error">{{ $order->payment_note }}</p>
                @endif
                <p class="order-pay-note" data-ar="الدفع هنا تجريبي لأغراض العرض، ويتم تسجيل رقم عملية وإيصال كما في التشغيل الفعلي." data-en="Payment here is a sandbox simulation; a transaction number and receipt are recorded as in production.">الدفع هنا تجريبي لأغراض العرض، ويتم تسجيل رقم عملية وإيصال كما في التشغيل الفعلي.</p>
                <a class="btn btn-lg btn-primary auth-submit order-pay-submit" href="{{ url(app()->getLocale().'/orders/'.$order->id.'/pay') }}" data-ar="ادفع العربون — {{ $money($order->deposit_amount) }} ريال" data-en="Pay deposit — {{ $money($order->deposit_amount) }} SAR">ادفع العربون — {{ $money($order->deposit_amount) }} ريال</a>
            </section>
        @else
            @if ($step <= 2)
            <section class="order-card">
                <h2 data-ar="جارٍ إعداد تصميمك" data-en="Your design is being prepared">جارٍ إعداد تصميمك</h2>
                <p class="order-lead" data-ar="فريق الإدارة يراجع طلبك والمخطط ويجهّز التصميم. ستتمكن من مراجعته ومناقشته هنا فور إرساله." data-en="The admin team is reviewing your order and plan, and preparing the design. You will be able to review it here once it is sent.">فريق الإدارة يراجع طلبك والمخطط ويجهّز التصميم. ستتمكن من مراجعته ومناقشته هنا فور إرساله.</p>
            </section>
            @endif
            <section class="order-card">
                <h2 data-ar="إيصال الدفع" data-en="Payment receipt">إيصال الدفع</h2>
                <div class="order-doc">
                    <p class="order-doc-title">{{ $receipt?->original_name ?: ($order->payment_method === 'visa' ? 'Visa' : $order->number) }}</p>
                    <p class="order-doc-meta">{{ $money($order->deposit_amount) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>
                    <p class="order-doc-meta" data-ar="{{ $stageDateAr }}" data-en="{{ $stageDateEn }}">{{ $stageDateAr }}</p>
                </div>
            </section>
        @endif

        @if ($designs->isNotEmpty() || $order->status === 'design_review')
            <section class="order-card">
                <h2 data-ar="التصميم" data-en="Design">التصميم</h2>
                @forelse ($designs as $file)
                    <a class="order-doc-title" href="{{ url(app()->getLocale().'/orders/'.$order->id.'/files/'.$file->id) }}" target="_blank" rel="noopener noreferrer">{{ $file->original_name }}</a>
                @empty
                    <p class="order-lead" data-ar="لم يُرسل التصميم بعد." data-en="The design has not been sent yet.">لم يُرسل التصميم بعد.</p>
                @endforelse
                @if ($order->status === 'design_review' && $order->design_status === 'sent')
                    <p class="order-lead" data-ar="راجع التصميم ثم أكّد موافقتك أو أرجعه للتعديل. لن تنتقل المرحلة قبل ذلك." data-en="Review the design, then approve it or send it back. The stage will not change before that.">راجع التصميم ثم أكّد موافقتك أو أرجعه للتعديل. لن تنتقل المرحلة قبل ذلك.</p>
                    <form method="post" action="{{ url(app()->getLocale().'/orders/'.$order->id.'/design') }}" class="order-actions">
                        @csrf
                        <button class="btn btn-lg btn-primary" type="submit" name="decision" value="approve" data-ar="أوافق على التصميم" data-en="Approve the design">أوافق على التصميم</button>
                        <button class="btn btn-lg btn-outline" type="submit" name="decision" value="reject" data-ar="أرفض وأرجع للتعديل" data-en="Reject and return">أرفض وأرجع للتعديل</button>
                    </form>
                @elseif ($order->design_status === 'approved')
                    <p class="order-lead" data-ar="تمت الموافقة على التصميم." data-en="The design is approved.">تمت الموافقة على التصميم.</p>
                @endif
            </section>
        @endif

        @if ($order->status === 'final_payment')
            <section class="order-card">
                <h2 data-ar="السداد النهائي" data-en="Final payment">السداد النهائي</h2>
                <p class="order-lead">{{ $money(max(0, (float) $order->price_incl + (float) $order->addons_total - (float) $order->deposit_amount)) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>
                @if ($order->final_payment_status === 'approved')
                    <p class="order-lead" data-ar="تم تأكيد السداد النهائي." data-en="The final payment is confirmed.">تم تأكيد السداد النهائي.</p>
                @elseif ($order->final_payment_status === 'pending')
                    <p class="order-lead" data-ar="تم إرسال السداد النهائي وبانتظار تأكيد الإدارة." data-en="The final payment was sent and is waiting for admin confirmation.">تم إرسال السداد النهائي وبانتظار تأكيد الإدارة.</p>
                @else
                    <p class="order-pay-note" data-ar="الدفع هنا تجريبي لأغراض العرض. لن تنتقل المرحلة قبل تأكيد الإدارة." data-en="Payment here is a sandbox simulation. The stage will not change before admin confirmation.">الدفع هنا تجريبي لأغراض العرض. لن تنتقل المرحلة قبل تأكيد الإدارة.</p>
                    <form method="post" action="{{ url(app()->getLocale().'/orders/'.$order->id.'/final') }}">
                        @csrf
                        <button class="btn btn-lg btn-primary auth-submit" type="submit" data-ar="إرسال السداد النهائي" data-en="Send final payment">إرسال السداد النهائي</button>
                    </form>
                @endif
            </section>
        @endif

        <section class="order-card">
            <h2><svg class="order-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg><span data-ar="مستندات الطلب" data-en="Order documents">مستندات الطلب</span></h2>
            <div class="order-docs">
                <article class="order-doc">
                    <div class="order-doc-row">
                        <span class="order-doc-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg></span>
                        <div class="min-w-0">
                            <p class="order-doc-title" data-ar="عقد خدمات التأثيث الموقّع" data-en="Signed furnishing agreement">عقد خدمات التأثيث الموقّع</p>
                            <p class="order-doc-meta" data-ar="مستند داخل النظام" data-en="In-app document">مستند داخل النظام</p>
                        </div>
                    </div>
                    <button class="btn btn-sm order-doc-preview" type="button" id="preview-contract"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg><span data-ar="معاينة" data-en="Preview">معاينة</span></button>
                    <pre class="order-contract" id="contract-preview" hidden>{{ $order->contract_body }}</pre>
                </article>
                @foreach ($plans as $file)
                    <a class="order-doc" href="{{ url(app()->getLocale().'/orders/'.$order->id.'/files/'.$file->id) }}" target="_blank" rel="noopener noreferrer">
                        <div class="order-doc-row">
                            <span class="order-doc-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg></span>
                            <div class="min-w-0">
                                <p class="order-doc-title">{{ $file->original_name }}</p>
                                <p class="order-doc-meta" data-ar="مخطط الوحدة" data-en="Unit plan">مخطط الوحدة</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="order-card">
            <h2 data-ar="مراحل الطلب" data-en="Order progress">مراحل الطلب</h2>
            <ol class="order-stages">
                @foreach (\App\Models\KaheelOrder::stageCopy() as $index => $stage)
                    <li class="{{ $index < $step ? 'is-on' : '' }}">
                        @if ($index < $step)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/></svg>
                        @endif
                        <span data-ar="{{ $stage['ar'] }}" data-en="{{ $stage['en'] }}">{{ $stage['ar'] }}</span>
                        @if ($index < $step)
                            <time data-ar="{{ $stageDateAr }}" data-en="{{ $stageDateEn }}">{{ $stageDateAr }}</time>
                        @endif
                    </li>
                @endforeach
            </ol>
            <p class="order-lead" data-ar="{{ $deliveryAr }}" data-en="{{ $deliveryEn }}">{{ $deliveryAr }}</p>
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
        const preview = document.getElementById("preview-contract");
        const contract = document.getElementById("contract-preview");
        if (preview && contract) {
            preview.addEventListener("click", () => {
                contract.hidden = !contract.hidden;
            });
        }
    </script>
</body>
</html>
