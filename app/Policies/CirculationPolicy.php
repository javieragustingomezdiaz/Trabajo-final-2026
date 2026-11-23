<?php

namespace App\Policies;

use App\Models\Circulation;
use App\Models\User;

class CirculationPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->create($user);
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && in_array($user->role?->name, ['admin', 'librarian'], true);
    }

    public function renew(User $user, Circulation $circulation): bool
    {
        return $this->create($user);
    }

    public function recordReturn(User $user, Circulation $circulation): bool
    {
        return $this->create($user);
    }

    public function markLost(User $user, Circulation $circulation): bool
    {
        return $this->create($user);
    }
}
