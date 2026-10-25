<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KaheelCategory extends Model
{
    protected $table = 'kaheel_categories';

    protected $fillable = [
        'code', 'name_ar', 'name_en', 'unit_label_ar', 'unit_label_en', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function catalog(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (self $row) => $row->present())
            ->values()
            ->all();
    }

    public static function codes(): array
    {
        return collect(static::catalog())->pluck('code')->all();
    }

    public static function makeCode(string $nameEn, string $nameAr): string
    {
        $base = Str::slug($nameEn);
        if ($base === '') {
            $base = 'cat-'.substr(md5($nameAr.$nameEn.microtime()), 0, 8);
        }
        $code = substr($base, 0, 40);
        $i = 2;
        while (static::query()->where('code', $code)->exists()) {
            $suffix = '-'.$i;
            $code = substr($base, 0, 40 - strlen($suffix)).$suffix;
            $i++;
        }

        return $code;
    }

    public function present(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'ar' => $this->name_ar,
            'en' => $this->name_en,
            'unitAr' => $this->unit_label_ar ?: 'قطعة',
            'unitEn' => $this->unit_label_en ?: 'piece',
        ];
    }
}
