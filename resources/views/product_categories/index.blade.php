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
                                <li class="breadcrumb-item"><a class="text-muted text-decoration-none"
                                        href="/">Beranda</a></li>
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
                                        @can('product-category-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addCategoryModal">
                                                    <i class="fa fa-plus"></i> Tambah Kategori
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
                                            <th>Nama Kategori</th>
                                            <th>Slug</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th width="200px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_category as $category)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($category->name, 50) }}</td>
                                                <td>{{ $category->slug }}</td>
                                                <td>{!! Str::limit($category->description, 50) !!}</td>
                                                <td>{{ $category->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                <td>
                                                    @if ($category->image)
                                                        <a href="{{ asset('upload/product_categories/' . $category->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $category->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-category"
                                                        data-id="{{ $category->id }}">
                                                        <i class="fa fa-eye"></i> Lihat
                                                    </button>
                                                    @can('product-category-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-category"
                                                            data-id="{{ $category->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('product-category-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $category->id }})">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </button>
                                                        <form id="delete-form-{{ $category->id }}" method="POST"
                                                            action="{{ route('product-categories.destroy', $category->id) }}"
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

                                <!-- Modal Tambah Kategori -->
                                <div class="modal fade" id="addCategoryModal" tabindex="-1"
                                    aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-category-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addCategoryModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Produk
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="category-name" class="form-label">Nama Kategori
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="category-name" name="name"
                                                                    placeholder="Contoh: Elektronik" required>
                                                                <div class="invalid-feedback" id="category-name-error">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="category-slug" class="form-label">Slug Kategori
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="category-slug" name="slug"
                                                                    placeholder="Contoh: elektronik" required>
                                                                <div class="invalid-feedback" id="category-slug-error">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="category-description"
                                                                    class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="category-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="category-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="category-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="category-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="category-status-error">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="category-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="category-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="category-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="category-image" class="form-label">Gambar
                                                                    Kategori (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="category-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateCategoryImageUpload()">
                                                                <div class="invalid-feedback" id="category-image-error">
                                                                </div>
                                                                <img id="category-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="category-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="category-error-message" class="text-danger small"></div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-save">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Edit Kategori -->
                                <div class="modal fade" id="editCategoryModal" tabindex="-1"
                                    aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-category-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-category-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editCategoryModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Kategori Produk
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-category-error-message" class="text-danger small mb-2">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-category-name" class="form-label">Nama
                                                                    Kategori <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-category-name" name="name"
                                                                    placeholder="Contoh: Elektronik" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-category-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-category-slug" class="form-label">Slug
                                                                    Kategori <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-category-slug" name="slug"
                                                                    placeholder="Contoh: elektronik" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-category-slug-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-category-description"
                                                                    class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-category-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-category-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-category-status"
                                                                    class="form-label">Status <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-category-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback"
                                                                    id="edit-category-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-category-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-category-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="edit-category-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-category-image" class="form-label">Gambar
                                                                    Kategori (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="edit-category-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateEditCategoryImageUpload()">
                                                                <div class="invalid-feedback"
                                                                    id="edit-category-image-error"></div>
                                                                <img id="edit-category-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-category-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-update">
                                                        <i class="fa fa-save"></i> Update
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Tampil Kategori -->
                                <div class="modal fade" id="showCategoryModal" tabindex="-1"
                                    aria-labelledby="showCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showCategoryModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Kategori Produk
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-category-error-message" class="text-danger small mb-2">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-category-name" class="form-label">Nama
                                                                Kategori</label>
                                                            <input type="text" class="form-control"
                                                                id="show-category-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-category-slug" class="form-label">Slug
                                                                Kategori</label>
                                                            <input type="text" class="form-control"
                                                                id="show-category-slug" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-category-description"
                                                                class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-category-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-category-status"
                                                                class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                id="show-category-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-category-order-display"
                                                                class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-category-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-category-image" class="form-label">Gambar
                                                                Kategori</label>
                                                            <div id="show-category-image"></div>
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
        function validateCategoryImageUpload() {
            const fileInput = document.getElementById('category-image');
            const errorDiv = document.getElementById('category-image-error');
            const previewImage = document.getElementById('category-image-preview');
            const previewCanvas = document.getElementById('category-image-preview-canvas');
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

                // Pratinjau untuk JPEG, PNG, atau WEBP
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

        function validateEditCategoryImageUpload() {
            const fileInput = document.getElementById('edit-category-image');
            const errorDiv = document.getElementById('edit-category-image-error');
            const previewImage = document.getElementById('edit-category-image-preview');
            const previewCanvas = document.getElementById('edit-category-image-preview-canvas');
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
                    errorDiv.textContent = 'File harus berupa JPEG atau PNG ';
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

                // Pratinjau untuk JPEG, PNG, atau WEBP
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

            $('#category-name').on('input', function() {
                const name = $(this).val();
                const slug = generateSlug(name);
                $('#category-slug').val(slug);
            });

            $('#edit-category-name').on('input', function() {
                const name = $(this).val();
                const slug = generateSlug(name);
                $('#edit-category-slug').val(slug);
            });

            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '') // Hapus karakter non-alfanumerik kecuali spasi dan tanda hubung
                    .replace(/\s+/g, '-') // Ganti spasi dengan tanda hubung
                    .replace(/-+/g, '-'); // Ganti beberapa tanda hubung berurutan dengan satu tanda hubung
            }


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
                        // Tampilkan error pada field spesifik
                        $.each(errors, function(key, value) {
                            $(`#${target.replace('#', '')}-${key}-error`).text(value[0]);
                            $(`#${target.replace('#', '')}-${key}`).addClass('is-invalid');
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

            // Submit Tambah Layanan
            $('#add-category-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#category-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('product-categories.store') }}",
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
                            $('#addCategoryModal').modal('hide');
                            form[0].reset();
                            $('#category-image-preview').hide();
                            $('#category-image-preview-canvas').hide();
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#category-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit: ambil data dan tampilkan modal edit
            $(document).on('click', '.btn-edit-category', function() {
                const categoryId = $(this).data('id');
                $('#edit-category-error-message').html('');
                $('#edit-category-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-category-image-preview').hide();
                $('#edit-category-image-preview-canvas').hide();

                $.ajax({
                    url: `{{ route('product-categories.edit', ':id') }}`.replace(':id',
                        categoryId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#edit-category-id').val(response.id);
                            $('#edit-category-name').val(response.name);
                            $('#edit-category-slug').val(response.slug);
                            $('#edit-category-description').val(response.description);
                            $('#edit-category-status').val(response.status);
                            $('#edit-category-order-display').val(response.order_display);
                            $('#edit-category-image').val('');
                            const imageUrl = response.image ?
                                `{{ asset('upload/product_categories') }}/${response.image}` :
                                null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-category-image-preview').attr('src', imageUrl).show();
                                $('#edit-category-image-preview-canvas').hide();
                            } else {
                                $('#edit-category-image-preview').hide();
                                $('#edit-category-image-preview-canvas').hide();
                            }
                            $('#editCategoryModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-category-error-message');
                    }
                });
            });

            // Submit form Edit Layanan
            $('#edit-category-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const categoryId = $('#edit-category-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-category-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('product-categories.update', ':id') }}`.replace(':id',
                        categoryId),
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
                            $('#editCategoryModal').modal('hide');
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-category-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Klik tombol Show: ambil data dan tampilkan modal show
            $(document).on('click', '.btn-show-category', function() {
                const categoryId = $(this).data('id');
                $('#show-category-error-message').html('');

                $.ajax({
                    url: `{{ route('product-categories.show', ':id') }}`.replace(':id',
                        categoryId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            $('#show-category-name').val(response.name || '');
                            $('#show-category-slug').val(response.slug || '');
                            $('#show-category-description').val(response.description || '');
                            $('#show-category-status').val(response.status === 'active' ?
                                'Active' : 'Nonactive');
                            $('#show-category-order-display').val(response.order_display ||
                            '0');
                            const imageUrl = response.image ?
                                `{{ asset('upload/product_categories') }}/${response.image}` :
                                null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-category-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Layanan"></a>`
                                );
                            } else {
                                $('#show-category-image').html('Tidak ada gambar');
                            }
                            $('#showCategoryModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-category-error-message');
                    }
                });
            });

            // Konfirmasi Hapus Layanan
            window.confirmDelete = function(categoryId) {
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
                            url: `{{ route('product-categories.destroy', ':id') }}`.replace(
                                ':id',
                                categoryId),
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
                                    window.location
                                        .reload(); // Tetap reload DataTables untuk delete
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
