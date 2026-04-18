<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@sasinna.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('Hey&368'), // Default password
            ]
        );
        $admin->assignRole('Admin');

        // Create Editor User
        $editor = User::firstOrCreate(
            ['email' => 'editor@sasinna.com'],
            [
                'name' => 'Editor User',
                'password' => bcrypt('Hey&358'), // Default password
            ]
        );
        $editor->assignRole('Editor');

        // Create Viewer User
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@sasinna.com'],
            [
                'name' => 'Viewer User',
                'password' => bcrypt('Hny&368'), // Default password
            ]
        );
        $viewer->assignRole('Viewer');
    }
}
