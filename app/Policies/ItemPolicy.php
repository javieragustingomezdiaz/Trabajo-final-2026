<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    private function canManageItems(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && in_array($user->role->name, ['admin', 'librarian'], true);
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->canManageItems($user);
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canManageItems($user);
    }

    public function update(User $user, Item $item): bool
    {
        return $this->canManageItems($user);
    }

    public function activate(User $user, Item $item): bool
    {
        return $this->canManageItems($user);
    }

    public function deactivate(User $user, Item $item): bool
    {
        return $this->canManageItems($user);
    }


}
