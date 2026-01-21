<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Route;
use Illuminate\Support\Facades\Route as LaravelRoute;

class RouteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:route-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:route-create', ['only' => ['create', 'store', 'generateRoutes']]);
        $this->middleware('permission:route-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:route-delete', ['only' => ['destroy']]);
    }


    public function index(Request $request)
    {
        $title = "Halaman Route";
        $subtitle = "Menu Route";
        return view('route', compact('title', 'subtitle'));
    }


    public function generateRoutes()
    {
        if (!auth()->user()->can('route-create')) {
            abort(403, 'User tidak memiliki izin untuk generate routes');
        }
     
        $routes = LaravelRoute::getRoutes();
        Route::truncate();
    
        foreach ($routes as $route) {
            Route::create([
                'name' => $route->getName() ?? $route->uri(),
            ]);
        }
    
        return redirect()->back()->with('success', 'Routes berhasil digenerate.');
    }
    
}
