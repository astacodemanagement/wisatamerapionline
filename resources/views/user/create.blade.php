@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/select2/dist/css/select2.min.css') }}">
    <style>
        .nav-tabs .nav-link.active {
            background: linear-gradient(to right, #00923f, #fff000) !important;
            color: white !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card bg-light-info shadow-none position-relative overflow-hidden" style="border: solid 0.5px #ccc;">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">{{ $title }}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">

                                <li class="breadcrumb-item" aria-current="page">{{ $subtitle }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-3 text-center mb-n5">
                        <img src="{{ asset('template/back/dist/images/breadcrumb/ChatBc.png') }}" alt=""
                            class="img-fluid mb-n4">
                    </div>
                </div>
            </div>
        </div>



        <!-- Form Section -->
        <section class="datatables">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> Ada beberapa masalah dengan data yang Anda masukkan.
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                                @csrf

                                <ul class="nav nav-tabs" id="userTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="data-diri-tab" data-bs-toggle="tab"
                                            data-bs-target="#data-diri" type="button" role="tab"
                                            aria-controls="data-diri" aria-selected="true">
                                            Data Diri
                                        </button>
                                    </li>
                                    

                                </ul>

                                <div class="tab-content mt-3" id="userTabsContent">
                                    <div class="tab-pane fade show active" id="data-diri" role="tabpanel"
                                        aria-labelledby="data-diri-tab">
                                        <div class="row">
                                            <!-- Gambar Diri -->
                                            <div class="col-md-12 mb-3">
                                                <div class="linear-gradient d-flex align-items-center justify-content-center rounded-circle"
                                                    style="width: 110px; height: 110px;">
                                                    <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                                                        style="width: 100px; height: 100px;">
                                                        <img src="{{ Auth::user()->image ? asset('/upload/users/' . Auth::user()->image) : 'https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg?format=1500w' }}"
                                                            alt="" class="w-100 h-100">
                                                    </div>
                                                </div>
                                                <br>
                                                <input type="file" name="image" class="form-control" id="image"
                                                    onchange="previewImage()">
                                                <canvas id="preview_canvas"
                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                <img id="preview_image" src="#" alt="Preview Logo"
                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                <script>
                                                    function previewImage() {
                                                        const previewCanvas = document.getElementById('preview_canvas');
                                                        const previewImage = document.getElementById('preview_image');
                                                        const fileInput = document.getElementById('image');
                                                        const file = fileInput.files[0];
                                                        const reader = new FileReader();

                                                        reader.onload = function(e) {
                                                            const img = new Image();
                                                            img.src = e.target.result;

                                                            img.onload = function() {
                                                                const canvasContext = previewCanvas.getContext('2d');
                                                                const maxWidth = 100;
                                                                const scaleFactor = maxWidth / img.width;
                                                                const newHeight = img.height * scaleFactor;

                                                                previewCanvas.width = maxWidth;
                                                                previewCanvas.height = newHeight;
                                                                canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);

                                                                previewCanvas.style.display = 'block';
                                                                previewImage.style.display = 'none';
                                                            };
                                                        };

                                                        if (file) {
                                                            reader.readAsDataURL(file);
                                                        } else {
                                                            previewImage.src = '';
                                                            previewCanvas.style.display = 'none';
                                                        }
                                                    }
                                                </script>
                                            </div>

                                            <h4 class="mt-3"><i class="fa fa-user-circle" style="color: #00923f;"></i>
                                                Data Diri</h4>
                                            <!-- Data Diri -->
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label"><strong>Nama:</strong> <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                    placeholder="Nama" value="{{ old('name', $user->name ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="user" class="form-label"><strong>User:</strong> <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="user" id="user" class="form-control"
                                                    placeholder="User" value="{{ old('user', $user->user ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label"><strong>Email:</strong> <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" name="email" id="email" class="form-control"
                                                    placeholder="Email" value="{{ old('email', $user->email ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="phone_number" class="form-label"><strong>Nomor
                                                        Telepon:</strong></label>
                                                <input type="text" name="phone_number" id="phone_number"
                                                    class="form-control" placeholder="Nomor Telepon"
                                                    value="{{ old('phone_number', $user->phone_number ?? '') }}">
                                            </div>

                                            <!-- Password -->
                                            <div class="col-md-6 mb-3">
                                                <label for="password" class="form-label"><strong>Password:</strong> <span
                                                        class="text-danger">*</span></label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control" placeholder="Password">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="password_confirmation" class="form-label"><strong>Konfirmasi
                                                        Password:</strong> <span class="text-danger">*</span></label>
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation" class="form-control"
                                                    placeholder="Konfirmasi Password">
                                            </div>
                                            <!-- Tempat dan Tanggal Lahir -->
                                            <div class="col-md-6 mb-3">
                                                <label for="birth_place" class="form-label"><strong>Tempat Lahir:</strong>
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" name="birth_place" id="birth_place"
                                                    class="form-control" placeholder="Tempat Lahir"
                                                    value="{{ old('birth_place', $user->birth_place ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="birth_date" class="form-label"><strong>Tanggal Lahir:</strong>
                                                    <span class="text-danger">*</span></label>
                                                <input type="date" name="birth_date" id="birth_date"
                                                    class="form-control"
                                                    value="{{ old('birth_date', $user->birth_date ?? '') }}">
                                            </div>

                                              @can('user-access')
                                                <!-- Roles -->
                                            <!-- Roles -->
                                            <div class="col-md-12 mb-3">
                                                <label for="roles" class="form-label"><strong>Role:</strong> <span
                                                        class="text-danger">*</span></label>
                                                <select name="roles[]" id="roles" class="select2 form-control"
                                                    style="width: 100%;" required>
                                                    <option></option>
                                                    @foreach ($roles as $value => $label)
                                                        <option value="{{ $value }}"
                                                            {{ in_array($value, old('roles', $userRoles ?? [])) ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endcan

                                            <!-- Alamat Sesuai KTP -->
                                            <h4 class="mt-3"><i class="fa fa-address-card" style="color: #00923f;"></i>
                                                Alamat Sesuai KTP</h4>
                                            <div class="col-md-12 mb-3">
                                                <label for="address_by_card" class="form-label"><strong>Alamat
                                                        Jalan:</strong></label>
                                                <textarea name="address_by_card" id="address_by_card" class="form-control" placeholder="Alamat lengkap">{{ old('address_by_card', $user->address_by_card ?? '') }}</textarea>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="rt_rw" class="form-label"><strong>RT/RW:</strong></label>
                                                <input type="text" name="rt_rw" id="rt_rw" class="form-control"
                                                    placeholder="RT/RW" value="{{ old('rt_rw', $user->rt_rw ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="subdistrict"
                                                    class="form-label"><strong>Desa/Kelurahan:</strong></label>
                                                <input type="text" name="subdistrict" id="subdistrict"
                                                    class="form-control" placeholder="Desa/Kelurahan"
                                                    value="{{ old('subdistrict', $user->subdistrict ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="district"
                                                    class="form-label"><strong>Kecamatan:</strong></label>
                                                <input type="text" name="district" id="district"
                                                    class="form-control" placeholder="Kecamatan"
                                                    value="{{ old('district', $user->district ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="city"
                                                    class="form-label"><strong>Kota/Kabupaten:</strong></label>
                                                <input type="text" name="city" id="city" class="form-control"
                                                    placeholder="Kota/Kabupaten"
                                                    value="{{ old('city', $user->city ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="province"
                                                    class="form-label"><strong>Provinsi:</strong></label>
                                                <input type="text" name="province" id="province"
                                                    class="form-control" placeholder="Provinsi"
                                                    value="{{ old('province', $user->province ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="postal_code" class="form-label"><strong>Kode
                                                        POS:</strong></label>
                                                <input type="text" name="postal_code" id="postal_code"
                                                    class="form-control" placeholder="Kode POS"
                                                    value="{{ old('postal_code', $user->postal_code ?? '') }}">
                                            </div>
                                             
                                        </div>
                                    </div>

                                   





                                </div>


                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary btn-md"><i class="fa fa-save"></i>
                                        Simpan File</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-warning btn-md"><i
                                            class="fa fa-undo"></i>
                                        Kembali</a>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>




    </div>
@endsection

@push('script')
    <script src="{{ asset('template/back/dist/libs/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "--Pilih Role--",
                allowClear: true
            });
        });
    </script>
@endpush
