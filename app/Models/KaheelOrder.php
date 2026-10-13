<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KaheelOrder extends Model
{
    protected $table = 'kaheel_orders';

    protected $fillable = [
        'number', 'bricks_user_id', 'username', 'customer_name',
        'package_code', 'package_name_ar', 'package_name_en',
        'tier_name_ar', 'tier_name_en',
        'compound_id', 'compound_name', 'compound_meta', 'compound_snapshot',
        'unit_id', 'unit_no', 'bedrooms_number', 'squar_meters', 'unit_snapshot',
        'price_excl', 'price_incl', 'deposit_percent', 'deposit_amount', 'addons_total',
        'contract_body', 'signature', 'design_components', 'supplier_assignments',
        'status', 'design_status', 'payment_method', 'payment_status', 'payment_note', 'final_payment_status',
    ];

    protected $casts = [
        'bricks_user_id' => 'integer',
        'bedrooms_number' => 'integer',
        'squar_meters' => 'float',
        'compound_snapshot' => 'array',
        'unit_snapshot' => 'array',
        'price_excl' => 'float',
        'price_incl' => 'float',
        'deposit_percent' => 'float',
        'deposit_amount' => 'float',
        'addons_total' => 'float',
        'design_components' => 'array',
        'supplier_assignments' => 'array',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(KaheelOrderFile::class, 'order_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(KaheelOrderEvent::class, 'order_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(KaheelOrderMessage::class, 'order_id');
    }

    public static function workflow(): array
    {
        return [
            'admin_review',
            'design_preparation',
            'design_review',
            'final_payment',
            'final_approval',
            'in_supply',
            'supplied',
            'closed',
        ];
    }

    public function progressStep(): int
    {
        if (in_array($this->payment_status, ['pending', 'rejected', null, ''], true)) {
            return 0;
        }

        $status = $this->status === 'awaiting_approval' ? 'admin_review' : $this->status;
        $index = array_search($status, self::workflow(), true);

        return $index === false ? 0 : $index + 1;
    }

    public function progressPercent(): int
    {
        return [0, 13, 25, 38, 50, 63, 75, 88, 100][$this->progressStep()] ?? 0;
    }

    public function statusCopy(): array
    {
        if ($this->payment_status === 'pending') {
            return ['ar' => 'بانتظار تأكيد الدفع', 'en' => 'Payment under review'];
        }
        if ($this->payment_status === 'rejected') {
            return ['ar' => 'الدفع مرفوض', 'en' => 'Payment rejected'];
        }

        $step = $this->progressStep();
        if ($step === 0) {
            return ['ar' => 'بانتظار العربون', 'en' => 'Awaiting deposit'];
        }

        return self::stageCopy()[$step - 1];
    }

    public function nextStatus(): ?string
    {
        $index = array_search($this->status === 'awaiting_approval' ? 'admin_review' : $this->status, self::workflow(), true);
        if ($index === false) {
            return null;
        }

        return self::workflow()[$index + 1] ?? null;
    }

    public static function stageCopy(): array
    {
        return [
            ['ar' => 'قيد مراجعة الإدارة', 'en' => 'Under review'],
            ['ar' => 'قيد إعداد التصميم', 'en' => 'Design in preparation'],
            ['ar' => 'بانتظار مراجعة العميل', 'en' => 'Awaiting your review'],
            ['ar' => 'بانتظار السداد النهائي', 'en' => 'Awaiting final payment'],
            ['ar' => 'الموافقة النهائية', 'en' => 'Final approval'],
            ['ar' => 'قيد التنفيذ والتوريد', 'en' => 'In production'],
            ['ar' => 'تم التوريد', 'en' => 'Supplied'],
            ['ar' => 'مغلق', 'en' => 'Closed'],
        ];
    }
}
