<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Roles dengan guard_name
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $restaurantRole = Role::firstOrCreate(['name' => 'restaurant', 'guard_name' => 'web']);

        // Buat Admin pertama jika belum ada
        $adminEmail = 'admin@example.com';

        $user = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'), // Pastikan mengganti 'password' dengan password yang aman
            ]
        );

        // Pastikan user mendapatkan role admin
        if (!$user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }
    }
}

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
        $this->call([
            CategorySeeder::class,
            // Seeder lainnya
        ]);
    }
    
}