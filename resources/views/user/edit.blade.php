@extends('layouts.app')


@push('css')
    <link rel="stylesheet" href="{{ asset('template/back') }}/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/select2/dist/css/select2.min.css') }}">
    <style>
        .nav-tabs .nav-link.active {
            background: linear-gradient(to right, #00923f, #fff000) !important;
            color: white !important;
        }
    </style>

    <style>
        .card-member {
            position: relative;
        }

        .card-member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #cece16;
            /* Gunakan #cece16 sebagai default */
            z-index: -1;
            border-radius: 10px;
        }

        @media print {
            .card-member::before {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: #cece16 !important;
                /* Ubah dari #ffffcc ke #cece16 */
            }

            body * {
                visibility: hidden;
            }

            .card-member,
            .card-member * {
                visibility: visible;
            }

            .card-member {
                position: absolute;
                top: 0;
                left: 0;
                width: 350px;
                height: 200px;
            }

            .col-md-12.mt-3,
            h4,
            span {
                display: none;
            }

            .d-flex {
                display: block !important;
            }

            .card-member+.card-member {
                top: 220px;
            }
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
                            {{-- @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> Ada beberapa masalah dengan data yang Anda masukkan.
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif --}}

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Berhasil!</strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Gagal!</strong> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div id="alertContainer"></div>

                            <form id="userForm" method="POST" action="{{ route('users.update', $data_user->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <!-- Isi form lainnya, termasuk field dokumen -->


                                <ul class="nav nav-tabs" id="userTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="data-diri-tab" data-bs-toggle="tab"
                                            data-bs-target="#data-diri" type="button" role="tab"
                                            aria-controls="data-diri" aria-selected="true">
                                            Data Diri
                                        </button>
                                    </li>
                                    {{-- <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="data-anggota-tab" data-bs-toggle="tab"
                                            data-bs-target="#data-anggota" type="button" role="tab"
                                            aria-controls="data-anggota" aria-selected="false">
                                            Data Anggota
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="dokumen-persyaratan-tab" data-bs-toggle="tab"
                                            data-bs-target="#dokumen-persyaratan" type="button" role="tab"
                                            aria-controls="dokumen-persyaratan" aria-selected="false">
                                            Dokumen Persyaratan
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="kartu-anggota-tab" data-bs-toggle="tab"
                                            data-bs-target="#kartu-anggota" type="button" role="tab"
                                            aria-controls="kartu-anggota" aria-selected="false">
                                            Kartu Anggota
                                        </button>
                                    </li> --}}
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
                                                        <a
                                                            href="{{ $data_user->image ? asset('/upload/users/' . $data_user->image) : 'https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg?format=1500w' }}">
                                                            <img src="{{ $data_user->image ? asset('/upload/users/' . $data_user->image) : 'https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg?format=1500w' }}"
                                                                alt="" class="w-100 h-100">
                                                        </a>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Upload File (Format: PDF,
                                                        JPG,
                                                        JPEG, PNG, Maks: 4MB)</label>
                                                    <input type="file" name="image" class="form-control"
                                                        id="image" accept=".pdf,.jpg,.jpeg,.png"
                                                        onchange="validateImage()">
                                                    <div class="invalid-feedback" id="image_error"></div>
                                                    <img id="preview_image" src="#" alt="Preview Logo"
                                                        style="display: none; max-width: 100%; margin-top: 10px;">
                                                    <canvas id="preview_canvas"
                                                        style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                </div>

                                                <script>
                                                    function validateImage() {
                                                        const fileInput = document.getElementById('image');
                                                        const errorDiv = document.getElementById('image_error');
                                                        const previewImage = document.getElementById('preview_image');
                                                        const previewCanvas = document.getElementById('preview_canvas');
                                                        const file = fileInput.files[0];
                                                        const maxSize = 4 * 1024 * 1024; // 4 MB dalam bytes
                                                        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

                                                        // Reset error dan pratinjau
                                                        errorDiv.style.display = 'none';
                                                        errorDiv.textContent = '';
                                                        previewImage.style.display = 'none';
                                                        previewCanvas.style.display = 'none';
                                                        fileInput.classList.remove('is-invalid');

                                                        if (file) {
                                                            // Validasi tipe file
                                                            if (!allowedTypes.includes(file.type)) {
                                                                errorDiv.textContent = 'File harus berupa PDF, JPEG, atau PNG.';
                                                                errorDiv.style.display = 'block';
                                                                fileInput.classList.add('is-invalid');
                                                                fileInput.value = ''; // Reset input file
                                                                return;
                                                            }

                                                            // Validasi ukuran file
                                                            if (file.size > maxSize) {
                                                                errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';
                                                                errorDiv.style.display = 'block';
                                                                fileInput.classList.add('is-invalid');
                                                                fileInput.value = ''; // Reset input file
                                                                return;
                                                            }

                                                            // Pratinjau hanya untuk JPEG/PNG
                                                            if (file.type === 'image/jpeg' || file.type === 'image/png') {
                                                                const reader = new FileReader();
                                                                reader.onload = function(e) {
                                                                    const img = new Image();
                                                                    img.src = e.target.result;

                                                                    img.onload = function() {
                                                                        const canvasContext = previewCanvas.getContext('2d');
                                                                        const maxWidth = 100; // Lebar maksimum pratinjau
                                                                        const scaleFactor = maxWidth / img.width;
                                                                        const newHeight = img.height * scaleFactor;

                                                                        previewCanvas.width = maxWidth;
                                                                        previewCanvas.height = newHeight;
                                                                        canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);

                                                                        previewCanvas.style.display = 'block';
                                                                        previewImage.style.display = 'none';
                                                                    };
                                                                };
                                                                reader.readAsDataURL(file);
                                                            } else {
                                                                // Untuk PDF, tidak ada pratinjau
                                                                previewImage.src = '';
                                                                previewCanvas.style.display = 'none';
                                                            }
                                                        } else {
                                                            // Jika tidak ada file
                                                            errorDiv.textContent = 'File wajib diunggah.';
                                                            errorDiv.style.display = 'block';
                                                            fileInput.classList.add('is-invalid');
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
                                                    placeholder="Nama" value="{{ old('name', $data_user->name) }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="user" class="form-label"><strong>User:</strong> <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="user" id="user" class="form-control"
                                                    placeholder="User" value="{{ old('user', $data_user->user) }}">
                                                @error('user')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label"><strong>Email:</strong> <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" name="email" id="email" class="form-control"
                                                    placeholder="Email" value="{{ old('email', $data_user->email) }}">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="phone_number" class="form-label"><strong>Nomor
                                                        Telepon:</strong></label>
                                                <input type="text" name="phone_number" id="phone_number"
                                                    class="form-control" placeholder="Nomor Telepon"
                                                    value="{{ old('phone_number', $data_user->phone_number) }}">
                                                @error('phone_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Password -->
                                            <div class="col-md-6 mb-3">
                                                <label for="password"
                                                    class="form-label"><strong>Password:</strong></label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control"
                                                    placeholder="Password (kosongkan jika tidak diubah)">
                                                @error('password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="password_confirmation" class="form-label"><strong>Konfirmasi
                                                        Password:</strong></label>
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation" class="form-control"
                                                    placeholder="Konfirmasi Password">
                                                @error('password_confirmation')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <!-- Tempat dan Tanggal Lahir -->
                                            <div class="col-md-6 mb-3">
                                                <label for="birth_place" class="form-label"><strong>Tempat Lahir:</strong>
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" name="birth_place" id="birth_place"
                                                    class="form-control" placeholder="Tempat Lahir"
                                                    value="{{ old('birth_place', $data_user->birth_place) }}">
                                                @error('birth_place')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="birth_date" class="form-label"><strong>Tanggal Lahir:</strong>
                                                    <span class="text-danger">*</span></label>
                                                <input type="date" name="birth_date" id="birth_date"
                                                    class="form-control"
                                                    value="{{ old('birth_date', $data_user->birth_date) }}">
                                                @error('birth_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>



                                            @can('user-access')
                                                <!-- Roles -->
                                                <div class="col-md-12 mb-3">
                                                    <label for="roles" class="form-label"><strong>Role:</strong> <span
                                                            class="text-danger">*</span></label>
                                                    <select name="roles[]" id="roles" class="select2 form-control"
                                                        style="width: 100%;" multiple>
                                                        <option></option>
                                                        @foreach ($roles as $value => $label)
                                                            <option value="{{ $value }}"
                                                                {{ in_array($value, old('roles', $usersRole)) ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('roles')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            @endcan


                                            
                                            <!-- Alamat Sesuai KTP -->
                                            <h4 class="mt-3"><i class="fa fa-address-card" style="color: #00923f;"></i>
                                                Alamat Sesuai KTP</h4>
                                            <div class="col-md-12 mb-3">
                                                <label for="address_by_card" class="form-label"><strong>Alamat
                                                        Jalan:</strong></label>
                                                <textarea name="address_by_card" id="address_by_card" class="form-control" placeholder="Alamat lengkap">{{ old('address_by_card', $data_user->address_by_card) }}</textarea>
                                                @error('address_by_card')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="rt_rw" class="form-label"><strong>RT/RW:</strong></label>
                                                <input type="text" name="rt_rw" id="rt_rw" class="form-control"
                                                    placeholder="RT/RW" value="{{ old('rt_rw', $data_user->rt_rw) }}">
                                                @error('rt_rw')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="subdistrict"
                                                    class="form-label"><strong>Desa/Kelurahan:</strong></label>
                                                <input type="text" name="subdistrict" id="subdistrict"
                                                    class="form-control" placeholder="Desa/Kelurahan"
                                                    value="{{ old('subdistrict', $data_user->subdistrict) }}">
                                                @error('subdistrict')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="district"
                                                    class="form-label"><strong>Kecamatan:</strong></label>
                                                <input type="text" name="district" id="district"
                                                    class="form-control" placeholder="Kecamatan"
                                                    value="{{ old('district', $data_user->district) }}">
                                                @error('district')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="city"
                                                    class="form-label"><strong>Kota/Kabupaten:</strong></label>
                                                <input type="text" name="city" id="city" class="form-control"
                                                    placeholder="Kota/Kabupaten"
                                                    value="{{ old('city', $data_user->city) }}">
                                                @error('city')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="province"
                                                    class="form-label"><strong>Provinsi:</strong></label>
                                                <input type="text" name="province" id="province"
                                                    class="form-control" placeholder="Provinsi"
                                                    value="{{ old('province', $data_user->province) }}">
                                                @error('province')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="postal_code" class="form-label"><strong>Kode
                                                        POS:</strong></label>
                                                <input type="text" name="postal_code" id="postal_code"
                                                    class="form-control" placeholder="Kode POS"
                                                    value="{{ old('postal_code', $data_user->postal_code) }}">
                                                @error('postal_code')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


 
                                        </div>
                                    </div>
 


                                </div>

                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary" id="submitButton">
                                        <i class="fa fa-save"></i> Simpan Perubahan
                                    </button>
                                    @can('user-access')
                                        <a href="{{ route('users.index') }}" class="btn btn-warning">
                                            <i class="fa fa-undo"></i> Kembali
                                        </a>
                                    @endcan
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/datatable/datatable-basic.init.js"></script>


    <script>
       
        function setButtonLoading(button, isLoading, loadingText = 'Menyimpan...') {
            if (!button || button.length === 0) return;
            if (isLoading) {
                button.data('original-html', button.html());
                button.prop('disabled', true).html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${loadingText}`
                    );
            } else {
                const original = button.data('original-html') || '<i class="fa fa-save"></i> Simpan Perubahan';
                button.prop('disabled', false).html(original);
            }
        }

      
        function handleAjaxError(xhr, target = null) {
            let message = "Terjadi kesalahan.";
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).map(e => e[0]).join('<br>');
                if (target) $(target).html(message);
            } else if (xhr.status === 403) {
                message = "Anda tidak memiliki izin.";
                if (target) $(target).html(message);
            } else if (xhr.responseJSON?.error) {
                message = xhr.responseJSON.error;
                if (target) $(target).html(message);
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: message,
                confirmButtonText: 'OK'
            });
        }

     
        $(document).ready(function() {
            $('#userForm').on('submit', function(e) {
                e.preventDefault();  

                const $form = $(this);
                const $submitButton = $('#submitButton');
                const formData = new FormData(this);  

          
                setButtonLoading($submitButton, true);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,  
                    contentType: false,  
                    success: function(response) {
                        
                        setButtonLoading($submitButton, false);

                      
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data berhasil disimpan!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                             
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                      
                        setButtonLoading($submitButton, false);

                       
                        handleAjaxError(xhr);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            console.log('jQuery dan Select2 dimuat');
            $('.select2').select2({
                placeholder: "--Pilih Role--",
                allowClear: true
            });

            
        });
    </script>
  
@endpush
