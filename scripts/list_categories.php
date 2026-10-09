<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$typeId = \App\Models\ItemsTypes::where('name', 'category')->value('id');

$cats = \App\Models\ItemsCategories::where('type_id', $typeId)->get();
$out = [];
foreach ($cats as $c) {
    $out[] = [
        'id' => $c->id,
        'status' => $c->status,
        'ar' => $c->translate('ar')?->name ?? '',
        'en' => $c->translate('en')?->name ?? '',
    ];
}
file_put_contents(__DIR__ . '/../storage/categories_all.json', json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo count($out);
