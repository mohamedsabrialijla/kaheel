<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class KaheelPackageImage extends Model
{
    protected $table = 'kaheel_package_images';

    protected $fillable = [
        'package_id', 'path', 'original_name', 'mime', 'caption_ar', 'caption_en', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(KaheelPackage::class, 'package_id');
    }

    public function isLegacy(): bool
    {
        return str_starts_with((string) $this->path, 'public:');
    }

    public function publicUrl(): string
    {
        if ($this->isLegacy()) {
            return asset('kaheel/'.substr($this->path, 7));
        }

        return url(app()->getLocale().'/packages/gallery/'.$this->id);
    }

    public function deleteFile(): void
    {
        if (!$this->isLegacy() && $this->path) {
            Storage::disk('local')->delete($this->path);
        }
    }
}
