<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaheelOrderMessage extends Model
{
    protected $table = 'kaheel_order_messages';

    protected $fillable = [
        'order_id', 'sender_role', 'sender_name', 'body',
        'kind', 'media_path', 'media_mime', 'media_name', 'duration', 'customer_seen_at', 'admin_seen_at',
    ];

    protected $casts = [
        'customer_seen_at' => 'datetime',
        'admin_seen_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(KaheelOrder::class, 'order_id');
    }
}
