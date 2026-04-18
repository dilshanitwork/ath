<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'create permissions', 'edit permissions', 'delete permissions', 'view permissions', 
            'create users', 'edit users', 'delete users', 'view users', 
            'create roles', 'edit roles', 'delete roles', 'view roles', 
            'create bills', 'edit bills', 'delete bills', 'view bills', 
            'create customers', 'edit customers', 'delete customers', 'view customers', 
            'view attributes', 'delete attributes', 'edit attributes', 'create attributes', 
            'print bills', 'printCollection bills', 'advance edit bills', 'view finance', 
            'view stock items', 'edit stock items', 'create stock items', 'delete stock items', 
            'view purchase orders', 'edit purchase orders', 'create purchase orders', 'delete purchase orders', 
            'view suppliers', 'edit suppliers', 'create suppliers', 'delete suppliers', 
            'view direct bills', 'create direct bills', 'edit direct bills', 'delete direct bills', 
            'print direct bills', 'view cost price',
            // Add these:
            'create editor', 'edit editor', 'delete editor', 'view editor',
            'create viewer', 'edit viewer', 'delete viewer', 'view viewer',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
