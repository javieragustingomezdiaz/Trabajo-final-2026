<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaterialType extends Model
{
    protected $fillable = [
        'name',
        'group',
    ];

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function loanTerm(): HasOne
    {
        return $this->hasOne(LoanTerm::class);
    }
}
