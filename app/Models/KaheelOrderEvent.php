<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaheelOrderEvent extends Model
{
    protected $table = 'kaheel_order_events';

    protected $fillable = [
        'order_id', 'from_status', 'to_status', 'note', 'actor_name',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(KaheelOrder::class, 'order_id');
    }
}
