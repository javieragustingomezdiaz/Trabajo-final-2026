<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    protected $fillable = [
        'affected_model',
        'action',
        'afected_record',
        'user_id',
        'registration_date',
    ];

    protected function casts(): array
    {
        return ['registration_date' => 'datetime'];
    }

    public function details(): HasMany
    {
        return $this->hasMany(AuditDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
