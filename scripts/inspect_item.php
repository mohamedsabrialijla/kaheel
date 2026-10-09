<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$c = \App\Models\ItemsCategories::find(6);
file_put_contents(__DIR__ . '/../storage/cat6.json', json_encode($c->getAttributes(), JSON_UNESCAPED_UNICODE));
