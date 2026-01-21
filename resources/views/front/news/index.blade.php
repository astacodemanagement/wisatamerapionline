@extends('fe.layouts.app')

@section('content')
    <section class="page-header"
        style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
        <div class="container">
            <h2>News Grid</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><span>News</span></li>
            </ul>
        </div>
    </section>

    <section class="blog-one blog-one__grid">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="blog-one__single">
                        <div class="blog-one__image">
                            <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" alt="">
                            <a href="/detail-news"><i class="fa fa-long-arrow-alt-right"></i></a>
                        </div>
                        <div class="blog-one__content">
                            <ul class="list-unstyled blog-one__meta">
                                <li><a href="/detail-news"><i class="far fa-user-circle"></i> Admin</a></li>
                            </ul>
                            <h3><a href="/detail-news">14 Things to see and do when
                                    visiting japan</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-one__single">
                        <div class="blog-one__image">
                            <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" alt="">
                            <a href="/detail-news"><i class="fa fa-long-arrow-alt-right"></i></a>
                        </div>
                        <div class="blog-one__content">
                            <ul class="list-unstyled blog-one__meta">
                                <li><a href="/detail-news"><i class="far fa-user-circle"></i> Admin</a></li>
                            </ul>
                            <h3><a href="/detail-news">Journeys are best measured
                                    in new friends</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-one__single">
                        <div class="blog-one__image">
                            <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" alt="">
                            <a href="/detail-news"><i class="fa fa-long-arrow-alt-right"></i></a>
                        </div>
                        <div class="blog-one__content">
                            <ul class="list-unstyled blog-one__meta">
                                <li><a href="/detail-news"><i class="far fa-user-circle"></i> Admin</a></li>
                            </ul>
                            <h3><a href="/detail-news">Travel the most beautiful
                                    places in the world</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-one__single">
                        <div class="blog-one__image">
                            <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" alt="">
                            <a href="/detail-news"><i class="fa fa-long-arrow-alt-right"></i></a>
                        </div>
                        <div class="blog-one__content">
                            <ul class="list-unstyled blog-one__meta">
                                <li><a href="/detail-news"><i class="far fa-user-circle"></i> Admin</a></li>
                            </ul>
                            <h3><a href="/detail-news">South asia tour limited time
                                    packages</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-one__single">
                        <div class="blog-one__image">
                            <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" alt="">
                            <a href="/detail-news"><i class="fa fa-long-arrow-alt-right"></i></a>
                        </div>
                        <div class="blog-one__content">
                            <ul class="list-unstyled blog-one__meta">
                                <li><a href="/detail-news"><i class="far fa-user-circle"></i> Admin</a></li>
                            </ul>
                            <h3><a href="/detail-news">Let’s start adventure with
                                    best tripo guides</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-one__single">
                        <div class="blog-one__image">
                            <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" alt="">
                            <a href="/detail-news"><i class="fa fa-long-arrow-alt-right"></i></a>
                        </div>
                        <div class="blog-one__content">
                            <ul class="list-unstyled blog-one__meta">
                                <li><a href="/detail-news"><i class="far fa-user-circle"></i> Admin</a></li>
                            </ul>
                            <h3><a href="/detail-news">Journeys are best measured
                                    in new friends</a></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="post-pagination">
                <a href="#"><i class="fa fa-angle-left"></i></a>
                <a class="active" href="#">01</a>
                <a href="#">02</a>
                <a href="#">03</a>
                <a href="#"><i class="fa fa-angle-right"></i></a>
            </div>
        </div>
    </section>
@endsection
