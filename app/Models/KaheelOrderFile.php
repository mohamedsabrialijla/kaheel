<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaheelOrderFile extends Model
{
    protected $table = 'kaheel_order_files';

    protected $fillable = [
        'order_id', 'kind', 'review_status', 'original_name', 'path', 'mime', 'size',
    ];

    public static function markLatest(int $orderId, string $kind, string $status): void
    {
        $file = static::query()->where('order_id', $orderId)->where('kind', $kind)->latest('id')->first();
        if ($file) {
            $file->review_status = $status;
            $file->save();
        }
    }

    public function resolvedStatus(?string $current = null): string
    {
        if (in_array($this->review_status, ['pending', 'approved', 'rejected'], true)) {
            return $this->review_status;
        }

        return in_array($current, ['pending', 'approved', 'rejected'], true) ? $current : 'pending';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(KaheelOrder::class, 'order_id');
    }
}
