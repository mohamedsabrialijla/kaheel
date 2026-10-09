<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaheelAlternative extends Model
{
    protected $table = 'kaheel_alternatives';

    protected $fillable = [
        'category', 'name_ar', 'name_en', 'price_delta', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price_delta' => 'float',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
