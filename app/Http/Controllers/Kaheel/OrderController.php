<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\KaheelOrder;
use App\Models\KaheelOrderFile;
use App\Models\KaheelPackage;
use App\Models\KaheelSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if (!str_starts_with($signature, 'data:image/png;base64,') || strlen($signature) < 80 || strlen($signature) > 500000) {
            return mainResponse(false, $isAr ? 'يلزم التوقيع.' : 'A signature is required.', null, 422, 'items', '');
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
                'unit_id' => (string) ($unit['id'] ?? ''),
                'unit_no' => (string) ($unit['unit_no'] ?? ''),
                'bedrooms_number' => $unit['bedrooms_number'] ?? null,
                'squar_meters' => $unit['squar_meters'] ?? null,
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

        $files = $request->file('plans', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }
        foreach (array_slice($files, 0, 5) as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], true)) {
                continue;
            }
            KaheelOrderFile::query()->create([
                'order_id' => $order->id,
                'original_name' => $file->getClientOriginalName(),
                'path' => $file->store('kaheel-orders/'.$order->id),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return mainResponse(true, $isAr ? 'تم إنشاء الطلب.' : 'Order created.', [
            'id' => $order->id,
            'number' => $order->number,
        ], 200, 'items', '');
    }

    public function index()
    {
        $customer = kaheel_customer();
        if (!$customer) {
            return redirect(url(app()->getLocale().'/auth?redirect=/orders'));
        }

        $orders = KaheelOrder::query()
            ->where('bricks_user_id', $customer->bricks_user_id)
            ->orderByDesc('id')
            ->get();

        return view('kaheel.orders', ['orders' => $orders]);
    }

    public function show(KaheelOrder $order)
    {
        $customer = kaheel_customer();
        if (!$customer || (int) $order->bricks_user_id !== (int) $customer->bricks_user_id) {
            return redirect(url(app()->getLocale().'/orders'));
        }

        $order->load('files');

        return view('kaheel.order-show', ['order' => $order]);
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
