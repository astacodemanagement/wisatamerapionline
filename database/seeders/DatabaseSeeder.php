<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MenuGroup;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Seeder untuk User
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Sesuaikan dengan password default yang Anda inginkan
        ]);

        // Seeder untuk MenuGroup
        $dashboardGroup = MenuGroup::create([
            'name' => 'Dashboard',
            'status' => true,
            'permission_name' => 'view_dashboard',
            'posision' => 1,
        ]);

        $settingsGroup = MenuGroup::create([
            'name' => 'Pengaturan',
            'status' => true,
            'permission_name' => 'view_settings',
            'posision' => 2,
        ]);

        // Seeder untuk MenuItem yang terkait dengan MenuGroup
        MenuItem::create([
            'name' => 'Home',
            'icon' => 'fa-home',
            'route' => 'home', // Ganti dengan route yang sesuai
            'status' => true,
            'permission_name' => 'view_home',
            'menu_group_id' => $dashboardGroup->id,
            'posision' => 1,
        ]);

        MenuItem::create([
            'name' => 'Analytics',
            'icon' => 'fa-chart-line',
            'route' => 'analytics', // Ganti dengan route yang sesuai
            'status' => true,
            'permission_name' => 'view_analytics',
            'menu_group_id' => $dashboardGroup->id,
            'posision' => 2,
        ]);

        MenuItem::create([
            'name' => 'General Settings',
            'icon' => 'fa-cogs',
            'route' => 'settings.general', // Ganti dengan route yang sesuai
            'status' => true,
            'permission_name' => 'view_general_settings',
            'menu_group_id' => $settingsGroup->id,
            'posision' => 1,
        ]);

        MenuItem::create([
            'name' => 'User Management',
            'icon' => 'fa-users',
            'route' => 'settings.users', // Ganti dengan route yang sesuai
            'status' => true,
            'permission_name' => 'view_user_management',
            'menu_group_id' => $settingsGroup->id,
            'posision' => 2,
        ]);
        $this->call(ProfilTableSeeder::class);
    }
}
