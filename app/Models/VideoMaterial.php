<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoMaterial extends Model
{
    protected $fillable = [
        'material_id',
        'duration_seconds',
        'video_format',
        'aspect_ratio',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
