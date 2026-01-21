@extends('front.layouts.app')
@section('title', $title)
@section('content')

 

<section class="page-header"
        style="position: relative; padding: 120px 0; background-size: cover; background-position: center; min-height: 300px;">
        <div class="page-header__bg"
            style="background-image: url('{{ $profil->breadcrumb_6 ? asset('upload/profil/' . $profil->breadcrumb_6) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;">
        </div>
        <div class="container" style="position: relative; z-index: 2;">
            <h2 style="color: white; margin-bottom: 20px; font-size: 48px; font-weight: bold;">{{ $destination->name }}</h2>
            <ul class="thm-breadcrumb list-unstyled" style="color: white; margin: 0; padding: 0;">
                <li><a href="/">Beranda</a></li>
            <li><a href="{{ route('front.destinations') }}">Destinasi</a></li>
            <li>{{ $destination->name }}</li>
            </ul>
        </div>
    </section>

<section class="destination-details" style="padding-top: 80px; padding-bottom: 80px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="destination-details__content">
                    @if($destination->thumbnail)
                        <div class="destination-details__image mb-4">
                            <img src="{{ asset('upload/destinations/' . $destination->thumbnail) }}" alt="{{ $destination->name }}" class="img-fluid">
                        </div>
                    @endif
                    
                    <div class="destination-details__meta mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="meta-item">
                                    <i class="fa fa-eye"></i>
                                    <span>{{ $destination->views ?? 0 }} views</span>
                                </div>
                            </div>
                            @if($destination->location_details)
                                <div class="col-md-6">
                                    <div class="meta-item">
                                        <i class="fa fa-map-marker"></i>
                                        <span>{{ $destination->location_details }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($destination->short_description)
                        <div class="destination-details__summary mb-4">
                            <h3>Ringkasan</h3>
                            <p>{{ $destination->short_description }}</p>
                        </div>
                    @endif

                    @if($destination->description)
                        <div class="destination-details__description">
                            <h3>Deskripsi Lengkap</h3>
                            <div class="content">
                                {!! $destination->description !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="sidebar__single sidebar__info">
                        <h3 class="sidebar__title">Informasi Destinasi</h3>
                        <ul class="sidebar__info-list">
                            <li>
                                <span>Nama:</span>
                                <span>{{ $destination->name }}</span>
                            </li>
                            @if($destination->location_details)
                                <li>
                                    <span>Lokasi:</span>
                                    <span>{{ $destination->location_details }}</span>
                                </li>
                            @endif
                            <li>
                                <span>Views:</span>
                                <span>{{ $destination->views ?? 0 }}</span>
                            </li>
                            <li>
                                <span>Status:</span>
                                <span class="badge {{ $destination->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $destination->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar__single sidebar__contact">
                        <h3 class="sidebar__title">Hubungi Kami</h3>
                        <p>Tertarik dengan destinasi ini? Hubungi kami untuk informasi lebih lanjut.</p>
                        @if($profil && $profil->no_wa)
                            <a href="https://wa.me/{{ $profil->no_wa }}?text=Halo, saya tertarik dengan destinasi {{ $destination->name }}" 
                               class="btn btn-primary" target="_blank">
                                <i class="fab fa-whatsapp"></i> Chat WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('css')
<style>
.destination-details__meta .meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.destination-details__meta .meta-item i {
    margin-right: 8px;
    color: #f7931e;
}

.sidebar__info-list {
    list-style: none;
    padding: 0;
}

.sidebar__info-list li {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.sidebar__info-list li:last-child {
    border-bottom: none;
}

.sidebar__info-list li span:first-child {
    font-weight: 600;
}

.badge-success {
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}
</style>
@endpush