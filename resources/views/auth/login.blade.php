<!DOCTYPE html>
<html lang="en">

<head>
    <!--  Title -->
    <title>{{ $profil->nama_profil }}</title>
    <!--  Required Meta Tag -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="description" content="{{ $profil->nama_profil }}" />
    <meta name="author" content="" />
    <meta name="keywords" content="{{ $profil->nama_profil }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--  Favicon -->
    <link rel="shortcut icon" type="image/png" href="/upload/profil/{{ $profil->favicon }}" />
    <!-- Core Css -->
    <link id="themeColors" rel="stylesheet" href="{{ asset('template/back') }}/dist/css/style.min.css" />
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="/upload/profil/{{ $profil->favicon }}" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!-- Preloader -->
    <div class="preloader">
        <img src="/upload/profil/{{ $profil->favicon }}" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100">
            <div class="position-relative z-index-5">
                <div class="row">
                    <div class="col-xl-7 col-xxl-8">
                        <a href="/" class="text-nowrap logo-img d-block px-4 py-9 w-100">
                            <img src="/upload/profil/{{ $profil->logo }}" width="100" alt="">
                        </a>
                        <div class="d-none d-xl-flex align-items-center justify-content-center"
                            style="height: calc(100vh - 80px);">
                            <img src="/upload/profil/{{ $profil->bg_login }}" alt="" class="img-fluid"
                                width="800">
                        </div>
                    </div>
                    <div class="col-xl-5 col-xxl-4">
                        <div
                            class="authentication-login min-vh-100 bg-body row justify-content-center align-items-center p-4">
                            <div class="col-sm-8 col-md-6 col-xl-9">
                                <h2 class="mb-3 fs-7 fw-bolder">{{ __('Welcome to') }} {{ $profil->nama_profil }}</h2>
                                <p class=" mb-9">{{ __('Please login with your account') }}</p>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password" required autocomplete="current-password">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="togglePassword">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <script>
                                        document.getElementById('togglePassword').addEventListener('click', function() {
                                            const passwordInput = document.getElementById('password');
                                            const toggleIcon = document.getElementById('toggleIcon');

                                            if (passwordInput.type === 'password') {
                                                passwordInput.type = 'text';
                                                toggleIcon.classList.remove('fa-eye');
                                                toggleIcon.classList.add('fa-eye-slash');
                                            } else {
                                                passwordInput.type = 'password';
                                                toggleIcon.classList.remove('fa-eye-slash');
                                                toggleIcon.classList.add('fa-eye');
                                            }
                                        });
                                    </script>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="flexCheckChecked">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2"><i
                                            class="fas fa-sign-in-alt"></i>
                                        {{ __('Login') }}
                                    </button>
                                     
                                    {{-- <div class="row">
                                        <div class="col-12 mb-3">
                                            <a class="btn btn-white text-dark border fw-normal d-flex align-items-center justify-content-center rounded-2 py-8"
                                                href="{{ route('google.login') }}" role="button">
                                                <img src="{{ asset('template/back') }}/dist/images/svgs/google-icon.svg"
                                                    alt="" class="img-fluid me-2" width="18"
                                                    height="18">
                                                <span
                                                    class="d-none d-sm-block me-1 flex-shrink-0">{{ __('Sign in with') }}</span>Google
                                            </a>
                                        </div>
                                    </div>
                                    <br> --}}
                                    {{-- <div class="d-flex align-items-center justify-content-center">
                                        <a class="text-warning fw-medium ms-2"
                                            href="/">{{ __('To Front Page') }}</a>
                                    </div>
                                    <br> --}}
                                    <!--<div class="d-flex align-items-center justify-content-center">-->
                                    <!--    <p class="fs-4 mb-0 fw-medium">Butuh bantuan?</p>-->
                                    <!--    <a class="text-success fw-medium ms-2"-->
                                    <!--        href="https://wa.me/{{ $profil->no_wa }}"-->
                                    <!--        target="_blank">-->
                                    <!--        <i class="fab fa-whatsapp me-1"></i>Hubungi Kami-->
                                    <!--    </a>-->
                                    <!--</div>-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  Import Js Files -->
    <script src="{{ asset('template/back') }}/dist/libs/jquery/dist/jquery.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!--  core files -->
    <script src="{{ asset('template/back') }}/dist/js/app.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/app.init.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/app-style-switcher.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/sidebarmenu.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/custom.js"></script>
</body>

</html>