@extends('front.layouts.app')
@section('title', $title)
@section('content')

<section class="page-header" style="position: relative; padding: 120px 0; background-size: cover; background-position: center; min-height: 300px;">
    <div class="page-header__bg" style="background-image: url('{{ $profil->breadcrumb_5 ? asset('upload/profil/' . $profil->breadcrumb_5) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <h2 style="color: white; margin-bottom: 20px; font-size: 48px; font-weight: bold;">{{ $title }}</h2>
        <ul class="thm-breadcrumb list-unstyled" style="color: white; margin: 0; padding: 0;">
            <li style="display: inline; color: white;"><a href="/" style="color: white; text-decoration: none;">Beranda</a> / </li>
            <li style="display: inline; color: white;">{{ $title }}</li>
        </ul>
    </div>
</section>

<iframe
    src="{{ $profil->embed_map }}"
    class="google-map__contact" allowfullscreen style="width: 100%; height: 400px; border: 0;"></iframe>

<section class="contact-one" style="padding: 80px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="contact-left">
                    <h2 style="font-size: 32px; margin: 0 0 30px; color: #333; font-weight: 700;">Kirim Pesan</h2>
                    
                    <form action="{{ route('front.contact.store') }}" method="POST" class="contact-form" id="contactForm">
                        @csrf
                        <div class="mb-3">
                            <input class="form-control" type="text" name="form_name" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="email" name="form_email" placeholder="Alamat Email" required>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="text" name="form_phone" placeholder="Nomor Telepon">
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="text" name="form_subject" placeholder="Subjek">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="form_message" rows="5" placeholder="Tulis Pesan" required></textarea>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="contact-right" style="padding-left: 24px;">
                    <h2 style="font-size: 32px; margin: 0 0 30px; color: #333; font-weight: 700;">Kontak Kami</h2>
                    
                    <div class="contact-info-card" style="background: #f8f9fa; border-radius: 12px; padding: 20px; display: flex; gap: 15px; align-items: center; margin-bottom: 20px;">
                        <div class="contact-icon" style="width: 56px; height: 56px; border-radius: 50%; background: #ffc107; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px;">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div>
                            <div style="color: #6c757d; font-size: 14px;">Admin 1</div>
                            <div style="font-weight: 700; color: #333;">{{ $profil->no_telp ?? '+62 812 3456 7890' }}</div>
                        </div>


                        <div class="contact-icon" style="width: 56px; height: 56px; border-radius: 50%; background: #ffc107; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px; margin-left: 50px;">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div>
                            <div style="color: #6c757d; font-size: 14px;">Admin 2 </div>
                            <div style="font-weight: 700; color: #333;">{{ $profil->no_wa ?? '+62 812 3456 7890' }}</div>
                        </div>
                        
                        
                    </div>
                    
                    <div class="contact-info-card" style="background: #f8f9fa; border-radius: 12px; padding: 20px; display: flex; gap: 15px; align-items: center; margin-bottom: 20px;">
                        <div class="contact-icon" style="width: 56px; height: 56px; border-radius: 50%; background: #ffc107; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px;">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div>
                            <div style="color: #6c757d; font-size: 14px;">Email</div>
                            <div style="font-weight: 700; color: #333;">{{ $profil->email ?? 'jeep@gmail.com' }}</div>
                        </div>
                    </div>
                    
                    <div class="contact-info-card" style="background: #f8f9fa; border-radius: 12px; padding: 20px; display: flex; gap: 15px; align-items: center; margin-bottom: 20px;">
                        <div class="contact-icon" style="width: 56px; height: 56px; border-radius: 50%; background: #ffc107; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px;">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <div>
                            <div style="color: #6c757d; font-size: 14px;">Alamat Kantor Pemasaran</div>
                            <div style="font-weight: 700; color: #333;">{{ $profil->alamat ?? 'Yogyakarta, Indonesia' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        .contact-cards-section .text-center h3 {
            font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
            font-weight: 600;
            margin: 0;
            color: #f9c21a;
        }

        .contact-cards {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: stretch;
            margin-bottom: 32px
        }

        .contact-card {
            border-radius: 14px;
            overflow: hidden;
            width: calc(25% - 18px);
            min-width: 250px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06)
        }

        .card-media {
            height: 350px;
            background-size: cover;
            background-position: center;
            display: block;
        }

        .contact-right .contact-info-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            gap: 14px;
            align-items: center;
            margin-bottom: 16px;
        }

        .contact-right .contact-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #f9c21a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f2f23;
            font-size: 20px;
            flex: 0 0 56px
        }

        .contact-right .contact-meta {
            color: #6b7280
        }


        @media (max-width:992px) {
            .contact-card {
                width: calc(50% - 12px)
            }
        }

        .contact-one-card {
            background: #fff;
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
            margin-bottom: 32px;
        }

        @media (max-width:576px) {
            .contact-one-card {
                padding: 18px
            }
        }

        @media (max-width:576px) {
            .contact-card {
                width: 100%
            }
        }
    </style>
