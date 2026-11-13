<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentType extends Model
{
    protected $fillable = ['term'];

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
