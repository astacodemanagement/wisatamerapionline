@extends('fe.layouts.app')

@section('content')
    <section class="page-header"
        style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
        <div class="container">
            <h2>Tour Guides</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><a href="/">Pages</a></li>
                <li><span>Tour Guides</span></li>
            </ul>
        </div>
    </section>

    <section class="team-one">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/placeholder.png') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Gregory Bowman</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/placeholder.png') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Daisy Phillips</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/placeholder.png') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Elijah Rios</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/placeholder.png') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Ryan Graves</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/placeholder.png') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Stephen Fowler</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/placeholder.png') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Flora Larson</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-one">
        <div class="container">
            <h3>Work with our amazing tour guides</h3>
            <div class="cta-one__button-block">
                <a href="/contact" class="thm-btn cta-one__btn">Join our team</a>
            </div>
        </div>
    </section>
@endsection
