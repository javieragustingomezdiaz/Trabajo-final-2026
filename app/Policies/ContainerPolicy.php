<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Container;
use App\Models\User;

class ContainerPolicy
{
    public function create(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && in_array($user->role->name, ['admin', 'librarian'], true);
    }

}
