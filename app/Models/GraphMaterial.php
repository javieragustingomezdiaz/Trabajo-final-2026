<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraphMaterial extends Model
{
    protected $fillable = [
        'material_id',
        'technical',
        'color',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
