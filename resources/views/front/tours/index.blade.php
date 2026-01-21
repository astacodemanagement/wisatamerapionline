@extends('front.layouts.app')
@section('title', $title)
@section('content')

<section class="page-header" style="position: relative; padding: 120px 0; background-size: cover; background-position: center; min-height: 300px;">
    <div class="page-header__bg" style="background-image: url('{{ $profil->breadcrumb_2 ? asset('upload/profil/' . $profil->breadcrumb_2) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <h2 style="color: white; margin-bottom: 20px; font-size: 48px; font-weight: bold;">{{ $title }}</h2>
        <ul class="thm-breadcrumb list-unstyled" style="color: white; margin: 0; padding: 0;">
            <li style="display: inline; color: white;"><a href="/" style="color: white; text-decoration: none;">Beranda</a> / </li>
            <li style="display: inline; color: white;">{{ $title }}</li>
        </ul>
    </div>
</section>

<section class="tour-one tour-grid" style="padding-top: 80px; padding-bottom: 80px;">
    <div class="container">
        <div class="row">
            @foreach($data_tour as $tour)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="tour-one__single">
                        <div class="tour-one__image">
                            @if($tour->image)
                                <img src="{{ asset('upload/tours/' . $tour->image) }}" alt="{{ $tour->name }}">
                            @else
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" alt="{{ $tour->name }}">
                            @endif
                            <a href="{{ route('tour.detail', $tour->slug) }}"><i class="fa fa-heart"></i></a>
                        </div>
                        <div class="tour-one__content">
                            <h3><a href="{{ route('tour.detail', $tour->slug) }}">{{ $tour->name }}</a></h3>
                            <p><span>{{ number_format($tour->price, 0, ',', '.') }}</span> {{ $tour->price_label }}</p>
                            <style>
                                .tour-one__meta-label { display: inline-block; width: 90px; font-weight: 600; margin-right: 8px; color: #333; }
                                .tour-one__meta li { margin-bottom: 6px; }
                            </style>
                            <ul class="tour-one__meta list-unstyled">
                                <li><a href="{{ route('tour.detail', $tour->slug) }}"><span class="tour-one__meta-label">Durasi:</span><i class="far fa-clock"></i> {{ $tour->duration_minutes }} minutes</a></li>
                                <li><a href="{{ route('tour.detail', $tour->slug) }}"><span class="tour-one__meta-label">Peserta:</span><i class="far fa-user-circle"></i> {{ $tour->max_participants }}</a></li>
                                <li><a href="{{ route('tour.detail', $tour->slug) }}"><span class="tour-one__meta-label">Lokasi:</span><i class="far fa-map"></i> {{ $tour->location }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="row">
            <div class="col-12">
                {{ $data_tour->links() }}
            </div>
        </div>
    </div>
</section>

@endsection
