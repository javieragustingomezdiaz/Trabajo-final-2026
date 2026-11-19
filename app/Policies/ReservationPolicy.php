<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function create(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && $user->role?->name === 'member';
    }

    public function cancelOwn(User $user, Reservation $reservation): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && $user->role?->name === 'member'
            && (int) $reservation->user_id === (int) $user->id;
    }

    public function createForMember(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && in_array($user->role?->name, ['admin', 'librarian'], true);
    }
}
