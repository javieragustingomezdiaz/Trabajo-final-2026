<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Material;
use App\Models\User;

class MaterialPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function create(User $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function update(User $user, Material $material): bool
    {
        return $this->canManageCatalog($user);
    }

    public function activate(User $user, Material $material): bool
    {
        return $this->canManageCatalog($user);
    }

    public function deactivate(User $user, Material $material): bool
    {
        return $this->canManageCatalog($user);
    }

    private function canManageCatalog(User $user): bool
    {
        return (bool) $user->is_active
            && (bool) $user->role?->is_active
            && in_array($user->role->name, ['admin', 'librarian'], true);
    }
}
