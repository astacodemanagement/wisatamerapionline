@extends('fe.layouts.app')

@section('content')
    <section class="page-header"
        style="background-image: url({{ asset('template/assets/images/backgrounds/slider1.jpg') }});">
        <div class="container">
            <h2>News Details</h2>
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><span>News</span></li>
            </ul>
        </div>
    </section>

    <section class="blog-list">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">

                    <div class="blog-details__image">
                        <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}" alt=""
                            class="img-fluid">
                    </div>
                    <div class="blog-details__content">
                        <h3>Journeys are best measured in new friends</h3>
                        <br>
                        <p>Lorem ipsum available isn but the majority have suffered alteratin in some or form injected.
                            Lorem Ipsum. Proin gravida nibh vel velit auctor aliqueenean sollicitudin, lorem quis bibendum
                            auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. vulputate cursus a sit amet
                            mauris. Morbi accumsan ipsum veliam nec tellus a odio tincidunt auctor.</p>

                        <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered
                            alteration in some injected or words which don't look even slightly believable. If you are going
                            to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in
                            the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined
                            chunks as necessary, making this the first true generator on the Internet. It uses a dictionary
                            of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem
                            Ipsum which looks reasonable. </p>

                        <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                            printer took a galley of type and scrambled it to make a type specimen book. It has survived not
                            only five centuries, but also the leap into electronic typesetting.</p>

                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                            the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley
                            of type and scrambled it to make a type specimen book. It has survived not only five centuries,
                            but also the leap into unchanged. Lorem Ipsum passages publishing.</p>
                    </div>


                    {{-- <div class="comment-form mt-5">
                        <h3 class="comment-form__title">Leave a Comment</h3><!-- /.comment-form__title -->
                        <form action="inc/sendemail.php" class="contact-one__form">
                            <div class="row low-gutters">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="name" placeholder="Your Name">
                                    </div><!-- /.input-group -->
                                </div><!-- /.col-md-6 -->
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="email" placeholder="Email Address">
                                    </div><!-- /.input-group -->
                                </div><!-- /.col-md-6 -->
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <textarea name="message" placeholder="Write Message"></textarea>
                                    </div><!-- /.input-group -->
                                </div><!-- /.col-md-12 -->
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <button type="submit" class="thm-btn contact-one__btn">Send message</button>
                                        <!-- /.thm-btn contact-one__btn -->
                                    </div><!-- /.input-group -->
                                </div><!-- /.col-md-12 -->
                            </div><!-- /.row low-gutters -->
                        </form>
                    </div> --}}
                </div>
                <div class="col-lg-4">
                    <div class="sidebar">
                        <div class="sidebar__single sidebar__search">
                            <form action="#" class="sidebar__search-form">
                                <input type="search" placeholder="Search">
                                <button type="submit"><i class="tripo-icon-magnifying-glass"></i></button>
                            </form>
                        </div>
                        <div class="sidebar__single sidebar__post">
                            <h3 class="sidebar__title">Recent Posts</h3>
                            <ul class="sidebar__post-list list-unstyled">
                                <li>
                                    <div class="sidebar__post-image">
                                        <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}"
                                            alt="" class="img-fluid">
                                    </div>
                                    <div class="sidebar__post-content">
                                        <h3><a href="/detail-news">Travel the most beautiful
                                                places in the world</a></h3>
                                    </div>
                                </li>
                                <li>
                                    <div class="sidebar__post-image">
                                        <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}"
                                            alt="">
                                    </div>
                                    <div class="sidebar__post-content">
                                        <h3><a href="/detail-news">Travel the most beautiful
                                                places in the world</a></h3>
                                    </div>
                                </li>
                                <li>
                                    <div class="sidebar__post-image">
                                        <img src="{{ asset('template/assets/images/backgrounds/slider1.jpg') }}"
                                            alt="">
                                    </div>
                                    <div class="sidebar__post-content">
                                        <h3><a href="/detail-news">Travel the most beautiful
                                                places in the world</a></h3>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="sidebar__single sidebar__category">
                            <h3 class="sidebar__title">All Categories</h3>
                            <ul class="sidebar__category-list list-unstyled">
                                <li><a href="#">Trip & Tours</a></li>
                                <li><a href="#">Traveling</a></li>
                                <li><a href="#">Adventures</a></li>
                                <li><a href="#">National Parks</a></li>
                                <li><a href="#">Beaches and Sea</a></li>
                            </ul>
                        </div>
                        <div class="sidebar__single sidebar__tags">
                            <h3 class="sidebar__title">Popular Tags</h3>
                            <div class="sidebar__tags-list">
                                <a href="#">Tour</a>
                                <a href="#">Travel</a>
                                <a href="#">beach</a>
                                <a href="#">Mountain</a>
                                <a href="#">Adventures</a>
                                <a href="#">parks</a>
                                <a href="#">Museums</a>
                            </div>
                        </div>

                        <div class="sidebar__single sidebar__social">
                            <h3 class="sidebar__title">Follow Us</h3>
                            <div class="sidebar__social-list">
                                <a href="#"><i class="fab fa-facebook-square"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-dribbble"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
