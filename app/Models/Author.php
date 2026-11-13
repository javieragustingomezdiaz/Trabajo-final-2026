<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    protected $fillable = [
        'name',
        'birth_date',
        'death_date',
        'author_type_id',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'death_date' => 'date',
        ];
    }

    public function authorType(): BelongsTo
    {
        return $this->belongsTo(AuthorType::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'author_material')
            ->withTimestamps();
    }
}
