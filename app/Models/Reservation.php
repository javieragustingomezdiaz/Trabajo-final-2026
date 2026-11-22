<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    public const STATUS_WAITING = 'En espera';
    public const STATUS_NOTIFIED = 'Notificada';
    public const STATUS_READY = 'Pendiente de retiro';
    public const STATUS_PICKED_UP = 'Retirada';
    public const STATUS_CANCELLED = 'Cancelada';
    public const STATUS_EXPIRED = 'Vencida';
    public const STATUS_REJECTED = 'Rechazada';

    public const ACTIVE_STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_NOTIFIED,
        self::STATUS_READY,
    ];

    protected $fillable = [
        'registration_date',
        'withdrawal_deadline',
        'withdrawal_date',
        'reservation_status_id',
        'material_id',
        'item_id',
        'user_id',
        'notified_at',
        'confirmation_expires_at',
        'confirmation_token',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'datetime',
            'withdrawal_deadline' => 'datetime',
            'withdrawal_date' => 'datetime',
            'notified_at' => 'datetime',
            'confirmation_expires_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ReservationStatus::class, 'reservation_status_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function isCancellable(): bool
    {
        return in_array($this->status?->name, self::ACTIVE_STATUSES, true);
    }
}
