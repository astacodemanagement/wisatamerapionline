@extends('front.layouts.app')
@section('title', $title)
@section('content')

<section class="page-header" style="position: relative; padding: 120px 0; background-size: cover; background-position: center; min-height: 300px;">
    <div class="page-header__bg" style="background-image: url('{{ $profil->breadcrumb_3 ? asset('upload/profil/' . $profil->breadcrumb_3) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <h2 style="color: white; margin-bottom: 20px; font-size: 48px; font-weight: bold;">{{ $title }}</h2>
        <ul class="thm-breadcrumb list-unstyled" style="color: white; margin: 0; padding: 0;">
            <li style="display: inline; color: white;"><a href="/" style="color: white; text-decoration: none;">Beranda</a> / </li>
            <li style="display: inline; color: white;">{{ $title }}</li>
        </ul>
    </div>
</section>

@foreach($data_gallery_category as $category)
<section class="gallery-category mb-5" style="padding: 40px 0;">
    <div class="container">
        <h3 style="margin-bottom: 30px; color: #333; font-weight: 600;">{{ $category->name }}</h3>
        <div class="row">
            @foreach($category->galleries as $gallery)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="gallery-item" style="position: relative; overflow: hidden; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <img src="{{ asset('upload/galleries/' . $gallery->image) }}" alt="{{ $gallery->name }}" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="gallery-caption" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 15px; text-align: center;">
                            <h5 style="margin: 0; font-size: 16px;">{{ $gallery->name }}</h5>
                            @if($gallery->description)
                                <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.9;">{{ Str::limit($gallery->description, 80) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endforeach

@endsection
