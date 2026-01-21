@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
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
        .ql-editor {
            min-height: 120px;
            font-size: 14px;
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
                                        @can('destination-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addDestinationModal">
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
                                            <th>Slug</th>
                                            <th>Deskripsi Singkat</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                            <th>Thumbnail</th>
                                            <th>Views</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_destination as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->slug }}</td>
                                                <td>{{ Str::limit($p->short_description, 50) ?? 'Tidak ada' }}</td>
                                                <td>{{ Str::limit($p->location_details, 30) ?? 'Tidak ada' }}</td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->thumbnail)
                                                        <a href="{{ asset('upload/destinations/' . $p->thumbnail) }}" target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->views ?? 0 }}</td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-destination" data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('destination-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-destination" data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('destination-delete')
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST" action="{{ route('destinations.destroy', $p->id) }}" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah Destinasi -->
                                <div class="modal fade" id="addDestinationModal" tabindex="-1" aria-labelledby="addDestinationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-destination-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addDestinationModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Destinasi
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="destination-name" class="form-label">Nama Destinasi <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="destination-name" name="name" placeholder="Contoh: Bromo" required>
                                                                <div class="invalid-feedback" id="destination-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="destination-slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="destination-slug" name="slug" placeholder="Contoh: bromo" required>
                                                                <div class="invalid-feedback" id="destination-slug-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="destination-description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                                                <div id="destination-description-editor"></div>
                                                                <input type="hidden" name="description" id="destination-description" required>
                                                                <div class="invalid-feedback" id="destination-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="destination-short-description" class="form-label">Deskripsi Singkat</label>
                                                                <textarea class="form-control" id="destination-short-description" name="short_description" rows="2" placeholder="Masukkan deskripsi singkat"></textarea>
                                                                <div class="invalid-feedback" id="destination-short-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="destination-location-details" class="form-label">Detail Lokasi</label>
                                                                <textarea class="form-control" id="destination-location-details" name="location_details" rows="3" placeholder="Masukkan detail lokasi, alamat, koordinat, dll"></textarea>
                                                                <div class="invalid-feedback" id="destination-location-details-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="destination-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="destination-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="destination-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="destination-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="destination-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="destination-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="destination-thumbnail" class="form-label">Thumbnail (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="destination-thumbnail" name="thumbnail" accept=".jpg,.jpeg,.png" onchange="validateDestinationImageUpload()">
                                                                <div class="invalid-feedback" id="destination-thumbnail-error"></div>
                                                                <img id="destination-thumbnail-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="destination-thumbnail-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="destination-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Destinasi -->
                                <div class="modal fade" id="editDestinationModal" tabindex="-1" aria-labelledby="editDestinationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-destination-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-destination-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editDestinationModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Destinasi
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-destination-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-destination-name" class="form-label">Nama Destinasi <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-destination-name" name="name" placeholder="Contoh: Bromo" required>
                                                                <div class="invalid-feedback" id="edit-destination-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-destination-slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-destination-slug" name="slug" placeholder="Contoh: bromo" required>
                                                                <div class="invalid-feedback" id="edit-destination-slug-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-destination-description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                                                <div id="edit-destination-description-editor"></div>
                                                                <input type="hidden" name="description" id="edit-destination-description" required>
                                                                <div class="invalid-feedback" id="edit-destination-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-destination-short-description" class="form-label">Deskripsi Singkat</label>
                                                                <textarea class="form-control" id="edit-destination-short-description" name="short_description" rows="2" placeholder="Masukkan deskripsi singkat"></textarea>
                                                                <div class="invalid-feedback" id="edit-destination-short-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-destination-location-details" class="form-label">Detail Lokasi</label>
                                                                <textarea class="form-control" id="edit-destination-location-details" name="location_details" rows="3" placeholder="Masukkan detail lokasi, alamat, koordinat, dll"></textarea>
                                                                <div class="invalid-feedback" id="edit-destination-location-details-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-destination-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-destination-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-destination-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-destination-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="edit-destination-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="edit-destination-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-destination-thumbnail" class="form-label">Thumbnail (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-destination-thumbnail" name="thumbnail" accept=".jpg,.jpeg,.png" onchange="validateEditDestinationImageUpload()">
                                                                <div class="invalid-feedback" id="edit-destination-thumbnail-error"></div>
                                                                <img id="edit-destination-thumbnail-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-destination-thumbnail-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
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

                                <!-- Modal Tampil Destinasi -->
                                <div class="modal fade" id="showDestinationModal" tabindex="-1" aria-labelledby="showDestinationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showDestinationModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Destinasi
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-destination-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-destination-name" class="form-label">Nama Destinasi</label>
                                                            <input type="text" class="form-control" id="show-destination-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-slug" class="form-label">Slug</label>
                                                            <input type="text" class="form-control" id="show-destination-slug" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-description" class="form-label">Deskripsi</label>
                                                            <div id="show-destination-description" class="border p-3" style="min-height: 200px;"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-short-description" class="form-label">Deskripsi Singkat</label>
                                                            <textarea class="form-control" id="show-destination-short-description" rows="2" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-location-details" class="form-label">Detail Lokasi</label>
                                                            <textarea class="form-control" id="show-destination-location-details" rows="3" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-destination-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-views" class="form-label">Jumlah Views</label>
                                                            <input type="text" class="form-control" id="show-destination-views" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-destination-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-destination-thumbnail" class="form-label">Thumbnail</label>
                                                            <div id="show-destination-thumbnail"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        function validateDestinationImageUpload() {
            const fileInput = document.getElementById('destination-thumbnail');
            const errorDiv = document.getElementById('destination-thumbnail-error');
            const previewImage = document.getElementById('destination-thumbnail-preview');
            const previewCanvas = document.getElementById('destination-thumbnail-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/png'];

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
                    errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = '';
                    return;
                }

                if (allowedTypes.includes(file.type)) {
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
                    reader.readAsDataURL(file);
                }
            }
        }

        function validateEditDestinationImageUpload() {
            const fileInput = document.getElementById('edit-destination-thumbnail');
            const errorDiv = document.getElementById('edit-destination-thumbnail-error');
            const previewImage = document.getElementById('edit-destination-thumbnail-preview');
            const previewCanvas = document.getElementById('edit-destination-thumbnail-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/png'];

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
                    errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = '';
                    return;
                }

                if (allowedTypes.includes(file.type)) {
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
                    reader.readAsDataURL(file);
                }
            }
        }

        $(document).ready(function() {
            // Inisialisasi Quill editors
            const quillConfig = {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'image'],
                        [{ 'header': [1, 2, 3, false] }],
                        ['clean']
                    ]
                }
            };

            const addDescriptionEditor = new Quill('#destination-description-editor', quillConfig);
            const editDescriptionEditor = new Quill('#edit-destination-description-editor', quillConfig);

            // Sinkronkan dengan hidden input
            addDescriptionEditor.on('text-change', function() {
                document.getElementById('destination-description').value = addDescriptionEditor.root.innerHTML;
            });

            editDescriptionEditor.on('text-change', function() {
                document.getElementById('edit-destination-description').value = editDescriptionEditor.root.innerHTML;
            });
            // Auto generate slug from name
            $('#destination-name').on('input', function() {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
                $('#destination-slug').val(slug);
            });

            $('#edit-destination-name').on('input', function() {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
                $('#edit-destination-slug').val(slug);
            });

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

            // Submit Tambah Destinasi
            $('#add-destination-form').submit(function(e) {
                e.preventDefault();
                // Sinkronkan Quill content
                document.getElementById('destination-description').value = addDescriptionEditor.root.innerHTML;
                
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#destination-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('destinations.store') }}",
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
                            $('#addDestinationModal').modal('hide');
                            form[0].reset();
                            $('#destination-thumbnail-preview').hide();
                            $('#destination-thumbnail-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#destination-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Edit destinasi
            $(document).on('click', '.btn-edit-destination', function() {
                const destinationId = $(this).data('id');
                $('#edit-destination-error-message').html('');
                $('#edit-destination-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-destination-thumbnail-preview').hide();
                $('#edit-destination-thumbnail-preview-canvas').hide();

                const url = `{{ route('destinations.edit', ':id') }}`.replace(':id', destinationId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#edit-destination-id').val(response.id);
                            $('#edit-destination-name').val(response.name);
                            $('#edit-destination-slug').val(response.slug);
                            editDescriptionEditor.root.innerHTML = response.description || '';
                            $('#edit-destination-short-description').val(response.short_description || '');
                            $('#edit-destination-location-details').val(response.location_details || '');
                            $('#edit-destination-status').val(response.status);
                            $('#edit-destination-order-display').val(response.order_display);
                            $('#edit-destination-thumbnail').val('');

                            const thumbnailUrl = response.thumbnail ?
                                `{{ asset('upload/destinations') }}/${response.thumbnail}` : null;
                            if (thumbnailUrl && /\.(jpg|jpeg|png|webp)$/i.test(thumbnailUrl)) {
                                $('#edit-destination-thumbnail-preview').attr('src', thumbnailUrl).show();
                                $('#edit-destination-thumbnail-preview-canvas').hide();
                            } else {
                                $('#edit-destination-thumbnail-preview').hide();
                                $('#edit-destination-thumbnail-preview-canvas').hide();
                            }

                            $('#editDestinationModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-destination-error-message');
                    }
                });
            });

            // Submit form Edit Destinasi
            $('#edit-destination-form').submit(function(e) {
                e.preventDefault();
                // Sinkronkan Quill content
                document.getElementById('edit-destination-description').value = editDescriptionEditor.root.innerHTML;
                
                const form = $(this);
                const btn = $('#btn-update');
                const destinationId = $('#edit-destination-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-destination-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('destinations.update', ':id') }}`.replace(':id', destinationId),
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
                            $('#editDestinationModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-destination-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Show destinasi
            $(document).on('click', '.btn-show-destination', function() {
                const destinationId = $(this).data('id');
                $('#show-destination-error-message').html('');
                const url = `{{ route('destinations.show', ':id') }}`.replace(':id', destinationId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#show-destination-name').val(response.name || '');
                            $('#show-destination-slug').val(response.slug || '');
                            $('#show-destination-description').html(response.description || 'Tidak ada');
                            $('#show-destination-short-description').val(response.short_description || 'Tidak ada');
                            $('#show-destination-location-details').val(response.location_details || 'Tidak ada');
                            $('#show-destination-status').val(response.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                            $('#show-destination-views').val(response.views || '0');
                            $('#show-destination-order-display').val(response.order_display || '0');
                            
                            const thumbnailUrl = response.thumbnail ?
                                `{{ asset('upload/destinations') }}/${response.thumbnail}` : null;
                            if (thumbnailUrl && /\.(jpg|jpeg|png|webp)$/i.test(thumbnailUrl)) {
                                $('#show-destination-thumbnail').html(
                                    `<a href="${thumbnailUrl}" target="_blank"><img src="${thumbnailUrl}" class="img-fluid" style="max-width: 50%;" alt="Thumbnail Destinasi"></a>`
                                );
                            } else {
                                $('#show-destination-thumbnail').html('Tidak ada thumbnail');
                            }
                            
                            $('#showDestinationModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-destination-error-message');
                    }
                });
            });

            // Konfirmasi Hapus Destinasi
            window.confirmDelete = function(destinationId) {
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
                            url: `{{ route('destinations.destroy', ':id') }}`.replace(':id', destinationId),
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