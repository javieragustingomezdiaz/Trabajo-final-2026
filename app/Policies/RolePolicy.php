<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    private function isAdministrator(User $user): bool
    {
        return $user->is_active
            && $user->role?->is_active
            && $user->role->name === 'admin';
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdministrator($user);
    }

 
    public function view(User $user, Role $role): bool
    {
        return $this->isAdministrator($user);
    }


    public function create(User $user): bool
    {
        return $this->isAdministrator($user);
    }


    public function update(User $user, Role $role): bool
    {
        return $this->isAdministrator($user);
    }


    public function deactivate(User $user, Role $role): bool
    {
        return $this->isAdministrator($user);
    }


    public function activate(User $user, Role $role): bool
    {
        return $this->isAdministrator($user);
    }

}
