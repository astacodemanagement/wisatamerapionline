@extends('front.layouts.app')
@section('title', $title)
@section('content')

<section class="page-header"
        style="position: relative; padding: 120px 0; background-size: cover; background-position: center; min-height: 300px;">
        <div class="page-header__bg"
            style="background-image: url('{{ $profil->breadcrumb_6 ? asset('upload/profil/' . $profil->breadcrumb_6) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;">
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

<section class="destinations-page" style="padding-top: 80px; padding-bottom: 80px;">
    <div class="container">
        <div class="row">
            @foreach($data_destination as $destination)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="destinations-two__single">
                        @if($destination->thumbnail)
                            <img src="{{ asset('upload/destinations/' . $destination->thumbnail) }}" alt="{{ $destination->name }}">
                        @else
                            <img src="{{ asset('template/front/assets/images/backgrounds/slider1.jpg') }}" alt="{{ $destination->name }}">
                        @endif
                        <h3><a href="{{ route('destination.detail', $destination->slug) }}">{{ $destination->name }}</a></h3>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="row">
            <div class="col-12">
                {{ $data_destination->links() }}
            </div>
        </div>
    </div>
</section>

@endsection