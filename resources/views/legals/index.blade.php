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
                                        @can('legal-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addLegalModal">
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
                                        @foreach ($legals as $legal)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $legal->name }}</td>
                                                <td>{{ $legal->description ? Str::limit($legal->description, 50) : 'N/A' }}</td>
                                                <td>{{ $legal->status }}</td>
                                                <td>{{ $legal->order_display }}</td>
                                                <td>
                                                    @if ($legal->image)
                                                        <a href="{{ asset('upload/legals/' . $legal->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-legal"
                                                        data-id="{{ $legal->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('legal-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-legal"
                                                            data-id="{{ $legal->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('legal-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $legal->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $legal->id }}" method="POST"
                                                            action="{{ route('legals.destroy', $legal->id) }}"
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

                                <!-- Modal Tambah Legal -->
                                <div class="modal fade" id="addLegalModal" tabindex="-1"
                                    aria-labelledby="addLegalModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-legal-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addLegalModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Legal
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="legal-name" class="form-label">Nama Legal
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="legal-name"
                                                                    name="name" placeholder="Contoh: Dokumen Hukum" required>
                                                                <div class="invalid-feedback" id="legal-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="legal-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="legal-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="legal-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="legal-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="legal-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="legal-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="legal-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="legal-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="legal-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="legal-image" class="form-label">Gambar Legal
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="legal-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="legal-image-error"></div>
                                                                <img id="legal-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="legal-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="legal-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Legal -->
                                <div class="modal fade" id="editLegalModal" tabindex="-1"
                                    aria-labelledby="editLegalModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-legal-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-legal-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editLegalModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Legal
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-legal-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-legal-name" class="form-label">Nama Legal
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-legal-name"
                                                                    name="name" placeholder="Contoh: Dokumen Hukum" required>
                                                                <div class="invalid-feedback" id="edit-legal-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-legal-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-legal-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="edit-legal-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-legal-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-legal-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-legal-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-legal-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="edit-legal-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="edit-legal-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-legal-image" class="form-label">Gambar Legal
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-legal-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="edit-legal-image-error"></div>
                                                                <img id="edit-legal-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-legal-image-preview-canvas"
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

                                <!-- Modal Tampil Legal -->
                                <div class="modal fade" id="showLegalModal" tabindex="-1"
                                    aria-labelledby="showLegalModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showLegalModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Legal
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-legal-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-legal-name" class="form-label">Nama Legal</label>
                                                            <input type="text" class="form-control" id="show-legal-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-legal-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-legal-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-legal-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-legal-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-legal-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-legal-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-legal-image" class="form-label">Gambar Legal</label>
                                                            <div id="show-legal-image"></div>
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
            function validateLegalImageUpload(inputId, errorId, previewId, canvasId, maxSize) {
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
            $('#legal-image').on('change', function() {
                validateLegalImageUpload(
                    'legal-image',
                    'legal-image-error',
                    'legal-image-preview',
                    'legal-image-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            // Binding event untuk modal edit
            $('#edit-legal-image').on('change', function() {
                validateLegalImageUpload(
                    'edit-legal-image',
                    'edit-legal-image-error',
                    'edit-legal-image-preview',
                    'edit-legal-image-preview-canvas',
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

            // Submit form tambah legal
            $('#add-legal-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#legal-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('legals.store') }}",
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
                            $('#addLegalModal').modal('hide');
                            form[0].reset();
                            $('#legal-image-preview').hide();
                            $('#legal-image-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#legal-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Event untuk tombol edit
            $(document).on('click', '.btn-edit-legal', function() {
                const legalId = $(this).data('id');
                $('#edit-legal-error-message').html('');
                $('#edit-legal-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-legal-image-preview').hide();
                $('#edit-legal-image-preview-canvas').hide();

                $.ajax({
                    url: `{{ route('legals.edit', ':id') }}`.replace(':id', legalId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.legal.id) {
                            $('#edit-legal-id').val(response.legal.id);
                            $('#edit-legal-name').val(response.legal.name);
                            $('#edit-legal-description').val(response.legal.description || '');
                            $('#edit-legal-status').val(response.legal.status);
                            $('#edit-legal-order-display').val(response.legal.order_display);
                            $('#edit-legal-image').val('');
                            const imageUrl = response.legal.image ? `{{ asset('upload/legals') }}/${response.legal.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-legal-image-preview').attr('src', imageUrl).show();
                                $('#edit-legal-image-preview-canvas').hide();
                            } else {
                                $('#edit-legal-image-preview').hide();
                                $('#edit-legal-image-preview-canvas').hide();
                            }
                            $('#editLegalModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-legal-error-message');
                    }
                });
            });

            // Submit form edit legal
            $('#edit-legal-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const legalId = $('#edit-legal-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-legal-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('legals.update', ':id') }}`.replace(':id', legalId),
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
                            $('#editLegalModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-legal-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Event untuk tombol show
            $(document).on('click', '.btn-show-legal', function() {
                const legalId = $(this).data('id');
                $('#show-legal-error-message').html('');

                $.ajax({
                    url: `{{ route('legals.show', ':id') }}`.replace(':id', legalId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            $('#show-legal-name').val(response.name || '');
                            $('#show-legal-description').val(response.description || 'N/A');
                            $('#show-legal-status').val(response.status || '');
                            $('#show-legal-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ? `{{ asset('upload/legals') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-legal-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Legal"></a>`
                                );
                            } else {
                                $('#show-legal-image').html('Tidak ada gambar');
                            }
                            $('#showLegalModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-legal-error-message');
                    }
                });
            });

            // Fungsi untuk konfirmasi hapus
            window.confirmDelete = function(legalId) {
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
                            url: `{{ route('legals.destroy', ':id') }}`.replace(':id', legalId),
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