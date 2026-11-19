<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sanction extends Model
{
    protected $fillable = [
        'start_on',
        'end_on',
        'lifted_on',
        'reason',
        'status',
        'observation',
        'circulation_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_on' => 'datetime',
            'end_on' => 'datetime',
            'lifted_on' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function circulation(): BelongsTo
    {
        return $this->belongsTo(Circulation::class);
    }
}
