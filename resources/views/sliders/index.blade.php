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
                        <img src="{{ asset('template/back/dist/images/breadcrumb/ChatBc.png') }}" alt="" class="img-fluid mb-n4">
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
                                        @can('slider-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addSliderModal">
                                                    <i class="fa fa-plus"></i> Tambah Data
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                </div>

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Berhasil!</strong> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Gagal!</strong> {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <table id="scroll_hor" class="table border table-striped table-bordered display nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama</th>
                                            <th>Deskripsi</th>
                                            <th>Link</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_slider as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ Str::limit($p->description, 50) }}</td>
                                                <td>{{ $p->link ?? 'Tidak ada' }}</td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/sliders/' . $p->image) }}" target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-slider" data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('slider-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-slider" data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('slider-delete')
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST" action="{{ route('sliders.destroy', $p->id) }}" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah Slider -->
                                <div class="modal fade" id="addSliderModal" tabindex="-1" aria-labelledby="addSliderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-slider-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addSliderModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Slider
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="slider-name" class="form-label">Nama Slider <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="slider-name" name="name" placeholder="Contoh: Slider 1" required>
                                                                <div class="invalid-feedback" id="slider-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="slider-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="slider-description" name="description" rows="4" placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="slider-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="slider-link" class="form-label">Link</label>
                                                                <input type="text" class="form-control" id="slider-link" name="link" placeholder="Contoh: https://example.com">
                                                                <div class="invalid-feedback" id="slider-link-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="slider-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="slider-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="slider-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="slider-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="slider-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="slider-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="slider-image" class="form-label">Gambar Slider (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="slider-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateSliderImageUpload()">
                                                                <div class="invalid-feedback" id="slider-image-error"></div>
                                                                <img id="slider-image-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="slider-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="slider-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Slider -->
                                <div class="modal fade" id="editSliderModal" tabindex="-1" aria-labelledby="editSliderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-slider-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-slider-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editSliderModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Slider
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-slider-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-slider-name" class="form-label">Nama Slider <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-slider-name" name="name" placeholder="Contoh: Slider 1" required>
                                                                <div class="invalid-feedback" id="edit-slider-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-slider-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-slider-description" name="description" rows="4" placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="edit-slider-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-slider-link" class="form-label">Link</label>
                                                                <input type="text" class="form-control" id="edit-slider-link" name="link" placeholder="Contoh: https://example.com">
                                                                <div class="invalid-feedback" id="edit-slider-link-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-slider-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-slider-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-slider-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-slider-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="edit-slider-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="edit-slider-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-slider-image" class="form-label">Gambar Slider (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-slider-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateEditSliderImageUpload()">
                                                                <div class="invalid-feedback" id="edit-slider-image-error"></div>
                                                                <img id="edit-slider-image-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-slider-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
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

                                <!-- Modal Tampil Slider -->
                                <div class="modal fade" id="showSliderModal" tabindex="-1" aria-labelledby="showSliderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showSliderModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Slider
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-slider-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-slider-name" class="form-label">Nama Slider</label>
                                                            <input type="text" class="form-control" id="show-slider-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-slider-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-slider-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-slider-link" class="form-label">Link</label>
                                                            <input type="text" class="form-control" id="show-slider-link" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-slider-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-slider-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-slider-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-slider-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-slider-image" class="form-label">Gambar Slider</label>
                                                            <div id="show-slider-image"></div>
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
    <script src="{{ asset('template/back/dist/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/back/dist/js/datatable/datatable-basic.init.js') }}"></script>
    <script>
        function validateSliderImageUpload() {
            const fileInput = document.getElementById('slider-image');
            const errorDiv = document.getElementById('slider-image-error');
            const previewImage = document.getElementById('slider-image-preview');
            const previewCanvas = document.getElementById('slider-image-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB dalam bytes
            const allowedTypes = ['image/jpeg', 'image/png'];

            // Reset error dan pratinjau
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewImage.style.display = 'none';
            previewCanvas.style.display = 'none';
            fileInput.classList.remove('is-invalid');

            if (file) {
                // Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    errorDiv.textContent = 'File harus berupa JPEG atau PNG.';
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

                // Pratinjau untuk JPEG atau PNG
                if (allowedTypes.includes(file.type)) {
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
                }
            }
        }

        function validateEditSliderImageUpload() {
            const fileInput = document.getElementById('edit-slider-image');
            const errorDiv = document.getElementById('edit-slider-image-error');
            const previewImage = document.getElementById('edit-slider-image-preview');
            const previewCanvas = document.getElementById('edit-slider-image-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB dalam bytes
            const allowedTypes = ['image/jpeg', 'image/png'];

            // Reset error dan pratinjau
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewImage.style.display = 'none';
            previewCanvas.style.display = 'none';
            fileInput.classList.remove('is-invalid');

            if (file) {
                // Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    errorDiv.textContent = 'File harus berupa JPEG atau PNG.';
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

                // Pratinjau untuk JPEG atau PNG
                if (allowedTypes.includes(file.type)) {
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
                }
            }
        }

        $(document).ready(function() {
            // Fungsi spinner tombol (loading state)
            function setButtonLoading(button, isLoading, loadingText = 'Menyimpan...') {
                if (!button || button.length === 0) return;
                if (isLoading) {
                    button.data('original-html', button.html());
                    button.prop('disabled', true).html(
                        `<span class="spinner-border spinner-border-sm"></span> ${loadingText}`);
                } else {
                    const original = button.data('original-html') || '<i class="bi bi-save"></i> Simpan';
                    button.prop('disabled', false).html(original);
                }
            }

            // Fungsi handle error AJAX
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

            // Submit Tambah Slider
            $('#add-slider-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#slider-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('sliders.store') }}",
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
                            $('#addSliderModal').modal('hide');
                            form[0].reset();
                            $('#slider-image-preview').hide();
                            $('#slider-image-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#slider-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit: ambil data dan tampilkan modal edit
            $(document).on('click', '.btn-edit-slider', function() {
                const sliderId = $(this).data('id');
                $('#edit-slider-error-message').html('');
                $('#edit-slider-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-slider-image-preview').hide();
                $('#edit-slider-image-preview-canvas').hide();

                const url = `{{ route('sliders.edit', ':id') }}`.replace(':id', sliderId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#edit-slider-id').val(response.id);
                            $('#edit-slider-name').val(response.name || '');
                            $('#edit-slider-description').val(response.description || '');
                            $('#edit-slider-link').val(response.link || '');
                            $('#edit-slider-status').val(response.status || '');
                            $('#edit-slider-order-display').val(response.order_display || '0');
                            $('#edit-slider-image').val('');

                            const imageUrl = response.image ? `{{ asset('upload/sliders') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-slider-image-preview').attr('src', imageUrl).show();
                                $('#edit-slider-image-preview-canvas').hide();
                            } else {
                                $('#edit-slider-image-preview').hide();
                                $('#edit-slider-image-preview-canvas').hide();
                            }

                            $('#editSliderModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-slider-error-message');
                    }
                });
            });

            // Submit form Edit Slider
            $('#edit-slider-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const sliderId = $('#edit-slider-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-slider-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `

 {{ route('sliders.update', ':id') }}`.replace(':id', sliderId),
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
                            $('#editSliderModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-slider-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Klik tombol Show: ambil data dan tampilkan modal show
            $(document).on('click', '.btn-show-slider', function() {
                const sliderId = $(this).data('id');
                $('#show-slider-error-message').html('');

                const url = `{{ route('sliders.show', ':id') }}`.replace(':id', sliderId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#show-slider-name').val(response.name || '');
                            $('#show-slider-description').val(response.description || '');
                            $('#show-slider-link').val(response.link || '');
                            $('#show-slider-status').val(response.status === 'active' ? 'Active' : 'Nonactive');
                            $('#show-slider-order-display').val(response.order_display || '0');

                            const imageUrl = response.image ? `{{ asset('upload/sliders') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-slider-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Slider"></a>`
                                );
                            } else {
                                $('#show-slider-image').html('Tidak ada gambar');
                            }

                            $('#showSliderModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-slider-error-message');
                    }
                });
            });

            // Konfirmasi Hapus Slider
            window.confirmDelete = function(sliderId) {
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
                            url: `{{ route('sliders.destroy', ':id') }}`.replace(':id', sliderId),
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