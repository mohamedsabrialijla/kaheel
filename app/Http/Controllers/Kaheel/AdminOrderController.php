<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelAlternative;
use App\Support\KaheelPermissions;
use App\Models\KaheelCategory;
use App\Models\KaheelOrder;
use App\Models\KaheelOrderEvent;
use App\Models\KaheelOrderFile;
use App\Models\KaheelOrderMessage;
use App\Models\KaheelPackage;
use App\Models\KaheelSupplier;
use App\Services\KaheelOrderChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminOrderController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index', 'show', 'packageItems', 'file', 'messageMedia' => 'orders.view',
            'reviewPayment', 'confirmFinal', 'advance', 'saveComponents', 'sendDesign',
            'assignSuppliers', 'markDelivered', 'message', 'upload' => 'orders.edit',
            'readMessages' => 'orders.view',
            default => null,
        };
    }

    public function index()
    {
        $orders = KaheelOrder::query()
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_role', 'customer')->whereNull('admin_seen_at');
            }])
            ->orderByDesc('id')
            ->get()
            ->each(fn (KaheelOrder $order) => $order->promoteApprovedFinalPayment())
            ->map(fn (KaheelOrder $order) => [
            'id' => $order->id,
            'number' => $order->number,
            'customer_name' => $order->customer_name ?: $order->username,
            'unit_no' => $order->unit_no,
            'compound_name' => $order->compound_name,
            'amount' => round((float) $order->price_incl + (float) $order->addons_total, 2),
            'status' => $order->listedStatus(),
            'status_ar' => $order->statusCopy()['ar'],
            'status_en' => $order->statusCopy()['en'],
            'step' => $order->progressStep(),
            'percent' => $order->progressPercent(),
            'created_at' => kaheel_iso($order->created_at),
            'unread' => (int) $order->unread_count,
        ]);

        return mainResponse(true, 'Success', $orders, 200, 'items', '');
    }

    public function show(Request $request)
    {
        $order = $this->order($request->integer('id'));

        return mainResponse(true, 'Success', $this->detail($order), 200, 'items', '');
    }

    public function notices()
    {
        if (!KaheelPermissions::allows(auth('admin')->user(), 'orders.view')) {
            return mainResponse(true, 'Success', ['total' => 0, 'orders' => [], 'items' => []], 200, 'items', '');
        }

        $isAr = app()->getLocale() !== 'en';
        $counts = KaheelOrderMessage::query()
            ->selectRaw('order_id, count(*) as unread')
            ->where('sender_role', 'customer')
            ->whereNull('admin_seen_at')
            ->groupBy('order_id')
            ->pluck('unread', 'order_id');
        $latest = KaheelOrderMessage::query()
            ->with('order:id,number,customer_name,username')
            ->where('sender_role', 'customer')
            ->whereNull('admin_seen_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $chatItems = $latest->map(fn (KaheelOrderMessage $message) => [
            'id' => 'chat-'.$message->id,
            'order_id' => $message->order_id,
            'number' => $message->order->number ?? '',
            'name' => $message->sender_name,
            'kind' => $message->kind ?: 'text',
            'body' => trim((string) $message->body) ?: ($isAr ? 'رسالة جديدة من العميل' : 'New customer message'),
            'created_at' => kaheel_iso($message->created_at),
            'chat' => true,
        ]);

        $alerts = collect();
        $pushAlert = function (KaheelOrder $order, string $id, string $kind, string $ar, string $en) use ($alerts, $isAr) {
            $alerts->push([
                'id' => $id,
                'order_id' => $order->id,
                'number' => $order->number,
                'name' => $order->customer_name ?: $order->username,
                'kind' => $kind,
                'body' => $isAr ? $ar : $en,
                'created_at' => kaheel_iso($order->updated_at),
                'chat' => false,
            ]);
        };

        KaheelOrder::query()->where('payment_status', 'pending')->orderByDesc('updated_at')->limit(8)->get()
            ->each(fn (KaheelOrder $order) => $pushAlert($order, 'pay-'.$order->id, 'payment', 'دفعة بانتظار التأكيد', 'Payment awaiting confirmation'));
        KaheelOrder::query()->where('final_payment_status', 'pending')->orderByDesc('updated_at')->limit(8)->get()
            ->each(fn (KaheelOrder $order) => $pushAlert($order, 'final-'.$order->id, 'final', 'سداد نهائي بانتظار التأكيد', 'Final payment awaiting confirmation'));
        KaheelOrder::query()->where('status', 'admin_review')->where('payment_status', 'approved')->orderByDesc('updated_at')->limit(8)->get()
            ->each(fn (KaheelOrder $order) => $pushAlert($order, 'review-'.$order->id, 'order', 'طلب جديد جاهز للمراجعة', 'New order ready for review'));

        $items = $chatItems->concat($alerts)->sortByDesc('created_at')->take(12)->values();

        return mainResponse(true, 'Success', [
            'total' => (int) $counts->sum() + $alerts->count(),
            'orders' => $counts->map(fn ($unread, $id) => ['id' => (int) $id, 'unread' => (int) $unread])->values(),
            'items' => $items,
        ], 200, 'items', '');
    }

    public function readMessages(Request $request)
    {
        $order = $this->order($request->integer('id'));
        $order->messages()
            ->where('sender_role', 'customer')
            ->whereNull('admin_seen_at')
            ->update(['admin_seen_at' => now()]);

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function reviewPayment(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if (!$order->payment_method) {
            return mainResponse(false, 'لا توجد عملية دفع على هذا الطلب.', null, 422, 'items', '');
        }

        $decision = $request->input('decision');
        if ($decision === 'approved') {
            $from = $order->status;
            $order->payment_status = 'approved';
            $order->payment_note = null;
            if ($order->status === 'deposit_pending') {
                $order->status = 'admin_review';
            }
            $order->save();
            KaheelOrderFile::markLatest($order->id, 'receipt', 'approved');
            $this->event($order, $from, $order->status, 'تم تأكيد الدفع');

            return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
        }

        $note = trim((string) $request->input('note'));
        if ($decision !== 'rejected' || $note === '') {
            return mainResponse(false, 'اكتب ملاحظة الرفض لتظهر للعميل.', null, 422, 'items', '');
        }

        $from = $order->status;
        $order->payment_status = 'rejected';
        $order->payment_note = $note;
        $order->status = 'deposit_pending';
        $order->save();
        KaheelOrderFile::markLatest($order->id, 'receipt', 'rejected');
        $this->event($order, $from, 'deposit_pending', 'تم رفض الدفع: '.$note);

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function confirmFinal(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($order->status !== 'final_payment') {
            return mainResponse(false, 'تأكيد السداد النهائي متاح في مرحلة السداد النهائي فقط.', null, 422, 'items', '');
        }
        if ($order->payment_status !== 'approved') {
            return mainResponse(false, 'لا يمكن تغيير المرحلة قبل تأكيد الدفع.', null, 422, 'items', '');
        }

        $decision = (string) $request->input('decision', 'approved');
        if ($decision === 'rejected') {
            if ($order->final_payment_status !== 'pending') {
                return mainResponse(false, 'رفض السداد النهائي متاح بعد أن يرسل العميل الدفع.', null, 422, 'items', '');
            }
            $note = trim((string) $request->input('note'));
            if ($note === '') {
                return mainResponse(false, 'اكتب ملاحظة الرفض لتظهر للعميل.', null, 422, 'items', '');
            }
            $order->final_payment_status = 'rejected';
            $order->final_payment_note = $note;
            $order->save();
            KaheelOrderFile::markLatest($order->id, 'final_receipt', 'rejected');
            $this->event($order, $order->status, $order->status, 'تم رفض السداد النهائي: '.$note);

            return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
        }

        if ($order->final_payment_status !== 'pending') {
            return mainResponse(false, 'لا يظهر تأكيد السداد النهائي قبل أن يرسل العميل الدفع.', null, 422, 'items', '');
        }

        $from = $order->status;
        $order->final_payment_status = 'approved';
        $order->final_payment_note = null;
        $order->status = 'final_approval';
        $order->save();
        KaheelOrderFile::markLatest($order->id, 'final_receipt', 'approved');
        $this->event($order, $from, $order->status, 'تم تأكيد السداد النهائي');

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function advance(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($block = $this->stageBlock($order)) {
            return mainResponse(false, $block['ar'], null, 422, 'items', '');
        }
        $next = $order->nextStatus();
        if (!$next || $request->input('to_status') !== $next) {
            return mainResponse(false, 'لا يمكن نقل هذا الطلب إلى المرحلة المطلوبة.', null, 422, 'items', '');
        }

        $from = $order->status;
        if ($next === 'closed' && !$this->hasHandover($order)) {
            return mainResponse(false, kaheel_msg('ارفع محضر الاستلام قبل إغلاق الطلب.', 'Upload the handover report before closing the order.'), null, 422, 'items', '');
        }
        if ($next === 'design_review') {
            $order->design_status = 'sent';
        }
        $order->status = $next;
        $order->save();
        $this->event($order, $from, $next, $request->input('note'));

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function packageItems(Request $request)
    {
        $order = $this->order($request->integer('id'));
        $package = KaheelPackage::query()->where('code', $order->package_code)->first();
        $tier = $package?->tiers()
            ->where(function ($query) use ($order) {
                $query->where('name_ar', $order->tier_name_ar)->orWhere('name_en', $order->tier_name_en);
            })
            ->first();
        $items = $tier ? $tier->items()->get() : collect();

        return mainResponse(true, 'Success', $items->map(fn ($item) => [
            'category' => $item->category ?: 'furniture',
            'name_ar' => $item->name_ar,
            'name_en' => $item->name_en,
            'unit_label_ar' => $item->unit_label_ar ?: 'قطعة',
            'unit_label_en' => $item->unit_label_en ?: 'piece',
            'base_qty' => (float) $item->quantity,
            'price_per_unit' => (float) $item->price_per_unit,
            'allow_alternatives' => (bool) $item->allow_alternatives,
            'category_key' => $item->category ?: 'furniture',
        ]), 200, 'items', '');
    }

    public function saveComponents(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($blocked = $this->needsPaymentApproval($order)) {
            return $blocked;
        }
        if ($order->status !== 'design_preparation') {
            return mainResponse(false, 'تسعير المكوّنات متاح أثناء إعداد التصميم.', null, 422, 'items', '');
        }

        $components = collect($request->input('components', []))->map(function ($row) {
            return [
                'id' => $row['id'] ?? (string) Str::uuid(),
                'category' => $row['category'] ?? 'furniture',
                'name_ar' => trim((string) ($row['name_ar'] ?? '')),
                'name_en' => trim((string) ($row['name_en'] ?? '')),
                'unit_label_ar' => $row['unit_label_ar'] ?? 'قطعة',
                'unit_label_en' => $row['unit_label_en'] ?? 'piece',
                'base_qty' => (float) ($row['base_qty'] ?? 0),
                'price_per_unit' => (float) ($row['price_per_unit'] ?? 0),
                'extra_cost' => (float) ($row['extra_cost'] ?? 0),
                'selected_key' => (string) ($row['selected_key'] ?? ''),
                'added' => !empty($row['added']),
                'priced' => !empty($row['priced']),
                'options' => collect($row['options'] ?? [])->map(fn ($option) => [
                    'name_ar' => trim((string) ($option['name_ar'] ?? '')),
                    'name_en' => trim((string) ($option['name_en'] ?? '')),
                    'price_delta' => (float) ($option['price_delta'] ?? 0),
                ])->filter(fn ($option) => $option['name_ar'] !== '' || $option['name_en'] !== '')->values()->all(),
            ];
        })->filter(fn ($row) => $row['name_ar'] !== '' || $row['name_en'] !== '')->values()->all();

        $order->design_components = $components;
        $order->addons_total = round(collect($components)->sum(fn ($row) => $this->componentCharge($row)), 2);
        $order->save();

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    private function componentCharge(array $row): float
    {
        $line = ((float) $row['base_qty'] * (float) $row['price_per_unit']) + (float) $row['extra_cost'];
        if (!empty($row['added']) || !empty($row['priced'])) {
            return $line;
        }

        return ($row['selected_key'] ?? '') !== '' ? (float) $row['extra_cost'] : 0.0;
    }

    public function sendDesign(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($blocked = $this->needsPaymentApproval($order)) {
            return $blocked;
        }
        $allowed = $order->status === 'design_preparation' || $order->design_status === 'rejected';
        if (!$allowed) {
            return mainResponse(false, 'إرسال التصميم متاح أثناء إعداده أو بعد رفض العميل.', null, 422, 'items', '');
        }
        if (!$this->hasFreshDesign($order)) {
            return mainResponse(false, 'ارفع التصميم (صورة أو ملف) قبل إرساله لمراجعة العميل.', null, 422, 'items', '');
        }

        $from = $order->status;
        $order->design_status = 'sent';
        $order->status = 'design_review';
        $order->save();
        $this->event($order, $from, $order->status, $from === 'design_review' ? 'تم إعادة إرسال التصميم للعميل' : 'تم إرسال التصميم للعميل');

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function assignSuppliers(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($blocked = $this->needsPaymentApproval($order)) {
            return $blocked;
        }
        if (!in_array($order->status, ['final_approval', 'in_supply'], true)) {
            return mainResponse(false, 'إسناد الموردين متاح بعد الموافقة النهائية وأثناء التوريد.', null, 422, 'items', '');
        }

        $current = $order->supplier_assignments ?? [];
        foreach ($request->input('assignments', []) as $row) {
            $supplier = KaheelSupplier::query()->find($row['supplier_id'] ?? 0);
            $scope = trim((string) ($row['scope_ar'] ?? ''));
            if (!$supplier || $scope === '') {
                continue;
            }
            $current[] = [
                'id' => (string) Str::uuid(),
                'supplier_id' => $supplier->id,
                'supplier_name_ar' => $supplier->name_ar,
                'supplier_name_en' => $supplier->name_en,
                'scope_ar' => $scope,
                'scope_en' => trim((string) ($row['scope_en'] ?? '')) ?: $scope,
                'cost' => (float) ($row['cost'] ?? 0),
                'expected_date' => $row['expected_date'] ?: null,
                'delivered_at' => null,
            ];
        }
        $order->supplier_assignments = array_values($current);
        $order->save();

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function markDelivered(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($blocked = $this->needsPaymentApproval($order)) {
            return $blocked;
        }
        $rows = $order->supplier_assignments ?? [];
        foreach ($rows as &$row) {
            if (($row['id'] ?? null) === $request->input('assignment_id')) {
                $row['delivered_at'] = $request->input('delivered_at') ?: now()->toDateString();
            }
        }
        unset($row);
        $order->supplier_assignments = $rows;
        $order->save();

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function message(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($blocked = $this->needsPaymentApproval($order)) {
            return $blocked;
        }

        $kind = (string) $request->input('kind', 'text');
        if (!in_array($kind, ['text', 'image', 'video', 'audio'], true)) {
            $kind = 'text';
        }
        $body = trim((string) $request->input('body', ''));
        $file = $request->file('file');

        if ($kind === 'text') {
            if ($body === '') {
                return mainResponse(false, 'اكتب رسالة أولاً.', null, 422, 'items', '');
            }
            $message = KaheelOrderMessage::query()->create([
                'order_id' => $order->id,
                'sender_role' => 'admin',
                'sender_name' => $this->actor(),
                'kind' => 'text',
                'body' => $body,
                'admin_seen_at' => now(),
            ]);
            app(KaheelOrderChat::class)->publish($message);

            return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
        }

        $tooBig = $file && (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || ($file->isValid() && $file->getSize() > 25 * 1024 * 1024));
        if ($tooBig) {
            return mainResponse(false, 'كل ملف يجب أن يكون 25 ميغابايت أو أقل.', null, 422, 'items', '');
        }
        if (!$file || !$file->isValid() || !$this->chatMimeAllowed($kind, $file)) {
            return mainResponse(false, 'صيغة الملف غير مدعومة. الصور: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF. الفيديو: MP4 و WEBM و MOV.', null, 422, 'items', '');
        }

        $message = KaheelOrderMessage::query()->create([
            'order_id' => $order->id,
            'sender_role' => 'admin',
            'sender_name' => $this->actor(),
            'kind' => $kind,
            'body' => $body,
            'media_path' => $file->store('kaheel-orders/'.$order->id.'/chat'),
            'media_mime' => $kind === 'image' ? kaheel_stored_mime($file) : ($file->getMimeType() ?: $file->getClientMimeType()),
            'media_name' => $file->getClientOriginalName(),
            'duration' => $kind === 'audio' ? max(0, (int) $request->input('duration', 0)) : null,
            'admin_seen_at' => now(),
        ]);
        app(KaheelOrderChat::class)->publish($message);

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function messageMedia(KaheelOrderMessage $message)
    {
        if (!in_array($message->kind, ['image', 'video', 'audio', 'pdf'], true) || !$message->media_path) {
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

    public function upload(Request $request)
    {
        $order = $this->order($request->integer('id'));
        if ($blocked = $this->needsPaymentApproval($order)) {
            return $blocked;
        }
        $kind = $request->input('kind') === 'handover' ? 'handover' : 'design';
        if (in_array($order->status, ['deposit_pending', 'closed'], true)) {
            return mainResponse(false, 'لا يمكن رفع ملفات في هذه المرحلة.', null, 422, 'items', '');
        }

        $file = $request->file('file');
        $tooBig = $file && (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || ($file->isValid() && $file->getSize() > 25 * 1024 * 1024));
        if ($tooBig) {
            return mainResponse(false, 'كل ملف يجب أن يكون 25 ميغابايت أو أقل.', null, 422, 'items', '');
        }
        if (!$file || !$file->isValid() || !kaheel_document_allowed($file)) {
            return mainResponse(false, 'الصيغ المقبولة: JPG و PNG و WEBP و GIF و BMP و TIFF و HEIC و AVIF و PDF.', null, 422, 'items', '');
        }

        KaheelOrderFile::query()->create([
            'order_id' => $order->id,
            'kind' => $kind,
            'original_name' => $file->getClientOriginalName(),
            'path' => $file->store('kaheel-orders/'.$order->id),
            'mime' => kaheel_stored_mime($file),
            'size' => $file->getSize(),
        ]);

        if ($kind === 'design' && $order->status === 'design_review' && $order->design_status !== 'approved') {
            $order->design_status = 'sent';
            $order->save();
        }

        return mainResponse(true, 'Success', $this->detail($order->fresh()), 200, 'items', '');
    }

    public function file(KaheelOrderFile $file)
    {
        $mime = (string) $file->mime;
        $allowed = (str_starts_with($mime, 'image/') && !str_contains($mime, 'svg')) || $mime === 'application/pdf';
        $path = Storage::disk('local')->path($file->path);
        if (!$allowed || !is_file($path)) {
            abort(404);
        }

        return response()
            ->file($path, [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
            ])
            ->setContentDisposition('inline', $file->original_name);
    }

    private function order(int $id): KaheelOrder
    {
        $order = KaheelOrder::query()->with(['files', 'events', 'messages'])->findOrFail($id);
        $order->promoteApprovedFinalPayment();

        return $order->fresh(['files', 'events', 'messages']);
    }

    private function detail(KaheelOrder $order): array
    {
        $order->load(['files', 'events', 'messages']);
        $copy = $order->statusCopy();
        $next = $order->nextStatus();
        $gate = $this->stageBlock($order);
        $nextCopy = $next ? KaheelOrder::stageCopy()[array_search($next, KaheelOrder::workflow(), true)] : null;
        $paid = $order->payment_status === 'approved' ? (float) $order->deposit_amount : 0;
        $final = round((float) $order->price_incl + (float) $order->addons_total, 2);
        if ($order->final_payment_status === 'approved') {
            $paid = $final;
        }
        $locale = app()->getLocale();

        return [
            'id' => $order->id,
            'number' => $order->number,
            'status' => $order->listedStatus(),
            'status_ar' => $copy['ar'],
            'status_en' => $copy['en'],
            'step' => $order->progressStep(),
            'percent' => $order->progressPercent(),
            'next_status' => $gate === null ? $next : null,
            'next_ar' => $nextCopy['ar'] ?? null,
            'next_en' => $nextCopy['en'] ?? null,
            'gate_ar' => $gate['ar'] ?? null,
            'gate_en' => $gate['en'] ?? null,
            'design_ready' => $this->hasFreshDesign($order),
            'customer_name' => $order->customer_name ?: '—',
            'username' => $order->username,
            'compound_name' => $order->compound_name,
            'unit_no' => $order->unit_no,
            'bedrooms' => $order->bedrooms_number,
            'area' => $order->squar_meters,
            'package_ar' => $order->package_name_ar,
            'package_en' => $order->package_name_en,
            'tier_ar' => $order->tier_name_ar,
            'tier_en' => $order->tier_name_en,
            'created_at' => kaheel_iso($order->created_at),
            'design_status' => $order->design_status ?: 'none',
            'design_signed_at' => kaheel_iso($order->design_signed_at),
            'price_excl' => (float) $order->price_excl,
            'vat' => round((float) $order->price_incl - (float) $order->price_excl, 2),
            'price_incl' => (float) $order->price_incl,
            'addons_total' => (float) $order->addons_total,
            'final_total' => $final,
            'deposit_amount' => (float) $order->deposit_amount,
            'deposit_paid' => $order->payment_status === 'approved',
            'paid' => $paid,
            'remaining' => round(max(0, $final - $paid), 2),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_note' => $order->payment_note,
            'final_payment_status' => $order->final_payment_status,
            'final_payment_method' => $order->final_payment_method,
            'final_payment_note' => $order->final_payment_note,
            'contract_body' => $order->contract_body,
            'components' => $order->design_components ?? [],
            'assignments' => $order->supplier_assignments ?? [],
            'files' => $order->files->map(fn (KaheelOrderFile $file) => [
                'id' => $file->id,
                'kind' => $file->kind,
                'name' => $file->original_name,
                'mime' => $file->mime,
                'review_status' => $file->review_status,
                'url' => url($locale.'/web/KaheelOrders/file/'.$file->id),
                'created_at' => kaheel_iso($file->created_at),
            ])->values(),
            'events' => $order->events->sortBy('id')->values()->map(fn (KaheelOrderEvent $event) => [
                'id' => $event->id,
                'from_status' => $event->from_status,
                'to_status' => $event->to_status,
                'note' => $event->note,
                'actor_name' => $event->actor_name,
                'created_at' => kaheel_iso($event->created_at),
            ]),
            'unread' => $order->messages->where('sender_role', 'customer')->whereNull('admin_seen_at')->count(),
            'messages' => $order->messages->sortBy('id')->values()->map(fn (KaheelOrderMessage $message) => [
                'id' => $message->id,
                'role' => $message->sender_role,
                'name' => $message->sender_name,
                'kind' => $message->kind ?: 'text',
                'body' => $message->body,
                'url' => $message->media_path ? url($locale.'/web/KaheelOrders/messageMedia/'.$message->id) : null,
                'file_name' => $message->media_name,
                'duration' => $message->duration,
                'created_at' => kaheel_iso($message->created_at),
            ]),
            'alternatives' => KaheelAlternative::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'category', 'name_ar', 'name_en', 'price_delta']),
            'categories' => KaheelCategory::catalog(),
            'suppliers' => KaheelSupplier::query()->orderBy('name_ar')->get(['id', 'name_ar', 'name_en']),
        ];
    }

    private function chatMimeAllowed(string $kind, $file): bool
    {
        [$mime, $client, $ext] = kaheel_upload_parts($file);
        if (kaheel_upload_blocked($mime, $client, $ext)) {
            return false;
        }
        if ($kind === 'image') {
            return kaheel_file_is_image($mime, $client, $ext);
        }
        if ($kind === 'video') {
            $video = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-m4v'];

            return in_array($mime, $video, true) || (kaheel_detected_weak($mime) && (in_array($client, $video, true) || in_array($ext, ['mp4', 'webm', 'mov', 'm4v'], true)));
        }
        $audio = ['audio/webm', 'audio/ogg', 'audio/mp4', 'audio/mpeg', 'audio/wav', 'audio/x-wav', 'video/webm'];

        return in_array($mime, $audio, true) || in_array($client, $audio, true);
    }

    private function event(KaheelOrder $order, ?string $from, string $to, ?string $note): void
    {
        KaheelOrderEvent::query()->create([
            'order_id' => $order->id,
            'from_status' => $from,
            'to_status' => $to,
            'note' => trim((string) $note) ?: null,
            'actor_name' => $this->actor(),
        ]);
    }

    private function stageBlock(KaheelOrder $order): ?array
    {
        if ($order->payment_status !== 'approved') {
            return ['ar' => 'لا يمكن تغيير المرحلة قبل تأكيد الدفع.', 'en' => 'The stage cannot change before the payment is confirmed.'];
        }

        $order->loadMissing('files', 'events');

        return match ($order->status) {
            'design_preparation' => $this->hasFreshDesign($order) ? null : [
                'ar' => 'ارفع التصميم (صورة أو ملف، ويمكن عدة ملفات) قبل نقل الطلب لمراجعة العميل.',
                'en' => 'Upload the design (an image or file, several files are allowed) before moving the order to customer review.',
            ],
            'design_review' => !$this->hasFreshDesign($order) ? [
                'ar' => 'ارفع التصميم أولاً. لن تتغير المرحلة قبل رفعه وموافقة العميل.',
                'en' => 'Upload the design first. The stage cannot change before the upload and the customer approval.',
            ] : ($order->design_status === 'approved' ? null : [
                'ar' => 'بانتظار موافقة العميل على التصميم. لا تتغير المرحلة قبلها.',
                'en' => 'Waiting for the customer to approve the design. The stage cannot change before that.',
            ]),
            'final_payment' => $order->final_payment_status === 'approved' ? null : (
                $order->final_payment_status === 'pending'
                    ? [
                        'ar' => 'أكّد استلام السداد النهائي قبل نقل الطلب.',
                        'en' => 'Confirm the final payment before moving the order.',
                    ]
                    : [
                        'ar' => 'بانتظار دفع العميل للسداد النهائي.',
                        'en' => 'Waiting for the customer to pay the final amount.',
                    ]
            ),
            'final_approval' => !empty($order->supplier_assignments) ? null : [
                'ar' => 'أسند مورداً واحداً على الأقل قبل نقل الطلب للتنفيذ.',
                'en' => 'Assign at least one supplier before moving the order into production.',
            ],
            'in_supply' => ($order->files->contains('kind', 'handover') || collect($order->supplier_assignments ?? [])->contains(fn ($row) => !empty($row['delivered_at']))) ? null : [
                'ar' => 'سجّل تاريخ توريد أو ارفع محضر الاستلام قبل تأكيد التوريد.',
                'en' => 'Record a delivery date or upload the handover report before confirming supply.',
            ],
            'supplied' => $this->hasHandover($order) ? null : [
                'ar' => 'ارفع محضر الاستلام قبل إغلاق الطلب.',
                'en' => 'Upload the handover report before closing the order.',
            ],
            'closed' => ['ar' => 'اكتملت دورة هذا الطلب.', 'en' => 'This order workflow is complete.'],
            default => null,
        };
    }

    private function hasFreshDesign(KaheelOrder $order): bool
    {
        $order->loadMissing('files');

        return $order->files->contains('kind', 'design');
    }

    private function hasHandover(KaheelOrder $order): bool
    {
        $order->loadMissing('files');

        return $order->files->contains('kind', 'handover');
    }

    private function needsPaymentApproval(KaheelOrder $order)
    {
        if ($order->payment_status === 'approved') {
            return null;
        }

        return mainResponse(false, 'أكّد الدفع أولاً قبل متابعة باقي الإجراءات.', null, 422, 'items', '');
    }

    private function actor(): string
    {
        return auth('admin')->user()->name ?? 'Admin';
    }
}
