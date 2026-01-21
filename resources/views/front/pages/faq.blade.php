@extends('fe.layouts.app')

@section('content')
    <section class="page-header"
        style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><a href="/">Pages</a></li>
                <li><span>FAQS</span></li>
            </ul>
        </div>
    </section>

    <section class="faq-one">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="accrodion-grp" data-grp-name="faq-one-accrodion">
                        <div class="accrodion active">
                            <div class="accrodion-title">
                                <h4>Why are your tours so expensive?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion ">
                            <div class="accrodion-title">
                                <h4>Which payment methods are acceptable?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion">
                            <div class="accrodion-title">
                                <h4>How to book the new tour for 2 persons?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion">
                            <div class="accrodion-title">
                                <h4>All taxes are included in the booking prices?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion">
                            <div class="accrodion-title">
                                <h4>I am having trouble while booking?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion">
                            <div class="accrodion-title">
                                <h4>Is it possible to manage details through dashboard?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion">
                            <div class="accrodion-title">
                                <h4>What is the best way to contact with the guide?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion">
                            <div class="accrodion-title">
                                <h4>Multiple tour bookings are allowed?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accrodion">
                            <div class="accrodion-title">
                                <h4>I want to cancel my booking?</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>There are many variations of passages of available but majority have alteration in
                                        some by inject humour or random words. Lorem ipsum dolor sit amet, error insolens
                                        reprimique no quo, ea pri verterem phaedr vel ea iisque aliquam.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sidebar">
                        <div class="offer-sidebar wow fadeInUp" data-wow-duration="1500ms"
                            style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
                            <h3><span class="offer-sidebar__price">20%</span> Off <br>
                                On <span>Paris <br>
                                    Tour</span></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
