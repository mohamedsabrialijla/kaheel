<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaheelOrderFile extends Model
{
    protected $table = 'kaheel_order_files';

    protected $fillable = [
        'order_id', 'kind', 'original_name', 'path', 'mime', 'size',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(KaheelOrder::class, 'order_id');
    }
}
