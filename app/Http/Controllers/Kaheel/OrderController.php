<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\KaheelOrder;
use App\Models\KaheelOrderEvent;
use App\Models\KaheelOrderFile;
use App\Models\KaheelOrderMessage;
use App\Models\KaheelPackage;
use App\Models\KaheelSetting;
use App\Services\KaheelOrderChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $customer = kaheel_customer();
        if (!$customer) {
            $query = array_filter([
                'redirect' => '/order',
                'pkg' => $request->query('pkg'),
            ]);

            return redirect(url(app()->getLocale().'/auth'.($query ? '?'.http_build_query($query) : '')));
        }

        $packages = KaheelPackage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['tiers' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order')->with('items');
            }])
            ->get();

        $settings = KaheelSetting::map();
        $days = $settings['delivery_working_days'] ?? '25';
        $deposit = (float) ($settings['deposit_percent'] ?? 25);

        return view('kaheel.order', [
            'customer' => $customer,
            'compounds' => $customer->units ?? [],
            'catalog' => $packages->map(fn (KaheelPackage $package) => [
                'code' => $package->code,
                'name_ar' => $package->name_ar,
                'name_en' => $package->name_en,
                'tagline_ar' => $package->tagline_ar,
                'tagline_en' => $package->tagline_en,
                'tiers' => $package->tiers->map(fn ($tier) => [
                    'id' => $tier->id,
                    'name_ar' => $tier->name_ar,
                    'name_en' => $tier->name_en,
                    'description_ar' => $tier->description_ar,
                    'description_en' => $tier->description_en,
                    'price_excl_vat' => (float) $tier->price_excl_vat,
                    'price_incl_vat' => (float) $tier->price_incl_vat,
                    'items' => $tier->items->map(fn ($item) => [
                        'name_ar' => $item->name_ar,
                        'name_en' => $item->name_en,
                        'product_name_ar' => $item->product_name_ar,
                        'product_name_en' => $item->product_name_en,
                        'detail_ar' => $item->detail_ar,
                        'detail_en' => $item->detail_en,
                        'quantity' => $item->quantity,
                        'unit_label_ar' => $item->unit_label_ar,
                        'unit_label_en' => $item->unit_label_en,
                    ])->values(),
                ])->values(),
            ])->values(),
            'pkg' => strtoupper((string) $request->query('pkg', '')),
            'depositPercent' => $deposit,
            'deliveryAr' => "مدة التنفيذ {$days} يوم عمل من تاريخ الدفع (عدا الجمعة والسبت).",
            'deliveryEn' => "Delivery is {$days} working days from payment date (Friday and Saturday excluded).",
        ]);
    }

    public function store(Request $request)
    {
        $customer = kaheel_customer();
        $isAr = app()->getLocale() === 'ar';
        if (!$customer) {
            return mainResponse(false, $isAr ? 'يلزم تسجيل الدخول.' : 'Sign in is required.', null, 401, 'items', '');
        }
        if ($this->requestTooLarge($request)) {
            return mainResponse(false, $isAr ? 'حجم الملفات أكبر من 20 ميغابايت. صغّر الملف ثم أعد الإرسال، ولن يُحفظ الطلب قبل نجاح الرفع.' : 'The files are larger than 20 MB. Reduce the file size and send again. The order is not saved until the upload succeeds.', null, 422, 'items', '');
        }

        $package = KaheelPackage::query()
            ->whereRaw('LOWER(code) = ?', [strtolower((string) $request->input('package_code'))])
            ->where('is_active', true)
            ->first();
        $tier = $package?->tiers()->where('is_active', true)->whereKey($request->input('tier_id'))->first();
        $compound = $this->compound($customer->units ?? [], (string) $request->input('compound_id'));
        $unit = $compound ? $this->unit($compound, (string) $request->input('unit_id')) : null;

        if (!$package || !$tier || !$compound || !$unit) {
            return mainResponse(false, $isAr ? 'اختر الباقة والمشروع والوحدة.' : 'Choose the package, project and unit.', null, 422, 'items', '');
        }
        if (!$request->boolean('agreed')) {
            return mainResponse(false, $isAr ? 'يلزم الموافقة على العقد.' : 'The contract must be accepted.', null, 422, 'items', '');
        }

        $signature = (string) $request->input('signature', '');
        if (!str_starts_with($signature, 'data:image/png;base64,') || strlen($signature) < 80 || strlen($signature) > 500000 || !$this->signatureHasInk($signature)) {
            return mainResponse(false, $isAr ? 'يلزم الموافقة على العقد وتوقيعه قبل إرسال الطلب.' : 'Accept the contract and sign it before sending the order.', null, 422, 'items', '');
        }

        $files = $request->file('plans', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }
        $fileError = $this->fileError($files, $isAr);
        if ($fileError) {
            return mainResponse(false, $fileError, null, 422, 'items', '');
        }

        $settings = KaheelSetting::map();
        $percent = (float) ($settings['deposit_percent'] ?? 25);
        $incl = (float) $tier->price_incl_vat;
        $excl = (float) $tier->price_excl_vat;
        $deposit = round($incl * $percent / 100, 2);
        $days = $settings['delivery_working_days'] ?? '25';
        $lang = $request->input('lang') === 'en' ? 'en' : 'ar';
        $packageName = ($lang === 'en' ? $package->name_en : $package->name_ar).' — '.($lang === 'en' ? $tier->name_en : $tier->name_ar);
        $contract = $this->contract($packageName, (string) $unit['unit_no'], $incl, $deposit, $days, $lang);

        $order = DB::transaction(function () use ($customer, $package, $tier, $compound, $unit, $excl, $incl, $percent, $deposit, $contract, $signature) {
            $order = KaheelOrder::query()->create([
                'number' => 'TMP-'.uniqid(),
                'bricks_user_id' => $customer->bricks_user_id,
                'username' => $customer->username,
                'customer_name' => $customer->name ?: $customer->username,
                'package_code' => $package->code,
                'package_name_ar' => $package->name_ar,
                'package_name_en' => $package->name_en,
                'tier_name_ar' => $tier->name_ar,
                'tier_name_en' => $tier->name_en,
                'compound_id' => (string) ($compound['id'] ?? ''),
                'compound_name' => (string) ($compound['display_name'] ?? ''),
                'compound_meta' => trim((string) ($compound['compound_number'] ?? '').' '.(string) ($compound['short_name'] ?? '')),
                'compound_snapshot' => $this->compoundSnapshot($compound),
                'unit_id' => (string) ($unit['id'] ?? ''),
                'unit_no' => (string) ($unit['unit_no'] ?? ''),
                'bedrooms_number' => $unit['bedrooms_number'] ?? null,
                'squar_meters' => $unit['squar_meters'] ?? null,
                'unit_snapshot' => $this->unitSnapshot($unit),
                'price_excl' => $excl,
                'price_incl' => $incl,
                'deposit_percent' => $percent,
                'deposit_amount' => $deposit,
                'contract_body' => $contract,
                'signature' => $signature,
                'status' => 'deposit_pending',
            ]);
            $order->number = 'ORD-'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            return $order;
        });

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }
            KaheelOrderFile::query()->create([
                'order_id' => $order->id,
                'kind' => 'plan',
                'original_name' => $file->getClientOriginalName(),
                'path' => $file->store('kaheel-orders/'.$order->id),
                'mime' => kaheel_stored_mime($file),
                'size' => $file->getSize(),
            ]);
        }

        return mainResponse(true, $isAr ? 'تم إنشاء الطلب.' : 'Order created.', [
            'id' => $order->id,
            'number' => $order->number,
        ], 200, 'items', '');
    }

    public function pay(KaheelOrder $order)
    {
        if (!$this->owns($order)) {
            return redirect(url(app()->getLocale().'/orders'));
        }

        $mode = $order->status === 'final_payment' ? 'final' : 'deposit';
        if ($mode === 'final' && !in_array($order->final_payment_status, [null, '', 'rejected'], true)) {
            // still allow viewing the confirmation screen when pending/approved
        } elseif ($mode === 'deposit' && $order->status !== 'deposit_pending' && $order->payment_status !== 'pending') {
            // deposit page also used after submit while awaiting admin
        }

        $amount = $mode === 'final'
            ? max(0, (float) $order->price_incl + (float) $order->addons_total - (float) $order->deposit_amount)
            : (float) $order->deposit_amount;

        return view('kaheel.order-pay', [
            'order' => $order,
            'mode' => $mode,
            'amount' => $amount,
        ]);
    }

    public function storePayment(Request $request, KaheelOrder $order)
    {
        $isAr = $request->input('lang') !== 'en';
        if (!$this->owns($order)) {
            return redirect(url(app()->getLocale().'/orders'));
        }

        $isFinal = $order->status === 'final_payment';
        if ($isFinal) {
            if ($order->final_payment_status === 'approved' || $order->final_payment_status === 'pending') {
                return redirect(url(app()->getLocale().'/orders/'.$order->id.'/pay'));
            }
        } elseif ($order->status !== 'deposit_pending') {
            return redirect(url(app()->getLocale().'/orders/'.$order->id.'/pay'));
        }

        $method = (string) $request->input('method');
        if ($method === 'visa') {
            if ($isFinal) {
                $order->final_payment_method = 'visa';
                $order->final_payment_status = 'pending';
                $order->final_payment_note = null;
                $order->save();
                $this->customerEvent($order, $order->status, $order->status, 'أرسل العميل السداد النهائي (فيزا)');
            } else {
                $order->payment_method = 'visa';
                $order->payment_status = 'pending';
                $order->payment_note = null;
                $order->status = 'admin_review';
                $order->save();
            }

            return redirect(url(app()->getLocale().'/orders/'.$order->id.'/pay'));
        }
        if ($method !== 'receipt') {
            return back()->with('pay_error', $isAr ? 'اختر طريقة الدفع.' : 'Choose a payment method.');
        }

        $file = $request->file('receipt');
        $tooBig = $file && (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || ($file->isValid() && $file->getSize() > 20 * 1024 * 1024));
        if ($this->requestTooLarge($request) || $tooBig) {
            return back()->with('pay_error', $isAr ? 'حجم الإيصال أكبر من 20 ميغابايت. الطلب نفسه ما زال محفوظاً.' : 'The receipt is larger than 20 MB. The order itself is still saved.');
        }
        if (!$file || !$file->isValid() || !kaheel_document_allowed($file)) {
            return back()->with('pay_error', $isAr ? 'الصيغ المقبولة للإيصال: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF.' : 'Accepted receipt formats: JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF, and PDF.');
        }

        DB::transaction(function () use ($order, $file, $isFinal) {
            $kind = $isFinal ? 'final_receipt' : 'receipt';
            KaheelOrderFile::query()
                ->where('order_id', $order->id)
                ->where('kind', $kind)
                ->where(function ($query) {
                    $query->whereNull('review_status')->orWhere('review_status', '!=', 'approved');
                })
                ->update(['review_status' => 'rejected']);
            KaheelOrderFile::query()->create([
                'order_id' => $order->id,
                'kind' => $kind,
                'review_status' => 'pending',
                'original_name' => $file->getClientOriginalName(),
                'path' => $file->store('kaheel-orders/'.$order->id),
                'mime' => kaheel_stored_mime($file),
                'size' => $file->getSize(),
            ]);
            if ($isFinal) {
                $order->final_payment_method = 'receipt';
                $order->final_payment_status = 'pending';
                $order->final_payment_note = null;
                $order->save();
            } else {
                $order->payment_method = 'receipt';
                $order->payment_status = 'pending';
                $order->payment_note = null;
                $order->status = 'admin_review';
                $order->save();
            }
        });

        if ($isFinal) {
            $this->customerEvent($order, $order->status, $order->status, 'أرسل العميل السداد النهائي (إيصال)');
        }

        return redirect(url(app()->getLocale().'/orders/'.$order->id.'/pay'));
    }

    public function index()
    {
        $customer = kaheel_customer();
        if (!$customer) {
            return redirect(url(app()->getLocale().'/auth?redirect=/orders'));
        }

        $orders = KaheelOrder::query()
            ->where('bricks_user_id', $customer->bricks_user_id)
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_role', 'admin')->whereNull('customer_seen_at');
            }])
            ->orderByDesc('id')
            ->get()
            ->each(fn (KaheelOrder $order) => $order->promoteApprovedFinalPayment());

        return view('kaheel.orders', ['orders' => $orders]);
    }

    public function show(KaheelOrder $order)
    {
        $customer = kaheel_customer();
        if (!$customer || (int) $order->bricks_user_id !== (int) $customer->bricks_user_id) {
            return redirect(url(app()->getLocale().'/orders'));
        }

        $order->load('files', 'events');
        $order->promoteApprovedFinalPayment();
        $settings = KaheelSetting::map();
        $days = $settings['delivery_working_days'] ?? '25';

        return view('kaheel.order-show', [
            'order' => $order,
            'unread' => $this->unreadCount($order),
            'deliveryAr' => "مدة التنفيذ {$days} يوم عمل من تاريخ الدفع (عدا الجمعة والسبت).",
            'deliveryEn' => "Delivery is {$days} working days from payment date (Friday and Saturday excluded).",
        ]);
    }

    public function notices()
    {
        $customer = kaheel_customer();
        if (!$customer) {
            abort(403);
        }

        $orders = KaheelOrder::query()
            ->where('bricks_user_id', $customer->bricks_user_id)
            ->get(['id', 'number']);
        $ids = $orders->pluck('id');
        $counts = KaheelOrderMessage::query()
            ->selectRaw('order_id, count(*) as unread')
            ->whereIn('order_id', $ids)
            ->where('sender_role', 'admin')
            ->whereNull('customer_seen_at')
            ->groupBy('order_id')
            ->pluck('unread', 'order_id');
        $latest = KaheelOrderMessage::query()
            ->whereIn('order_id', $ids)
            ->where('sender_role', 'admin')
            ->whereNull('customer_seen_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();
        $byId = $orders->keyBy('id');

        return response()->json([
            'ok' => true,
            'total' => (int) $counts->sum(),
            'items' => $latest->map(function (KaheelOrderMessage $message) use ($byId) {
                return [
                    'id' => $message->id,
                    'order_id' => $message->order_id,
                    'number' => $byId->get($message->order_id)->number ?? '',
                    'name' => $message->sender_name,
                    'kind' => $message->kind ?: 'text',
                    'body' => trim((string) $message->body),
                    'created_at' => kaheel_iso($message->created_at),
                ];
            })->values(),
        ]);
    }

    public function messages(KaheelOrder $order)
    {
        if (!$this->owns($order)) {
            abort(403);
        }

        return response()->json([
            'ok' => true,
            'unread' => $this->unreadCount($order),
            'messages' => $this->customerThread($order),
        ]);
    }

    public function readMessages(KaheelOrder $order)
    {
        if (!$this->owns($order)) {
            abort(403);
        }

        $order->messages()
            ->where('sender_role', 'admin')
            ->whereNull('customer_seen_at')
            ->update(['customer_seen_at' => now()]);

        return response()->json([
            'ok' => true,
            'unread' => 0,
            'messages' => $this->customerThread($order),
        ]);
    }

    public function storeMessage(Request $request, KaheelOrder $order)
    {
        if (!$this->owns($order)) {
            abort(403);
        }

        $customer = kaheel_customer();
        $body = trim((string) $request->input('body', ''));
        $file = $request->file('file');
        $isAr = app()->getLocale() !== 'en';
        if ($this->customerChatLocked($order)) {
            return response()->json([
                'ok' => false,
                'error' => $isAr
                    ? 'تم اعتماد التصميم وأُقفلت المناقشة. يمكنك الاطلاع على المحادثة فقط.'
                    : 'The design is approved and the discussion is locked. You can view the chat only.',
            ], 422);
        }
        $tooBig = $file && (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || ($file->isValid() && $file->getSize() > 25 * 1024 * 1024));
        if ($tooBig) {
            return response()->json(['ok' => false, 'error' => $isAr ? 'كل ملف يجب أن يكون 25 ميغابايت أو أقل.' : 'Each file must be 25MB or less.'], 422);
        }

        $kind = 'text';
        $path = null;
        $mime = null;
        $name = null;
        if ($file) {
            if (!$file->isValid()) {
                return response()->json(['ok' => false, 'error' => $isAr ? 'تعذر رفع الملف.' : 'The file could not be uploaded.'], 422);
            }
            $kind = $this->customerChatKind($file);
            if (!$kind) {
                return response()->json(['ok' => false, 'error' => $isAr ? 'الصيغ المقبولة: صور (JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF) أو فيديو (MP4, WEBM, MOV) أو PDF.' : 'Accepted formats: images (JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF), video (MP4, WEBM, MOV), or PDF.'], 422);
            }
            $path = $file->store('kaheel-orders/'.$order->id.'/chat');
            $mime = kaheel_stored_mime($file);
            if ($kind === 'video' && !str_starts_with((string) $mime, 'video/')) {
                $byExt = ['mp4' => 'video/mp4', 'webm' => 'video/webm', 'mov' => 'video/quicktime', 'm4v' => 'video/mp4'];
                $mime = $byExt[strtolower((string) $file->getClientOriginalExtension())] ?? 'video/mp4';
            }
            $name = $file->getClientOriginalName();
        } elseif ($body === '') {
            return response()->json(['ok' => false, 'error' => $isAr ? 'اكتب رسالة أولاً.' : 'Write a message first.'], 422);
        }

        $message = KaheelOrderMessage::query()->create([
            'order_id' => $order->id,
            'sender_role' => 'customer',
            'sender_name' => $customer->name ?: $customer->username,
            'kind' => $kind,
            'body' => $body,
            'media_path' => $path,
            'media_mime' => $mime,
            'media_name' => $name,
            'customer_seen_at' => now(),
        ]);
        app(KaheelOrderChat::class)->publish($message);

        return response()->json([
            'ok' => true,
            'unread' => $this->unreadCount($order),
            'messages' => $this->customerThread($order->fresh()),
        ]);
    }

    public function messageMedia(KaheelOrder $order, KaheelOrderMessage $message)
    {
        if (!$this->owns($order) || (int) $message->order_id !== (int) $order->id || !$message->media_path) {
            abort(404);
        }
        if (!in_array($message->kind, ['image', 'video', 'audio', 'pdf'], true)) {
            abort(404);
        }

        $path = Storage::disk('local')->path($message->media_path);
        if (!is_file($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $message->media_mime ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    public function saveDesignSelections(Request $request, KaheelOrder $order)
    {
        $isAr = app()->getLocale() !== 'en';
        if (!$this->owns($order) || $order->status !== 'design_review' || $order->design_status === 'approved' || $order->design_signed_at) {
            return response()->json([
                'ok' => false,
                'error' => $isAr ? 'لا يمكن حفظ التعديلات في هذه المرحلة.' : 'Changes cannot be saved at this stage.',
            ], 422);
        }

        $selections = $request->input('selections', []);
        if (!is_array($selections)) {
            $selections = [];
        }

        $this->applyDesignSelections($order, $selections);
        $order->save();

        return response()->json([
            'ok' => true,
            'message' => $isAr ? 'تم حفظ التعديلات.' : 'Changes saved.',
            'addons_total' => (float) $order->addons_total,
            'components' => $order->design_components ?? [],
        ]);
    }

    public function confirmDesign(Request $request, KaheelOrder $order)
    {
        if (!$this->owns($order) || $order->status !== 'design_review') {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => 'forbidden'], 403);
            }

            return redirect(url(app()->getLocale().'/orders/'.$order->id));
        }

        $from = $order->status;
        $decision = $request->input('decision');

        if ($decision === 'approve') {
            $signature = (string) $request->input('signature', '');
            $isAr = app()->getLocale() !== 'en';
            if (!str_starts_with($signature, 'data:image/png;base64,') || strlen($signature) < 80 || strlen($signature) > 500000 || !$this->signatureHasInk($signature)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => false,
                        'error' => $isAr ? 'ارسم توقيعك أولاً لاعتماد التصميم.' : 'Draw your signature before approving the design.',
                    ], 422);
                }

                return redirect(url(app()->getLocale().'/orders/'.$order->id));
            }

            if ($request->filled('selections')) {
                $this->applyDesignSelections($order, $request->input('selections', []));
            }

            $order->design_status = 'approved';
            $order->design_signature = $signature;
            $order->design_signed_at = now();
            $order->status = 'final_payment';
            $order->save();
            $this->customerEvent($order, $from, 'final_payment', 'وافق العميل على التصميم ووقّع');
        } else {
            $isAr = app()->getLocale() !== 'en';
            $note = trim((string) $request->input('note', ''));
            if ($note === '') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => false,
                        'error' => $isAr ? 'اكتب سبب الرفض قبل إرجاع التصميم.' : 'Write the rejection reason before returning the design.',
                    ], 422);
                }

                return redirect(url(app()->getLocale().'/orders/'.$order->id));
            }
            $order->design_status = 'rejected';
            $order->status = 'design_preparation';
            $order->design_signature = null;
            $order->design_signed_at = null;
            $order->save();
            $this->customerEvent($order, $from, 'design_preparation', 'رفض العميل للتصميم: '.$note);
            KaheelOrderMessage::query()->create([
                'order_id' => $order->id,
                'sender_role' => 'customer',
                'sender_name' => $order->customer_name ?: $order->username,
                'kind' => 'text',
                'body' => $isAr ? 'سبب رفض التصميم: '.$note : 'Design rejection reason: '.$note,
                'customer_seen_at' => now(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'status' => $order->status,
                'design_status' => $order->design_status,
            ]);
        }

        return redirect(url(app()->getLocale().'/orders/'.$order->id));
    }

    private function applyDesignSelections(KaheelOrder $order, array $selections): void
    {
        $byId = [];
        foreach ($selections as $row) {
            $id = (string) ($row['componentId'] ?? $row['id'] ?? '');
            if ($id === '') {
                continue;
            }
            $byId[$id] = [
                'extra_qty' => max(0, (float) ($row['extraQty'] ?? $row['extra_qty'] ?? 0)),
                'option_key' => (string) ($row['optionKey'] ?? $row['option_key'] ?? $row['optionId'] ?? ''),
            ];
        }

        $components = collect($order->design_components ?? [])->map(function ($row) use ($byId) {
            $id = (string) ($row['id'] ?? '');
            $pick = $byId[$id] ?? null;
            if ($pick === null) {
                return $row;
            }

            $row['extra_qty'] = $pick['extra_qty'];
            $options = collect($row['options'] ?? []);
            $selected = $options->first(function ($option) use ($pick) {
                $key = trim((string) ($option['name_ar'] ?? '')).'|'.trim((string) ($option['name_en'] ?? ''));

                return $key !== '|' && $key === $pick['option_key'];
            });

            if ($selected) {
                $row['selected_key'] = trim((string) ($selected['name_ar'] ?? '')).'|'.trim((string) ($selected['name_en'] ?? ''));
                $row['extra_cost'] = (float) ($selected['price_delta'] ?? 0);
            } else {
                $row['selected_key'] = '';
                $row['extra_cost'] = 0;
            }

            return $row;
        })->values()->all();

        $order->design_components = $components;
        $order->addons_total = round($this->customerAddonsPreVat($components) * 1.15, 2);
    }

    private function customerAddonsPreVat(array $components): float
    {
        return (float) collect($components)->sum(function ($row) {
            $extra = max(0, (float) ($row['extra_qty'] ?? 0)) * (float) ($row['price_per_unit'] ?? 0);
            $delta = ($row['selected_key'] ?? '') !== '' ? (float) ($row['extra_cost'] ?? 0) : 0.0;

            return $extra + $delta;
        });
    }

    public function submitFinalPayment(KaheelOrder $order)
    {
        if (!$this->owns($order) || $order->status !== 'final_payment' || in_array($order->final_payment_status, ['pending', 'approved'], true)) {
            return redirect(url(app()->getLocale().'/orders/'.$order->id));
        }

        $order->final_payment_method = $order->final_payment_method ?: 'visa';
        $order->final_payment_status = 'pending';
        $order->final_payment_note = null;
        $order->save();
        $this->customerEvent($order, $order->status, $order->status, 'تم السداد النهائي');

        return redirect(url(app()->getLocale().'/orders/'.$order->id));
    }

    private function customerChatLocked(KaheelOrder $order): bool
    {
        if ($order->design_status === 'approved' || $order->design_signed_at) {
            return true;
        }

        return in_array($order->status, ['final_payment', 'final_approval', 'in_supply', 'supplied', 'closed'], true);
    }

    public function file(KaheelOrder $order, KaheelOrderFile $file)
    {
        $mime = (string) $file->mime;
        $allowed = (str_starts_with($mime, 'image/') && !str_contains($mime, 'svg')) || $mime === 'application/pdf';
        if (!$this->owns($order) || (int) $file->order_id !== (int) $order->id || !in_array($file->kind, ['plan', 'design', 'receipt', 'final_receipt', null, ''], true) || !$allowed) {
            abort(404);
        }

        $path = Storage::disk('local')->path($file->path);
        if (!is_file($path)) {
            abort(404);
        }

        return response()
            ->file($path, [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
            ])
            ->setContentDisposition('inline', $file->original_name);
    }

    private function customerEvent(KaheelOrder $order, ?string $from, string $to, string $note): void
    {
        KaheelOrderEvent::query()->create([
            'order_id' => $order->id,
            'from_status' => $from,
            'to_status' => $to,
            'note' => $note,
            'actor_name' => $order->customer_name ?: $order->username,
        ]);
    }

    private function signatureHasInk(string $data): bool
    {
        $raw = base64_decode(substr($data, strpos($data, ',') + 1), true);
        if ($raw === false || !function_exists('imagecreatefromstring')) {
            return strlen($data) > 8000;
        }

        $image = @imagecreatefromstring($raw);
        if (!$image) {
            return false;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $ink = 0;
        for ($y = 0; $y < $height; $y += 3) {
            for ($x = 0; $x < $width; $x += 3) {
                $color = imagecolorat($image, $x, $y);
                $alpha = ($color >> 24) & 0x7F;
                $red = ($color >> 16) & 0xFF;
                $green = ($color >> 8) & 0xFF;
                $blue = $color & 0xFF;
                if ($alpha < 100 && ($red < 245 || $green < 245 || $blue < 245)) {
                    $ink++;
                    if ($ink > 12) {
                        imagedestroy($image);

                        return true;
                    }
                }
            }
        }
        imagedestroy($image);

        return false;
    }

    private function unreadCount(KaheelOrder $order): int
    {
        return $order->messages()->where('sender_role', 'admin')->whereNull('customer_seen_at')->count();
    }

    private function customerThread(KaheelOrder $order): array
    {
        $locale = app()->getLocale();

        return $order->messages()->orderBy('id')->get()->map(function (KaheelOrderMessage $message) use ($locale, $order) {
            return [
                'id' => $message->id,
                'role' => $message->sender_role,
                'name' => $message->sender_name,
                'kind' => $message->kind ?: 'text',
                'body' => $message->body,
                'file_name' => $message->media_name,
                'url' => $message->media_path ? url($locale.'/orders/'.$order->id.'/messages/'.$message->id.'/media') : null,
                'created_at' => kaheel_iso($message->created_at),
            ];
        })->all();
    }

    private function customerChatKind($file): ?string
    {
        [$mime, $client, $ext] = kaheel_upload_parts($file);
        if (kaheel_upload_blocked($mime, $client, $ext)) {
            return null;
        }
        $video = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-m4v'];
        if (in_array($mime, $video, true) || (kaheel_detected_weak($mime) && (in_array($client, $video, true) || in_array($ext, ['mp4', 'webm', 'mov', 'm4v'], true)))) {
            return 'video';
        }
        if (kaheel_file_is_pdf($mime, $client, $ext)) {
            return 'pdf';
        }
        if (kaheel_file_is_image($mime, $client, $ext)) {
            return 'image';
        }

        return null;
    }

    private function owns(KaheelOrder $order): bool
    {
        $customer = kaheel_customer();

        return $customer && (int) $order->bricks_user_id === (int) $customer->bricks_user_id;
    }

    private function requestTooLarge(Request $request): bool
    {
        $length = (int) $request->server('CONTENT_LENGTH');

        return $length > 20 * 1024 * 1024 && $request->request->count() === 0 && $request->files->count() === 0;
    }

    private function fileError(array $files, bool $isAr): ?string
    {
        $present = array_values(array_filter($files));
        if ($present === []) {
            return $isAr ? 'مخطط الوحدة إلزامي. ارفع صورة أو ملف PDF للمخطط.' : 'The unit plan is required. Upload a plan image or PDF.';
        }
        if (count($present) > 5) {
            return $isAr ? 'يمكن رفع 5 ملفات كحد أقصى.' : 'You can upload up to 5 files.';
        }

        foreach ($present as $file) {
            if (!$file) {
                continue;
            }
            $tooBig = in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || ($file->isValid() && $file->getSize() > 20 * 1024 * 1024);
            if ($tooBig) {
                return $isAr ? 'أحد الملفات أكبر من 20 ميغابايت. صغّر الملف ثم أعد الإرسال، ولن يُحفظ الطلب قبل نجاح الرفع.' : 'One file is larger than 20 MB. Reduce it and send again. The order is not saved until the upload succeeds.';
            }
            if (!$file->isValid() || !kaheel_document_allowed($file)) {
                return $isAr ? 'الصيغ المقبولة: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF.' : 'Accepted formats: JPG, PNG, WEBP, GIF, BMP, TIFF, HEIC, AVIF, and PDF.';
            }
        }

        return null;
    }

    private function compoundSnapshot(array $compound): array
    {
        return [
            'id' => $compound['id'] ?? null,
            'display_name' => (string) ($compound['display_name'] ?? ''),
            'compound_number' => (string) ($compound['compound_number'] ?? ''),
            'short_name' => (string) ($compound['short_name'] ?? ''),
            'owned_units_count' => $compound['owned_units_count'] ?? null,
        ];
    }

    private function unitSnapshot(array $unit): array
    {
        return [
            'id' => $unit['id'] ?? null,
            'unit_no' => (string) ($unit['unit_no'] ?? ''),
            'bedrooms_number' => $unit['bedrooms_number'] ?? null,
            'bathrooms_number' => $unit['bathrooms_number'] ?? null,
            'status_text' => (string) ($unit['status_text'] ?? ''),
            'squar_meters' => $unit['squar_meters'] ?? null,
        ];
    }

    private function compound(array $compounds, string $id): ?array
    {
        foreach ($compounds as $compound) {
            if ((string) ($compound['id'] ?? '') === $id) {
                return $compound;
            }
        }

        return null;
    }

    private function unit(array $compound, string $id): ?array
    {
        foreach ($compound['units'] ?? [] as $unit) {
            if ((string) ($unit['id'] ?? '') === $id) {
                return $unit;
            }
        }

        return null;
    }

    private function contract(string $packageName, string $unitNo, float $total, float $deposit, string $days, string $lang): string
    {
        $totalText = number_format($total, 2, '.', ',');
        $depositText = number_format($deposit, 2, '.', ',');
        if ($lang === 'en') {
            return implode("\n", [
                'Furnishing services agreement — FORMA BY AZURE / KAHEEL',
                "1. Scope: supply, delivery and installation of the {$packageName} furnishing package for unit {$unitNo}.",
                "2. Value: {$totalText} SAR including VAT. A deposit of {$depositText} SAR is payable on signature.",
                '3. Design: the company prepares the design after reviewing the unit plan submitted by the client.',
                '4. Modifications: any change or upgrade selected by the client is priced separately and added to the final invoice.',
                '5. Final payment: the remaining balance and all add-ons are paid before production begins.',
                "6. Delivery: {$days} working days from final payment, excluding Friday and Saturday.",
                '7. Handover: handover takes place at the unit and is recorded with a signed handover report.',
                '8. This agreement is stored with the order and cannot be modified after signature.',
            ]);
        }

        return implode("\n", [
            'عقد تقديم خدمات تأثيث — FORMA BY AZURE / كحيل',
            "١. نطاق العمل: توريد وتركيب وتسليم باقة التأثيث {$packageName} للوحدة {$unitNo}.",
            "٢. القيمة: {$totalText} ريال شاملة ضريبة القيمة المضافة، ويُسدَّد عربون قدره {$depositText} ريال عند التوقيع.",
            '٣. التصميم: تقوم الشركة بإعداد التصميم بعد مراجعة مخطط الوحدة المرفوع من العميل.',
            '٤. التعديلات: أي تغيير أو بديل يختاره العميل يُسعَّر بشكل منفصل ويُضاف إلى الفاتورة النهائية.',
            '٥. السداد النهائي: يُسدَّد المتبقي مع قيمة التعديلات قبل بدء التنفيذ والتوريد.',
            "٦. مدة التسليم: {$days} يوم عمل من تاريخ السداد النهائي، عدا الجمعة والسبت.",
            '٧. الاستلام: يتم الاستلام في الوحدة ويُوثَّق بمحضر استلام موقّع يُرفق بالطلب.',
            '٨. يُحفظ هذا العقد ضمن مستندات الطلب ولا يمكن تعديله بعد التوقيع.',
        ]);
    }
}
