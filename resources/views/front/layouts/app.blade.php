<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} </title>
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{ asset('/upload/profil/' . ($profil->favicon ?: 'https://static1.squarespace.com/static/524883b7e4b03fcb7c64e24c/524bba63e4b0bf732ffc8bce/646fb10bc178c30b7c6a31f2/1712669811602/Squarespace+Favicon.jpg?format=1500w')) }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('/upload/profil/' . ($profil->favicon ?: 'https://static1.squarespace.com/static/524883b7e4b03fcb7c64e24c/524bba63e4b0bf732ffc8bce/646fb10bc178c30b7c6a31f2/1712669811602/Squarespace+Favicon.jpg?format=1500w')) }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('/upload/profil/' . ($profil->favicon ?: 'https://static1.squarespace.com/static/524883b7e4b03fcb7c64e24c/524bba63e4b0bf732ffc8bce/646fb10bc178c30b7c6a31f2/1712669811602/Squarespace+Favicon.jpg?format=1500w')) }}">
    <link rel="manifest" href="assets/images/favicons/site.webmanifest">


    <link
        href="https://fonts.googleapis.com/css?family=Barlow+Condensed:200,300,400,400i,500,600,700,800,900%7CSatisfy&display=swap"
        rel="stylesheet">


    <link rel="stylesheet" href="{{ asset('template/front/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/tripo-icons.css ') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/jquery.mCustomScrollbar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/vegas.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/nouislider.pips.css') }}">

    <link rel="stylesheet" href="{{ asset('template/front/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/front/assets/css/responsive.css') }}">
    <style>
        :root {
            --heading-font: var(--thm-font);
        }
    </style>
    @stack('styles')
    <style>
        /* Google Translate Styles (adopted from stasolar) */
        .google-translate-desktop {
            display: inline-block !important;
        }

        .google-translate-mobile {
            display: none !important;
        }

        @media screen and (max-width: 991px) {
            .google-translate-desktop {
                display: none !important;
            }

            .google-translate-mobile {
                display: block !important;
                margin: 15px;
                text-align: center;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 5px;
            }
        }

        .goog-te-gadget {
            font-family: inherit !important;
        }

        .goog-te-gadget-simple {
            background-color: transparent !important;
            border: 1px solid #ddd !important;
            padding: 5px 10px !important;
            border-radius: 5px !important;
            font-size: 14px !important;
        }

        .goog-te-gadget-simple:hover {
            background-color: #f8f9fa !important;
        }

        .goog-te-gadget img,
        .goog-te-gadget-icon {
            display: none !important;
        }

        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        .custom-translate-dropdown select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            background-color: white;
            font-family: inherit;
            cursor: pointer;
            width: auto;
            min-width: 140px;
        }

        .custom-translate-dropdown select:focus {
            outline: none;
            border-color: #25D366;
        }

        @media screen and (max-width: 991px) {
            .custom-translate-dropdown {
                display: block !important;
                text-align: center;
                margin: 15px 0;
                padding: 10px;
                border-radius: 5px;
            }

            .custom-translate-dropdown select {
                width: 100%;
                font-size: 14px;
                padding: 8px 12px;
                min-width: auto;
            }
        }
    </style>
    <style>
        .whatsapp-button {
            position: fixed;
            bottom: 120px;
            right: 18px;
            z-index: 1000;
            background-color: #25D366;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .whatsapp-button:hover {
            transform: scale(1.1);
            background-color: #20b354;
        }

        .whatsapp-button img {
            width: 30px;
            height: 30px;
        }

        .whatsapp-button span {
            display: none;
            position: absolute;
            right: 70px;
            background-color: #fff;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            white-space: nowrap;
            font-size: 14px;
        }

        .whatsapp-button:hover span {
            display: block;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .whatsapp-button:not(:hover) {
            animation: pulse 2s infinite;
        }

        @media screen and (max-width: 768px) {
            .whatsapp-button {
                width: 50px;
                height: 50px;
                bottom: 100px;
                right: 15px;
            }

            .whatsapp-button img {
                width: 25px;
                height: 25px;
            }

            .whatsapp-button span {
                font-size: 12px;
                padding: 4px 8px;
                right: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="preloader">
        <img src="{{ asset('/upload/profil/' . ($profil->favicon ?: 'https://static1.squarespace.com/static/524883b7e4b03fcb7c64e24c/524bba63e4b0bf732ffc8bce/646fb10bc178c30b7c6a31f2/1712669811602/Squarespace+Favicon.jpg?format=1500w')) }}"
            class="preloader__image" alt="">
    </div>

    <div class="page-wrapper">
        <div class="site-header__header-one-wrap">
            <div class="topbar-one">
                <div class="container-fluid">
                    <div class="topbar-one__left">
                        <a href="mailto:{{ $profil->email }}">{{ $profil->email }}</a>
                        <a href="tel:081234567890">{{ $profil->no_telp }}</a>
                        <a href="#">{{ $profil->nama_profil }}</a>
                    </div>
                    <div class="topbar-one__right">
                        <div class="topbar-one__social">

                            <a href="{{ $profil->youtube }}" target="_blank" rel="noopener"><i
                                    class="fab fa-youtube"></i></a>
                            <a href="{{ $profil->tiktok }}" target="_blank" rel="noopener" aria-label="TikTok">
                                <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/tiktok.svg" alt="TikTok"
                                    style="width:18px;height:18px;" />
                            </a>
                            <a href="{{ $profil->instagram }}" target="_blank" rel="noopener"><i
                                    class="fab fa-instagram"></i></a>
                        </div>
                        <a href="/contact" class="topbar-one__guide-btn">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
        <header class="main-nav__header-one ">
            <nav class="header-navigation stricky">
                <div class="container">

                    <div class="main-nav__logo-box">
                        <a href="/" class="main-nav__logo">
                            <img src="{{ asset('/upload/profil/' . ($profil->logo ?: 'https://static1.squarespace.com/static/524883b7e4b03fcb7c64e24c/524bba63e4b0bf732ffc8bce/646fb10bc178c30b7c6a31f2/1712669811602/Squarespace+Favicon.jpg?format=1500w')) }}"
                                class="main-logo" width="150" alt="Awesome Image" />
                        </a>
                        <a href="#" class="side-menu__toggler"><i class="fa fa-bars"></i>
                        </a>
                    </div>

                    <div class="main-nav__main-navigation">
                        <ul class=" main-nav__navigation-box">
                            <li class="dropdown {{ request()->is('/') ? 'current' : '' }}">
                                <a href="/">Beranda</a>
                            </li>
                            <li class="dropdown {{ request()->is('about') ? 'current' : '' }}">
                                <a href="/about">Tentang Wimo</a>
                            </li>
                            <li class="dropdown {{ request()->is('tour') ? 'current' : '' }}">
                                <a href="/tour">Paket Jeep</a>
                            </li>
                            <li class="dropdown {{ request()->is('gallery') ? 'current' : '' }}">
                                <a href="/gallery">Galeri</a>
                            </li>
                            <li class="dropdown {{ request()->is('blog*') ? 'current' : '' }}">
                                <a href="{{ route('front.blog') }}">Blog</a>
                            </li>
                            <li class="{{ request()->is('contact') ? 'current' : '' }}">
                                <a href="{{ route('front.contact') }}">Kontak</a>
                            </li>

                            {{-- <li class="dropdown">
                                <a href="/destinasi">Destinations</a>
                            </li> --}}
                            {{-- <li class="dropdown">
                                <a href="#">Pages</a>
                                <ul>
                                    <li><a href="/aboutus">About Us</a></li>
                                    <li><a href="/guide">Tour Guides</a></li>
                                    <li><a href="/galeri">Gallery</a></li>
                                    <li><a href="/faq">FAQ's</a></li>

                                </ul>
                            </li> --}}

                            {{-- <li class="dropdown">
                                <a href="/news">News</a>
                            </li> --}}
                        </ul>
                    </div>

                    <div class="main-nav__right">
                        <div class="custom-translate-dropdown google-translate-desktop">
                            <select id="language-select-desktop" onchange="changeLanguage(this.value)">
                                <option value="id">🇮🇩 Indonesia</option>
                                <option value="en">🇬🇧 English</option>
                                <option value="es">🇪🇸 Español</option>
                                <option value="fr">🇫🇷 Français</option>
                                <option value="ja">🇯🇵 日本語</option>
                                <option value="zh-CN">🇨🇳 中文</option>
                                <option value="ar">🇸🇦 العربية</option>
                                <option value="ru">🇷🇺 Русский</option>
                                <option value="ms">🇲🇾 Bahasa Melayu</option>
                                <option value="hi">🇮🇳 हिन्दी</option>
                            </select>
                        </div>
                    </div><!-- /.main-nav__right -->
                </div>

            </nav>
        </header>
    </div>

    <div id="google_translate_element" style="display:none;"></div>

    @yield('content')

    <footer class="site-footer">
        <div class="site-footer__bg"
            style="background-image: url({{ $profil->banner ? asset('upload/profil/' . $profil->banner) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }});">
        </div>

        <div class="container">
            <div class="row">
                <div class="footer-widget__column footer-widget__about">
                    <a href="index.html" class="footer-widget__logo"><img
                            src="{{ asset('/upload/profil/' . ($profil->logo ?: 'https://static1.squarespace.com/static/524883b7e4b03fcb7c64e24c/524bba63e4b0bf732ffc8bce/646fb10bc178c30b7c6a31f2/1712669811602/Squarespace+Favicon.jpg?format=1500w')) }}"
                            width="123" alt=""></a>
                    <p>{!! $profil->deskripsi_2 !!}</p>

                </div>
                <div class="footer-widget__column footer-widget__links">
                    <h3 class="footer-widget__title">Perusahaan</h3>
                    <ul class="footer-widget__links-list list-unstyled">
                        <li><a href="/about">Tentang Kami</a></li>
                        <li><a href="/tour">Paket Jeep</a></li>
                        <li><a href="/destination">Destinasi</a></li>

                    </ul>
                </div>
                <div class="footer-widget__column footer-widget__links">
                    <h3 class="footer-widget__title">Tautan</h3>
                    <ul class="footer-widget__links-list list-unstyled">
                        {{-- <li><a href="/destinasi">Destinations</a></li> --}}
                        <li><a href="/gallery">Galeri</a></li>
                        <li><a href="/blog">Blog</a></li>
                        <li><a href="/contact">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-widget__column footer-widget__gallery">
                    {{-- <h3 class="footer-widget__title">Instagram</h3> --}}
                    {{-- <ul class="footer-widget__gallery-list list-unstyled">
                        <li><a href="#">
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" alt="">
                            </a></li>
                        <li><a href="#">
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" alt="">
                            </a></li>
                        <li><a href="#">
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" alt="">
                            </a></li>
                        <li><a href="#">
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" alt="">
                            </a></li>
                        <li><a href="#">
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" alt="">
                            </a></li>
                        <li><a href="#">
                                <img src="{{ asset('template/front/assets/images/tour/tour1.jpg') }}" alt="">
                            </a></li>
                    </ul> --}}
                    <!-- SnapWidget -->
                    {{-- <iframe src="https://snapwidget.com/embed/1115174" class="snapwidget-widget"
                        allowtransparency="true" frameborder="0" scrolling="no"
                        style="border:none; overflow:hidden;  width:315px; height:210px"
                        title="Posts from Instagram"></iframe> --}}
                </div>
            </div>
        </div>
    </footer>

    <div class="site-footer__bottom">
        <div class="container">
            <p>@ All copyright 2026, <a href="#">{{ $profil->nama_profil }}</a></p>
            <div class="site-footer__social">
                <a href="{{ $profil->youtube }}"><i class="fab fa-youtube"></i></a>
                <a href="{{ $profil->instagram }}"><i class="fab fa-instagram"></i></a>
                <a href="{{ $profil->tiktok }}" target="_blank" rel="noopener" aria-label="TikTok">
                    <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/tiktok.svg" alt="TikTok"
                        style="width:18px;height:18px;" />
            </div>
        </div>
    </div>


    <a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="fa fa-angle-up"></i></a>


    <div class="side-menu__block">


        <div class="side-menu__block-overlay custom-cursor__overlay">
            <div class="cursor"></div>
            <div class="cursor-follower"></div>
        </div>
        <div class="side-menu__block-inner ">
            <div class="side-menu__top justify-content-end">

                <a href="#" class="side-menu__toggler side-menu__close-btn"><i class="fa fa-times"></i></a>
            </div>


            <nav class="mobile-nav__container">

            </nav>
            <div class="side-menu__sep"></div>
            <div class="side-menu__content">
                <p>{{ $profil->nama_profil }} - PLATFORM BOOKING ONLINE TERDEPAN</p>
                <p><a href="mailto:{{ $profil->email }}">{{ $profil->email }}</a> <br> <a
                        href="tel:{{ $profil->no_telp }}">{{ $profil->no_telp }}</a></p>
                <div class="side-menu__social">
                    <a href="{{ $profil->youtube }}"><i class="fab fa-youtube"></i></a>
                    <a href="{{ $profil->instagram }}"><i class="fab fa-instagram"></i></a>
                      <a href="{{ $profil->tiktok }}" target="_blank" rel="noopener" aria-label="TikTok">
                    <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/tiktok.svg" alt="TikTok"
                        style="width:18px;height:18px;" />
                </div>
            </div>
        </div>
    </div>



    {{-- <div class="search-popup">
        <div class="search-popup__overlay custom-cursor__overlay">
            <div class="cursor"></div>
            <div class="cursor-follower"></div>
        </div>
        <div class="search-popup__inner">
            <form action="#" class="search-popup__form">
                <input type="text" name="search" placeholder="Type here to Search....">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div> --}}


    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/{{ $profil->no_wa }}?text={{ urlencode($profil->deskripsi_3) }}" class="whatsapp-button"
        title="Hubungi Kami via WhatsApp">
        <img src="https://cdn-icons-png.freepik.com/256/3983/3983877.png?semt=ais_white_label"
            style="border-radius: 30%" alt="WhatsApp">
        <span>Chat via WhatsApp</span>
    </a>

    <script src="{{ asset('template/front/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/waypoints.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/TweenMax.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/wow.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/swiper.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/typed-2.0.11.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/vegas.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/countdown.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/nouislider.min.js') }}"></script>
    <script src="{{ asset('template/front/assets/js/isotope.js') }}"></script>

    <!-- template scripts -->
    <script src="{{ asset('template/front/assets/js/theme.js') }}"></script>
    @stack('scripts')

    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                includedLanguages: 'id,en,es,fr,ja,zh-CN,ar,ru,ms,hi',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function changeLanguage(lang) {
            let attempts = 0;
            const maxAttempts = 50;
            const interval = setInterval(() => {
                const select = document.querySelector('.goog-te-combo');
                if (select) {
                    clearInterval(interval);
                    if (select.value !== lang) {
                        select.value = lang;
                        select.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                    document.getElementById('language-select-desktop')?.setAttribute('value', lang);
                    document.querySelectorAll('.custom-translate-dropdown select').forEach(el => {
                        el.value = lang;
                    });
                } else if (++attempts >= maxAttempts) {
                    clearInterval(interval);
                    console.warn('Google Translate widget not loaded.');
                }
            }, 100);
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>

</html>
