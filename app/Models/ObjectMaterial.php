<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjectMaterial extends Model
{
     protected $fillable = [
        'material_id',
        'number_pieces',
        'requires_assembly',
        'manufacter',
        'serial_number',
    ];

    protected function casts(): array
    {
        return [
            'number_pieces' => 'integer',
            'requires_assembly' => 'boolean',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
