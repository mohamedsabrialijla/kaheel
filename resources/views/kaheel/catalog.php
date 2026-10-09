<?php

$line = function ($titleAr, $titleEn, $specAr, $specEn, $qty, $unitAr, $unitEn) {
    return [
        'title_ar' => $titleAr,
        'title_en' => $titleEn,
        'spec_ar' => $specAr,
        'spec_en' => $specEn,
        'qty_ar' => $qty.' '.$unitAr,
        'qty_en' => $qty.' '.$unitEn,
    ];
};

$sofa = $line('طقم كنب رئيسي', 'Main sofa set', 'قماش مخملي — 3+2 مقاعد', 'Velvet fabric — 3+2 seats', 1, 'طقم', 'set');
$dining = $line('طاولة طعام وكراسي', 'Dining table & chairs', 'خشب بلوط — 6 كراسي', 'Oak wood — 6 chairs', 1, 'طقم', 'set');
$bed = $line('سرير رئيسي', 'Master bed', 'كنج 200×200 مع كومودينو', 'King 200x200 with nightstands', 1, 'قطعة', 'piece');
$extra = function ($qty) use ($line) {
    return $line('غرفة نوم إضافية', 'Additional bedroom', 'كوين 160×200', 'Queen 160x200', $qty, 'غرفة', 'room');
};
$fridge = $line('ثلاجة', 'Refrigerator', 'سامسونج بابين', 'Samsung two-door', 1, 'جهاز', 'unit');
$washer = $line('غسالة', 'Washing machine', 'LG 8 كجم', 'LG 8 kg', 1, 'جهاز', 'unit');
$soft = $line('مفروشات وأغطية', 'Soft furnishings', 'قطن مصري', 'Egyptian cotton', 1, 'طقم', 'set');
$decor = $line('إكسسوارات وديكور', 'Accessories & decor', 'لوحات ومرايا وإضاءة', 'Art, mirrors and lighting', 1, 'طقم', 'set');
$handover = $line('التوريد والتركيب والتسليم', 'Supply, installation & handover', 'يشمل النقل والتركيب', 'Includes delivery and installation', 1, 'خدمة', 'service');

$tv = function ($vip) use ($line) {
    return $vip
        ? $line('تلفزيون', 'Television', 'TCL 65 بوصة QLED', 'TCL 65 inch QLED', 1, 'جهاز', 'unit')
        : $line('تلفزيون', 'Television', 'TCL 55 بوصة 4K', 'TCL 55 inch 4K', 1, 'جهاز', 'unit');
};
$counter = function ($meters) use ($line) {
    return $line('سطح المطبخ', 'Kitchen countertop', 'كوارتز', 'Quartz', $meters, 'متر طولي', 'linear metre');
};
$wardrobe = function ($meters) use ($line) {
    return $line('دواليب الملابس', 'Wardrobes', 'MDF بأبواب منزلقة', 'MDF with sliding doors', $meters, 'متر طولي', 'linear metre');
};
$curtains = function ($meters) use ($line) {
    return $line('ستائر', 'Curtains', 'بلاك أوت مزدوج', 'Double blackout', $meters, 'متر', 'metre');
};

$tiers = function ($vip, $prices) use ($sofa, $dining, $bed, $extra, $tv, $fridge, $washer, $counter, $wardrobe, $curtains, $soft, $decor, $handover) {
    $tail = function ($meters) use ($vip, $tv, $fridge, $washer, $counter, $wardrobe, $curtains, $soft, $decor, $handover) {
        return [
            $tv($vip),
            $fridge,
            $washer,
            $counter($meters[0]),
            $wardrobe($meters[1]),
            $curtains($meters[2]),
            $soft,
            $decor,
            $handover,
        ];
    };

    return [
        [
            'name_ar' => 'نوع A',
            'name_en' => 'Type A',
            'desc_ar' => 'غرفة نوم واحدة',
            'desc_en' => '1 bedroom',
            'excl' => $prices[0][0],
            'incl' => $prices[0][1],
            'items' => array_merge([$sofa, $dining, $bed], $tail([5, 4, 6])),
        ],
        [
            'name_ar' => 'أنواع B · C · G',
            'name_en' => 'Types B · C · G',
            'desc_ar' => 'غرفتا نوم',
            'desc_en' => '2 bedrooms',
            'excl' => $prices[1][0],
            'incl' => $prices[1][1],
            'items' => array_merge([$sofa, $dining, $bed, $extra(1)], $tail([6, 5, 7])),
        ],
        [
            'name_ar' => 'أنواع D · F',
            'name_en' => 'Types D · F',
            'desc_ar' => 'ثلاث غرف نوم',
            'desc_en' => '3 bedrooms',
            'excl' => $prices[2][0],
            'incl' => $prices[2][1],
            'items' => array_merge([$sofa, $dining, $bed, $extra(2)], $tail([7, 6, 8])),
        ],
    ];
};

return [
    'vip' => [
        'code' => 'VIP',
        'title_ar' => 'باقة VIP',
        'title_en' => 'VIP package',
        'gallery' => [
            ['file' => 'vip-living.jpg', 'ar' => 'غرفة الطعام والمعيشة', 'en' => 'Dining & living'],
            ['file' => 'vip-kitchen.jpg', 'ar' => 'المطبخ ومنطقة الطعام', 'en' => 'Kitchen & dining'],
            ['file' => 'vip-master.jpg', 'ar' => 'غرفة النوم الرئيسية', 'en' => 'Master bedroom'],
            ['file' => 'vip-wardrobes.jpg', 'ar' => 'تفاصيل غرفة النوم والدواليب', 'en' => 'Master · wardrobes'],
            ['file' => 'vip-bedroom.jpg', 'ar' => 'غرفة النوم الثانية', 'en' => 'Second bedroom'],
            ['file' => 'vip-detail.jpg', 'ar' => 'تفاصيل جانب السرير', 'en' => 'Bedside detail'],
        ],
        'tiers' => $tiers(true, [
            [103596.07, 119135.48],
            [150488.60, 173061.89],
            [166257.90, 191196.59],
        ]),
    ],
    'classic' => [
        'code' => 'CLASSIC',
        'title_ar' => 'باقة CLASSIC',
        'title_en' => 'CLASSIC package',
        'gallery' => [
            ['file' => 'classic-living.jpg', 'ar' => 'غرفة المعيشة والطعام', 'en' => 'Living & dining'],
            ['file' => 'classic-kitchen.jpg', 'ar' => 'المطبخ', 'en' => 'Kitchen'],
            ['file' => 'classic-bedroom.jpg', 'ar' => 'غرفة النوم', 'en' => 'Bedroom'],
            ['file' => 'classic-kitchen-detail.jpg', 'ar' => 'تفاصيل المطبخ', 'en' => 'Kitchen · detail'],
            ['file' => 'classic-living-detail.jpg', 'ar' => 'تفاصيل غرفة المعيشة', 'en' => 'Living · detail'],
        ],
        'tiers' => $tiers(false, [
            [86064.42, 98974.09],
            [129203.25, 148583.74],
            [143514.88, 165042.11],
        ]),
    ],
];
