<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationStatus extends Model
{
    protected $fillable = ['name'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'reservation_status_id');
    }
}
