<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class BibliographicMaterial extends Model
{
    protected $fillable = [
        'material_id',
        'is_illustrated',
        'binding_type',
    ];

    protected function casts(): array
    {
        return [
            'is_illustrated' => 'boolean',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
