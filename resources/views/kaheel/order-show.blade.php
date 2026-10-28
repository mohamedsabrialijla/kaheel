@php
    $home = url(app()->getLocale());
    $money = fn ($amount) => number_format((float) $amount, 2, '.', ',');
    $status = $order->statusCopy();
    $step = $order->progressStep();
    $vat = (float) $order->price_incl - (float) $order->price_excl;
    $receipt = $order->files->firstWhere('kind', 'receipt');
    $receiptFiles = $order->files->where('kind', 'receipt')->values();
    $finalReceiptFiles = $order->files->where('kind', 'final_receipt')->values();
    $plans = $order->files->filter(fn ($file) => $file->kind === 'plan' || $file->kind === null);
    $designs = $order->files->where('kind', 'design');
    $payLabel = function (string $status) {
        return match ($status) {
            'approved' => ['ar' => 'تم التأكيد', 'en' => 'Confirmed'],
            'rejected' => ['ar' => 'مرفوض', 'en' => 'Rejected'],
            'pending' => ['ar' => 'بانتظار التأكيد', 'en' => 'Pending'],
            default => ['ar' => '—', 'en' => '—'],
        };
    };
    $paymentRows = collect();
    if ($order->payment_method) {
        $sortedReceipts = $receiptFiles->sortBy('id')->values();
        $lastReceiptId = optional($sortedReceipts->last())->id;
        if ($receiptFiles->isEmpty()) {
            $label = $payLabel((string) $order->payment_status);
            $paymentRows->push([
                'kind' => 'deposit',
                'title_ar' => 'عربون',
                'title_en' => 'Deposit',
                'ref' => $order->payment_method === 'visa' ? 'Visa' : $order->number,
                'amount' => (float) $order->deposit_amount,
                'method_ar' => $order->payment_method === 'visa' ? 'فيزا' : 'رفع إيصال',
                'method_en' => $order->payment_method === 'visa' ? 'Visa' : 'Receipt',
                'status' => $order->payment_status,
                'status_ar' => $label['ar'],
                'status_en' => $label['en'],
                'at' => $order->updated_at,
                'url' => null,
            ]);
        }
        foreach ($receiptFiles as $file) {
            $state = $file->resolvedStatus((int) $file->id === (int) $lastReceiptId ? $order->payment_status : 'rejected');
            $label = $payLabel($state);
            $paymentRows->push([
                'kind' => 'deposit',
                'title_ar' => 'عربون',
                'title_en' => 'Deposit',
                'ref' => $file->original_name,
                'amount' => (float) $order->deposit_amount,
                'method_ar' => 'رفع إيصال',
                'method_en' => 'Receipt',
                'status' => $state,
                'status_ar' => $label['ar'],
                'status_en' => $label['en'],
                'at' => $file->created_at,
                'url' => url(app()->getLocale().'/orders/'.$order->id.'/files/'.$file->id),
            ]);
        }
    }
    if (in_array($order->final_payment_status, ['pending', 'approved', 'rejected'], true)) {
        $finalDue = max(0, (float) $order->price_incl + (float) $order->addons_total - (float) $order->deposit_amount);
        $finalMethodAr = $order->final_payment_method === 'receipt' ? 'رفع إيصال' : 'فيزا';
        $finalMethodEn = $order->final_payment_method === 'receipt' ? 'Receipt' : 'Visa';
        $sortedFinal = $finalReceiptFiles->sortBy('id')->values();
        $lastFinalId = optional($sortedFinal->last())->id;
        if ($finalReceiptFiles->isEmpty()) {
            $label = $payLabel((string) $order->final_payment_status);
            $paymentRows->push([
                'kind' => 'final',
                'title_ar' => 'السداد النهائي',
                'title_en' => 'Final payment',
                'ref' => $order->final_payment_method === 'visa' ? 'Visa' : 'FINAL-'.$order->number,
                'amount' => $finalDue,
                'method_ar' => $finalMethodAr,
                'method_en' => $finalMethodEn,
                'status' => $order->final_payment_status,
                'status_ar' => $label['ar'],
                'status_en' => $label['en'],
                'at' => $order->updated_at,
                'url' => null,
            ]);
        }
        foreach ($finalReceiptFiles as $file) {
            $state = $file->resolvedStatus((int) $file->id === (int) $lastFinalId ? $order->final_payment_status : 'rejected');
            $label = $payLabel($state);
            $paymentRows->push([
                'kind' => 'final',
                'title_ar' => 'السداد النهائي',
                'title_en' => 'Final payment',
                'ref' => $file->original_name,
                'amount' => $finalDue,
                'method_ar' => 'رفع إيصال',
                'method_en' => 'Receipt',
                'status' => $state,
                'status_ar' => $label['ar'],
                'status_en' => $label['en'],
                'at' => $file->created_at,
                'url' => url(app()->getLocale().'/orders/'.$order->id.'/files/'.$file->id),
            ]);
        }
    }
    $components = collect($order->design_components ?? [])->values();
    $designApproved = $order->design_status === 'approved' || $order->design_signed_at;
    $designRejected = $order->design_status === 'rejected';
    $designRejectNote = optional($order->events->sortByDesc('id')->first(fn ($event) => str_starts_with((string) $event->note, 'رفض العميل للتصميم')))->note;
    $designRejectNote = $designRejectNote ? trim(preg_replace('/^رفض العميل للتصميم:\s*/u', '', $designRejectNote)) : null;
    $designReviewOpen = $order->status === 'design_review' && !$designApproved;
    $showDesignPanel = $designs->isNotEmpty() || $order->status === 'design_review' || $designApproved || $designRejected || $components->isNotEmpty();
    $stageDateAr = $order->updated_at->locale('ar')->isoFormat('D MMMM YYYY، h:mm A');
    $stageDateEn = $order->updated_at->locale('en')->isoFormat('D MMMM YYYY, h:mm A');
    $signedAtAr = $order->design_signed_at?->locale('ar')->isoFormat('D MMMM YYYY');
    $signedAtEn = $order->design_signed_at?->locale('en')->isoFormat('D MMMM YYYY');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $order->number }} | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=45" rel="stylesheet">
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
                <div><dt data-ar="العربون" data-en="Deposit">العربون</dt><dd class="is-primary">{{ $money($order->deposit_amount) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                @if ((float) $order->addons_total > 0)
                    <div><dt data-ar="التعديلات الإضافية" data-en="Add-ons">التعديلات الإضافية</dt><dd>{{ $money($order->addons_total) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                @endif
                @if ($order->design_signed_at)
                    <div><dt data-ar="الإجمالي النهائي" data-en="Final total">الإجمالي النهائي</dt><dd class="is-primary">{{ $money((float) $order->price_incl + (float) $order->addons_total) }} <span data-ar="ريال" data-en="SAR">ريال</span></dd></div>
                @endif
            </dl>
        </section>

        @if ($order->payment_status === 'pending')
            <section class="order-card">
                <h2 data-ar="بانتظار تأكيد الدفع" data-en="Payment under review">بانتظار تأكيد الدفع</h2>
                <p class="order-lead" data-ar="تم استلام الدفع وبانتظار تأكيد الإدارة." data-en="Your payment was received and is waiting for admin confirmation.">تم استلام الدفع وبانتظار تأكيد الإدارة.</p>
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
        @elseif ($step <= 2)
            <section class="order-card">
                <h2><svg class="order-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72"/><path d="m14 7 3 3"/><path d="M5 6v4"/><path d="M19 14v4"/><path d="M10 2v2"/><path d="M7 8H3"/><path d="M21 16h-4"/><path d="M11 3H9"/></svg><span data-ar="جارٍ إعداد تصميمك" data-en="Your design is being prepared">جارٍ إعداد تصميمك</span></h2>
                <p class="order-lead" data-ar="فريق الإدارة يراجع طلبك والمخطط ويجهّز التصميم. ستتمكن من مراجعته ومناقشته هنا فور إرساله." data-en="The admin team is reviewing your order and plan, and preparing the design. You will be able to review it here once it is sent.">فريق الإدارة يراجع طلبك والمخطط ويجهّز التصميم. ستتمكن من مراجعته ومناقشته هنا فور إرساله.</p>
            </section>
        @endif

        @if ($paymentRows->isNotEmpty())
            <section class="order-card">
                <h2 data-ar="الإيصالات" data-en="Payments">الإيصالات</h2>
                <div class="order-pay-list">
                    @foreach ($paymentRows as $row)
                        @php
                            $rowDateAr = optional($row['at'])->locale('ar')->isoFormat('D MMMM YYYY، h:mm A');
                            $rowDateEn = optional($row['at'])->locale('en')->isoFormat('D MMMM YYYY, h:mm A');
                            $rejected = ($row['status'] ?? '') === 'rejected';
                        @endphp
                        <article class="order-pay-row {{ $rejected ? 'is-rejected' : '' }}">
                            <div class="order-pay-row-main">
                                <p class="order-doc-title" data-ar="{{ $row['title_ar'] }} — {{ $row['ref'] }}" data-en="{{ $row['title_en'] }} — {{ $row['ref'] }}">{{ $row['title_ar'] }} — {{ $row['ref'] }}</p>
                                <p class="order-doc-meta">
                                    <span data-ar="{{ $row['method_ar'] }}" data-en="{{ $row['method_en'] }}">{{ $row['method_ar'] }}</span>
                                    ·
                                    <span class="order-pay-status {{ $rejected ? 'is-rejected' : '' }}" data-ar="{{ $row['status_ar'] }}" data-en="{{ $row['status_en'] }}">{{ $row['status_ar'] }}</span>
                                    ·
                                    <span data-ar="{{ $rowDateAr }}" data-en="{{ $rowDateEn }}">{{ $rowDateAr }}</span>
                                </p>
                            </div>
                            <div class="order-pay-row-side">
                                <strong>{{ $money($row['amount']) }} <span data-ar="ريال" data-en="SAR">ريال</span></strong>
                                @if ($row['url'])
                                    <a class="btn btn-sm btn-outline" href="{{ $row['url'] }}" target="_blank" rel="noopener noreferrer" data-ar="عرض" data-en="View">عرض</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($showDesignPanel)
            <section class="order-card order-design-card" id="order-design"
                data-review="{{ $designReviewOpen ? '1' : '0' }}"
                data-has-components="{{ $components->isNotEmpty() ? '1' : '0' }}"
                data-price="{{ (float) $order->price_incl }}"
                data-deposit="{{ (float) $order->deposit_amount }}"
                data-save="{{ url(app()->getLocale().'/orders/'.$order->id.'/design/selections') }}"
                data-confirm="{{ url(app()->getLocale().'/orders/'.$order->id.'/design') }}"
                data-token="{{ csrf_token() }}"
                data-components='@json($components)'>
                @if ($designReviewOpen)
                    <div class="order-design-banner">
                        <img src="{{ asset('kaheel/design-ready-banner.jpg') }}" alt="" width="1280" height="512" loading="lazy">
                        <div class="order-design-banner-copy">
                            <p class="order-design-banner-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72"/><path d="m14 7 3 3"/></svg>
                                <span data-ar="تصميم وحدتك جاهز!" data-en="Your design is ready!">تصميم وحدتك جاهز!</span>
                            </p>
                            <p class="order-lead" data-ar="أكمل فريقنا تصميم وحدتك ورفعه لك. راجع المكوّنات وإن رغبت اطلب تعديلاتك، ثم وقّع واعتمد — ينتقل طلبك بعدها مباشرة إلى السداد النهائي." data-en="Our team has finished and sent your design. Review the components, request changes if you wish, then sign and approve — your order moves straight to the final payment.">أكمل فريقنا تصميم وحدتك ورفعه لك. راجع المكوّنات وإن رغبت اطلب تعديلاتك، ثم وقّع واعتمد — ينتقل طلبك بعدها مباشرة إلى السداد النهائي.</p>
                            <button class="btn btn-sm btn-primary" type="button" id="design-start-review" data-ar="ابدأ المراجعة الآن" data-en="Start reviewing now">ابدأ المراجعة الآن</button>
                        </div>
                    </div>
                @else
                    <div class="order-design-status {{ $designApproved ? 'is-ok' : 'is-back' }}">
                        <h2>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72"/><path d="m14 7 3 3"/></svg>
                            @if ($designApproved)
                                <span data-ar="التصميم معتمد" data-en="Design approved">التصميم معتمد</span>
                            @elseif ($designRejected)
                                <span data-ar="أعدنا التصميم للتعديل" data-en="Design returned for revision">أعدنا التصميم للتعديل</span>
                            @else
                                <span data-ar="جارٍ إعداد تصميمك" data-en="Your design is being prepared">جارٍ إعداد تصميمك</span>
                            @endif
                        </h2>
                        @if ($designApproved)
                            <p class="order-lead" data-ar="تم اعتمادك وتوقيعك{{ $signedAtAr ? ' بتاريخ '.$signedAtAr : '' }} — لا يمكن تعديل التصميم بعد الآن." data-en="Approved and signed{{ $signedAtEn ? ' on '.$signedAtEn : '' }} — changes are locked.">تم اعتمادك وتوقيعك{{ $signedAtAr ? ' بتاريخ '.$signedAtAr : '' }} — لا يمكن تعديل التصميم بعد الآن.</p>
                        @elseif ($designRejected)
                            <p class="order-lead" data-ar="فريقنا يعمل على تعديل التصميم حسب ملاحظاتك — تابع ملاحظاتك في المناقشة." data-en="Our team is revising the design based on your notes — keep the conversation going.">فريقنا يعمل على تعديل التصميم حسب ملاحظاتك — تابع ملاحظاتك في المناقشة.</p>
                            @if ($designRejectNote)
                                <p class="auth-alert is-error">{{ $designRejectNote }}</p>
                            @endif
                        @else
                            <p class="order-lead" data-ar="فريق الإدارة ينهي تصميمك الآن — ستصلك رسالة تنبيه فور جاهزيته للمراجعة." data-en="Our team is finalizing your design — you will be notified as soon as it is ready for review.">فريق الإدارة ينهي تصميمك الآن — ستصلك رسالة تنبيه فور جاهزيته للمراجعة.</p>
                        @endif
                    </div>
                @endif

                <div class="order-design-body">
                    <div class="order-design-files">
                        <h3 data-ar="ملف التصميم" data-en="Design file">ملف التصميم</h3>
                        @if ($designs->isEmpty())
                            <p class="order-lead" data-ar="لم يُرسل التصميم بعد." data-en="The design has not been sent yet.">لم يُرسل التصميم بعد.</p>
                        @else
                            <div class="order-file-grid">
                                @foreach ($designs as $file)
                                    @php
                                        $href = url(app()->getLocale().'/orders/'.$order->id.'/files/'.$file->id);
                                        $isPdf = str_contains(strtolower(($file->mime ?: '').' '.$file->original_name), 'pdf');
                                        $isImg = (str_starts_with((string) $file->mime, 'image/') && !str_contains(strtolower((string) $file->mime), 'svg')) || preg_match('/\.(jpe?g|jfif|png|gif|webp|bmp|tiff?|heic|heif|avif)$/i', (string) $file->original_name);
                                    @endphp
                                    <a class="order-file-tile" href="{{ $href }}" target="_blank" rel="noopener noreferrer">
                                        <span class="order-file-thumb {{ $isPdf ? 'is-pdf' : '' }}" @if($isImg) style="background-image:url('{{ $href }}')" @endif>
                                            @if ($isPdf)<span class="order-file-pdf" aria-hidden="true">PDF</span>@endif
                                        </span>
                                        <span class="order-file-name">{{ $file->original_name }}</span>
                                        <span class="order-file-time" data-ar="{{ kaheel_local_time($file->created_at, 'ar') }}" data-en="{{ kaheel_local_time($file->created_at, 'en') }}">{{ kaheel_local_time($file->created_at, 'ar') }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if ($designReviewOpen && $components->isNotEmpty())
                        <nav class="design-steps" id="design-steps" aria-label="Design review steps">
                            <button type="button" class="design-step is-on" data-step="1"><span class="design-step-num">1</span><span data-ar="راجع التصميم" data-en="Review the design">راجع التصميم</span></button>
                            <button type="button" class="design-step" data-step="2"><span class="design-step-num">2</span><span data-ar="هل ترغب بتعديل؟" data-en="Want any changes?">هل ترغب بتعديل؟</span></button>
                            <button type="button" class="design-step" data-step="3"><span class="design-step-num">3</span><span data-ar="وقّع واعتمد" data-en="Sign and approve">وقّع واعتمد</span></button>
                        </nav>
                    @endif

                    @if ($components->isNotEmpty() && $designReviewOpen)
                        <div id="design-panel-1" class="design-panel">
                            <p class="order-lead" data-ar="هذه مكوّنات وحدتك كما صممناها. اطّلع عليها ثم انتقل للخطوة التالية." data-en="These are your unit components as designed. Review them, then continue.">هذه مكوّنات وحدتك كما صممناها. اطّلع عليها ثم انتقل للخطوة التالية.</p>
                            <ul class="design-comp-list" id="design-comp-summary"></ul>
                            <dl class="order-rows design-totals" id="design-totals-1"></dl>
                            <button class="btn btn-lg btn-primary auth-submit" type="button" id="design-to-2" data-ar="راجعتُ التصميم — التالي" data-en="I reviewed the design — next">راجعتُ التصميم — التالي</button>
                        </div>
                    @endif

                    @if ($designReviewOpen && $components->isNotEmpty())
                        <div id="design-panel-2" class="design-panel" hidden>
                            <p class="design-q" data-ar="هل ترغب بتعديل التصميم؟" data-en="Would you like to change the design?">هل ترغب بتعديل التصميم؟</p>
                            <p class="order-lead" data-ar="يمكنك زيادة الكميات أو اختيار بدائل، وسيُحتسب الفرق تلقائياً ويُضاف للفاتورة النهائية." data-en="You can add quantities or pick alternatives; the difference is calculated and added to the final invoice.">يمكنك زيادة الكميات أو اختيار بدائل، وسيُحتسب الفرق تلقائياً ويُضاف للفاتورة النهائية.</p>
                            <div class="design-choice-grid">
                                <button type="button" class="design-choice" id="design-no-change">
                                    <span class="design-choice-title" data-ar="لا، التصميم مناسب" data-en="No, the design is fine">لا، التصميم مناسب</span>
                                    <span class="design-choice-sub" data-ar="انتقل مباشرة للتوقيع والاعتماد" data-en="Go straight to signing">انتقل مباشرة للتوقيع والاعتماد</span>
                                </button>
                                <button type="button" class="design-choice" id="design-want-change">
                                    <span class="design-choice-title" data-ar="نعم، أريد التعديل" data-en="Yes, I want changes">نعم، أريد التعديل</span>
                                    <span class="design-choice-sub" data-ar="غيّر الكميات أو اختر بدائل" data-en="Change quantities or pick alternatives">غيّر الكميات أو اختر بدائل</span>
                                </button>
                            </div>
                            <div id="design-editor" class="design-editor" hidden>
                                <div class="design-chat-nudge">
                                    <div>
                                        <p class="design-chat-nudge-title" data-ar="هل تعديلك يحتاج شرحاً؟" data-en="Need to explain your change?">هل تعديلك يحتاج شرحاً؟</p>
                                        <p class="order-lead" data-ar="تحدث مباشرة مع فريق التصميم المخصص لتصميمك، ويمكنك إرفاق صور أو مقاطع فيديو أو ملفات لتوضيح طلبك بدقة." data-en="Talk directly with your dedicated design team — attach photos, videos or files to explain exactly what you want.">تحدث مباشرة مع فريق التصميم المخصص لتصميمك، ويمكنك إرفاق صور أو مقاطع فيديو أو ملفات لتوضيح طلبك بدقة.</p>
                                    </div>
                                    <button class="btn btn-sm btn-primary" type="button" id="design-open-chat" data-ar="افتح المحادثة" data-en="Open chat">افتح المحادثة</button>
                                </div>
                                <div id="design-comp-editor" class="design-comp-editor"></div>
                                <div class="design-sticky-bar">
                                    <div class="design-sticky-row">
                                        <span data-ar="التعديلات الإضافية" data-en="Add-ons">التعديلات الإضافية</span>
                                        <strong id="design-addons-live">0.00</strong>
                                    </div>
                                    <p class="design-sel-msg" id="design-sel-msg" hidden></p>
                                    <div class="design-sticky-actions">
                                        <button class="btn btn-outline" type="button" id="design-save-sel" data-ar="حفظ التعديلات" data-en="Save changes">حفظ التعديلات</button>
                                        <button class="btn btn-primary" type="button" id="design-done-sel" data-ar="انتهيت — للاعتماد" data-en="Done — continue">انتهيت — للاعتماد</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="design-panel-3" class="design-panel" @if ($designReviewOpen && $components->isNotEmpty()) hidden @endif>
                        @if ($designReviewOpen)
                            <dl class="order-rows design-totals" id="design-totals-3"></dl>
                            <p class="order-lead" data-ar="بعد الاعتماد تُقفل التعديلات والمناقشة ويُطلب السداد النهائي." data-en="After approval, changes and the discussion are locked and the final payment is requested.">بعد الاعتماد تُقفل التعديلات والمناقشة ويُطلب السداد النهائي.</p>
                            <div class="design-sign-box">
                                <p class="design-q" data-ar="التوقيع" data-en="Signature">التوقيع</p>
                                <canvas class="order-pad" id="design-pad" width="900" height="220"></canvas>
                                <div class="order-actions">
                                    <button class="btn btn-sm btn-outline" type="button" id="design-clear-sign" data-ar="مسح التوقيع" data-en="Clear">مسح التوقيع</button>
                                </div>
                                <label class="auth-field design-reject-field">
                                    <span data-ar="سبب الرفض" data-en="Rejection reason">سبب الرفض</span>
                                    <textarea id="design-reject-note" rows="3" data-ar-placeholder="اكتب سبب رفض التصميم..." data-en-placeholder="Write why you are rejecting the design..." placeholder="اكتب سبب رفض التصميم..."></textarea>
                                </label>
                                <div class="design-decide">
                                    <button class="btn btn-lg btn-primary" type="button" id="design-approve" disabled data-ar="اعتماد التصميم والتوقيع" data-en="Approve & sign design">اعتماد التصميم والتوقيع</button>
                                    <button class="btn btn-lg btn-outline design-reject" type="button" id="design-reject" data-ar="رفض وإرجاع للتعديل" data-en="Reject and return">رفض وإرجاع للتعديل</button>
                                </div>
                                <p class="order-lead" id="design-sign-hint" data-ar="ارسم توقيعك في المربع أعلاه لتفعيل زر الاعتماد." data-en="Draw your signature above to enable approval.">ارسم توقيعك في المربع أعلاه لتفعيل زر الاعتماد.</p>
                                <p class="auth-alert is-error" id="design-error" hidden></p>
                            </div>
                        @elseif ($components->isNotEmpty())
                            <ul class="design-comp-list" id="design-comp-summary"></ul>
                            <dl class="order-rows design-totals" id="design-totals-1"></dl>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if ($order->status === 'final_payment')
            @php $finalDue = max(0, (float) $order->price_incl + (float) $order->addons_total - (float) $order->deposit_amount); @endphp
            <section class="order-card order-final-pay">
                <h2>
                    <svg class="order-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    <span data-ar="ادفع المبلغ النهائي" data-en="Pay final amount">ادفع المبلغ النهائي</span>
                </h2>
                @if ($order->final_payment_status === 'approved')
                    <p class="order-lead" data-ar="تم تأكيد السداد النهائي." data-en="The final payment is confirmed.">تم تأكيد السداد النهائي.</p>
                    <p class="order-doc-meta">{{ $money($finalDue) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>
                @elseif ($order->final_payment_status === 'pending')
                    <p class="order-lead" data-ar="تم السداد النهائي وبانتظار تأكيد الإدارة." data-en="Final payment received and waiting for admin confirmation.">تم السداد النهائي وبانتظار تأكيد الإدارة.</p>
                    <p class="order-doc-meta">{{ $money($finalDue) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>
                @else
                    <p class="order-lead">{{ $money($finalDue) }} <span data-ar="ريال" data-en="SAR">ريال</span></p>
                    @if ($order->final_payment_status === 'rejected' && $order->final_payment_note)
                        <p class="auth-alert is-error">{{ $order->final_payment_note }}</p>
                    @endif
                    <p class="order-lead" data-ar="اختر طريقة السداد النهائي: رفع إيصال أو فيزا." data-en="Choose a final payment method: upload a receipt or pay by Visa.">اختر طريقة السداد النهائي: رفع إيصال أو فيزا.</p>
                    @include('kaheel.partials.payment-methods')
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
                <div class="order-file-grid">
                @foreach ($plans as $file)
                    @php
                        $href = url(app()->getLocale().'/orders/'.$order->id.'/files/'.$file->id);
                        $isPdf = str_contains(strtolower(($file->mime ?: '').' '.$file->original_name), 'pdf');
                        $isImg = (str_starts_with((string) $file->mime, 'image/') && !str_contains(strtolower((string) $file->mime), 'svg')) || preg_match('/\.(jpe?g|jfif|png|gif|webp|bmp|tiff?|heic|heif|avif)$/i', (string) $file->original_name);
                    @endphp
                    <a class="order-file-tile" href="{{ $href }}" target="_blank" rel="noopener noreferrer">
                        <span class="order-file-thumb {{ $isPdf ? 'is-pdf' : '' }}" @if($isImg) style="background-image:url('{{ $href }}')" @endif>
                            @if ($isPdf)<span class="order-file-pdf" aria-hidden="true">PDF</span>@endif
                        </span>
                        <span class="order-file-name">{{ $file->original_name }}</span>
                    </a>
                @endforeach
                </div>
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
    <button class="order-chat-launch" id="order-chat-open" type="button">
        <span data-ar="تحدث مع فريق التصميم المخصص لتصميمك" data-en="Talk with the design team assigned to you">تحدث مع فريق التصميم المخصص لتصميمك</span>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="order-chat-badge" id="order-chat-badge" @if ($unread < 1) hidden @endif>{{ $unread }}</span>
    </button>
    @php $chatLocked = $designApproved || in_array($order->status, ['final_payment', 'final_approval', 'in_supply', 'supplied', 'closed'], true); @endphp
    <div class="order-chat" id="order-chat" hidden
        data-list="{{ url(app()->getLocale().'/orders/'.$order->id.'/messages') }}"
        data-send="{{ url(app()->getLocale().'/orders/'.$order->id.'/messages') }}"
        data-read="{{ url(app()->getLocale().'/orders/'.$order->id.'/messages/read') }}"
        data-token="{{ csrf_token() }}"
        data-unread="{{ $unread }}"
        data-locked="{{ $chatLocked ? '1' : '0' }}">
        <button class="order-chat-dim" id="order-chat-dim" type="button" aria-label="إغلاق"></button>
        <section class="order-chat-panel {{ $chatLocked ? 'is-locked' : '' }}" role="dialog" aria-modal="true" aria-labelledby="order-chat-title">
            <header class="order-chat-head">
                <div class="order-chat-title">
                    <h2 id="order-chat-title" data-ar="محادثة التصميم" data-en="Design conversation">محادثة التصميم</h2>
                    <span class="order-chat-count" id="order-chat-count" @if ($unread < 1) hidden @endif>{{ $unread }}</span>
                    <button class="order-chat-close" id="order-chat-close" type="button" aria-label="إغلاق">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3h6v6"/><path d="m21 3-7 7"/><path d="M9 21H3v-6"/><path d="m3 21 7-7"/></svg>
                    </button>
                </div>
                @if ($chatLocked)
                    <p data-ar="تم اعتماد التصميم وأُقفلت المناقشة. يمكنك الاطلاع على المحادثة فقط." data-en="The design is approved and the discussion is locked. You can view the chat only.">تم اعتماد التصميم وأُقفلت المناقشة. يمكنك الاطلاع على المحادثة فقط.</p>
                @else
                    <p data-ar="ناقش التصميم ومكوناته مع فريق الإدارة قبل الاعتماد." data-en="Discuss the design and its components with the admin team before approval.">ناقش التصميم ومكوناته مع فريق الإدارة قبل الاعتماد.</p>
                @endif
            </header>
            <div class="order-chat-log" id="order-chat-log"></div>
            <p class="order-chat-error" id="order-chat-error" hidden></p>
            @if ($chatLocked)
                <div class="order-chat-locked" data-ar="المراسلة غير متاحة بعد اعتماد التصميم." data-en="Messaging is unavailable after design approval.">المراسلة غير متاحة بعد اعتماد التصميم.</div>
            @else
                <form class="order-chat-form" id="order-chat-form">
                    <textarea id="order-chat-body" data-ar-placeholder="اكتب ملاحظتك على الطلب..." data-en-placeholder="Write your note on the order..." placeholder="اكتب ملاحظتك على الطلب..."></textarea>
                    <div class="order-chat-tools">
                        <button class="btn btn-primary" type="submit">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg>
                            <span data-ar="إرسال" data-en="Send">إرسال</span>
                        </button>
                        <button class="btn btn-outline" id="order-chat-attach" type="button">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                            <span data-ar="إرفاق ملفات" data-en="Attach files">إرفاق ملفات</span>
                        </button>
                    </div>
                    <p class="order-chat-hint" data-ar="الصيغ المقبولة: صور (JPG, PNG, WEBP, GIF, BMP, HEIC, AVIF) أو فيديو (MP4, WEBM, MOV) أو PDF — حتى 25 ميغابايت." data-en="Accepted: images (JPG, PNG, WEBP, GIF, BMP, HEIC, AVIF), video (MP4, WEBM, MOV), or PDF — up to 25 MB.">الصيغ المقبولة: صور (JPG, PNG, WEBP, GIF, BMP, HEIC, AVIF) أو فيديو (MP4, WEBM, MOV) أو PDF — حتى 25 ميغابايت.</p>
                    <input id="order-chat-file" type="file" hidden accept="image/*,.jpg,.jpeg,.png,.webp,.gif,.bmp,.heic,.heif,.avif,.pdf,video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov,application/pdf">
                </form>
            @endif
            <div class="order-chat-preview" id="order-chat-preview" hidden>
                <button type="button" id="order-chat-preview-close" aria-label="إغلاق">×</button>
            </div>
        </section>
    </div>
    <script>
        const root = document.documentElement;
        document.getElementById("lang-toggle").addEventListener("click", () => {
            const en = root.lang !== "en";
            root.lang = en ? "en" : "ar";
            root.dir = en ? "ltr" : "rtl";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                node.textContent = en ? node.dataset.en : node.dataset.ar;
            });
            document.querySelectorAll("[data-ar-placeholder]").forEach((node) => {
                node.placeholder = en ? node.dataset.enPlaceholder : node.dataset.arPlaceholder;
            });
            document.querySelectorAll('input[name="lang"]').forEach((node) => { node.value = en ? "en" : "ar"; });
            document.getElementById("lang-label").textContent = en ? "ع" : "EN";
            localStorage.setItem("forma-lang", en ? "en" : "ar");
            if (window.__designReviewRender) window.__designReviewRender();
        });
        if (localStorage.getItem("forma-lang") === "en") document.getElementById("lang-toggle").click();
        const preview = document.getElementById("preview-contract");
        const contract = document.getElementById("contract-preview");
        if (preview && contract) {
            preview.addEventListener("click", () => {
                contract.hidden = !contract.hidden;
            });
        }

        (function initDesignReview() {
            const box = document.getElementById("order-design");
            if (!box) return;
            const review = box.dataset.review === "1";
            const components = JSON.parse(box.dataset.components || "[]");
            const packageTotal = Number(box.dataset.price || 0);
            const deposit = Number(box.dataset.deposit || 0);
            const state = {};
            components.forEach((row) => {
                state[row.id] = {
                    extraQty: Number(row.extra_qty || 0),
                    optionKey: row.selected_key || "",
                };
            });
            let step = components.length && review ? 1 : 3;
            let maxStep = step;
            let wantChange = components.some((row) => Number(row.extra_qty || 0) > 0 || row.selected_key);
            let signed = false;
            let busy = false;

            const en = () => document.documentElement.lang === "en";
            const money = (n) => Number(n || 0).toLocaleString(en() ? "en-US" : "en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const pick = (row, ar, eng) => en() ? (row[eng] || row[ar] || "") : (row[ar] || row[eng] || "");
            const altKey = (option) => `${(option.name_ar || "").trim()}|${(option.name_en || "").trim()}`;
            const lineDelta = (row) => {
                const sel = state[row.id] || { extraQty: 0, optionKey: "" };
                const option = (row.options || []).find((item) => altKey(item) === sel.optionKey);
                return sel.extraQty * Number(row.price_per_unit || 0) + Number(option?.price_delta || 0);
            };
            const addons = () => components.reduce((sum, row) => sum + lineDelta(row), 0);
            const sar = () => en() ? "SAR" : "ريال";

            const fillTotals = (node) => {
                if (!node) return;
                const z = addons();
                const final = packageTotal + z * 1.15;
                const remaining = Math.max(0, final - deposit);
                const rows = [
                    [en() ? "Add-ons" : "التعديلات الإضافية", `${money(z)} ${sar()}`],
                    [en() ? "VAT" : "ضريبة القيمة المضافة", `${money(z * 0.15)} ${sar()}`],
                    [en() ? "Final total" : "الإجمالي النهائي", `${money(final)} ${sar()}`, true],
                    [en() ? "Remaining" : "المتبقي", `${money(remaining)} ${sar()}`],
                ];
                node.replaceChildren();
                rows.forEach(([label, value, strong]) => {
                    const wrap = document.createElement("div");
                    const dt = document.createElement("dt");
                    const dd = document.createElement("dd");
                    dt.textContent = label;
                    dd.textContent = value;
                    if (strong) dd.className = "is-primary";
                    wrap.append(dt, dd);
                    node.append(wrap);
                });
            };

            const renderSummary = () => {
                const list = document.getElementById("design-comp-summary");
                if (!list) return;
                list.replaceChildren();
                components.forEach((row) => {
                    const sel = state[row.id] || { extraQty: 0, optionKey: "" };
                    const option = (row.options || []).find((item) => altKey(item) === sel.optionKey);
                    const delta = lineDelta(row);
                    const li = document.createElement("li");
                    const name = document.createElement("span");
                    name.className = "design-comp-name";
                    name.textContent = pick(row, "name_ar", "name_en");
                    const meta = document.createElement("span");
                    meta.className = "design-comp-meta";
                    meta.textContent = `${Number(row.base_qty || 0) + sel.extraQty} ${pick(row, "unit_label_ar", "unit_label_en")}${option ? " · " + pick(option, "name_ar", "name_en") : ""}`;
                    const price = document.createElement("span");
                    price.className = "design-comp-delta";
                    price.textContent = delta > 0 ? `+${money(delta)} ${sar()}` : "—";
                    li.append(name, meta, price);
                    list.append(li);
                });
                fillTotals(document.getElementById("design-totals-1"));
                fillTotals(document.getElementById("design-totals-3"));
                const live = document.getElementById("design-addons-live");
                if (live) live.textContent = `${money(addons())} ${sar()}`;
            };

            const renderEditor = () => {
                const wrap = document.getElementById("design-comp-editor");
                if (!wrap) return;
                wrap.replaceChildren();
                components.forEach((row) => {
                    const sel = state[row.id] || { extraQty: 0, optionKey: "" };
                    const card = document.createElement("article");
                    card.className = "design-comp-card";
                    const head = document.createElement("div");
                    head.className = "design-comp-card-head";
                    const copy = document.createElement("div");
                    const title = document.createElement("p");
                    title.className = "design-comp-name";
                    title.textContent = pick(row, "name_ar", "name_en");
                    const meta = document.createElement("p");
                    meta.className = "design-comp-meta";
                    meta.textContent = `${row.base_qty || 0} ${pick(row, "unit_label_ar", "unit_label_en")} · ${money(row.price_per_unit)} ${sar()} / ${pick(row, "unit_label_ar", "unit_label_en")}`;
                    copy.append(title, meta);
                    const delta = document.createElement("span");
                    delta.className = "design-comp-delta";
                    delta.textContent = `+${money(lineDelta(row))} ${sar()}`;
                    head.append(copy, delta);
                    const ask = document.createElement("button");
                    ask.type = "button";
                    ask.className = "design-ask";
                    ask.textContent = en() ? "Ask about this item" : "استفسار عن هذا العنصر";
                    ask.addEventListener("click", () => document.getElementById("order-chat-open")?.click());
                    const grid = document.createElement("div");
                    grid.className = "design-comp-fields";
                    const qtyLabel = document.createElement("label");
                    qtyLabel.innerHTML = `<span>${en() ? "Extra quantity" : "كمية إضافية"}</span>`;
                    const qty = document.createElement("input");
                    qty.type = "number";
                    qty.min = "0";
                    qty.step = "0.5";
                    qty.value = String(sel.extraQty);
                    qty.addEventListener("input", () => {
                        state[row.id].extraQty = Number(qty.value) || 0;
                        renderSummary();
                        delta.textContent = `+${money(lineDelta(row))} ${sar()}`;
                    });
                    qtyLabel.append(qty);
                    grid.append(qtyLabel);
                    const options = row.options || [];
                    if (options.length) {
                        const altLabel = document.createElement("label");
                        altLabel.innerHTML = `<span>${en() ? "Alternative" : "بديل"}</span>`;
                        const select = document.createElement("select");
                        const blank = document.createElement("option");
                        blank.value = "";
                        blank.textContent = en() ? "Default option" : "الخيار الأساسي";
                        select.append(blank);
                        options.forEach((option) => {
                            const opt = document.createElement("option");
                            opt.value = altKey(option);
                            opt.textContent = `${pick(option, "name_ar", "name_en")} (+${money(option.price_delta)})`;
                            if (sel.optionKey === opt.value) opt.selected = true;
                            select.append(opt);
                        });
                        select.addEventListener("change", () => {
                            state[row.id].optionKey = select.value || "";
                            renderSummary();
                            delta.textContent = `+${money(lineDelta(row))} ${sar()}`;
                        });
                        altLabel.append(select);
                        grid.append(altLabel);
                    }
                    card.append(head, ask, grid);
                    wrap.append(card);
                });
            };

            const showStep = (next) => {
                step = next;
                maxStep = Math.max(maxStep, next);
                ["1", "2", "3"].forEach((id) => {
                    const panel = document.getElementById("design-panel-" + id);
                    if (panel) panel.hidden = String(step) !== id;
                });
                document.querySelectorAll("#design-steps .design-step").forEach((btn) => {
                    const n = Number(btn.dataset.step);
                    btn.classList.toggle("is-on", n === step);
                    btn.classList.toggle("is-done", n < step);
                    btn.disabled = n > maxStep;
                });
                const editor = document.getElementById("design-editor");
                if (editor) editor.hidden = !(step === 2 && wantChange);
                document.getElementById("design-want-change")?.classList.toggle("is-on", wantChange);
                if (step === 2 && wantChange) renderEditor();
                renderSummary();
                box.scrollIntoView({ behavior: "smooth", block: "start" });
            };

            const selectionsPayload = () => Object.entries(state).map(([componentId, row]) => ({
                componentId,
                extraQty: row.extraQty,
                optionKey: row.optionKey || "",
            }));

            const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || box.dataset.token || "";

            const setSelMsg = (text, isError) => {
                const msg = document.getElementById("design-sel-msg");
                if (!msg) return;
                msg.hidden = !text;
                msg.textContent = text || "";
                msg.classList.toggle("is-error", !!isError);
                msg.classList.toggle("is-ok", !!text && !isError);
            };

            const postJson = async (url, body) => {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": csrfToken(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({ ...body, _token: csrfToken() }),
                });
                const data = await response.json().catch(() => ({}));
                if (response.status === 419) {
                    throw new Error(en() ? "Session expired. Refresh the page and try again." : "انتهت الجلسة. حدّث الصفحة وحاول مرة أخرى.");
                }
                if (!response.ok || !data.ok) {
                    throw new Error(data.error || data.message || (en() ? "Request failed." : "تعذر تنفيذ الطلب."));
                }
                return data;
            };

            const saveSelections = async () => {
                const data = await postJson(box.dataset.save, { selections: selectionsPayload() });
                setSelMsg(data.message || (en() ? "Changes saved." : "تم حفظ التعديلات."), false);
                return data;
            };

            window.__designReviewRender = () => {
                renderSummary();
                if (step === 2 && wantChange) renderEditor();
            };

            if (review && components.length) {
                document.getElementById("design-start-review")?.addEventListener("click", () => showStep(1));
                document.getElementById("design-to-2")?.addEventListener("click", () => showStep(2));
                document.querySelectorAll("#design-steps .design-step").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const n = Number(btn.dataset.step);
                        if (n <= maxStep) showStep(n);
                    });
                });
                document.getElementById("design-no-change")?.addEventListener("click", () => {
                    wantChange = false;
                    showStep(3);
                });
                document.getElementById("design-want-change")?.addEventListener("click", () => {
                    wantChange = true;
                    showStep(2);
                });
                document.getElementById("design-open-chat")?.addEventListener("click", () => {
                    document.getElementById("order-chat-open")?.click();
                });
                document.getElementById("design-save-sel")?.addEventListener("click", async () => {
                    if (busy) return;
                    busy = true;
                    const saveBtn = document.getElementById("design-save-sel");
                    const doneBtn = document.getElementById("design-done-sel");
                    if (saveBtn) saveBtn.disabled = true;
                    if (doneBtn) doneBtn.disabled = true;
                    setSelMsg(en() ? "Saving…" : "جاري الحفظ…", false);
                    try {
                        await saveSelections();
                    } catch (error) {
                        setSelMsg(error.message, true);
                    } finally {
                        if (saveBtn) saveBtn.disabled = false;
                        if (doneBtn) doneBtn.disabled = false;
                        busy = false;
                    }
                });
                document.getElementById("design-done-sel")?.addEventListener("click", async () => {
                    if (busy) return;
                    busy = true;
                    const saveBtn = document.getElementById("design-save-sel");
                    const doneBtn = document.getElementById("design-done-sel");
                    if (saveBtn) saveBtn.disabled = true;
                    if (doneBtn) doneBtn.disabled = true;
                    setSelMsg(en() ? "Saving…" : "جاري الحفظ…", false);
                    try {
                        await saveSelections();
                        showStep(3);
                    } catch (error) {
                        setSelMsg(error.message, true);
                    } finally {
                        if (saveBtn) saveBtn.disabled = false;
                        if (doneBtn) doneBtn.disabled = false;
                        busy = false;
                    }
                });
                if (wantChange) {
                    maxStep = Math.max(maxStep, 2);
                    showStep(1);
                    const editor = document.getElementById("design-editor");
                    if (editor) editor.hidden = false;
                } else {
                    showStep(1);
                }
            } else {
                renderSummary();
            }

            const pad = document.getElementById("design-pad");
            const approve = document.getElementById("design-approve");
            if (pad && approve) {
                const pen = pad.getContext("2d");
                pen.lineWidth = 2;
                pen.lineCap = "round";
                pen.strokeStyle = "#005573";
                let drawing = false;
                const pos = (event) => {
                    const rect = pad.getBoundingClientRect();
                    const point = event.touches ? event.touches[0] : event;
                    return {
                        x: (point.clientX - rect.left) * (pad.width / rect.width),
                        y: (point.clientY - rect.top) * (pad.height / rect.height),
                    };
                };
                const start = (event) => {
                    drawing = true;
                    const p = pos(event);
                    pen.beginPath();
                    pen.moveTo(p.x, p.y);
                    event.preventDefault();
                };
                const move = (event) => {
                    if (!drawing) return;
                    const p = pos(event);
                    pen.lineTo(p.x, p.y);
                    pen.stroke();
                    signed = true;
                    approve.disabled = false;
                    document.getElementById("design-sign-hint").hidden = true;
                    event.preventDefault();
                };
                const end = () => { drawing = false; };
                pad.addEventListener("mousedown", start);
                pad.addEventListener("mousemove", move);
                window.addEventListener("mouseup", end);
                pad.addEventListener("touchstart", start, { passive: false });
                pad.addEventListener("touchmove", move, { passive: false });
                pad.addEventListener("touchend", end);
                document.getElementById("design-clear-sign")?.addEventListener("click", () => {
                    pen.clearRect(0, 0, pad.width, pad.height);
                    signed = false;
                    approve.disabled = true;
                    document.getElementById("design-sign-hint").hidden = false;
                });
                approve.addEventListener("click", async () => {
                    if (!signed || busy) return;
                    busy = true;
                    approve.disabled = true;
                    const err = document.getElementById("design-error");
                    if (err) err.hidden = true;
                    try {
                        await postJson(box.dataset.confirm, {
                            decision: "approve",
                            signature: pad.toDataURL("image/png"),
                            selections: selectionsPayload(),
                        });
                        location.assign(location.pathname);
                    } catch (error) {
                        if (err) { err.hidden = false; err.textContent = error.message; }
                        approve.disabled = false;
                    } finally {
                        busy = false;
                    }
                });
                document.getElementById("design-reject")?.addEventListener("click", async () => {
                    if (busy) return;
                    const note = (document.getElementById("design-reject-note")?.value || "").trim();
                    const err = document.getElementById("design-error");
                    if (!note) {
                        if (err) {
                            err.hidden = false;
                            err.textContent = document.documentElement.lang === "en"
                                ? "Write the rejection reason before returning the design."
                                : "اكتب سبب الرفض قبل إرجاع التصميم.";
                        }
                        document.getElementById("design-reject-note")?.focus();
                        return;
                    }
                    busy = true;
                    if (err) err.hidden = true;
                    try {
                        await postJson(box.dataset.confirm, { decision: "reject", note });
                        location.assign(location.pathname);
                    } catch (error) {
                        if (err) { err.hidden = false; err.textContent = error.message; }
                    } finally {
                        busy = false;
                    }
                });
            }
        })();

        const chatRoot = document.getElementById("order-chat");
        const chatOpen = document.getElementById("order-chat-open");
        const chatLog = document.getElementById("order-chat-log");
        const chatBadge = document.getElementById("order-chat-badge");
        const chatCount = document.getElementById("order-chat-count");
        const chatError = document.getElementById("order-chat-error");
        const chatBody = document.getElementById("order-chat-body");
        const chatFile = document.getElementById("order-chat-file");
        const chatPreview = document.getElementById("order-chat-preview");
        const chatForm = document.getElementById("order-chat-form");
        const chatLocked = chatRoot?.dataset.locked === "1";
        let chatSeen = Number(chatRoot?.dataset.unread || 0);

        const chatCopy = () => document.documentElement.lang === "en"
            ? { empty: "No messages in this order yet.", close: "Close" }
            : { empty: "لا رسائل في هذا الطلب بعد.", close: "إغلاق" };

        const setCount = (value, target) => {
            const count = Number(value || 0);
            target.textContent = String(count);
            target.hidden = count < 1;
        };

        const stamp = (iso) => {
            const date = new Date(iso);
            if (Number.isNaN(date.getTime())) return "";
            const en = document.documentElement.lang === "en";
            const time = new Intl.DateTimeFormat(en ? "en" : "ar", { hour: "numeric", minute: "2-digit" }).format(date);
            const day = new Intl.DateTimeFormat(en ? "en" : "ar", { year: "numeric", month: "2-digit", day: "2-digit" }).format(date);
            return time + " · " + day;
        };

        const mediaLabel = (kind) => {
            const en = document.documentElement.lang === "en";
            const labels = en
                ? { image: "Photo", video: "Video", audio: "Voice message", pdf: "PDF" }
                : { image: "صورة", video: "فيديو", audio: "رسالة صوتية", pdf: "PDF" };
            return labels[kind] || "";
        };

        let chatMessages = [];
        const renderChat = (messages) => {
            chatMessages = messages || [];
            chatLog.replaceChildren();
            if (!messages.length) {
                const empty = document.createElement("p");
                empty.className = "order-chat-empty";
                empty.textContent = chatCopy().empty;
                chatLog.append(empty);
                return;
            }
            messages.forEach((message) => {
                const row = document.createElement("article");
                row.className = "order-chat-row" + (message.role === "customer" ? " is-mine" : "");
                const meta = document.createElement("p");
                meta.className = "order-chat-meta";
                meta.textContent = (message.name || "") + " · " + stamp(message.created_at);
                const bubble = document.createElement("div");
                bubble.className = "order-chat-bubble";
                if (message.kind === "image" && message.url) {
                    const img = document.createElement("img");
                    img.src = message.url;
                    img.alt = "";
                    img.addEventListener("click", () => openPreview(message));
                    bubble.append(img);
                } else if (message.kind === "video" && message.url) {
                    const video = document.createElement("video");
                    video.src = message.url;
                    video.controls = true;
                    video.addEventListener("click", () => openPreview(message));
                    bubble.append(video);
                } else if (message.kind === "audio" && message.url) {
                    const audio = document.createElement("audio");
                    audio.src = message.url;
                    audio.controls = true;
                    bubble.append(audio);
                } else if (message.kind === "pdf" && message.url) {
                    const link = document.createElement("a");
                    link.href = message.url;
                    link.target = "_blank";
                    link.rel = "noopener";
                    link.textContent = message.file_name || mediaLabel("pdf");
                    bubble.append(link);
                }
                if (message.body) {
                    const text = document.createElement("p");
                    text.textContent = message.body;
                    bubble.append(text);
                } else if (message.kind && message.kind !== "text" && message.kind !== "pdf") {
                    const text = document.createElement("p");
                    text.textContent = mediaLabel(message.kind);
                    bubble.append(text);
                }
                row.append(meta, bubble);
                chatLog.append(row);
            });
            chatLog.scrollTop = chatLog.scrollHeight;
        };
        document.getElementById("lang-toggle")?.addEventListener("click", () => {
            setTimeout(() => renderChat(chatMessages), 0);
        });

        const openPreview = (message) => {
            chatPreview.replaceChildren();
            const close = document.createElement("button");
            close.type = "button";
            close.textContent = "×";
            close.setAttribute("aria-label", chatCopy().close);
            close.addEventListener("click", () => { chatPreview.hidden = true; });
            const node = message.kind === "video" ? document.createElement("video") : document.createElement("img");
            node.src = message.url;
            if (message.kind === "video") node.controls = true;
            chatPreview.append(close, node);
            chatPreview.hidden = false;
        };

        const showChatError = (text) => {
            chatError.hidden = !text;
            chatError.textContent = text || "";
        };

        const loadChat = async (markRead) => {
            const response = await fetch(markRead ? chatRoot.dataset.read : chatRoot.dataset.list, {
                method: markRead ? "POST" : "GET",
                headers: { "X-CSRF-TOKEN": chatRoot.dataset.token, "Accept": "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json();
            if (!response.ok || !data.ok) throw new Error(data.error || "");
            renderChat(data.messages || []);
            if (!markRead) setCount(data.unread, chatBadge);
            return data;
        };

        const setChatOpen = async (open) => {
            chatRoot.hidden = !open;
            chatOpen.hidden = open;
            if (!open) {
                chatPreview.hidden = true;
                return;
            }
            setCount(chatSeen, chatCount);
            try {
                await loadChat(true);
                chatSeen = 0;
                setCount(0, chatBadge);
            } catch (error) {
                showChatError(error.message);
            }
        };

        chatOpen?.addEventListener("click", () => setChatOpen(true));
        document.getElementById("order-chat-close")?.addEventListener("click", () => setChatOpen(false));
        document.getElementById("order-chat-dim")?.addEventListener("click", () => setChatOpen(false));
        document.getElementById("order-chat-attach")?.addEventListener("click", () => chatFile?.click());
        document.getElementById("order-chat-preview-close")?.addEventListener("click", () => { if (chatPreview) chatPreview.hidden = true; });
        chatForm?.addEventListener("submit", async (event) => {
            event.preventDefault();
            if (chatLocked) {
                showChatError(document.documentElement.lang === "en"
                    ? "Messaging is unavailable after design approval."
                    : "المراسلة غير متاحة بعد اعتماد التصميم.");
                return;
            }
            const body = (chatBody?.value || "").trim();
            if (!body && !(chatFile?.files?.length)) return;
            const form = new FormData();
            form.append("body", body);
            if (chatFile?.files?.length) form.append("file", chatFile.files[0]);
            showChatError("");
            const response = await fetch(chatRoot.dataset.send, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": chatRoot.dataset.token, "Accept": "application/json" },
                credentials: "same-origin",
                body: form,
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.ok) {
                showChatError(data.error || (document.documentElement.lang === "en" ? "The message could not be sent." : "تعذر إرسال الرسالة."));
                return;
            }
            if (chatBody) chatBody.value = "";
            if (chatFile) chatFile.value = "";
            renderChat(data.messages || []);
        });
        chatFile?.addEventListener("change", () => {
            if (chatFile.files.length) chatForm?.requestSubmit();
        });
        if (new URLSearchParams(location.search).get("chat") === "1") setChatOpen(true);
        setInterval(async () => {
            try {
                if (chatRoot.hidden) {
                    const data = await loadChat(false);
                    chatSeen = Number(data.unread || 0);
                } else {
                    await loadChat(true);
                }
            } catch (error) {}
        }, 8000);
    </script>
    @include('kaheel.partials.payment-methods-script')
</body>
</html>
