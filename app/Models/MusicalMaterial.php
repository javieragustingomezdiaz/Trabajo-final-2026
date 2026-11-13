<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicalMaterial extends Model
{
    protected $fillable = [
        'material_id',
        'musical_notation',
        'performance_medium',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
