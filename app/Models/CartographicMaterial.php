<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartographicMaterial extends Model
{
    protected $primaryKey = 'material_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'material_id',
        'scale',
        'projection',
        'coordinate',
        'orientation',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
