<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Services\RoleService;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->paginate(10);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = Permission::whereNotIn('name', [
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.deactivate',
            'permissions.view',
            'permissions.assign',
        ])
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permissions) => explode('.', $permissions->name)[0]);
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
            ]);

            $role->permissions()->sync($validated['permissions']);
        });


        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $permissions = Permission::whereNotIn('name', [
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.deactivate',
            'permissions.view',
            'permissions.assign',
        ])
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => explode('.', $permission->name)[0]);

        $role->load('permissions');

        return view('roles.edit', compact('role','permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $role) {
            $role->update([
                'name' => $validated['name'],
            ]);

            $role->permissions()->sync($validated['permissions']);
        });

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol actualizado correctamente');
    }

    public function deactivate(RoleService $roleService, Role $role): RedirectResponse
    {
        $roleService->deactivate($role);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol desactivado correctamente.');
    }

    public function activate(Role $role, RoleService $roleService): RedirectResponse
    {
        $roleService->activate($role);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol activado correctamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


}