@endpush

@section('content')
    <section class="page-header"
        style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
        <div class="container">
            <h2>Contact</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><span>Contact</span></li>
            </ul>
        </div>
    </section>

    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4562.753041141002!2d-118.80123790098536!3d34.152323469614075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80e82469c2162619%3A0xba03efb7998eef6d!2sCostco+Wholesale!5e0!3m2!1sbn!2sbd!4v1562518641290!5m2!1sbn!2sbd"
        class="google-map__contact" allowfullscreen></iframe>

    <section class="contact-cards-section pt-5">
        <div class="container">
            <div class="text-center mb-5">
                <h3>Senang Menjawab Semua Pertanyaan Anda</h3>
            </div>

            <div class="contact-cards">
                <div class="contact-card">
                    <div class="card-media"
                        style="background-image: url({{ asset('template/assets/images/tour/tour1.jpg') }});"></div>
                </div>
                <div class="contact-card">
                    <div class="card-media"
                        style="background-image: url({{ asset('template/assets/images/tour/tour1.jpg') }});"></div>
                </div>
                <div class="contact-card">
                    <div class="card-media"
                        style="background-image: url({{ asset('template/assets/images/tour/tour1.jpg') }});"></div>
                </div>
                <div class="contact-card">
                    <div class="card-media"
                        style="background-image: url({{ asset('template/assets/images/tour/tour1.jpg') }});"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-one">
        <div class="container">
            <div class="contact-one-card">
                <div class="row align-items-start">
                    <div class="col-lg-6 mb-4">
                        <div class="contact-left">
                            <p style="color:#0b6b3a;font-weight:600;margin-bottom:6px">Tulis Pesan</p>
                            <h2
                                style="font-family:'Poppins', sans-serif;font-size:48px;margin:0 0 18px;color:#0f2f23;font-weight:700">
                                Kirim Pesan</h2>

                            <form action="inc/sendemail.php" class="contact-form" autocomplete="off">
                                <div class="mb-3"><input class="form-control" type="text" name="name"
                                        placeholder="Nama Lengkap"></div>
                                <div class="mb-3"><input class="form-control" type="email" name="email"
                                        placeholder="Alamat Email"></div>
                                <div class="mb-3"><input class="form-control" type="text" name="phone"
                                        placeholder="Nomor Telepon"></div>
                                <div class="mb-3"><input class="form-control" type="text" name="subject"
                                        placeholder="Subjek"></div>
                                <div class="mb-3">
                                    <textarea class="form-control" name="message" rows="5" placeholder="Tulis Pesan"></textarea>
                                </div>
                                <div><button type="submit" class="btn btn-primary">Kirim Pesan</button></div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="contact-right" style="padding-left:24px">
                            <p style="color:#0b6b3a;font-weight:600;margin-bottom:6px">Hubungi Kami</p>
                            <h2
                                style="font-family:'Poppins', sans-serif;font-size:40px;margin:0 0 16px;color:#0f2f23;font-weight:700">
                                Kontak dengan Tim</h2>
                            <p style="color:#6b7280;margin-bottom:22px">Hubungi kami untuk informasi lebih lanjut atau
                                pertanyaan terkait layanan kami.</p>

                            <div class="contact-info-list">
                                <div class="contact-info-card">
                                    <div class="contact-icon"><i class="fa fa-phone"></i></div>
                                    <div>
                                        <div class="contact-meta" style="font-size:0.9rem">Ada pertanyaan?</div>
                                        <div style="font-weight:700;color:#0f2f23">WA : +6283849898679</div>
                                    </div>
                                </div>

                                <div class="contact-info-card">
                                    <div class="contact-icon"><i class="fa fa-envelope"></i></div>
                                    <div>
                                        <div class="contact-meta" style="font-size:0.9rem">Kirim email</div>
                                        <div style="font-weight:700;color:#0f2f23">distributor5758@gmail.com</div>
                                    </div>
                                </div>

                                <div class="contact-info-card">
                                    <div class="contact-icon"><i class="fa fa-map-marker"></i></div>
                                    <div>
                                        <div class="contact-meta" style="font-size:0.9rem">Kunjungi lokasi kami</div>
                                        <div style="font-weight:700;color:#0f2f23">Perum Putri Juanda Jl. Putri Ayu B13 no
                                            4-5 Pepe Sedati</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
