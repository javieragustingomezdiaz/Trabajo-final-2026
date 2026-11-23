<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Circulation extends Model
{
    public const TYPE_HOME = 'Domicilio';
    public const TYPE_IN_LIBRARY = 'Consulta en sala';
    public const STATUS_ACTIVE = 'Vigente';
    public const STATUS_OVERDUE = 'Demorado';
    public const STATUS_RETURNED = 'Devuelto';
    public const STATUS_LOST = 'Perdido';
    public const ACTIVE_STATUSES = [self::STATUS_ACTIVE, self::STATUS_OVERDUE];

    protected $fillable = [
        'loan_type',
        'loan_date',
        'estimated_return_date',
        'actual_return_date',
        'observation',
        'number_renewals',
        'item_id',
        'reservation_id',
        'user_id',
        'loan_status_id',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'datetime',
            'estimated_return_date' => 'datetime',
            'actual_return_date' => 'datetime',
            'number_renewals' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(LoanStatus::class, 'loan_status_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('actual_return_date')
            ->whereHas('status', fn ($statusQuery) => $statusQuery->whereIn('name', self::ACTIVE_STATUSES));
    }
}
