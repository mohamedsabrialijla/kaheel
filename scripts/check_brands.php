<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$brandTypeId = \App\Models\ItemsTypes::where('name', 'brand')->value('id');
$sample = \App\Models\ItemsCategories::where('type_id', $brandTypeId)->orderByDesc('id')->limit(5)->get();
$out = [];
foreach ($sample as $b) {
    $out[] = [
        'id' => $b->id,
        'category' => $b->getAttributes()['category'] ?? null,
        'ar' => $b->translate('ar')?->name,
        'en' => $b->translate('en')?->name,
    ];
}
file_put_contents(__DIR__ . '/../storage/brand_sample.json', json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
