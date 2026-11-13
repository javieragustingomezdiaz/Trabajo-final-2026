<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoundMaterial extends Model
{
    protected $fillable = [
        'material_id',
        'duration_seconds',
        'recording_type',
        'audio_channel',
        'playback_speed',
        'number_tracks',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'number_tracks' => 'integer',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
