<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);

        // Assign all permissions to Admin
        $admin->syncPermissions(Permission::all());

        // Assign specific permissions to Editor
        $editor->syncPermissions([
            'create users',
            'edit users',
            'view users',
            'view roles',
            'create bills',
            'edit bills',
            'view bills',
            'create customers',
            'edit customers',
            'view customers',
        ]);

        // Assign view-only permissions to Viewer
        $viewer->syncPermissions([
            'view users',
            'view roles',
            'view bills',
            'view customers',
        ]);
    }
}