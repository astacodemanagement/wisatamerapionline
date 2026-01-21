<?php

namespace App\Providers;

use App\Models\MenuGroup;
use App\Models\Profil;
use App\Models\User;
use App\Models\Service;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $profil = Profil::first();
        View::share('profil', $profil);

        // Tetap mempertahankan bagian untuk dashboard/admin
        $menus = MenuGroup::with(['items' => function ($query) {
            $query->where('status', 'Aktif');
        }])->where('status', 'Aktif')->get();
        View::share('menus', $menus);

        // Ambil pengguna dengan status nonactive dan hitung jumlahnya
        $nonactiveUsers = User::where('status', 'nonactive')->get();
        $nonactiveUsersCount = $nonactiveUsers->count();
        View::share('nonactiveUsers', $nonactiveUsers);
        View::share('nonactiveUsersCount', $nonactiveUsersCount);

      // Ambil semua services tanpa relasi
        $services = Service::all();
        View::share('services', $services);

        // Ambil blogs dengan status aktif, diurutkan berdasarkan order_display
        $blogs = Blog::where('status', 'active')->orderBy('order_display')->get();
        View::share('blogs', $blogs);

        // Ambil item menu untuk frontend dengan route yang dimulai dengan 'front.'
        $frontendMenuItems = \App\Models\MenuItem::where('route', 'like', 'front.%')
            ->where('status', 'Aktif')
            ->where('head', 'yes')
            ->with(['children' => function ($query) {
                $query->where('status', 'Aktif')->orderBy('position');
            }])
            ->orderBy('position')
            ->get();
        View::share('frontendMenuItems', $frontendMenuItems);
    }
}