<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'barcode',
        'material_id',
        'container_id',
        'item_condition_id',
        'item_status_id',
        'observation',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(ItemCondition::class, 'item_condition_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id');
    }
}
