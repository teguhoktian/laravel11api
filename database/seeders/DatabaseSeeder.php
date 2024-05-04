<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        if ($this->command->confirm('Jalankan migrate:refresh sebelum seeding?')) {
            $this->command->call('migrate:refresh');
            $this->command->warn("Database telah dikosongkan!");
        }

        // Masukkan Permissions Awal
        $permissions = Permission::defaultPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Default Permission added.');

        if ($this->command->confirm('Buat Role untuk Pengguna, admin dan user sebagai bawaan? [y|N]', true)) {

            $input_roles = $this->command->ask('Masukkan role dalam format terpisah koma.', 'Admin,User');

            $roles_array = explode(',', $input_roles);

            foreach ($roles_array as $role) {
                $role = Role::firstOrCreate(['name' => trim($role)]);

                if ($role->name == 'Admin') {

                    $role->syncPermissions(Permission::all());
                    $this->command->info('Admin diberikan semua permission');
                } else {

                    $role->syncPermissions(Permission::where('name', 'LIKE', 'view_%')->get());
                }

                // create one user for each role
                $this->createUser($role);
            }

            $this->command->info('Roles ' . $input_roles . ' berhasil ditambahkan');
        } else {
            Role::firstOrCreate(['name' => 'User']);
            $this->command->info('Added only default user role.');
        }
    }

    function createUser($role): void
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role->name);

        if ($role->name == 'Admin') {
            $this->command->info('Here is your admin details to login:');
            $this->command->warn($user->email);
            $this->command->warn('Password is "password"');
        }
    }
}
