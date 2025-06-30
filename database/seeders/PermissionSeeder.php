<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar modul
        $modules = ['posts', 'pages', 'categories', 'media_manager'];

        // Daftar aksi CRUD
        $actions = ['view', 'create', 'edit', 'delete'];

        // Buat permissions untuk setiap modul dan aksi
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::updateOrCreate(
                    ['name' => "{$action} {$module}"],
                    ['guard_name' => 'web']
                );
            }
        }

        // -- Buat Roles dan User Default (Opsional, tapi sangat direkomendasikan) --

        // Buat role Admin dan berikan semua permission
        $adminRole = Role::updateOrCreate(['name' => 'Super Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Buat role Writer
        $writerRole = Role::updateOrCreate(['name' => 'Admin']);
        $writerRole->givePermissionTo([
            'view posts', 'create posts', 'edit posts',
            'view pages'
        ]);

        // Buat user Superadmin
        $adminUser = User::firstOrCreate(
            ['email' => 'superadmin@headless.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $adminUser->assignRole('Super Admin');

        // Buat user Admin
        $writerUser = User::firstOrCreate(
            ['email' => 'lily@headless.com'],
            [
                'name' => 'Lily James',
                'password' => bcrypt('password'),
            ]
        );
        $writerUser->assignRole('Admin');
    }
}
