<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use App\Models\Tour; // Import Tour model
use App\Models\Destination; // Import Destination model
use App\Models\Gallery; // Import Gallery model
use App\Models\Testimonial; // Import Testimonial model
use App\Models\Agent; // Import Agent model
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y')); // Default ke tahun sekarang

        // Ambil data dari tabel tours
        $totalTours = Tour::whereYear('created_at', $year)->count();
        $activeTours = Tour::whereYear('created_at', $year)->where('status', 'active')->count();

        // Ambil data dari tabel destinations
        $totalDestinations = Destination::whereYear('created_at', $year)->count();
        $activeDestinations = Destination::whereYear('created_at', $year)->where('status', 'active')->count();

        // Ambil data dari tabel galleries
        $totalGalleries = Gallery::whereYear('created_at', $year)->count();
        $activeGalleries = Gallery::whereYear('created_at', $year)->where('status', 'active')->count();

        // Ambil data dari tabel testimonials
        $totalTestimonials = Testimonial::whereYear('created_at', $year)->count();
        $activeTestimonials = Testimonial::whereYear('created_at', $year)->where('status', 'active')->count();

        // Ambil data dari tabel agents
        $totalAgents = Agent::whereYear('created_at', $year)->count();
        $activeAgents = Agent::whereYear('created_at', $year)->where('status', 'active')->count();

        // Ambil data dari tabel services
        $totalServices = Service::whereYear('created_at', $year)->count();
        $activeServices = Service::whereYear('created_at', $year)->where('status', 'active')->count();

        return view('home', [
            'title' => 'Dashboard',
            'subtitle' => 'Overview',
            'year' => $year,
            'totalTours' => $totalTours,
            'activeTours' => $activeTours,
            'totalDestinations' => $totalDestinations,
            'activeDestinations' => $activeDestinations,
            'totalGalleries' => $totalGalleries,
            'activeGalleries' => $activeGalleries,
            'totalTestimonials' => $totalTestimonials,
            'activeTestimonials' => $activeTestimonials,
            'totalAgents' => $totalAgents,
            'activeAgents' => $activeAgents,
            'totalServices' => $totalServices,
            'activeServices' => $activeServices,
        ]);
    }
}
