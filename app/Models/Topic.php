<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
     protected $fillable = [
        'name',
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'material_topic')
            ->withTimestamps();
    }
}
