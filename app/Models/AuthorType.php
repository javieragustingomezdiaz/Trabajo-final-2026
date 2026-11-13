<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuthorType extends Model
{
    protected $fillable = [
        'type_name',
    ];

    public function authors(): HasMany
    {
        return $this->hasMany(Author::class);
    }
}
