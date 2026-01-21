@extends('front.layouts.app')
@section('title', $title)

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        /* Place nav arrows inside the slider image area */
        .slider-inside {
            position: relative;
        }

        .slider-inside .item img {
            display: block;
            width: 100%;
            height: auto;
        }

        .slider-inside .owl-nav {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            pointer-events: none;
        }

        .slider-inside .owl-nav button {
            pointer-events: auto;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.45) !important;
            color: #fff !important;
            width: 42px;
            height: 42px;
            border-radius: 50% !important;
            font-size: 18px !important;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
            border: none;
        }

        .slider-inside .owl-prev {
            left: 12px;
        }

        .slider-inside .owl-next {
            right: 12px;
        }

        /* Gallery specific tweaks */
        #galleryMain .item img {
            height: 360px;
            object-fit: cover;
        }

        .gallery-slider .item img {
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Category title underline (yellow) */
        .gallery-category>h5,
        .gallery-category>p {
            position: relative;
            display: inline-block;
            padding-bottom: 6px;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .gallery-category>h5::after,
        .gallery-category>p::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 56px;
            height: 4px;
            background: #ffd300;
            /* yellow */
            border-radius: 2px;
        }

        /* Reduce spacing between previous section and the table */
        .gallery-section {
            padding-top: 1.25rem !important;
        }

        .gallery-section .mb-4 {
            margin-top: 0.5rem;
        }
    </style>
@endpush

@section('content')

    <section class="page-header"
        style="position: relative; padding: 120px 0; background-size: cover; background-position: center; min-height: 300px;">
        <div class="page-header__bg"
            style="background-image: url('{{ $profil->breadcrumb_1 ? asset('upload/profil/' . $profil->breadcrumb_1) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;">
        </div>
        <div class="container" style="position: relative; z-index: 2;">
            <h2 style="color: white; margin-bottom: 20px; font-size: 48px; font-weight: bold;">{{ $title }}</h2>
            <ul class="thm-breadcrumb list-unstyled" style="color: white; margin: 0; padding: 0;">
                <li style="display: inline; color: white;"><a href="/"
                        style="color: white; text-decoration: none;">Beranda</a> / </li>
                <li style="display: inline; color: white;">{{ $title }}</li>
            </ul>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 wow fadeInLeft">
                    <div class="owl-carousel owl-theme" id="ctaSlider">
                        @foreach ($other_sliders as $p)
                            <div class="item"><img src="{{ asset('upload/other_sliders/' . $p->image) }}"
                                    alt="{{ $p->name }}" class="img-fluid"></div>
                        @endforeach

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="cta-two__content" style="margin-top: -8px; text-align: justify;">
                        {!! $profil->deskripsi_1 !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery section -->
    <section class="gallery-section" style="padding-bottom: 30px;">
        <div class="container">

            <!-- Agent DataTable -->
            <div class="mb-4">
                <div class="section-title mb-3">
                    <h1>Data Agent</h1>
                </div>
                <div class="table-responsive">
                    <table id="agentTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Kota</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_agent as $agent)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $agent->name }}</td>
                                    <td>{{ $agent->phone ?? 'Tidak ada' }}</td>
                                    <td>{{ $agent->city ?? 'Tidak ada' }}</td>
                                    <td>{{ $agent->address ?? 'Tidak ada' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-title mb-4">
                <h1>Galeri</h1>
            </div>
            @foreach ($data_gallery_category as $category)
                @if ($category->galleries->count() > 0)
                    <div class="gallery-category mt-3">
                        <p>{{ $category->name }}</p>
                        <div class="gallery-slider owl-carousel owl-theme slider-inside"
                            id="gallery-{{ Str::slug($category->name) }}">
                            @foreach ($category->galleries as $gallery)
                                <div class="item" data-name="{{ $gallery->name }}">
                                    @if ($gallery->image)
                                        <img src="{{ asset('upload/galleries/' . $gallery->image) }}"
                                            alt="{{ $gallery->name }}">
                                    @else
                                        <img src="https://via.placeholder.com/600x400?text=No+Image" alt="No Image">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        (function() {
            function initSliders() {
                if (!window.jQuery) return;

                // CTA slider
                if ($('#ctaSlider').length && !$('#ctaSlider').hasClass('owl-loaded')) {
                    $('#ctaSlider').owlCarousel({
                        items: 1,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: false,
                        nav: true,
                        dots: true,
                        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
                    });
                }

                // Main gallery
                if ($('#galleryMain').length && !$('#galleryMain').hasClass('owl-loaded')) {
                    $('#galleryMain').owlCarousel({
                        items: 1,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3500,
                        nav: true,
                        dots: true,
                        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
                    });
                }

                // Per-category galleries
                $('.gallery-slider').each(function() {
                    var $s = $(this);
                    if (!$s.hasClass('owl-loaded')) {
                        $s.owlCarousel({
                            loop: $s.find('.item').length > 1,
                            autoplay: false,
                            nav: true,
                            dots: false,
                            margin: 10,
                            navText: ['<i class="fa fa-chevron-left"></i>',
                                '<i class="fa fa-chevron-right"></i>'
                            ],
                            responsive: {
                                0: {
                                    items: 1
                                },
                                768: {
                                    items: 2
                                },
                                992: {
                                    items: 3
                                }
                            }
                        });
                    }
                });

                // Initialize DataTable for agentTable
                if (typeof $.fn.DataTable !== 'undefined' && $('#agentTable').length && !$.fn.DataTable.isDataTable(
                        '#agentTable')) {
                    $('#agentTable').DataTable({
                        pageLength: 5,
                        lengthMenu: [
                            [5, 10, 25, 50],
                            [5, 10, 25, 50]
                        ]
                    });
                }
            }

            // Wait until theme scripts (including jQuery/owl) are loaded
            if (document.readyState === 'complete') {
                initSliders();
            } else {
                window.addEventListener('load', initSliders);
            }
        })();
    </script>
@endpush
