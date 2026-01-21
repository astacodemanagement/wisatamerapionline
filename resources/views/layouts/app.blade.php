<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-bs-theme="light"
    data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png"
        href="{{ asset('/upload/profil/' . ($profil->favicon ?: 'https://static1.squarespace.com/static/524883b7e4b03fcb7c64e24c/524bba63e4b0bf732ffc8bce/646fb10bc178c30b7c6a31f2/1712669811602/Squarespace+Favicon.jpg?format=1500w')) }}" />


    <!-- Core Css -->
    <link id="themeColors" rel="stylesheet" href="{{ asset('template/back') }}/dist/css/styles.css" />


    <title>{{ $title }}</title>
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-657l83W+fJv1vWf623tB5rWf1YyW3rYy1X1m25Q5Wf1U0524z1592+S1Ym0U1O056Y0U1Z0U1P141S4O01V591U46244M6254"
        crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    <!-- Owl Carousel  -->


    @stack('css')



    <style>
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #888 transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: 20px;
            position: absolute;
            top: 50%;
            width: 0;
        }

        .sidebar-link.active-submenu {
            background-color: #d5dbe0;
            color: #5b5656;
        }

        .sidebar-link.active-submenu .ti-circle {
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('/loader.png') }}" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper">
        <!-- Sidebar Start -->
        <aside class="left-sidebar with-vertical">
            <div><!-- ---------------------------------- -->
                <!-- Start Vertical Layout Sidebar -->
                <!-- ---------------------------------- -->
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="/home" class="text-nowrap logo-img mt-2">
                        <img src="{{ asset('/upload/profil/' . $profil->logo) }}" class="dark-logo" alt="Logo-Dark"
                            style="width: 50%; max-width: 150px; height: auto; object-fit: contain;" />

                        <img src="{{ asset('/upload/profil/' . $profil->logo_dark) }}" class="light-logo"
                            alt="Logo-light"
                            style="width: 50%; max-width: 150px; height: auto; object-fit: contain;" />

                    </a>
                    <a href="javascript:void(0)"
                        class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                        <i class="ti ti-x"></i>
                    </a>
                </div>

                <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                    <ul id="sidebarnav">
                        @foreach ($menus->sortBy('position') as $menu)
                            @can($menu->permission_name)
                                <li class="nav-small-cap">
                                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                    <span class="hide-menu">{{ $menu->name }}</span>
                                </li>

                                @foreach ($menu->items->sortBy('position') as $item)
                                    @can($item->permission_name)
                                        <li class="sidebar-item">
                                            @if ($item->children->isNotEmpty())
                                                <a class="sidebar-link has-arrow {{ request()->routeIs($item->children->pluck('route')->toArray()) ? 'active-collapse' : '' }}"
                                                    href="javascript:void(0)" aria-expanded="false">
                                                    <span class="d-flex">
                                                        <i class="{{ $item->icon }}"></i>
                                                    </span>
                                                    <span class="hide-menu">{{ $item->name }}</span>
                                                </a>
                                                <ul aria-expanded="false"
                                                    class="collapse first-level {{ request()->routeIs($item->children->pluck('route')->toArray()) ? 'show' : '' }}">
                                                    @foreach ($item->children->sortBy('position') as $subItem)
                                                        @can($subItem->permission_name)
                                                            <li class="sidebar-item">
                                                                <a href="{{ route($subItem->route) }}"
                                                                    class="sidebar-link {{ request()->routeIs($subItem->route) ? 'active-submenu' : '' }}">
                                                                    <div
                                                                        class="round-16 d-flex align-items-center justify-content-center">
                                                                        <i class="ti ti-circle"></i>
                                                                    </div>
                                                                    <span class="hide-menu">{{ $subItem->name }}</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    @endforeach
                                                </ul>
                                            @else
                                                <a class="sidebar-link {{ request()->routeIs($item->route) ? 'active' : '' }}"
                                                    href="{{ route($item->route) }}" aria-expanded="false">
                                                    <span>
                                                        <i class="{{ $item->icon }}"></i>
                                                    </span>
                                                    <span class="hide-menu">{{ $item->name }}</span>
                                                </a>
                                            @endif
                                        </li>
                                    @endcan
                                @endforeach
                            @endcan
                        @endforeach
                    </ul>
                </nav>


                <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
                    <div class="hstack gap-3">
                        <div class="john-img">

                            <img src="{{ Auth::user()->image ? asset('/upload/users/' . Auth::user()->image) : 'https://img.freepik.com/premium-vector/character-avatar-isolated_729149-194801.jpg?semt=ais_hybrid&w=740' }}"
                                class="rounded-circle" width="40" height="40" alt="modernize-img" />

                        </div>
                        <div class="john-title">
                            <h6 class="mb-0 fs-4 fw-semibold" title="{{ Auth::user()->name }}">
                                {{ explode(' ', Auth::user()->name)[0] }}</h6>

                            <span class="fs-2">{{ Auth::user()->getRoleNames()->first() }}</span>
                        </div>
                        <!-- Tombol Logout -->
                        <button class="border-0 bg-transparent text-primary ms-auto" tabindex="0" type="button"
                            aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="logout"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-power fs-6"></i>
                        </button>

                        <!-- Link dan Form Logout -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    </div>
                </div>


            </div>
        </aside>



        <!--  Sidebar End -->
        <div class="page-wrapper">
            <!--  Header Start -->
            <header class="topbar">
                <div class="with-vertical"><!-- ---------------------------------- -->
                    <!-- Start Vertical Layout Header -->
                    <!-- ---------------------------------- -->
                    <nav class="navbar navbar-expand-lg p-0">
                        <ul class="navbar-nav">
                            <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                                <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                                    <i class="ti ti-menu-2"></i>
                                </a>
                            </li>

                        </ul>

                        <ul class="navbar-nav quick-links d-none d-lg-flex align-items-center">
                            <!-- <li class="nav-item dropdown-hover d-none d-lg-block">
                                <a class="nav-link" href="{{ asset('template/back') }}/main/app-calendar.html">Kalender</a>
                            </li> -->
                            <li class="nav-item dropdown-hover d-none d-lg-block">
                                <a class="nav-link" target="_blank"
                                    href="https://wa.me/{{ $profil->no_wa }}">Kontak</a>
                            </li>

                            <li class="nav-item dropdown-hover d-none d-lg-block">
                                <a class="nav-link" target="_blank" href="/">Halaman Depan</a>
                            </li>
                        </ul>

                        <div class="d-block d-lg-none py-4">
                            {{-- <a href="/upload/profil/{{ $profil->favicon }}" class="text-nowrap logo-img">
                                <img src="{{ asset('/upload/profil/' . $profil->logo) }}" class="dark-logo" alt="Logo-Dark"
                            style="width: 100%; max-width: 50%; height: auto; object-fit: contain;" />
                        <img src="{{ asset('/upload/profil/' . $profil->logo_dark) }}" class="light-logo"
                            alt="Logo-light"
                            style="width: 100%; max-width: 50%; height: auto; object-fit: contain;" />
                            </a> --}}
                        </div>
                        <a class="navbar-toggler nav-icon-hover-bg rounded-circle p-0 mx-0 border-0"
                            href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="ti ti-dots fs-7"></i>
                        </a>
                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0)"
                                    class="nav-link nav-icon-hover-bg rounded-circle mx-0 ms-n1 d-flex d-lg-none align-items-center justify-content-center"
                                    type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                                    aria-controls="offcanvasWithBothOptions">
                                    <i class="ti ti-align-justified fs-7"></i>
                                </a>
                                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                                    <!-- ------------------------------- -->
                                    <!-- start language Dropdown -->
                                    <!-- ------------------------------- -->
                                    <li class="nav-item nav-icon-hover-bg rounded-circle" title="Halaman Depan">
                                        <a class="nav-link" href="/" target="_blank">
                                            <i class="fas fa-globe"></i>
                                        </a>

                                    </li>
                                    @can('purchase-create')
                                        <li class="nav-item nav-icon-hover-bg rounded-circle" title="Menu Penjualan">
                                            <a class="nav-link" href="{{ route('orders.create') }}">
                                                <i class="fas fa-cart-plus"></i>
                                            </a>

                                        </li>
                                    @endcan


                                    @can('user-access')
                                        <li class="nav-item nav-icon-hover-bg rounded-circle dropdown">
                                            <a class="nav-link position-relative" href="javascript:void(0)"
                                                id="drop2" aria-expanded="false">
                                                <i class="ti ti-bell-ringing"></i>
                                                @if ($nonactiveUsersCount > 0)
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $nonactiveUsersCount }}
                                                        <span class="visually-hidden">unread notifications</span>
                                                    </span>
                                                @endif
                                            </a>
                                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                                aria-labelledby="drop2">
                                                <div class="d-flex align-items-center justify-content-between py-3 px-7">
                                                    <h5 class="mb-0 fs-5 fw-semibold">Notifikasi</h5>
                                                    @if ($nonactiveUsersCount > 0)
                                                        <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm">
                                                            {{ $nonactiveUsersCount }} baru
                                                        </span>
                                                    @else
                                                        <span class="badge text-bg-secondary rounded-4 px-3 py-1 lh-sm">
                                                            Tidak ada notifikasi
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="message-body" data-simplebar>
                                                    @if ($nonactiveUsers->isEmpty())
                                                        <div class="py-3 px-7">
                                                            <p class="mb-0 fs-3">Tidak ada pengguna nonaktif.</p>
                                                        </div>
                                                    @else
                                                        @foreach ($nonactiveUsers as $user)
                                                            <a href="/users"
                                                                class="d-flex align-items-center dropdown-item">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-0 fs-3">
                                                                        {{ $user->name ?? 'Pengguna Tanpa Nama' }}</h6>
                                                                    <p class="mb-0 fs-2 text-muted">Status: Nonaktif</p>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endcan







                                    <!-- ------------------------------- -->
                                    <!-- start profile Dropdown -->
                                    <!-- ------------------------------- -->
                                    <li class="nav-item dropdown">
                                        <a class="nav-link pe-0" href="javascript:void(0)" id="drop1"
                                            aria-expanded="false">
                                            <div class="d-flex align-items-center">
                                                <div class="user-profile-img">
                                                    <img src="{{ Auth::user()->image ? asset('/upload/users/' . Auth::user()->image) : 'https://img.freepik.com/premium-vector/character-avatar-isolated_729149-194801.jpg?semt=ais_hybrid&w=740' }}"
                                                        class="rounded-circle" width="40" height="40"
                                                        alt="modernize-img" />

                                                </div>
                                            </div>
                                        </a>

                                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                            aria-labelledby="drop1">
                                            <div class="profile-dropdown position-relative" data-simplebar>
                                                <div class="py-3 px-7 pb-0">
                                                    <h5 class="mb-0 fs-5 fw-semibold">Profil Pengguna</h5>
                                                </div>
                                                <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                                    <img src="{{ Auth::user()->image ? asset('/upload/users/' . Auth::user()->image) : 'https://img.freepik.com/premium-vector/character-avatar-isolated_729149-194801.jpg?semt=ais_hybrid&w=740' }}"
                                                        class="rounded-circle" width="40" height="40"
                                                        alt="modernize-img" />

                                                    <div class="ms-3">
                                                        <h5 class="mb-1 fs-3">{{ Auth::user()->name }}</h5>
                                                        <span
                                                            class="mb-1 d-block">{{ Auth::user()->getRoleNames()->first() }}</span>
                                                        <p class="mb-0 d-flex align-items-center gap-2"
                                                            style="font-size: 10px;">
                                                            <i class="ti ti-mail fs-4"></i> {{ Auth::user()->email }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="message-body">
                                                    <a href="{{ route('users.profile.edit', Auth::user()) }}"
                                                        class="py-8 px-7 mt-8 d-flex align-items-center">
                                                        <span
                                                            class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                            <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/svgs/icon-account.svg"
                                                                alt="modernize-img" width="24" height="24" />
                                                        </span>
                                                        <div class="w-100 ps-3">
                                                            <h6 class="mb-1 fs-3 fw-semibold lh-base">Profil</h6>
                                                            <span class="fs-2 d-block text-body-secondary">Pengaturan
                                                                Profil</span>
                                                        </div>
                                                    </a>

                                                </div>
                                                <div class="d-grid py-4 px-7 pt-8">
                                                    <button class="btn btn-outline-primary" tabindex="0"
                                                        type="button" aria-label="logout" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" data-bs-title="logout"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout
                                                        <i class="ti ti-power fs-3"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- ------------------------------- -->
                                    <!-- end profile Dropdown -->
                                    <!-- ------------------------------- -->
                                </ul>
                            </div>
                        </div>
                    </nav>
                    <!-- ---------------------------------- -->
                    <!-- End Vertical Layout Header -->
                    <!-- ---------------------------------- -->




                </div>



            </header>
            <!--  Header End -->


            <div class="body-wrapper">
                <div class="container-fluid">


                    @yield('content')


                </div>
            </div>
            <script>
                function handleColorTheme(e) {
                    document.documentElement.setAttribute("data-color-theme", e);
                }
            </script>
            @can('user-access')
                <button
                    class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
                    aria-controls="offcanvasExample">
                    <i class="icon ti ti-settings fs-7"></i>
                </button>
            @endcan
            <div class="offcanvas customizer offcanvas-end" tabindex="-1" id="offcanvasExample"
                aria-labelledby="offcanvasExampleLabel">
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                    <h4 class="offcanvas-title fw-semibold" id="offcanvasExampleLabel">
                        Settings
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <form id="updateProfilForm" action="{{ route('profil.update_setting', $profil->id) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="offcanvas-body h-n80" data-simplebar>
                        <h6 class="fw-semibold fs-4 mb-2">Tema</h6>

                        <div class="d-flex flex-row gap-3 customizer-box" role="group">
                            <input type="radio" class="btn-check light-layout" name="theme" id="light-layout"
                                value="light" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary rounded-2" for="light-layout">
                                <i class="icon ti ti-brightness-up fs-7 me-2"></i>Light
                            </label>

                            <input type="radio" class="btn-check dark-layout" name="theme" id="dark-layout"
                                value="dark" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary rounded-2" for="dark-layout">
                                <i class="icon ti ti-moon fs-7 me-2"></i>Dark
                            </label>
                        </div>


                        <h6 class="mt-5 fw-semibold fs-4 mb-2">Theme Colors</h6>

                        <div class="d-flex flex-row flex-wrap gap-3 customizer-box color-pallete" role="group">
                            <input type="radio" class="btn-check" name="theme_color" id="Blue_Theme"
                                value="Blue_Theme" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                                onclick="handleColorTheme('Blue_Theme')" for="Blue_Theme" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="BLUE_THEME">
                                <div
                                    class="color-box rounded-circle d-flex align-items-center justify-content-center skin-1">
                                    <i class="ti ti-check text-white d-flex icon fs-5"></i>
                                </div>
                            </label>

                            <input type="radio" class="btn-check" name="theme_color" id="Aqua_Theme"
                                value="Aqua_Theme" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                                onclick="handleColorTheme('Aqua_Theme')" for="Aqua_Theme" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="AQUA_THEME">
                                <div
                                    class="color-box rounded-circle d-flex align-items-center justify-content-center skin-2">
                                    <i class="ti ti-check text-white d-flex icon fs-5"></i>
                                </div>
                            </label>

                            <input type="radio" class="btn-check" name="theme_color" id="Purple_Theme"
                                value="Purple_Theme" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                                onclick="handleColorTheme('Purple_Theme')" for="Purple_Theme"
                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PURPLE_THEME">
                                <div
                                    class="color-box rounded-circle d-flex align-items-center justify-content-center skin-3">
                                    <i class="ti ti-check text-white d-flex icon fs-5"></i>
                                </div>
                            </label>

                            <input type="radio" class="btn-check" name="theme_color" id="Green_Theme"
                                value="Green_Theme" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                                onclick="handleColorTheme('Green_Theme')" for="Green_Theme" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="GREEN_THEME">
                                <div
                                    class="color-box rounded-circle d-flex align-items-center justify-content-center skin-4">
                                    <i class="ti ti-check text-white d-flex icon fs-5"></i>
                                </div>
                            </label>

                            <input type="radio" class="btn-check" name="theme_color" id="Cyan_Theme"
                                value="Cyan_Theme" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                                onclick="handleColorTheme('Cyan_Theme')" for="Cyan_Theme" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="CYAN_THEME">
                                <div
                                    class="color-box rounded-circle d-flex align-items-center justify-content-center skin-5">
                                    <i class="ti ti-check text-white d-flex icon fs-5"></i>
                                </div>
                            </label>

                            <input type="radio" class="btn-check" name="theme_color" id="Orange_Theme"
                                value="Orange_Theme" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                                onclick="handleColorTheme('Orange_Theme')" for="Orange_Theme"
                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ORANGE_THEME">
                                <div
                                    class="color-box rounded-circle d-flex align-items-center justify-content-center skin-6">
                                    <i class="ti ti-check text-white d-flex icon fs-5"></i>
                                </div>
                            </label>

                            <input type="radio" class="btn-check" name="theme_color" id="Akp_Theme"
                                value="Akp_Theme" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center"
                                onclick="handleColorTheme('Akp_Theme')" for="Akp_Theme" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="AKP_THEME">
                                <div
                                    class="color-box rounded-circle d-flex align-items-center justify-content-center skin-7">
                                    <i class="ti ti-check text-white d-flex icon fs-5"></i>
                                </div>
                            </label>
                        </div>

                        <h6 class="mt-5 fw-semibold fs-4 mb-2">Container Option</h6>

                        <div class="d-flex flex-row gap-3 customizer-box" role="group">
                            <input type="radio" class="btn-check" name="boxed_layout" id="boxed-layout"
                                value="true" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary" for="boxed-layout">
                                <i class="icon ti ti-layout-distribute-vertical fs-7 me-2"></i>Boxed
                            </label>

                            <input type="radio" class="btn-check" name="boxed_layout" id="full-layout"
                                value="false" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary" for="full-layout">
                                <i class="icon ti ti-layout-distribute-horizontal fs-7 me-2"></i>Full
                            </label>
                        </div>

                        <h6 class="fw-semibold fs-4 mb-2 mt-5">Sidebar Type</h6>
                        <div class="d-flex flex-row gap-3 customizer-box" role="group">
                            <a href="javascript:void(0)" class="fullsidebar">
                                <input type="radio" class="btn-check" name="sidebar_type" id="full-sidebar"
                                    value="full" autocomplete="off" />
                                <label class="btn p-9 btn-outline-primary" for="full-sidebar">
                                    <i class="icon ti ti-layout-sidebar-right fs-7 me-2"></i>Full
                                </label>
                            </a>
                            <div>
                                <input type="radio" class="btn-check" name="sidebar_type" id="mini-sidebar"
                                    value="mini-sidebar" autocomplete="off" />
                                <label class="btn p-9 btn-outline-primary" for="mini-sidebar">
                                    <i class="icon ti ti-layout-sidebar fs-7 me-2"></i>Collapse
                                </label>
                            </div>
                        </div>

                        <h6 class="mt-5 fw-semibold fs-4 mb-2">Card With</h6>

                        <div class="d-flex flex-row gap-3 customizer-box" role="group">
                            <input type="radio" class="btn-check" name="card_border" id="card-with-border"
                                value="true" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary" for="card-with-border">
                                <i class="icon ti ti-border-outer fs-7 me-2"></i>Border
                            </label>

                            <input type="radio" class="btn-check" name="card_border" id="card-without-border"
                                value="false" autocomplete="off" />
                            <label class="btn p-9 btn-outline-primary" for="card-without-border">
                                <i class="icon ti ti-border-none fs-7 me-2"></i>Shadow
                            </label>
                        </div>


                        <br>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-end mt-4 gap-3">
                                <button type="submit" id="updateButton" class="btn btn-primary"><i
                                        class="fa fa-save"></i>
                                    Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            var userSettings = {
                Layout: "vertical", // vertical | horizontal
                Direction: "ltr", // ltr | rtl
                SidebarType: "{{ request()->is('orders/create') || request()->is('orders/*/edit') ? 'mini-sidebar' : $profil->sidebar_type }}", // full | mini-sidebar
                BoxedLayout: {{ $profil->boxed_layout }}, // true | false
                Theme: "{{ $profil->theme }}", // light | dark
                ColorTheme: "{{ $profil->theme_color }}", // Blue_Theme | Aqua_Theme | Purple_Theme | Green_Theme | Cyan_Theme | Orange_Theme
                cardBorder: {{ $profil->card_border }}, // true | false
            }

            function handleColorTheme(e) {
                document.documentElement.setAttribute("data-color-theme", e);
            }
        </script>



        <!--  Search Bar -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content rounded-1">
                    <div class="modal-header border-bottom">
                        <input type="search" class="form-control fs-3" placeholder="Search here" id="search" />
                        <a href="javascript:void(0)" data-bs-dismiss="modal" class="lh-1">
                            <i class="ti ti-x fs-5 ms-3"></i>
                        </a>
                    </div>
                    <div class="modal-body message-body" data-simplebar="">
                        <h5 class="mb-0 fs-5 p-1">Quick Page Links</h5>
                        <ul class="list mb-0 py-2">

                            <li class="p-1 mb-1 bg-hover-light-black">
                                <a href="javascript:void(0)">
                                    <span class="d-block">Dashboard</span>
                                    <span class="text-muted d-block">/dashboards/dashboard2</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="dark-transparent sidebartoggler"></div>





    <!-- Import Js Files -->
    <script src="{{ asset('template/back') }}/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/vendor.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/theme/app.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/theme/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- <script src="{{ asset('template/back') }}/dist/libs/simplebar/dist/simplebar.min.js"></script> -->
    <!-- <script src="{{ asset('template/back') }}/dist/js/theme/app.init.js"></script> -->
    <!-- solar icons -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script> -->

    <script>
        $('.sidebar-link.has-arrow').on('click', function() {
            var $submenu = $(this).next('.collapse');
            $submenu.toggleClass('show');
            $(this).attr('aria-expanded', $submenu.hasClass('show'));
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#updateProfilForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let id = '{{ $profil->id }}';

                $.ajax({
                    url: '/profil/update_setting/' + id,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';

                        $.each(errors, function(field, messages) {
                            errorMessages += messages.join(' ') + '\n';
                        });

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessages || 'Gagal memperbarui profil.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        $('#updateButton').prop('disabled', false).text('Update');
                    }
                });

            });
        });
    </script>
    @stack('script')

</body>

</html>
