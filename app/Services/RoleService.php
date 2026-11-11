<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function deactivate(Role $role): void
    {
        $activeUsersCount = $role->users()
            ->where('is_active', true)
            ->count();

        if ($activeUsersCount > 0) {
            throw ValidationException::withMessages([
                'role' => "No se puede desactivar el rol porque está asignado a {$activeUsersCount} usuarios activos. Reasignar los usuarios antes de continuar.",
            ]);
        }

        $role->update([
            'is_active' => false,
        ]);
    }

    public function activate(Role $role): void
    {
        $role->is_active = true;
        $role->save();
    }
}
