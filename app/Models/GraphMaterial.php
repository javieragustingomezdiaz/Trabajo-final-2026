<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraphMaterial extends Model
{
    protected $primaryKey = 'material_id';

    public $incrementing = false;

    protected $keyType = 'int';

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
