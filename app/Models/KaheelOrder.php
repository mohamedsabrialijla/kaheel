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
        'contract_body', 'signature', 'design_components', 'design_history', 'design_signature', 'design_signed_at', 'supplier_assignments',
        'status', 'design_status', 'payment_method', 'payment_status', 'payment_note',
        'final_payment_status', 'final_payment_method', 'final_payment_note',
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
        'design_history' => 'array',
        'design_signed_at' => 'datetime',
        'supplier_assignments' => 'array',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(KaheelOrderFile::class, 'order_id');
    }

    public function recordDesignChange(string $role, string $ar, string $en): void
    {
        $history = collect($this->design_history ?? [])->values();
        $history->push([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'actor_role' => $role === 'customer' ? 'customer' : 'admin',
            'summary_ar' => $ar,
            'summary_en' => $en,
            'created_at' => kaheel_iso(now()) ?: now()->toIso8601String(),
        ]);
        $this->design_history = $history->all();
    }

    public function designChangeLog(): array
    {
        $stored = collect($this->design_history ?? [])->filter(fn ($row) => is_array($row) && (($row['summary_ar'] ?? '') !== '' || ($row['summary_en'] ?? '') !== ''))->values();
        if ($stored->isNotEmpty()) {
            return $stored->all();
        }

        return $this->events
            ->filter(fn ($event) => str_contains((string) $event->note, 'تصميم') || str_contains(strtolower((string) $event->note), 'design'))
            ->values()
            ->map(function ($event) {
                $note = trim((string) $event->note);
                $customer = str_contains($note, 'العميل') || str_contains(strtolower((string) $event->actor_name), 'customer');

                return [
                    'id' => $event->id,
                    'actor_role' => $customer ? 'customer' : 'admin',
                    'summary_ar' => $note,
                    'summary_en' => $note,
                    'created_at' => kaheel_iso($event->created_at),
                ];
            })
            ->all();
    }

    public static function groupDesignVersions($files): array
    {
        $sorted = collect($files)->sortByDesc('id')->values();
        if ($sorted->isEmpty()) {
            return ['current' => collect(), 'previous' => collect()];
        }

        $latestAt = $sorted->first()->created_at;
        $current = $sorted->filter(function ($file) use ($latestAt) {
            if (!$latestAt || !$file->created_at) {
                return false;
            }

            return abs($file->created_at->diffInSeconds($latestAt)) <= 90;
        })->values();
        $ids = $current->pluck('id')->all();
        $previous = $sorted->reject(fn ($file) => in_array($file->id, $ids, true))->values();

        return ['current' => $current, 'previous' => $previous];
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

        $status = $this->listedStatus();
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

        if ($this->status === 'final_payment') {
            if ($this->final_payment_status === 'pending') {
                return ['ar' => 'بانتظار تأكيد السداد النهائي', 'en' => 'Final payment under review'];
            }
            if ($this->final_payment_status === 'rejected') {
                return ['ar' => 'السداد النهائي مرفوض', 'en' => 'Final payment rejected'];
            }
        }

        $step = $this->progressStep();
        if ($step === 0) {
            return ['ar' => 'بانتظار العربون', 'en' => 'Awaiting deposit'];
        }

        return self::stageCopy()[$step - 1];
    }

    public function listedStatus(): string
    {
        if ($this->status === 'awaiting_approval') {
            return 'admin_review';
        }
        if ($this->status === 'final_payment' && $this->final_payment_status === 'approved') {
            return 'final_approval';
        }

        return (string) $this->status;
    }

    public function promoteApprovedFinalPayment(): void
    {
        if ($this->status !== 'final_payment' || $this->final_payment_status !== 'approved') {
            return;
        }

        $from = $this->status;
        $this->status = 'final_approval';
        $this->save();
        $this->events()->create([
            'order_id' => $this->id,
            'from_status' => $from,
            'to_status' => $this->status,
            'note' => 'تم تأكيد السداد النهائي',
            'actor_name' => optional($this->events()->latest('id')->first())->actor_name ?: 'admin',
        ]);
    }

    public function nextStatus(): ?string
    {
        $index = array_search($this->listedStatus(), self::workflow(), true);
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
