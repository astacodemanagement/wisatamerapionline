<?php

namespace App\Http\Controllers;

use App\Models\OtherSlider;
use App\Models\Gallery;
use App\Models\GalleryCategory;


use App\Models\Blog;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Count;

use App\Models\Legal;
use App\Models\Newsletter;
use App\Models\PostingJob;
use App\Models\PrivacyPolicy;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Profil;

use App\Models\Reason;
use App\Models\Service;
use App\Models\JobVacancy;
use App\Models\Slider;

use App\Models\Team;
use App\Models\Testimonial;
use App\Models\Tour;
use App\Models\Destination;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactMail;


class FrontController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $profile = Profil::first();
        $title = $profile->nama_profil ?? "Default Title";
        $subtitle = "Menu " . ($profile->nama_profil ?? "Default Subtitle");

        $data_slider = Slider::where('status', 'active')->orderBy('order_display')->get();

        $data_reason = Reason::where('status', 'active')->orderBy('order_display')->get();
        $data_service = Service::where('status', 'active')->orderBy('order_display')->get();
        $data_testimonial = Testimonial::where('status', 'active')->orderBy('order_display')->get();
        $data_client = Client::where('status', 'active')->orderBy('order_display')->get();
        $data_product_category = ProductCategory::where('status', 'active')->orderBy('order_display')->get();
        $data_count = Count::where('status', 'active')->orderBy('order_display')->get();
        $data_blog = Blog::where('status', 'active')->orderBy('order_display')->get();
        $data_tour = Tour::where('status', 'active')->orderBy('order_display')->take(6)->get();
        $data_destination = Destination::where('status', 'active')->orderBy('order_display')->get();

        return view('front.home', compact(
            'title',
            'subtitle',
            'data_service',
            'data_reason',
            'data_slider',
            'data_testimonial',
            'data_client',
            'data_product_category',
            'data_count',
            'data_blog',
            'data_tour',
            'data_destination'
        ));
    }
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletters,email'
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar.'
        ]);

        try {
            Newsletter::create(['email' => $validated['email']]);
            return response()->json([
                'message' => 'Berhasil berlangganan newsletter!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan newsletter: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan email.'
            ], 500);
        }
    }


    public function service()
    {
        $title = "Layanan Kami";
        $subtitle = "Menu Layanan Kami";
        $data_service = Service::where('status', 'active')->paginate(9); // Paginate with 9 items per page
        $profil = Profil::first(); // Assuming Profil model for global data

        return view('front.service', compact(
            'title',
            'subtitle',
            'data_service',
            'profil'
        ));
    }

    public function show_service($id)
    {
        $service = Service::where('id', $id)->where('status', 'active')->firstOrFail();
        $title = $service->name;
        $subtitle = "Detail Layanan";
        $profil = Profil::first(); // Assuming Profil model is needed for global data

        return view('front.service_detail', compact('service', 'title', 'subtitle', 'profil'));
    }

    public function job()
    {
        $title = "Lowongan Kami";
        $subtitle = "Menu Lowongan Kami";
        $data_job = JobVacancy::where('status', 'active')->paginate(9); // Paginate with 9 items per page
        $profil = Profil::first(); // Assuming Profil model for global data

        return view('front.job', compact(
            'title',
            'subtitle',
            'data_job',
            'profil'
        ));
    }


    public function reason()
    {
        $title = "Alasan Memilih Kami";
        $subtitle = "Keunggulan Kami";
        $data_reason = Reason::where('status', 'active')->orderBy('order_display')->paginate(9); // Paginate 9 items per page
        $profil = Profil::first(); // Untuk data global seperti no_wa dan deskripsi_2

        return view('front.reason', compact(
            'title',
            'subtitle',
            'data_reason',
            'profil'
        ));
    }

    public function show_reason($id)
    {
        $reason = Reason::where('id', $id)->where('status', 'active')->firstOrFail();
        $title = $reason->name;
        $subtitle = "Detail Alasan";
        $profil = Profil::first();

        return view('front.reason_detail', compact('reason', 'title', 'subtitle', 'profil'));
    }



    public function contact()
    {
        $title = "Kontak Kami";
        $subtitle = "Menu Kontak Kami";
        $profil = Profil::first();
        return view('front.contact', compact(
            'title',
            'subtitle',
            'profil'
        ));
    }

    public function storeContact(Request $request)
    {
        // Cek apakah request dari AJAX
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi
            $validated = $request->validate([
                'form_name'    => 'required|string|max:255',
                'form_email'   => 'required|email|max:255',
                'form_phone'   => 'nullable|string|max:20',
                'form_subject' => 'nullable|string|max:255',
                'form_message' => 'required|string',
            ]);

            try {
                $contact = Contact::create([
                    'name'    => $request->form_name,
                    'email'   => $request->form_email,
                    'phone'   => $request->form_phone,
                    'subject' => $request->form_subject,
                    'message' => $request->form_message,
                ]);

                try {
                    $profil = Profil::first();
                    if ($profil && filter_var($profil->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($profil->email)->send(new ContactMail($contact));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send contact email: ' . $e->getMessage());
                }

                return response()->json([
                    'success' => 'Your message has been sent successfully!'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to save message. Please try again.'
                ], 500);
            }
        }

        // Jika bukan AJAX (misal langsung submit biasa)
        $request->validate([
            'form_name'    => 'required|string|max:255',
            'form_email'   => 'required|email|max:255',
            'form_phone'   => 'nullable|string|max:20',
            'form_subject' => 'nullable|string|max:255',
            'form_message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name'    => $request->form_name,
            'email'   => $request->form_email,
            'phone'   => $request->form_phone,
            'subject' => $request->form_subject,
            'message' => $request->form_message,
        ]);

        try {
            $profil = Profil::first();
            if ($profil && filter_var($profil->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($profil->email)->send(new ContactMail($contact));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage());
        }

        return back()->with('success', 'Your message has been sent successfully!');
    }
    public function product()
    {
        $title = "Produk Kami";
        $subtitle = "Menu Produk Kami";
        $profil = Profil::first();

        // Ambil kategori & subkategori untuk sidebar/filter
        $product_categories = ProductCategory::with(['subCategories' => function ($q) {
            $q->orderBy('order_display', 'asc');
        }])
            ->orderBy('order_display', 'asc')
            ->get();

        // Query dasar produk aktif
        $query = Product::with(['category', 'subCategory', 'images'])
            ->where('status', 'active');

        // 🔍 Filter pencarian
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($qc) => $qc->where('name', 'like', "%{$search}%"));
            });
        }

        // 🏷️ Filter kategori berdasarkan slug
        if ($categorySlug = request('category')) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // 🏷️ Filter subkategori berdasarkan slug
        if ($subCategorySlug = request('sub_category')) {
            $query->whereHas('subCategory', function ($q) use ($subCategorySlug) {
                $q->where('slug', $subCategorySlug);
            });
        }

        // ⚙️ Sorting
        if ($sort = request('sort')) {
            switch ($sort) {
                case 'latest':
                    $query->latest();
                    break;
                case 'price_low':
                    $query->orderBy('selling_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('selling_price', 'desc');
                    break;
            }
        }

        // 🔢 Pagination
        $data_product = $query->paginate(12)->appends(request()->query());

        // 🆕 Produk terbaru untuk sidebar
        $recent_products = Product::where('status', 'active')->latest()->take(6)->get();

        return view('front.product', compact(
            'title',
            'subtitle',
            'data_product',
            'recent_products',
            'product_categories',
            'profil'
        ));
    }

    public function productDetail($slug)
    {
        $product = Product::with(['category', 'subCategory', 'images'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $title = $product->name;
        $subtitle = "Detail Produk";
        $profil = Profil::first();

        return view('front.product_detail', compact(
            'title',
            'subtitle',
            'product',
            'profil'
        ));
    }



    public function blog()
    {
        $title = "Blog Kami";
        $subtitle = "Menu Blog Kami";
        $profil = Profil::first();

        $query = Blog::with(['category' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('headline', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($qc) use ($search) {
                        $qc->where('category_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($category = request('category')) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category_name', $category);
            });
        }

        if (request('sort') === 'latest') {
            $query->latest();
        } elseif (request('sort') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $data_blog = $query->paginate(9);

        $categories = \App\Models\BlogCategory::where('status', 'active')->get();

        return view('front.blogs.index', compact('title', 'subtitle', 'data_blog', 'profil', 'categories'));
    }

    public function show_blog($news_slug)
    {
        $blog = Blog::with(['category' => function ($q) {
            $q->where('status', 'active');
        }])->where('news_slug', $news_slug)->where('status', 'active')->firstOrFail();
        $title = $blog->headline;
        $subtitle = "Detail Blog";
        $profil = Profil::first();

        return view('front.blogs.detail', compact('blog', 'title', 'subtitle', 'profil'));
    }


    public function about()
    {
        $title = "Tentang Kami";
        $subtitle = "Menu Tentang Kami";

        $data_reason = Reason::all();
        $data_agent = Agent::where('status', 'active')->orderBy('order_display')->get();
        $other_sliders = OtherSlider::where('status', 'active')->orderBy('order_display')->get();

        $data_gallery_category = GalleryCategory::with(['galleries' => function ($query) {
            $query->where('status', 'active')->orderBy('order_display');
        }])->where('status', 'active')->orderBy('order_display')->get();

        return view('front.about', compact(
            'title',
            'subtitle',
            'data_reason',
            'data_agent',
            'other_sliders',
            'data_gallery_category'
        ));
    }


    public function team()
    {
        $title = "Tim Kami";
        $subtitle = "Menu Tim Kami";
        $data_team = Team::all();
        return view('front.team', compact(
            'title',
            'subtitle',
            'data_team',
        ));
    }

    public function legal()
    {
        $title = "Legalitas Kami";
        $subtitle = "Menu Legalitas Kami";
        $data_legal = Legal::all();
        return view('front.legal', compact(
            'title',
            'subtitle',
            'data_legal',
        ));
    }

    public function privacyPolicy()
    {
        $title = "Kebijakan Privasi Kami";
        $subtitle = "Menu Kebijakan Privasi Kami";
        $data_privacy_policy = PrivacyPolicy::where('status', 'active')->orderBy('order_display')->get();
        $last_updated = PrivacyPolicy::max('updated_at') ? Carbon::parse(PrivacyPolicy::max('updated_at'))->format('d M Y') : '10 Jun 2025';
        return view('front.privacy_policy', compact(
            'title',
            'subtitle',
            'data_privacy_policy',
            'last_updated'
        ));
    }
    public function termOfUse()
    {
        $title = "Syarat & Ketentuan Kami";
        $subtitle = "Menu Syarat & Ketentuan Kami";
        return view('front.term_of_use', compact(
            'title',
            'subtitle',

        ));
    }

    public function postingJob()
    {
        $title = "Karir Dari Kami";
        $subtitle = "Menu Karir Dari Kami";
        $data_posting_job = PostingJob::where('status', 'active')->orderBy('order_display')->get();
        return view('front.posting_job', compact(
            'title',
            'subtitle',
            'data_posting_job',

        ));
    }

    public function destinationIndex()
    {
        $title = "Destinasi Kami";
        $subtitle = "Menu Destinasi Kami";
        $data_destination = Destination::where('status', 'active')->orderBy('order_display')->paginate(9);
        $profil = Profil::first();

        return view('front.destinations.index', compact(
            'title',
            'subtitle',
            'data_destination',
            'profil'
        ));
    }

    public function destinationDetail($slug)
    {
        $destination = Destination::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Increment views
        $destination->increment('views');

        $title = $destination->name;
        $subtitle = "Detail Destinasi";
        $profil = Profil::first();

        return view('front.destinations.detail', compact(
            'destination',
            'title',
            'subtitle',
            'profil'
        ));
    }
    public function tourIndex()
    {
        $title = "Tour Kami";
        $subtitle = "Menu Tour Kami";
        $data_tour = Tour::where('status', 'active')->orderBy('order_display')->paginate(9);
        $profil = Profil::first();

        return view('front.tours.index', compact(
            'title',
            'subtitle',
            'data_tour',
            'profil'
        ));
    }

    public function tourDetail($slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $title = $tour->name;
        $subtitle = "Detail Tour";
        $profil = Profil::first();

        return view('front.tours.detail', compact(
            'tour',
            'title',
            'subtitle',
            'profil'
        ));
    }
    public function galleryIndex()
    {
        $title = "Galeri Kami";
        $subtitle = "Menu Galeri Kami";
        $data_gallery_category = GalleryCategory::with(['galleries' => function ($query) {
            $query->where('status', 'active')->orderBy('order_display');
        }])->where('status', 'active')->orderBy('order_display')->get();
        $profil = Profil::first();

        return view('front.galleries.index', compact(
            'title',
            'subtitle',
            'data_gallery_category',
            'profil'
        ));
    }

    public function apply(Request $request)
    {
        try {
            // Validasi input formulir
            $validated = $request->validate([
                'job_id' => 'required|exists:posting_jobs,id',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'required|string|max:20',
                'cv' => 'required|file|mimes:pdf|max:2048', // Maks 2MB
                'portfolio' => 'nullable|file|mimes:pdf,zip,doc,docx|max:5120', // Maks 5MB
            ]);

            // Ambil email tujuan dari model Profil
            $profil = Profil::first();
            if (!$profil || !$profil->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email penerima tidak ditemukan.'
                ], 400);
            }

            // Simpan file yang diunggah
            $cvPath = $request->file('cv')->store('applications/cvs', 'public');
            $portfolioPath = $request->hasFile('portfolio') ? $request->file('portfolio')->store('applications/portfolios', 'public') : null;

            // Siapkan data untuk email
            $job = PostingJob::find($validated['job_id']);
            $emailData = [
                'job_title' => $job->name,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'cv_url' => Storage::url($cvPath),
                'portfolio_url' => $portfolioPath ? Storage::url($portfolioPath) : null,
            ];

            // Kirim email
            Mail::send('emails.job_application', $emailData, function ($message) use ($profil, $validated, $request) {
                $message->to($profil->email)
                    ->subject('Lamaran Pekerjaan: ' . $validated['full_name'])
                    ->replyTo($validated['email']);
                $message->attach($request->file('cv')->getRealPath(), [
                    'as' => 'CV_' . $validated['full_name'] . '.' . $request->file('cv')->getClientOriginalExtension(),
                    'mime' => $request->file('cv')->getMimeType(),
                ]);
                if ($request->hasFile('portfolio')) {
                    $message->attach($request->file('portfolio')->getRealPath(), [
                        'as' => 'Portfolio_' . $validated['full_name'] . '.' . $request->file('portfolio')->getClientOriginalExtension(),
                        'mime' => $request->file('portfolio')->getMimeType(),
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Lamaran Anda berhasil dikirim!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim lamaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
