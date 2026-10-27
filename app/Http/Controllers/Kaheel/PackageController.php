<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelOrder;
use App\Models\KaheelPackage;
use App\Models\KaheelPackageImage;
use App\Models\KaheelPackageItem;
use App\Models\KaheelPackageTier;
use App\Support\KaheelPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PackageController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => $this->canBrowsePackages() ? null : 'packages.view',
            'savePackage', 'saveTier' => $request->filled('id') ? 'packages.edit' : 'packages.create',
            'togglePackage', 'copyItems', 'saveItems' => 'packages.edit',
            'deletePackage', 'deleteTier' => 'packages.delete',
            default => null,
        };
    }

    public function index()
    {
        $stats = KaheelOrder::query()
            ->selectRaw('package_code, count(*) as orders_count, coalesce(sum(price_incl + addons_total), 0) as sales')
            ->groupBy('package_code')
            ->get()
            ->keyBy('package_code');

        $rows = KaheelPackage::query()
            ->with(['tiers.items', 'images'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (KaheelPackage $package) => $this->present($package, $stats->get($package->code)));

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
        if ($request->boolean('sync_images') || $request->has('keep_ids') || $request->hasFile('images')) {
            $this->syncImages($package, $request);
        }

        return mainResponse(true, 'Success', $this->present($package->fresh(['tiers.items', 'images'])), 200, 'items', '');
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
        $package = KaheelPackage::query()->with('images')->find($request->input('id'));
        if ($package) {
            foreach ($package->images as $image) {
                $image->deleteFile();
            }
            $package->delete();
        }

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
        $isAr = app()->getLocale() !== 'en';
        if (!is_array($items)) {
            return mainResponse(false, $isAr ? 'العناصر غير صالحة' : 'Invalid items', null, 422, 'items', '');
        }

        $required = ['category', 'name_ar', 'name_en', 'product_name_ar', 'product_name_en', 'detail_ar', 'detail_en', 'unit_label_ar', 'unit_label_en'];
        foreach (array_values($items) as $index => $item) {
            if (!is_array($item)) {
                return mainResponse(false, $isAr ? 'العناصر غير صالحة' : 'Invalid items', null, 422, 'items', '');
            }
            foreach ($required as $key) {
                if (trim((string) ($item[$key] ?? '')) === '') {
                    return mainResponse(false, $isAr
                        ? 'جميع حقول العنصر إلزامية، أكمل العنصر رقم '.($index + 1).'.'
                        : 'Every item field is required. Complete item '.($index + 1).'.', null, 422, 'items', '');
                }
            }
            if (!is_numeric($item['quantity'] ?? null) || (float) $item['quantity'] <= 0) {
                return mainResponse(false, $isAr ? 'العدد يجب أن يكون أكبر من صفر.' : 'Quantity must be greater than zero.', null, 422, 'items', '');
            }
            if (!is_numeric($item['price_per_unit'] ?? null) || (float) $item['price_per_unit'] < 0) {
                return mainResponse(false, $isAr ? 'سعر الوحدة الإضافية إلزامي.' : 'Extra unit price is required.', null, 422, 'items', '');
            }
        }

        DB::transaction(function () use ($tier, $items) {
            KaheelPackageItem::query()->where('tier_id', $tier->id)->delete();
            foreach (array_values($items) as $index => $item) {
                KaheelPackageItem::query()->create([
                    'package_id' => $tier->package_id,
                    'tier_id' => $tier->id,
                    'category' => $item['category'],
                    'name_ar' => trim((string) $item['name_ar']),
                    'name_en' => trim((string) $item['name_en']),
                    'product_name_ar' => trim((string) $item['product_name_ar']),
                    'product_name_en' => trim((string) $item['product_name_en']),
                    'detail_ar' => trim((string) $item['detail_ar']),
                    'detail_en' => trim((string) $item['detail_en']),
                    'quantity' => $item['quantity'],
                    'unit_label_ar' => trim((string) $item['unit_label_ar']),
                    'unit_label_en' => trim((string) $item['unit_label_en']),
                    'price_per_unit' => $item['price_per_unit'],
                    'allow_alternatives' => (bool) ($item['allow_alternatives'] ?? false),
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return mainResponse(true, 'Success', null, 200, 'items', '');
    }

    private function canBrowsePackages(): bool
    {
        $admin = auth('admin')->user();

        return KaheelPermissions::allows($admin, 'packages.view')
            || KaheelPermissions::allows($admin, 'overview.view');
    }

    private function syncImages(KaheelPackage $package, Request $request): void
    {
        $keep = collect($request->input('keep_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $package->loadMissing('images');
        foreach ($package->images as $image) {
            if (!$keep->contains($image->id)) {
                $image->deleteFile();
                $image->delete();
            }
        }

        foreach ($keep as $index => $id) {
            $image = $package->images()->whereKey($id)->first();
            if (!$image) {
                continue;
            }
            $image->caption_ar = trim((string) $request->input('caption_ar.'.$id, '')) ?: null;
            $image->caption_en = trim((string) $request->input('caption_en.'.$id, '')) ?: null;
            $image->sort_order = $index + 1;
            $image->save();
        }

        $files = $request->file('images', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }
        $sort = $keep->count();
        foreach (array_values($files) as $index => $file) {
            if (!$file || !$file->isValid() || !kaheel_file_is_image(...kaheel_upload_parts($file)) || kaheel_upload_blocked(...kaheel_upload_parts($file))) {
                continue;
            }
            if ($file->getSize() > 8 * 1024 * 1024) {
                continue;
            }
            $sort++;
            KaheelPackageImage::query()->create([
                'package_id' => $package->id,
                'path' => $file->store('kaheel-packages/'.$package->id),
                'original_name' => $file->getClientOriginalName(),
                'mime' => kaheel_stored_mime($file),
                'caption_ar' => trim((string) $request->input('new_caption_ar.'.$index, '')) ?: null,
                'caption_en' => trim((string) $request->input('new_caption_en.'.$index, '')) ?: null,
                'sort_order' => $sort,
            ]);
        }
    }

    private function present(KaheelPackage $package, $stats = null): array
    {
        $package->loadMissing('tiers.items', 'images');

        return [
            'id' => $package->id,
            'code' => $package->code,
            'name_ar' => $package->name_ar,
            'name_en' => $package->name_en,
            'tagline_ar' => $package->tagline_ar,
            'tagline_en' => $package->tagline_en,
            'sort_order' => $package->sort_order,
            'is_active' => $package->is_active,
            'images' => $package->images->map(fn (KaheelPackageImage $image) => [
                'id' => $image->id,
                'url' => $image->publicUrl(),
                'caption_ar' => $image->caption_ar,
                'caption_en' => $image->caption_en,
            ])->values(),
            'ordersCount' => (int) ($stats->orders_count ?? 0),
            'sales' => round((float) ($stats->sales ?? 0), 2),
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
