@extends('fe.layouts.app')

@section('content')
    <section class="page-header"
        style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
        <div class="container">
            <h2>About Page</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><a href="/">Pages</a></li>
                <li><span>About</span></li>
            </ul>
        </div>
    </section>

    <section class="cta-two">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 wow fadeInLeft">
                    <img src="{{ asset('template/assets/images/tour/tour3.png') }}" alt="" class="img-fluid">
                </div>
                <div class="col-lg-6">
                    <div class="cta-two__content">
                        <div class="block-title text-left">
                            <p>Best tour services</p>
                            <h3>Trusted & Award Winning <br> Tour Agency</h3>
                        </div>
                        <div class="cta-two__content-highlight">
                            <p>There are many variations of lorem ipsum but the majority have <br> alteration in some form,
                                by randomised words look.</p>
                        </div>
                        <ul class="list-unstyled cta-two__list">
                            <li><i class="fa fa-check"></i>Every employee wears a photo ID badge.</li>
                            <li><i class="fa fa-check"></i>Mobiles are custom wrapped for easy identification.</li>
                            <li><i class="fa fa-check"></i>We are a fully insured nationally ranked brand.</li>
                            <li><i class="fa fa-check"></i>All work is backed by our exclusive “Streak-Free Guarantee”.</li>
                            <li><i class="fa fa-check"></i>Our services are more affordable than you think.</li>
                        </ul>
                        <a href="/aboutus" class="thm-btn cta-two__btn">Discover more</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-one">
        <div class="container">
            <div class="block-title text-center">
                <p>checkout our</p>
                <h3>Top Tour Reviews</h3>
            </div>
            <div class="testimonials-one__carousel thm__owl-carousel light-dots owl-carousel owl-theme"
                data-options='{"nav": false, "autoplay": true, "autoplayTimeout": 5000, "smartSpeed": 700, "dots": true, "margin": 30, "loop": true, "responsive": { "0": { "items": 1, "nav": true, "navText": ["Prev", "Next"], "dots": false }, "767": { "items": 1, "nav": true, "navText": ["Prev", "Next"], "dots": false }, "991": { "items": 2 }, "1199": { "items": 2 }, "1200": { "items": 3 } }}'>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Kevin Smith</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Christine Eve</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Mike Hardson</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Kevin Smith</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Christine Eve</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Mike Hardson</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Kevin Smith</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Christine Eve</h3>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="testimonials-one__single">
                        <div class="testimonials-one__content">
                            <div class="testimonials-one__stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>There are many variations of passages of lorem ipsum but the majority have alteration in some
                                form, by randomised words look. Aene an commodo ligula eget dolorm sociis.</p>
                        </div>
                        <div class="testimonials-one__info">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                            <h3>Mike Hardson</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="video-one"
        style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
        <div class="container text-center">
            <a href="https://www.youtube.com/watch?v=i9E_Blai8vk" class="video-one__btn video-popup"><i
                    class="fa fa-play"></i></a>
            <p>Love where you're going</p>
            <h3><span>Wimo</span> is a World Leading <br> Online <span>Tour Booking Platform</span></h3>
        </div>
    </section>

    <section class="team-one">
        <div class="container">
            <div class="block-title text-center">
                <p>meet the team</p>
                <h3>Expert People</h3>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Loretta Sutton</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Roxie Palmer</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Lucille Schneider</h3>
                            <p class="text-uppercase">Tour Guide</p>
                            <div class="team-one__social">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__image">
                            <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                        </div>
                        <div class="team-one__content">
                            <h3>Cory Wilkins</h3>
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

    <div class="brand-one">
        <div class="container">
            <div class="brand-one__carousel owl-theme owl-carousel thm__owl-carousel"
                data-options='{"nav": false, "autoplay": true, "autoplayTimeout": 5000, "smartSpeed": 700, "dots": false, "margin": 115, "responsive": { "0": { "items": 2, "margin": 20 }, "480": { "items": 2, "margin": 20 }, "767": { "items": 3, "margin": 20 }, "991": { "items": 4, "margin": 40 }, "1199": { "items": 5 } }}'>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
                <div class="item">
                    <img src="{{ asset('template/assets/images/tour/tour2.jpg') }}" alt="">
                </div>
            </div>
        </div>
    </div>


    <section class="mailchimp-one">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h3>Get Latest Tour Updates <br>
                        by Signing Up</h3>
                </div>
                <div class="col-lg-7">
                    <form action="#" class="mailchimp-one__form mc-form"
                        data-url="https://xyz.us18.list-manage.com/subscribe/post?u=20e91746ef818cd941998c598&amp;id=cc0ee8140e">
                        <input type="text" placeholder="Email Address">
                        <button class="thm-btn mailchimp-one__btn" type="submit">Subscribe now</button>
                    </form>
                    <div class="mc-form__response"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
