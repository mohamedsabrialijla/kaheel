<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\KaheelCategory;
use App\Models\KaheelPackage;
use App\Models\KaheelSetting;
use Illuminate\Support\Collection;

class SiteController extends Controller
{
    public function home()
    {
        $packages = $this->visiblePackages();
        $catalog = collect(KaheelCategory::catalog())->keyBy('code');

        return view('kaheel.home', array_merge($this->publicSettings(), [
            'packages' => $packages,
            'highlights' => $packages->mapWithKeys(fn (KaheelPackage $package) => [
                $package->id => $this->highlightLines($package, $catalog),
            ]),
            'outline' => $this->contentLines($packages, $catalog),
        ]));
    }

    public function package(string $code)
    {
        $package = KaheelPackage::query()
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->where('is_active', true)
            ->with(['tiers' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order')->with('items');
            }])
            ->firstOrFail();

        return view('kaheel.package', array_merge($this->publicSettings(), [
            'package' => $package,
            'gallery' => $this->gallery($package->code),
        ]));
    }

    private function publicSettings(): array
    {
        $settings = KaheelSetting::map();
        $days = $settings['delivery_working_days'] ?? '25';
        $phones = collect([
            $settings['contact_phone_primary'] ?? null,
            $settings['contact_phone_secondary'] ?? null,
        ])->filter(fn ($phone) => filled($phone))->implode(' · ');

        return [
            'phones' => $phones,
            'deliveryAr' => "مدة التنفيذ {$days} يوم عمل من تاريخ الدفع (عدا الجمعة والسبت).",
            'deliveryEn' => "Delivery is {$days} working days from payment date (Friday and Saturday excluded).",
        ];
    }

    private function visiblePackages()
    {
        return KaheelPackage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with([
                'outline',
                'tiers' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order')->with('items');
                },
            ])
            ->get();
    }

    private function uniqueCategories(Collection $items, Collection $catalog): Collection
    {
        $order = $catalog->keys()->flip();

        return $items->unique('category')->sortBy(fn ($item) => $order[$item->category] ?? 999)->values();
    }

    private function highlightLines(KaheelPackage $package, Collection $catalog): array
    {
        $items = $this->uniqueCategories($package->tiers->flatMap->items, $catalog);
        if ($items->isNotEmpty()) {
            return $items->take(3)->map(function ($item) use ($catalog) {
                $cat = $catalog->get($item->category) ?: [];

                return [
                    'ar' => $cat['ar'] ?? $item->name_ar,
                    'en' => $cat['en'] ?? $item->name_en,
                ];
            })->all();
        }

        return $package->outline->take(3)->map(fn ($item) => [
            'ar' => $item->name_ar,
            'en' => $item->name_en,
        ])->all();
    }

    private function contentLines(Collection $packages, Collection $catalog): Collection
    {
        $items = $this->uniqueCategories(
            $packages->flatMap(fn (KaheelPackage $package) => $package->tiers->flatMap->items),
            $catalog
        );
        if ($items->isNotEmpty()) {
            return $items->map(function ($item) use ($catalog) {
                $cat = $catalog->get($item->category) ?: [];

                return (object) [
                    'name_ar' => $cat['ar'] ?? $item->name_ar,
                    'name_en' => $cat['en'] ?? $item->name_en,
                ];
            });
        }

        return $packages->first()?->outline ?? collect();
    }

    private function gallery(string $code): array
    {
        $sets = [
            'VIP' => [
                ['file' => 'vip-living.jpg', 'ar' => 'غرفة الطعام والمعيشة', 'en' => 'Dining & living'],
                ['file' => 'vip-kitchen.jpg', 'ar' => 'المطبخ ومنطقة الطعام', 'en' => 'Kitchen & dining'],
                ['file' => 'vip-master.jpg', 'ar' => 'غرفة النوم الرئيسية', 'en' => 'Master bedroom'],
                ['file' => 'vip-wardrobes.jpg', 'ar' => 'تفاصيل غرفة النوم والدواليب', 'en' => 'Master · wardrobes'],
                ['file' => 'vip-bedroom.jpg', 'ar' => 'غرفة النوم الثانية', 'en' => 'Second bedroom'],
                ['file' => 'vip-detail.jpg', 'ar' => 'تفاصيل جانب السرير', 'en' => 'Bedside detail'],
            ],
            'CLASSIC' => [
                ['file' => 'classic-living.jpg', 'ar' => 'غرفة المعيشة والطعام', 'en' => 'Living & dining'],
                ['file' => 'classic-kitchen.jpg', 'ar' => 'المطبخ', 'en' => 'Kitchen'],
                ['file' => 'classic-bedroom.jpg', 'ar' => 'غرفة النوم', 'en' => 'Bedroom'],
                ['file' => 'classic-kitchen-detail.jpg', 'ar' => 'تفاصيل المطبخ', 'en' => 'Kitchen · detail'],
                ['file' => 'classic-living-detail.jpg', 'ar' => 'تفاصيل غرفة المعيشة', 'en' => 'Living · detail'],
            ],
        ];

        return $sets[strtoupper($code)] ?? [
            ['file' => 'vip-living.jpg', 'ar' => 'الباقة', 'en' => 'Package'],
        ];
    }
}
