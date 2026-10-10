<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\KaheelPackage;
use App\Models\KaheelPackageItem;
use App\Models\KaheelPackageTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function index()
    {
        $rows = KaheelPackage::query()
            ->with(['tiers.items'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (KaheelPackage $package) => $this->present($package));

        return mainResponse(true, 'Success', $rows, 200, 'items', '');
    }

    public function savePackage(Request $request)
    {
        $package = $request->filled('id') ? KaheelPackage::query()->find($request->input('id')) : new KaheelPackage();
        if ($request->filled('id') && !$package) {
            return mainResponse(false, $this->missing(), null, 404, 'items', '');
        }

        $codeRule = 'required|string|max:40|unique:kaheel_packages,code';
        if ($package->exists) {
            $codeRule .= ','.$package->id;
        }

        $validator = Validator::make($request->all(), [
            'code' => $codeRule,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'tagline_ar' => 'nullable|string|max:255',
            'tagline_en' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $package->fill([
            'code' => strtoupper($request->string('code')->toString()),
            'name_ar' => $request->input('name_ar'),
            'name_en' => $request->input('name_en'),
            'tagline_ar' => $request->input('tagline_ar') ?: null,
            'tagline_en' => $request->input('tagline_en') ?: null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);
        $package->save();

        return mainResponse(true, 'Success', $this->present($package->fresh('tiers.items')), 200, 'items', '');
    }

    public function togglePackage(Request $request)
    {
        $package = KaheelPackage::query()->find($request->input('id'));
        if (!$package) {
            return mainResponse(false, $this->missing(), null, 404, 'items', '');
        }
        $package->is_active = $request->boolean('is_active');
        $package->save();

        return mainResponse(true, 'Success', $this->present($package->fresh('tiers.items')), 200, 'items', '');
    }

    public function deletePackage(Request $request)
    {
        KaheelPackage::query()->whereKey($request->input('id'))->delete();

        return mainResponse(true, 'Success', null, 200, 'items', '');
    }

    public function saveTier(Request $request)
    {
        $package = KaheelPackage::query()->find($request->input('package_id'));
        if (!$package) {
            return mainResponse(false, $this->missing(), null, 404, 'items', '');
        }

        $tier = $request->filled('id') ? KaheelPackageTier::query()->find($request->input('id')) : new KaheelPackageTier();
        if ($request->filled('id') && !$tier) {
            return mainResponse(false, $this->missing(), null, 404, 'items', '');
        }

        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string|max:255',
            'price_excl_vat' => 'nullable|numeric|min:0',
            'supplier_cost' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $excl = round((float) $request->input('price_excl_vat', 0), 2);
        $tier->fill([
            'package_id' => $package->id,
            'name_ar' => $request->input('name_ar'),
            'name_en' => $request->input('name_en'),
            'description_ar' => $request->input('description_ar') ?: null,
            'description_en' => $request->input('description_en') ?: null,
            'price_excl_vat' => $excl,
            'price_incl_vat' => round($excl * 1.15, 2),
            'supplier_cost' => round((float) $request->input('supplier_cost', 0), 2),
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);
        $tier->save();

        return mainResponse(true, 'Success', $this->present($package->fresh('tiers.items')), 200, 'items', '');
    }

    public function deleteTier(Request $request)
    {
        KaheelPackageTier::query()->whereKey($request->input('id'))->delete();

        return mainResponse(true, 'Success', null, 200, 'items', '');
    }

    public function copyItems(Request $request)
    {
        $from = KaheelPackageTier::query()->with('items')->find($request->input('from_tier_id'));
        $to = KaheelPackageTier::query()->find($request->input('to_tier_id'));
        if (!$from || !$to) {
            return mainResponse(false, $this->missing(), null, 404, 'items', '');
        }

        DB::transaction(function () use ($from, $to) {
            KaheelPackageItem::query()->where('tier_id', $to->id)->delete();
            foreach ($from->items as $item) {
                $copy = $item->replicate(['tier_id', 'package_id']);
                $copy->package_id = $to->package_id;
                $copy->tier_id = $to->id;
                $copy->save();
            }
        });

        return mainResponse(true, 'Success', null, 200, 'items', '');
    }

    public function saveItems(Request $request)
    {
        $tier = KaheelPackageTier::query()->find($request->input('tier_id'));
        if (!$tier) {
            return mainResponse(false, $this->missing(), null, 404, 'items', '');
        }

        $items = $request->input('items', []);
        if (!is_array($items)) {
            return mainResponse(false, app()->getLocale() === 'ar' ? 'العناصر غير صالحة' : 'Invalid items', null, 422, 'items', '');
        }

        DB::transaction(function () use ($tier, $items) {
            KaheelPackageItem::query()->where('tier_id', $tier->id)->delete();
            foreach (array_values($items) as $index => $item) {
                if (!is_array($item) || trim((string) ($item['name_ar'] ?? '')) === '' || trim((string) ($item['name_en'] ?? '')) === '') {
                    continue;
                }
                KaheelPackageItem::query()->create([
                    'package_id' => $tier->package_id,
                    'tier_id' => $tier->id,
                    'category' => $item['category'] ?? 'general',
                    'name_ar' => $item['name_ar'],
                    'name_en' => $item['name_en'],
                    'product_name_ar' => $item['product_name_ar'] ?: null,
                    'product_name_en' => $item['product_name_en'] ?: null,
                    'detail_ar' => trim((string) ($item['detail_ar'] ?? '')) ?: null,
                    'detail_en' => trim((string) ($item['detail_en'] ?? '')) ?: null,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_label_ar' => $item['unit_label_ar'] ?: 'قطعة',
                    'unit_label_en' => $item['unit_label_en'] ?: 'piece',
                    'price_per_unit' => $item['price_per_unit'] ?? 0,
                    'allow_alternatives' => (bool) ($item['allow_alternatives'] ?? false),
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return mainResponse(true, 'Success', null, 200, 'items', '');
    }

    private function present(KaheelPackage $package): array
    {
        $package->loadMissing('tiers.items');

        return [
            'id' => $package->id,
            'code' => $package->code,
            'name_ar' => $package->name_ar,
            'name_en' => $package->name_en,
            'tagline_ar' => $package->tagline_ar,
            'tagline_en' => $package->tagline_en,
            'sort_order' => $package->sort_order,
            'is_active' => $package->is_active,
            'ordersCount' => 0,
            'tiers' => $package->tiers->map(function (KaheelPackageTier $tier) {
                return [
                    'id' => $tier->id,
                    'name_ar' => $tier->name_ar,
                    'name_en' => $tier->name_en,
                    'description_ar' => $tier->description_ar,
                    'description_en' => $tier->description_en,
                    'price_excl_vat' => $tier->price_excl_vat,
                    'price_incl_vat' => $tier->price_incl_vat,
                    'supplier_cost' => $tier->supplier_cost,
                    'sort_order' => $tier->sort_order,
                    'is_active' => $tier->is_active,
                    'items' => $tier->items->map(fn (KaheelPackageItem $item) => $this->presentItem($item))->values(),
                ];
            })->values(),
        ];
    }

    private function presentItem(KaheelPackageItem $item): array
    {
        return [
            'id' => $item->id,
            'category' => $item->category,
            'name_ar' => $item->name_ar,
            'name_en' => $item->name_en,
            'product_name_ar' => $item->product_name_ar,
            'product_name_en' => $item->product_name_en,
            'detail_ar' => $item->detail_ar,
            'detail_en' => $item->detail_en,
            'quantity' => $item->quantity,
            'unit_label_ar' => $item->unit_label_ar,
            'unit_label_en' => $item->unit_label_en,
            'price_per_unit' => $item->price_per_unit,
            'allow_alternatives' => $item->allow_alternatives,
            'sort_order' => $item->sort_order,
        ];
    }

    private function missing(): string
    {
        return app()->getLocale() === 'ar' ? 'الباقة غير موجودة' : 'Package not found';
    }
}
