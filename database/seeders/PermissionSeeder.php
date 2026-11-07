<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $permissions = [
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.deactivate',

                'permissions.view',
                'permissions.assign',

                'users.view',
                'users.create',
                'users.update',
                'users.deactivate',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                ]);
            }

            $permissionIds = Permission::pluck('id', 'name');

            $admin = Role::where('name', 'admin')->firstOrFail();
            $librarian = Role::where('name', 'librarian')->firstOrFail();
            $member = Role::where('name', 'member')->firstOrFail();

            $admin->permissions()->sync(
                $permissionIds->values()->all()
            );

            $librarian->permissions()->sync(
                $permissionIds->only([
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.deactivate',
                ])->values()->all()
            );

            $member->permissions()->sync([]);
        });
    }
}
