<?php

namespace Database\Seeders;
use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Menu Utama
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'permission_name' => 'dashboard-list',
            'route' => 'home',
            'icon' => 'ti ti-dashboard',
            'parent_id' => null,
        ]);

        // Menu Produk
        $produk = Menu::create([
            'name' => 'Produk',
            'permission_name' => 'produk-list',
            'route' => 'produk.index',
            'icon' => 'ti ti-app-window',
            'parent_id' => null,
        ]);

        // Submenu Blog
        $blog = Menu::create([
            'name' => 'Blog',
            'permission_name' => 'blog-list',
            'route' => 'blog.index',
            'icon' => 'ti ti-blog',
            'parent_id' => null,
        ]);

        $kategoriBlog = Menu::create([
            'name' => 'Kategori Blog',
            'permission_name' => 'kategori-blog-list',
            'route' => 'kategori_blog.index',
            'icon' => 'ti ti-category',
            'parent_id' => $blog->id,
        ]);

        // Role dan Permission
        Menu::create([
            'name' => 'Role',
            'permission_name' => 'role-list',
            'route' => 'roles.index',
            'icon' => 'ti ti-lock',
            'parent_id' => null,
        ]);
    }
}
