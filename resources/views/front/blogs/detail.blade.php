@extends('front.layouts.app')
@section('title', $title)

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .blog-title { font-family: 'Poppins', sans-serif; color: #000; font-weight: 700; font-size: 32px; line-height: 1.1; }
        @media (max-width: 767px) { .blog-title { font-size: 22px; } }
        .blog-detail-content img { max-width: 100%; height: auto; border-radius: 12px; margin: 1.5rem 0; }
        .blog-meta { font-size: 0.95rem; color: #666; }
        .blog-meta i { color: #ffcc00; margin-right: 6px; }
        .back-btn { color: #ffcc00; font-weight: 600; text-decoration: none; transition: color 0.3s ease; }
        .back-btn:hover { color: #e6b800; }
        .recent-post-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .list-group-item.active-category { background-color: #f8f9fa; font-weight: 600; }
        .list-group-item a { color: #555; transition: color 0.3s ease; }
        .list-group-item a:hover { color: #000; }
    </style>
@endpush

@section('content')
<section class="blog-detail-page py-5">
    <div class="container">
        <div class="row">

            {{-- Sidebar kiri: Search, Kategori, Artikel Terbaru --}}
            <div class="col-lg-3 mb-4">
                {{-- Search Bar --}}
                <form action="{{ route('front.blog') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari artikel...">
                        <button class="btn btn-dark" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                {{-- Kategori --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-semibold">Kategori</div>
                    <ul class="list-group list-group-flush">
                        @php
                            $categories = \App\Models\BlogCategory::where('status', 'active')->get();
                        @endphp
                        @foreach ($categories as $cat)
                            <li class="list-group-item {{ $blog->category && $blog->category->category_name == $cat->category_name ? 'active-category' : '' }}">
                                <a href="{{ route('front.blog') }}?category={{ urlencode($cat->category_name) }}" class="text-decoration-none {{ $blog->category && $blog->category->category_name == $cat->category_name ? 'text-dark fw-bold' : 'text-secondary' }}">
                                    {{ $cat->category_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Artikel Terbaru --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-semibold">Artikel Terbaru</div>
                    <div class="card-body p-0">
                        @php
                            $recent_blogs = \App\Models\Blog::where('status', 'active')
                                ->when(isset($blog->id), fn($q) => $q->where('id', '!=', $blog->id))
                                ->latest()
                                ->take(3)
                                ->get();
                        @endphp
                        @forelse ($recent_blogs as $recent)
                            <div class="d-flex p-3 border-bottom">
                                <img src="{{ $recent->thumbnail ? asset('upload/blogs/' . $recent->thumbnail) : asset('template/assets/images/backgrounds/slider1.jpg') }}" class="recent-post-img me-3" alt="{{ $recent->headline }}">
                                <div class="flex-grow-1">
                                    <a href="{{ route('front.blog.detail', $recent->news_slug) }}" class="text-dark fw-semibold d-block small mb-1">
                                        {{ Str::limit($recent->headline, 50) }}
                                    </a>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-calendar"></i> {{ $recent->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted p-3 small">Tidak ada artikel lain.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Konten kanan: breadcrumb, judul, meta, gambar, isi --}}
            <div class="col-lg-9">
                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/" class="text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('front.blog') }}" class="text-dark">Blog</a></li>
                        <li class="breadcrumb-item active text-truncate" style="max-width: 400px;" aria-current="page">
                            {{ Str::limit($blog->headline, 50) }}
                        </li>
                    </ol>
                </nav>

                {{-- Tombol Kembali --}}
                <a href="{{ route('front.blog') }}" class="back-btn d-inline-block mb-3">Kembali ke Daftar Artikel</a>

                {{-- Judul --}}
                <h1 class="fw-bold mb-4" style="font-size: 2.2rem; line-height: 1.2;">{{ $blog->headline }}</h1>

                {{-- Meta Info --}}
                <div class="blog-meta mb-4 d-flex flex-wrap gap-3">
                    <span><i class="bi bi-person"></i> oleh {{ $blog->author ?? 'Admin' }}</span>
                    <span><i class="bi bi-calendar3"></i> {{ $blog->created_at->format('d M Y') }}</span>
                    @if($blog->category)
                        <span><i class="bi bi-tag"></i> {{ $blog->category->category_name }}</span>
                    @endif
                </div>

                {{-- Gambar Utama --}}
                <div class="mb-4">
                    @if($blog->thumbnail)
                        <img src="{{ asset('upload/blogs/' . $blog->thumbnail) }}" class="img-fluid rounded-4 w-100 shadow-sm" alt="{{ $blog->headline }}" style="max-height: 500px; object-fit: cover;">
                    @else
                        <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" class="img-fluid rounded-4 w-100 shadow-sm" alt="{{ $blog->headline }}" style="max-height: 500px; object-fit: cover;">
                    @endif
                </div>

                {{-- Isi Artikel --}}
                <div class="blog-detail-content fs-5 text-muted lh-lg">
                    {!! $blog->body !!}
                </div>

                <hr class="my-5">

                {{-- Tombol Kembali Bawah --}}
                <a href="{{ route('front.blog') }}" class="btn btn-dark btn-lg">Kembali ke Blog</a>
            </div>
        </div>
    </div>
</section>
@endsection
