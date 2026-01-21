@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <style>
        .modal-dialog-scrollable .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-footer {
            position: sticky;
            bottom: 0;
            z-index: 1;
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
                                <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="/">Beranda</a></li>
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

        <section class="datatables">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-lg-12 margin-tb">
                                        @can('privacy-policy-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addPrivacyPolicyModal">
                                                    <i class="fa fa-plus"></i> Tambah Data
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                </div>

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

                                <table id="scroll_hor" class="table border table-striped table-bordered display nowrap"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Urutan Tampilan</th>
                                            <th>Gambar</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($privacyPolicies as $privacyPolicy)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $privacyPolicy->name }}</td>
                                                <td>{{ $privacyPolicy->description ? Str::limit($privacyPolicy->description, 50) : 'N/A' }}</td>
                                                <td>{{ $privacyPolicy->status }}</td>
                                                <td>{{ $privacyPolicy->order_display }}</td>
                                                <td>
                                                    @if ($privacyPolicy->image)
                                                        <a href="{{ asset('upload/privacy_policies/' . $privacyPolicy->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-privacy-policy"
                                                        data-id="{{ $privacyPolicy->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('privacy-policy-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-privacy-policy"
                                                            data-id="{{ $privacyPolicy->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('privacy-policy-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $privacyPolicy->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $privacyPolicy->id }}" method="POST"
                                                            action="{{ route('privacy-policies.destroy', $privacyPolicy->id) }}"
                                                            style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah Privacy Policy -->
                                <div class="modal fade" id="addPrivacyPolicyModal" tabindex="-1"
                                    aria-labelledby="addPrivacyPolicyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-privacy-policy-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addPrivacyPolicyModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Kebijakan Privasi
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="privacy-policy-name" class="form-label">Nama Kebijakan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="privacy-policy-name"
                                                                    name="name" placeholder="Contoh: Kebijakan Privasi 2025" required>
                                                                <div class="invalid-feedback" id="privacy-policy-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="privacy-policy-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="privacy-policy-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="privacy-policy-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="privacy-policy-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="privacy-policy-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="privacy-policy-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="privacy-policy-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="privacy-policy-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="privacy-policy-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="privacy-policy-image" class="form-label">Gambar Kebijakan
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="privacy-policy-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="privacy-policy-image-error"></div>
                                                                <img id="privacy-policy-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="privacy-policy-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="privacy-policy-error-message" class="text-danger small"></div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-save">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Edit Privacy Policy -->
                                <div class="modal fade" id="editPrivacyPolicyModal" tabindex="-1"
                                    aria-labelledby="editPrivacyPolicyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-privacy-policy-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-privacy-policy-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editPrivacyPolicyModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Kebijakan Privasi
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-privacy-policy-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-privacy-policy-name" class="form-label">Nama Kebijakan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-privacy-policy-name"
                                                                    name="name" placeholder="Contoh: Kebijakan Privasi 2025" required>
                                                                <div class="invalid-feedback" id="edit-privacy-policy-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-privacy-policy-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-privacy-policy-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="edit-privacy-policy-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-privacy-policy-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-privacy-policy-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-privacy-policy-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-privacy-policy-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="edit-privacy-policy-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="edit-privacy-policy-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-privacy-policy-image" class="form-label">Gambar Kebijakan
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-privacy-policy-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="edit-privacy-policy-image-error"></div>
                                                                <img id="edit-privacy-policy-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-privacy-policy-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-update">
                                                        <i class="fa fa-save"></i> Update
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Tampil Privacy Policy -->
                                <div class="modal fade" id="showPrivacyPolicyModal" tabindex="-1"
                                    aria-labelledby="showPrivacyPolicyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showPrivacyPolicyModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Kebijakan Privasi
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-privacy-policy-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-privacy-policy-name" class="form-label">Nama Kebijakan</label>
                                                            <input type="text" class="form-control" id="show-privacy-policy-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-privacy-policy-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-privacy-policy-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-privacy-policy-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-privacy-policy-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-privacy-policy-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-privacy-policy-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-privacy-policy-image" class="form-label">Gambar Kebijakan</label>
                                                            <div id="show-privacy-policy-image"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fa fa-undo"></i> Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('script')
    <script src="{{ asset('template/back/dist/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/back/dist/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/back/dist/js/datatable/datatable-basic.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Fungsi generik untuk validasi dan pratinjau gambar
            function validatePrivacyPolicyImageUpload(inputId, errorId, previewId, canvasId, maxSize) {
                const fileInput = document.getElementById(inputId);
                const errorDiv = document.getElementById(errorId);
                const previewImage = document.getElementById(previewId);
                const previewCanvas = document.getElementById(canvasId);
                const file = fileInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png'];
                const maxSizeMB = maxSize / (1024 * 1024);

                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
                previewImage.style.display = 'none';
                previewCanvas.style.display = 'none';
                fileInput.classList.remove('is-invalid');

                if (file) {
                    if (!allowedTypes.includes(file.type)) {
                        errorDiv.textContent = 'File harus berupa JPEG atau PNG.';
                        errorDiv.style.display = 'block';
                        fileInput.classList.add('is-invalid');
                        fileInput.value = '';
                        return;
                    }

                    if (file.size > maxSize) {
                        errorDiv.textContent = `Ukuran file terlalu besar. Maksimum ${maxSizeMB} MB.`;
                        errorDiv.style.display = 'block';
                        fileInput.classList.add('is-invalid');
                        fileInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = new Image();
                        img.src = e.target.result;
                        img.onload = function() {
                            const canvasContext = previewCanvas.getContext('2d');
                            if (!canvasContext) {
                                console.error(`Gagal mendapatkan konteks 2D untuk canvas: ${canvasId}`);
                                errorDiv.textContent = 'Gagal membuat pratinjau gambar.';
                                errorDiv.style.display = 'block';
                                return;
                            }
                            const maxWidth = 100;
                            const scaleFactor = maxWidth / img.width;
                            const newHeight = img.height * scaleFactor;
                            previewCanvas.width = maxWidth;
                            previewCanvas.height = newHeight;
                            canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);
                            previewCanvas.style.display = 'block';
                            previewImage.style.display = 'none';
                        };
                        img.onerror = function() {
                            console.error(`Gagal memuat gambar untuk pratinjau: ${inputId}`);
                            errorDiv.textContent = 'Gagal memuat gambar untuk pratinjau.';
                            errorDiv.style.display = 'block';
                        };
                    };
                    reader.onerror = function() {
                        console.error(`Gagal membaca file: ${inputId}`);
                        errorDiv.textContent = 'Gagal membaca file.';
                        errorDiv.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Binding event untuk modal tambah
            $('#privacy-policy-image').on('change', function() {
                validatePrivacyPolicyImageUpload(
                    'privacy-policy-image',
                    'privacy-policy-image-error',
                    'privacy-policy-image-preview',
                    'privacy-policy-image-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            // Binding event untuk modal edit
            $('#edit-privacy-policy-image').on('change', function() {
                validatePrivacyPolicyImageUpload(
                    'edit-privacy-policy-image',
                    'edit-privacy-policy-image-error',
                    'edit-privacy-policy-image-preview',
                    'edit-privacy-policy-image-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            // Fungsi untuk mengatur tombol loading
            function setButtonLoading(button, isLoading, loadingText = 'Menyimpan...') {
                if (!button || button.length === 0) return;
                if (isLoading) {
                    button.data('original-html', button.html());
                    button.prop('disabled', true).html(
                        `<span class="spinner-border spinner-border-sm"></span> ${loadingText}`);
                } else {
                    const original = button.data('original-html') || '<i class="fa fa-save"></i> Simpan';
                    button.prop('disabled', false).html(original);
                }
            }

            // Fungsi untuk menangani error AJAX
            function handleAjaxError(xhr, target = null) {
                let message = "Terjadi kesalahan.";
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors).map(e => e[0]).join('<br>');
                    if (target) {
                        $(target).html(message);
                        $.each(errors, function(key, value) {
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}-error`).text(value[0]);
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}`).addClass('is-invalid');
                        });
                    }
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

            // Submit form tambah privacy policy
            $('#add-privacy-policy-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#privacy-policy-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('privacy-policies.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            $('#addPrivacyPolicyModal').modal('hide');
                            form[0].reset();
                            $('#privacy-policy-image-preview').hide();
                            $('#privacy-policy-image-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#privacy-policy-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Event untuk tombol edit
            $(document).on('click', '.btn-edit-privacy-policy', function() {
                const privacyPolicyId = $(this).data('id');
                $('#edit-privacy-policy-error-message').html('');
                $('#edit-privacy-policy-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-privacy-policy-image-preview').hide();
                $('#edit-privacy-policy-image-preview-canvas').hide();

                $.ajax({
                    url: `{{ route('privacy-policies.edit', ':id') }}`.replace(':id', privacyPolicyId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.privacyPolicy.id) {
                            $('#edit-privacy-policy-id').val(response.privacyPolicy.id);
                            $('#edit-privacy-policy-name').val(response.privacyPolicy.name);
                            $('#edit-privacy-policy-description').val(response.privacyPolicy.description || '');
                            $('#edit-privacy-policy-status').val(response.privacyPolicy.status);
                            $('#edit-privacy-policy-order-display').val(response.privacyPolicy.order_display);
                            $('#edit-privacy-policy-image').val('');
                            const imageUrl = response.privacyPolicy.image ? `{{ asset('upload/privacy_policies') }}/${response.privacyPolicy.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-privacy-policy-image-preview').attr('src', imageUrl).show();
                                $('#edit-privacy-policy-image-preview-canvas').hide();
                            } else {
                                $('#edit-privacy-policy-image-preview').hide();
                                $('#edit-privacy-policy-image-preview-canvas').hide();
                            }
                            $('#editPrivacyPolicyModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-privacy-policy-error-message');
                    }
                });
            });

            // Submit form edit privacy policy
            $('#edit-privacy-policy-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const privacyPolicyId = $('#edit-privacy-policy-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-privacy-policy-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('privacy-policies.update', ':id') }}`.replace(':id', privacyPolicyId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            $('#editPrivacyPolicyModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-privacy-policy-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Event untuk tombol show
            $(document).on('click', '.btn-show-privacy-policy', function() {
                const privacyPolicyId = $(this).data('id');
                $('#show-privacy-policy-error-message').html('');

                $.ajax({
                    url: `{{ route('privacy-policies.show', ':id') }}`.replace(':id', privacyPolicyId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            $('#show-privacy-policy-name').val(response.name || '');
                            $('#show-privacy-policy-description').val(response.description || 'N/A');
                            $('#show-privacy-policy-status').val(response.status || '');
                            $('#show-privacy-policy-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ? `{{ asset('upload/privacy_policies') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-privacy-policy-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Kebijakan"></a>`
                                );
                            } else {
                                $('#show-privacy-policy-image').html('Tidak ada gambar');
                            }
                            $('#showPrivacyPolicyModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-privacy-policy-error-message');
                    }
                });
            });

            // Fungsi untuk konfirmasi hapus
            window.confirmDelete = function(privacyPolicyId) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('privacy-policies.destroy', ':id') }}`.replace(':id', privacyPolicyId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                handleAjaxError(xhr);
                            }
                        });
                    }
                });
            };
        });
    </script>
@endpush