<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\KaheelPackage;
use App\Models\KaheelSetting;

class SiteController extends Controller
{
    public function home()
    {
        $packages = $this->visiblePackages();

        return view('kaheel.home', array_merge($this->publicSettings(), [
            'packages' => $packages,
            'outline' => $packages->first()?->outline ?? collect(),
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
                    $query->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->get();
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
