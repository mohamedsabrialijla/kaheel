<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaheelInquiry extends Model
{
    protected $table = 'kaheel_inquiries';

    protected $fillable = [
        'name', 'phone', 'customer_type', 'message',
    ];

    public static function typeLabel(string $type, string $locale = 'ar'): string
    {
        $map = [
            'owner' => ['ar' => 'مالك وحدة في مشروع', 'en' => 'Project unit owner'],
            'individual' => ['ar' => 'عميل فردي', 'en' => 'Individual'],
            'developer' => ['ar' => 'مطوّر عقاري', 'en' => 'Real estate developer'],
        ];

        return $map[$type][$locale] ?? $type;
    }
}
