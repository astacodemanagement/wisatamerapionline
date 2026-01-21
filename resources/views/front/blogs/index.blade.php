@extends('front.layouts.app')
@section('title', $title)

@push('styles')
    <style>
        .pagination-wrapper {
            margin-top: 30px;
        }

        .pagination .page-item .page-link {
            color: #333;
            border: none;
            border-radius: 8px;
            margin: 0 5px;
            padding: 8px 14px;
            background-color: #f3f3f3;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background-color: #ffc107;
            color: #000;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .pagination .page-item .page-link:hover {
            background-color: #ffdb6a;
            color: #000;
        }

        /* Filter Form */
        .filter-form {
            display: flex;
            gap: 1rem;
        }

        .filter-form .form-select {
            width: 150px !important;
        }

        @media (max-width: 767px) {
            .filter-form {
                width: 100%;
                margin-top: 1rem;
            }
            .filter-form .form-select {
                width: 100% !important;
                max-width: none !important;
            }
        }

        /* Blog Card */
        .blog-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .blog-card img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        .blog-date {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffc107;
            color: #000;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        /* Kategori Active Style */
        .list-group-item.bg-light {
            background-color: #f8f9fa !important;
        }
        .list-group-item a.text-dark {
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<section class="page-header" style="position: relative; padding: 120px 0; background-size: cover; background-position: center; min-height: 300px;">
    <div class="page-header__bg" style="background-image: url('{{ $profil->breadcrumb_4 ? asset('upload/profil/' . $profil->breadcrumb_4) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <h2 style="color: white; margin-bottom: 20px; font-size: 48px; font-weight: bold;">{{ $title }}</h2>
        <ul class="thm-breadcrumb list-unstyled" style="color: white; margin: 0; padding: 0;">
            <li style="display: inline; color: white;"><a href="/" style="color: white; text-decoration: none;">Beranda</a> / </li>
            <li style="display: inline; color: white;">{{ $title }}</li>
        </ul>
    </div>
</section>

<section class="blog-page py-5">
    <div class="container">
        <div class="row">

            {{-- ===== Sidebar (Kiri) ===== --}}
            <div class="col-lg-3 mb-4">

                {{-- Search Bar --}}
                <form action="{{ route('front.blog') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                               placeholder="Cari artikel...">
                        <button class="btn btn-dark" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>

                {{-- Kategori Card --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-semibold">
                        Kategori
                    </div>
                    <ul class="list-group list-group-flush">
                        @php
                            $categories = \App\Models\BlogCategory::all();
                        @endphp
                        <li class="list-group-item {{ !request('category') ? 'bg-light' : '' }}">
                            <a href="{{ route('front.blog') }}"
                               class="text-decoration-none {{ !request('category') ? 'text-dark fw-bold' : 'text-secondary' }}">
                                Semua Artikel
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                            <li class="list-group-item {{ request('category') == $cat->category_name ? 'bg-light' : '' }}">
                                <a href="{{ route('front.blog') }}?category={{ urlencode($cat->category_name) }}"
                                   class="text-decoration-none {{ request('category') == $cat->category_name ? 'text-dark fw-bold' : 'text-secondary' }}">
                                    {{ $cat->category_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Artikel Terbaru --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-semibold">
                        Artikel Terbaru
                    </div>
                    <div class="card-body">
                        @php
                            $recent_blogs = \App\Models\Blog::latest()
                                ->take(3)
                                ->get();
                        @endphp
                        @forelse ($recent_blogs as $recent)
                            <div class="d-flex mb-3">
                                <img src="{{ $recent->thumbnail ? asset('upload/blogs/' . $recent->thumbnail) : asset('template/assets/images/backgrounds/slider1.jpg') }}"
                                     alt="{{ $recent->headline }}"
                                     style="width:80px; height:80px; object-fit:cover; border-radius:8px; margin-right:10px;">
                                <div>
                                    <a href="{{ route('front.blog.detail', $recent->news_slug) }}"
                                       class="text-dark fw-semibold d-block" style="font-size: 14px; text-decoration: none;">{{ Str::limit($recent->headline, 40) }}</a>
                                    <p class="text-muted small mb-0">
                                        <i class="fa fa-calendar-alt"></i> {{ $recent->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">Tidak ada artikel terbaru.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- ===== Daftar Blog (Kanan) ===== --}}
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <h2 class="fw-bold mb-0">Blog Kami</h2>
                    <form method="GET" action="{{ route('front.blog') }}" class="d-flex gap-3 filter-form">
                        <div>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->category_name }}" {{ request('category') == $cat->category_name ? 'selected' : '' }}>
                                        {{ $cat->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="">Default</option>
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="row g-4">
                    @forelse ($data_blog as $blog)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm blog-card position-relative">
                                <img src="{{ $blog->thumbnail ? asset('upload/blogs/' . $blog->thumbnail) : asset('template/assets/images/backgrounds/slider1.jpg') }}"
                                     class="card-img-top" alt="{{ $blog->headline }}">
                                <div class="blog-date">
                                    {{ $blog->created_at->format('d') }} {{ $blog->created_at->format('M') }}
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-semibold mb-2" style="font-size: 16px;">
                                        <a href="{{ route('front.blog.detail', $blog->news_slug) }}"
                                           class="text-dark text-decoration-none">
                                            {{ $blog->headline }}
                                        </a>
                                    </h5>
                                    @if($blog->category)
                                        <p class="text-muted small mb-2">
                                            <i class="fa fa-tag"></i> {{ $blog->category->category_name }}
                                        </p>
                                    @endif
                                    <p class="text-muted small flex-grow-1">
                                        {!! Str::limit(strip_tags($blog->body), 100) !!}
                                    </p>
                                    <div class="mt-auto">
                                        <a href="{{ route('front.blog.detail', $blog->news_slug) }}"
                                           class="btn btn-dark w-100">Baca Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted">Tidak ada artikel ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="row">
                    <div class="col-xl-12 pagination-wrapper d-flex justify-content-center">
                        {{ $data_blog->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
