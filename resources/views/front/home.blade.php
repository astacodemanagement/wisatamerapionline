@extends('front.layouts.app')

@section('content')
    <div>
        {{-- hero slider --}}
        <section class="banner-one">
            <style>
                .banner-one {
                    padding: 0
                }

                .banner-one__carousel,
                .banner-one__carousel .owl-stage-outer,
                .banner-one__carousel .owl-stage,
                .banner-one__carousel .owl-item,
                .banner-one__single {
                    height: 100vh
                }

                .banner-one__single {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    position: relative
                }

                .banner-one__single .container {
                    z-index: 2
                }

                .banner-one__single:before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.25);
                    z-index: 1
                }

                .banner-one__single h2,
                .banner-one__single p {
                    color: #fff;
                    position: relative;
                    z-index: 2
                }

                /* Nav arrows: positioned at left/right edges inside image */
                .banner-one__carousel .owl-nav {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 100%;
                    z-index: 4;
                    pointer-events: none
                }

                .banner-one__carousel .owl-nav button {
                    pointer-events: auto;
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    background: rgba(0, 0, 0, 0.45);
                    border: none;
                    color: #fff;
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center
                }

                .banner-one__carousel .owl-nav .owl-prev {
                    left: 18px
                }

                .banner-one__carousel .owl-nav .owl-next {
                    right: 18px
                }

                /* Dots inside image at bottom center */
                .banner-one__carousel .owl-dots {
                    position: absolute;
                    bottom: 28px;
                    left: 50%;
                    transform: translateX(-50%);
                    z-index: 4
                }

                .banner-one__carousel .owl-dot {
                    margin: 0 6px
                }

                .banner-one__carousel .owl-dot span {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.45);
                    display: inline-block
                }

                .banner-one__carousel .owl-dot.active span {
                    background: #fff
                }
            </style>
            <div class="banner-one__carousel owl-theme owl-carousel">
                @forelse($data_slider as $s)
                    @php
                        $img = $s->image ? asset('upload/sliders/' . $s->image) : asset('template/front/assets/images/backgrounds/slider1.jpg');
                        $title = $s->name ?? '';
                        $desc = $s->description ?? '';
                        $link = $s->link ?? '';
                    @endphp
                    <div class="banner-one__single" style="background-image: url({{ $img }});">
                        <div class="container">
                            @if(!empty($link))
                                <h2><a href="{{ $link }}" style="color:#fff; text-decoration:none;">{{ $title }}</a></h2>
                            @else
                                <h2>{{ $title }}</h2>
                            @endif
                            @if(!empty($desc))
                                <p>{{ $desc }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="banner-one__single" style="background-image: url({{ asset('template/front/assets/images/backgrounds/slider1.jpg') }});">
                        <div class="container">
                            <h2>Selamat Datang</h2>
                            <p>Temukan pengalaman wisata terbaik</p>
                        </div>
                    </div>
                @endforelse
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof jQuery !== 'undefined' && jQuery().owlCarousel) {
                        jQuery('.banner-one__carousel').owlCarousel({
                            items: 1,
                            loop: true,
                            nav: true,
                            dots: true,
                            autoplay: true,
                            autoplayTimeout: 4000,
                            smartSpeed: 700,
                            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>']
                        });
                    }
                });
            </script>
        </section>

        {{-- wimo awward --}}
        <section class="features-one__title">
            <div class="container">
                <div class="block-title text-center">
                    {{-- <p>Call our agents to book!</p> --}}
                    <h3>Fasilitas Kami</h3>
                </div>
            </div>
        </section>

        <section class="features-one__content">
            <div class="container">
                <div class="row justify-content-center"> <!-- Tambahkan ini -->
                    @foreach ($data_service as $index => $service)
                        <div class="col-lg-2 col-md-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="{{ $index * 100 }}ms">
                            <div class="features-one__single">
                                @if ($service->image)
                                    <img src="{{ asset('upload/services/' . $service->image) }}" alt="{{ $service->name }}"
                                        style="width: 100%; height: 100%; object-fit: contain; padding: 10px;">
                                @else
                                    <i class="fa fa-star"></i>
                                @endif
                                <h3>{{ $service->name }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- popular tour --}}
        <section class="tour-one">
            <div class="container">
                <div class="block-title text-center">
                    <p>Paket Jeep</p>
                    <h3>Paket Jeep Paling Populer</h3>
                </div>
                <div class="row">
                    @foreach ($data_tour as $tour)
                        <div class="col-xl-4 col-lg-6">
                            <div class="tour-one__single">
                                <div class="tour-one__image">
                                    @if ($tour->image)
                                        <img src="{{ asset('upload/tours/' . $tour->image) }}" alt="{{ $tour->name }}">
                                    @else
                                        <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}"
                                            alt="{{ $tour->name }}">
                                    @endif
                                    <a href="{{ route('tour.detail', $tour->slug) }}"><i class="fa fa-heart"></i></a>
                                </div>
                                <div class="tour-one__content">
                                    <h3><a href="{{ route('tour.detail', $tour->slug) }}">{{ $tour->name }}</a></h3>
                                    <p><span>{{ number_format($tour->price, 0, ',', '.') }}</span>
                                        {{ $tour->price_label }}</p>
                                    <ul class="tour-one__meta list-unstyled">
                                        <li>
                                            <div class="tour-one__meta-label">Durasi</div>
                                            <a href="{{ route('tour.detail', $tour->slug) }}"><i class="far fa-clock"></i>
                                                {{ $tour->duration_minutes }} menit</a>
                                        </li>
                                        <li>
                                            <div class="tour-one__meta-label">Peserta</div>
                                            <a href="{{ route('tour.detail', $tour->slug) }}"><i class="far fa-user-circle"></i>
                                                {{ $tour->max_participants }}</a>
                                        </li>
                                        <li>
                                            <div class="tour-one__meta-label">Lokasi</div>
                                            <a href="{{ route('tour.detail', $tour->slug) }}"><i class="far fa-map"></i>
                                                {{ $tour->location }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- yt video --}}
        <section class="video-one"
            style="background-image: url({{ $profil->banner ? asset('upload/profil/' . $profil->banner) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }});">
            <div class="container text-center">
                <a href="{{ $profil->embed_youtube }}" class="video-one__btn video-popup"><i
                        class="fa fa-play"></i></a><!-- /.video-one__btn -->
                <p>WIMO</p>
                <h3>PLATFORM BOOKING ONLINE TERDEPAN</h3>
            </div>
        </section>

        {{-- trusted & profesional --}}
        {{-- <section class="cta-three">
            <div class="container">
                <div class="row no-gutters">
                    <div class="col-lg-6">
                        <div class="cta-three__content">
                            <div class="cta-three__content-inner">
                                <div class="block-title text-left">
                                    <p>Trusted & Professional</p>
                                    <h3>We’re Trusted by <br> More Than <span class="counter">84,106</span> <br> Clients
                                    </h3>
                                </div>
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" data-wow-duration="1500ms"
                                    class="wow fadeInLeft img-fluid" alt="">
                                <div class="cta-three__box">
                                    <p>We only chosse the best <br> one for you</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-lg-right text-md-center">
                        <div class="cta-three__images wow fadeInRight" data-wow-duration="1500ms">
                            <img src="{{ asset('template/front/assets/images/tour/tour3.png') }}" class="img-fluid"
                                alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section> --}}

        {{-- top destinasi (slider) --}}
        <section class="destinations-two">
            <div class="container">
                <div class="block-title text-center">
                    {{-- <p>Lihat pilihan unggulan</p> --}}
                    <h3>Destinasi Terbaik</h3>
                </div>
                <div class="row">
                    <div class="destinations-two__carousel owl-theme owl-carousel thm__owl-carousel"
                        data-options='{"nav": true, "autoplay": true, "autoplayTimeout": 4500, "smartSpeed": 700, "dots": false, "margin": 30, "loop": true, "responsive": {"0": {"items": 1}, "576": {"items": 2}, "992": {"items": 3}}}'>
                        @foreach ($data_destination as $destination)
                            <div class="item">
                                <div class="destinations-two__single wow fadeInLeft" data-wow-duration="1500ms"
                                    data-wow-delay="{{ $loop->index * 100 }}ms">
                                    @if ($destination->thumbnail)
                                        <img src="{{ asset('upload/destinations/' . $destination->thumbnail) }}"
                                            alt="{{ $destination->name }}">
                                    @else
                                        <img src="{{ asset('template/front/assets/images/backgrounds/slider1.jpg') }}"
                                            alt="{{ $destination->name }}">
                                    @endif
                                    <h3><a href="{{ route('destination.detail', $destination->slug) }}">{{ $destination->name }}</a>
                                    </h3>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof jQuery !== 'undefined' && jQuery().owlCarousel) {
                    jQuery('.destinations-two__carousel').each(function() {
                        var $el = jQuery(this);
                        var options = $el.data('options') || {};
                        $el.owlCarousel(options);
                    });
                }
            });
        </script>

        {{-- mitra/brand --}}
        <div class="brand-one">
            <div class="container">
                <div class="brand-one__carousel owl-theme owl-carousel thm__owl-carousel"
                    data-options='{"nav": false, "autoplay": true, "autoplayTimeout": 5000, "smartSpeed": 700, "dots": false, "margin": 115, "responsive": { "0": { "items": 2, "margin": 20 }, "480": { "items": 2, "margin": 20 }, "767": { "items": 3, "margin": 20 }, "991": { "items": 4, "margin": 40 }, "1199": { "items": 5 } }}'>
                    @foreach ($data_client as $client)
                        <div class="item">
                            <img src="{{ asset('upload/clients/' . $client->image) }}" alt="{{ $client->name }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- testimoni --}}
        <section class="testimonials-one">
            <div class="container">
                <div class="block-title text-center">
                    <p>Lihat ulasan kami</p>
                    <h3>Ulasan Pengunjung Wisata</h3>
                </div>
                <div class="testimonials-one__carousel thm__owl-carousel light-dots owl-carousel owl-theme"
                    data-options='{"nav": false, "autoplay": true, "autoplayTimeout": 5000, "smartSpeed": 700, "dots": true, "margin": 30, "loop": true, "responsive": { "0": { "items": 1, "nav": true, "navText": ["Prev", "Next"], "dots": false }, "767": { "items": 1, "nav": true, "navText": ["Prev", "Next"], "dots": false }, "991": { "items": 2 }, "1199": { "items": 2 }, "1200": { "items": 3 } }}'>
                    @foreach ($data_testimonial as $testimonial)
                        <div class="item">
                            <div class="testimonials-one__single">
                                <div class="testimonials-one__content">
                                    <div class="testimonials-one__stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star{{ $i <= ($testimonial->rating ?? 5) ? '' : '-o' }}"></i>
                                        @endfor
                                    </div>
                                    <p>{{ $testimonial->message ?? $testimonial->description }}</p>
                                </div>
                                <div class="testimonials-one__info">
                                    @if ($testimonial->image)
                                        <img src="{{ asset('upload/testimonies/' . $testimonial->image) }}"
                                            alt="{{ $testimonial->name }}">
                                    @else
                                        <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}"
                                            alt="{{ $testimonial->name }}">
                                    @endif
                                    <h3>{{ $testimonial->name }}</h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- data count --}}
        <section class="funfact-one">
            <div class="container">
                <div class="row justify-content-between">
                    @foreach ($data_count as $index => $count)
                        <div
                            class="col-xl-2 col-md-6 {{ $index == 1 || $index == 2 ? 'justify-content-xl-center' : ($index == 3 ? 'justify-content-xl-end text-xl-right' : '') }}">
                            <div class="funfact-one__single">
                                <h3><span
                                        class="counter">{{ $count->amount }}</span>{{ $index == 1 || $index == 3 ? 'k' : '+' }}
                                </h3>
                                <p>{{ $count->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- news --}}
        {{-- <section class="blog-one">
            <div class="container">
                <div class="block-title text-center">
                    <p>Check out Our</p>
                    <h3>Latest News & Articles</h3>
                </div>
                <div class="row">
                    @foreach ($data_blog->take(3) as $index => $blog)
                        <div class="col-lg-4 wow fadeInUp" data-wow-duration="1500ms"
                            data-wow-delay="{{ $index * 100 }}ms">
                            <div class="blog-one__single">
                                <div class="blog-one__image">
                                    @if ($blog->thumbnail)
                                        <img src="{{ asset('upload/blogs/' . $blog->thumbnail) }}"
                                            alt="{{ $blog->headline }}">
                                    @else
                                        <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}"
                                            alt="{{ $blog->headline }}">
                                    @endif
                                    <a href="{{ route('front.blog.detail', $blog->news_slug) }}"><i
                                            class="fa fa-long-arrow-alt-right"></i></a>
                                </div>
                                <div class="blog-one__content">
                                    <ul class="list-unstyled blog-one__meta">
                                        <li><a href="{{ route('front.blog.detail', $blog->news_slug) }}"><i
                                                    class="far fa-user-circle"></i> {{ $blog->author }}</a></li>
                                    </ul>
                                    <h3><a
                                            href="{{ route('front.blog.detail', $blog->news_slug) }}">{{ Str::limit($blog->headline, 50) }}</a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section> --}}
    </div>
@endsection
