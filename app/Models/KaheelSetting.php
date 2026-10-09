<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaheelSetting extends Model
{
    protected $table = 'kaheel_settings';

    protected $fillable = [
        'key', 'label_ar', 'label_en', 'value', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public static function map(): array
    {
        return static::query()->orderBy('sort_order')->pluck('value', 'key')->all();
    }
}
