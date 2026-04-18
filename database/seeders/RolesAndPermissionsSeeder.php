<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $admin = Role::create(['name' => 'admin']);
        // $user = Role::create(['name' => 'user']);

        // $permissions = ['view -users', 'edit-users', 'delete-users'];
        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission]);
        // }

        // $admin->givePermissionTo(Permission::all());
        // $user->givePermissionTo('view articles');
    }
}
