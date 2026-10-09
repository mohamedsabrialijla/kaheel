<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$keys = ['username', 'password', 'app_id', 'app_key'];
$out = [];

foreach ($keys as $key) {
    $val = config("services.yakeen.{$key}");
    $raw = is_string($val) ? $val : '';
    $out[$key] = [
        'configured' => $raw !== '',
        'length' => strlen($raw),
        'has_leading_space' => $raw !== ltrim($raw),
        'has_trailing_space' => $raw !== rtrim($raw),
        'wrapped_in_quotes' => strlen($raw) >= 2 && (
            (str_starts_with($raw, '"') && str_ends_with($raw, '"')) ||
            (str_starts_with($raw, "'") && str_ends_with($raw, "'"))
        ),
    ];
}

$out['cache_has_jwt'] = \Illuminate\Support\Facades\Cache::has('yakeen_jwt_token');

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
