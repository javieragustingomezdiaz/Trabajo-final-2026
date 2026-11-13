<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Material extends Model
{
    protected $fillable = [
        'title',
        'edition',
        'publication_date',
        'identifier_value',
        'dimension',
        'extent',
        'description',
        'cover',
        'publisher_id',
        'identifier_type_id',
        'material_type_id',
        'language_id',
        'media_type_id',
        'content_type_id',
        'carrier_type_id',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
        ];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function identifierType(): BelongsTo
    {
        return $this->belongsTo(IdentifierType::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function mediaType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class);
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function carrierType(): BelongsTo
    {
        return $this->belongsTo(CarrierType::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_material')
            ->withTimestamps();
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'material_topic')
            ->withTimestamps();
    }

    public function bibliographic(): HasOne
    {
        return $this->hasOne(BibliographicMaterial::class);
    }

    public function serialpublication(): HasOne
    {
        return $this->hasOne(SerialPublication::class);
    }

    public function cartographic(): HasOne
    {
        return $this->hasOne(CartographicMaterial::class);
    }

    public function graph(): HasOne
    {
        return $this->hasOne(GraphMaterial::class);
    }

    public function musical(): HasOne
    {
        return $this->hasOne(MusicalMaterial::class);
    }

    public function sound(): HasOne
    {
        return $this->hasOne(SoundMaterial::class);
    }

    public function video(): HasOne
    {
        return $this->hasOne(VideoMaterial::class);
    }

    public function object(): HasOne
    {
        return $this->hasOne(ObjectMaterial::class);
    }
}