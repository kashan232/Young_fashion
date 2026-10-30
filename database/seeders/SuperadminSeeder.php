<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Super Admin role
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        
        // Sync all permissions to Super Admin role
        $role->syncPermissions(Permission::all());

        // Create Main Super Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@youngfashion.com'],
            [
                'name' => 'Young Fashion Admin',
                'password' => Hash::make('admin123')
            ]
        );

        $user->assignRole($role);

        // Also ensure admin@example.com / admin@gmail.com backup convenience account
        $altUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123')
            ]
        );
        $altUser->assignRole($role);
    }
}
