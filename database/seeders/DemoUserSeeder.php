<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException(
                'DemoUsersSeeder solo se puede ejecutar en local o testing.'
            );
        }

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        $accounts = [
            [
                'role' => 'admin',
                'dni' => 'DEMO-ADMIN-001',
                'name' => 'Admin',
                'last_name' => 'Demo',
                'email' => 'admin@example.test',
                'password' => 'admin',
            ],
            [
                'role' => 'librarian',
                'dni' => 'DEMO-LIBRARIAN-001',
                'name' => 'Librarian',
                'last_name' => 'Demo',
                'email' => 'librarian@example.test',
                'password' => 'librarian',
            ],
            [
                'role' => 'member',
                'dni' => 'DEMO-MEMBER-001',
                'name' => 'Member',
                'last_name' => 'Demo',
                'email' => 'member@example.test',
                'password' => 'member',
            ],
        ];

        DB::transaction(function () use ($accounts): void {
            foreach ($accounts as $account) {
                $person = Person::query()
                    ->where('dni', $account['dni'])
                    ->first();

                if (! $person) {
                    $person = (new Person())->forceFill([
                        'dni' => $account['dni'],
                        'name' => $account['name'],
                        'last_name' => $account['last_name'],
                        'birth_date' => '1990-01-01',
                        'sex' => 'No especificado',
                        'email' => $account['email'],
                        'phone' => '0000000000',
                        'address' => 'Domicilio de prueba',
                    ]);

                    $person->save();
                }

                $user = User::query()
                    ->where('person_id', $person->id)
                    ->first() ?? new User();

                $user->forceFill([
                    'person_id' => $person->id,
                    'role_id' => Role::query()
                        ->where('name', $account['role'])
                        ->firstOrFail()
                        ->id,
                    'is_active' => true,
                    'password' => Hash::make($account['password']),
                ]);

                $user->save();

                $this->command?->info(
                    "{$account['role']} | {$account['email']} | contraseña: {$account['password']}"
                );
            }
        });
    }
}