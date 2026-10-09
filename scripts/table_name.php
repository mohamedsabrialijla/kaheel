<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$m = new \App\Models\ItemsCategories;
echo $m->getTable()."\n";
$t = $m->translations()->getRelated();
echo $t->getTable()."\n";
echo $m->getTranslationRelationKey()."\n";
