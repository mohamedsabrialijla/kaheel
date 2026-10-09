<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$categoryTypeId = \App\Models\ItemsTypes::where('name', 'category')->value('id');
$brandTypeId = \App\Models\ItemsTypes::where('name', 'brand')->value('id');

$sheets = ['مجوهرات', 'أزياء', 'أحذية', 'أحزمة', 'نظارات', 'أقلام', 'ساعات', 'شنط', 'عطور', 'مكياج'];
$out = [];
$noCat = \App\Models\ItemsCategories::where('type_id', $brandTypeId)
    ->where(fn ($q) => $q->whereNull('category')->orWhere('category', 0))
    ->count();

foreach ($sheets as $ar) {
    $cat = \App\Models\ItemsCategories::where('type_id', $categoryTypeId)
        ->where('status', 'active')
        ->whereHas('translations', fn ($q) => $q->where('locale', 'ar')->where('name', $ar))
        ->orderBy('id')
        ->first();
    $count = $cat
        ? \App\Models\ItemsCategories::where('type_id', $brandTypeId)->where('category', $cat->id)->count()
        : 0;
    $wrong = $cat
        ? \App\Models\ItemsCategories::where('type_id', $brandTypeId)
            ->whereHas('translations', fn ($t) => $t->where('locale', 'en'))
            ->where('category', '!=', $cat->id)
            ->whereIn('id', function ($q) use ($brandTypeId, $cat) {
                // skip - just count brands for this category
            })
        : 0;
    $out[] = ['category' => $ar, 'id' => $cat?->id, 'brands' => $count];
}

$out[] = ['summary' => 'brands_without_category', 'count' => $noCat];
$out[] = ['summary' => 'total_brands', 'count' => \App\Models\ItemsCategories::where('type_id', $brandTypeId)->count()];

file_put_contents(__DIR__ . '/../storage/import_verify.json', json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode($out, JSON_UNESCAPED_UNICODE);
