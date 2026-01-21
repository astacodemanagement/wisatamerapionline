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
                                        @can('planning-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addPlanningModal">
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
                                            <th>Sub Layanan</th>
                                            <th>Layanan Utama</th>
                                            <th>Deskripsi</th>
                                            <th>Gambar</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($plannings as $planning)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $planning->name }}</td>
                                                <td>{{ $planning->sub_service ? $planning->sub_service->name : 'N/A' }}</td>
                                                <td>{{ $planning->sub_service && $planning->sub_service->service ? $planning->sub_service->service->name : 'N/A' }}</td>
                                                <td>{{ $planning->description ? Str::limit($planning->description, 50) : 'N/A' }}</td>
                                                <td>
                                                    @if ($planning->image)
                                                        <a href="{{ asset('upload/plannings/' . $planning->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-planning"
                                                        data-id="{{ $planning->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('planning-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-planning"
                                                            data-id="{{ $planning->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('planning-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $planning->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $planning->id }}" method="POST"
                                                            action="{{ route('plannings.destroy', $planning->id) }}"
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

                                <!-- Modal Tambah Planning -->
                                <div class="modal fade" id="addPlanningModal" tabindex="-1"
                                    aria-labelledby="addPlanningModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-planning-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addPlanningModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Perencanaan
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="planning-sub-service-id" class="form-label">Sub Layanan
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="planning-sub-service-id"
                                                                    name="sub_service_id" required>
                                                                    <option value="">Pilih Sub Layanan</option>
                                                                    @foreach ($sub_services as $sub_service)
                                                                        <option value="{{ $sub_service->id }}">{{ $sub_service->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="planning-sub-service-id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="planning-name" class="form-label">Nama Perencanaan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="planning-name"
                                                                    name="name" placeholder="Contoh: Rencana Proyek" required>
                                                                <div class="invalid-feedback" id="planning-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="planning-description" class="form-label">Deskripsi
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="planning-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi" required></textarea>
                                                                <div class="invalid-feedback" id="planning-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="planning-image" class="form-label">Gambar Perencanaan
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="planning-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="planning-image-error"></div>
                                                                <img id="planning-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="planning-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="planning-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Planning -->
                                <div class="modal fade" id="editPlanningModal" tabindex="-1"
                                    aria-labelledby="editPlanningModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-planning-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-planning-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editPlanningModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Perencanaan
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-planning-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-planning-sub-service-id" class="form-label">Sub Layanan
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-planning-sub-service-id"
                                                                    name="sub_service_id" required>
                                                                    <option value="">Pilih Sub Layanan</option>
                                                                    @foreach ($sub_services as $sub_service)
                                                                        <option value="{{ $sub_service->id }}">{{ $sub_service->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-planning-sub-service-id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-planning-name" class="form-label">Nama Perencanaan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-planning-name"
                                                                    name="name" placeholder="Contoh: Rencana Proyek" required>
                                                                <div class="invalid-feedback" id="edit-planning-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-planning-description" class="form-label">Deskripsi
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-planning-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi" required></textarea>
                                                                <div class="invalid-feedback" id="edit-planning-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-planning-image" class="form-label">Gambar Perencanaan
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-planning-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="edit-planning-image-error"></div>
                                                                <img id="edit-planning-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-planning-image-preview-canvas"
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

                                <!-- Modal Tampil Planning -->
                                <div class="modal fade" id="showPlanningModal" tabindex="-1"
                                    aria-labelledby="showPlanningModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showPlanningModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Perencanaan
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-planning-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-planning-sub-service-name" class="form-label">Sub Layanan</label>
                                                            <input type="text" class="form-control" id="show-planning-sub-service-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-planning-service-name" class="form-label">Layanan Utama</label>
                                                            <input type="text" class="form-control" id="show-planning-service-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-planning-name" class="form-label">Nama Perencanaan</label>
                                                            <input type="text" class="form-control" id="show-planning-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-planning-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-planning-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-planning-image" class="form-label">Gambar Perencanaan</label>
                                                            <div id="show-planning-image"></div>
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
            function validatePlanningImageUpload(inputId, errorId, previewId, canvasId, maxSize) {
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
            $('#planning-image').on('change', function() {
                validatePlanningImageUpload(
                    'planning-image',
                    'planning-image-error',
                    'planning-image-preview',
                    'planning-image-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            // Binding event untuk modal edit
            $('#edit-planning-image').on('change', function() {
                validatePlanningImageUpload(
                    'edit-planning-image',
                    'edit-planning-image-error',
                    'edit-planning-image-preview',
                    'edit-planning-image-preview-canvas',
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

            // Submit form tambah planning
            $('#add-planning-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#planning-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('plannings.store') }}",
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
                            $('#addPlanningModal').modal('hide');
                            form[0].reset();
                            $('#planning-image-preview').hide();
                            $('#planning-image-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#planning-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Event untuk tombol edit
            $(document).on('click', '.btn-edit-planning', function() {
                const planningId = $(this).data('id');
                $('#edit-planning-error-message').html('');
                $('#edit-planning-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-planning-image-preview').hide();
                $('#edit-planning-image-preview-canvas').hide();

                $.ajax({
                    url: `{{ route('plannings.edit', ':id') }}`.replace(':id', planningId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.planning.id) {
                            $('#edit-planning-id').val(response.planning.id);
                            $('#edit-planning-sub-service-id').val(response.planning.sub_service_id || '');
                            $('#edit-planning-name').val(response.planning.name);
                            $('#edit-planning-description').val(response.planning.description);
                            $('#edit-planning-image').val('');
                            const imageUrl = response.planning.image ? `{{ asset('upload/plannings') }}/${response.planning.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-planning-image-preview').attr('src', imageUrl).show();
                                $('#edit-planning-image-preview-canvas').hide();
                            } else {
                                $('#edit-planning-image-preview').hide();
                                $('#edit-planning-image-preview-canvas').hide();
                            }
                            $('#editPlanningModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-planning-error-message');
                    }
                });
            });

            // Submit form edit planning
            $('#edit-planning-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const planningId = $('#edit-planning-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-planning-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('plannings.update', ':id') }}`.replace(':id', planningId),
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
                            $('#editPlanningModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-planning-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Event untuk tombol show
            $(document).on('click', '.btn-show-planning', function() {
                const planningId = $(this).data('id');
                $('#show-planning-error-message').html('');

                $.ajax({
                    url: `{{ route('plannings.show', ':id') }}`.replace(':id', planningId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            $('#show-planning-sub-service-name').val(response.sub_service ? response.sub_service.name : 'N/A');
                            $('#show-planning-service-name').val(response.sub_service && response.sub_service.service ? response.sub_service.service.name : 'N/A');
                            $('#show-planning-name').val(response.name || '');
                            $('#show-planning-description').val(response.description || '');
                            const imageUrl = response.image ? `{{ asset('upload/plannings') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-planning-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Perencanaan"></a>`
                                );
                            } else {
                                $('#show-planning-image').html('Tidak ada gambar');
                            }
                            $('#showPlanningModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-planning-error-message');
                    }
                });
            });

            // Fungsi untuk konfirmasi hapus
            window.confirmDelete = function(planningId) {
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
                            url: `{{ route('plannings.destroy', ':id') }}`.replace(':id', planningId),
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