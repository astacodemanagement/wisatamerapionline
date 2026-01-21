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
                                        @can('project-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addProjectModal">
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
                                            <th>Nama Proyek</th>
                                            <th>Kategori</th>
                                            <th>Lokasi</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th width="280px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_project as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->category ? $p->category->name : 'Tidak ada' }}</td>
                                                <td>{{ $p->location ?? 'Tidak ada' }}</td>
                                                <td>{{ $p->start_date ? \Carbon\Carbon::parse($p->start_date)->format('d-m-Y') : 'Tidak ada' }}</td>
                                                <td>{{ $p->end_date ? \Carbon\Carbon::parse($p->end_date)->format('d-m-Y') : 'Tidak ada' }}</td>
                                                <td>{{ Str::limit($p->description, 50) }}</td>
                                                <td>{{ $p->status == 'active' ? 'Aktif' : 'Nonaktif' }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/projects/' . $p->image) }}" target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-project" data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('project-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-project" data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('project-delete')
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST" action="{{ route('projects.destroy', $p->id) }}" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah Project -->
                                <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-project-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addProjectModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Proyek
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="project-name" class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="project-name" name="name" placeholder="Contoh: Pembangunan Gedung" required>
                                                                <div class="invalid-feedback" id="project-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-category" class="form-label">Kategori Proyek</label>
                                                                <select class="form-control" id="project-category" name="project_category_id">
                                                                    <option value="">Pilih Kategori</option>
                                                                    @foreach ($data_categories as $category)
                                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="project-category-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-location" class="form-label">Lokasi</label>
                                                                <input type="text" class="form-control" id="project-location" name="location" placeholder="Contoh: Jakarta">
                                                                <div class="invalid-feedback" id="project-location-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-start-date" class="form-label">Tanggal Mulai</label>
                                                                <input type="date" class="form-control" id="project-start-date" name="start_date">
                                                                <div class="invalid-feedback" id="project-start-date-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-end-date" class="form-label">Tanggal Selesai</label>
                                                                <input type="date" class="form-control" id="project-end-date" name="end_date">
                                                                <div class="invalid-feedback" id="project-end-date-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="project-description" name="description" rows="4" placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="project-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="project-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="project-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="project-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="project-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="project-image" class="form-label">Gambar Proyek (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="project-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateProjectImageUpload()">
                                                                <div class="invalid-feedback" id="project-image-error"></div>
                                                                <img id="project-image-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="project-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="project-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Project -->
                                <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-project-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-project-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editProjectModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Proyek
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-project-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-project-name" class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-project-name" name="name" placeholder="Contoh: Pembangunan Gedung" required>
                                                                <div class="invalid-feedback" id="edit-project-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-category" class="form-label">Kategori Proyek</label>
                                                                <select class="form-control" id="edit-project-category" name="project_category_id">
                                                                    <option value="">Pilih Kategori</option>
                                                                    @foreach ($data_categories as $category)
                                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-project-category-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-location" class="form-label">Lokasi</label>
                                                                <input type="text" class="form-control" id="edit-project-location" name="location" placeholder="Contoh: Jakarta">
                                                                <div class="invalid-feedback" id="edit-project-location-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-start-date" class="form-label">Tanggal Mulai</label>
                                                                <input type="date" class="form-control" id="edit-project-start-date" name="start_date">
                                                                <div class="invalid-feedback" id="edit-project-start-date-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-end-date" class="form-label">Tanggal Selesai</label>
                                                                <input type="date" class="form-control" id="edit-project-end-date" name="end_date">
                                                                <div class="invalid-feedback" id="edit-project-end-date-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-project-description" name="description" rows="4" placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="edit-project-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-project-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-project-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="edit-project-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="edit-project-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-project-image" class="form-label">Gambar Proyek (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-project-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateEditProjectImageUpload()">
                                                                <div class="invalid-feedback" id="edit-project-image-error"></div>
                                                                <img id="edit-project-image-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-project-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
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

                                <!-- Modal Tampil Project -->
                                <div class="modal fade" id="showProjectModal" tabindex="-1" aria-labelledby="showProjectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showProjectModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Proyek
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-project-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-project-name" class="form-label">Nama Proyek</label>
                                                            <input type="text" class="form-control" id="show-project-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-category" class="form-label">Kategori Proyek</label>
                                                            <input type="text" class="form-control" id="show-project-category" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-location" class="form-label">Lokasi</label>
                                                            <input type="text" class="form-control" id="show-project-location" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-start-date" class="form-label">Tanggal Mulai</label>
                                                            <input type="text" class="form-control" id="show-project-start-date" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-end-date" class="form-label">Tanggal Selesai</label>
                                                            <input type="text" class="form-control" id="show-project-end-date" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-project-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-project-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-project-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-project-image" class="form-label">Gambar Proyek</label>
                                                            <div id="show-project-image"></div>
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
        function validateProjectImageUpload() {
            const fileInput = document.getElementById('project-image');
            const errorDiv = document.getElementById('project-image-error');
            const previewImage = document.getElementById('project-image-preview');
            const previewCanvas = document.getElementById('project-image-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB dalam bytes
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

        function validateEditProjectImageUpload() {
            const fileInput = document.getElementById('edit-project-image');
            const errorDiv = document.getElementById('edit-project-image-error');
            const previewImage = document.getElementById('edit-project-image-preview');
            const previewCanvas = document.getElementById('edit-project-image-preview-canvas');
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

            $('#add-project-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#project-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('projects.store') }}",
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
                            $('#addProjectModal').modal('hide');
                            form[0].reset();
                            $('#project-image-preview').hide();
                            $('#project-image-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#project-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            $(document).on('click', '.btn-edit-project', function() {
                const projectId = $(this).data('id');
                $('#edit-project-error-message').html('');
                $('#edit-project-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-project-image-preview').hide();
                $('#edit-project-image-preview-canvas').hide();

                const url = `{{ route('projects.edit', ':id') }}`.replace(':id', projectId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#edit-project-id').val(response.id);
                            $('#edit-project-name').val(response.name);
                            $('#edit-project-category').val(response.project_category_id || '');
                            $('#edit-project-location').val(response.location || '');
                            $('#edit-project-start-date').val(response.start_date || '');
                            $('#edit-project-end-date').val(response.end_date || '');
                            $('#edit-project-description').val(response.description || '');
                            $('#edit-project-status').val(response.status);
                            $('#edit-project-order-display').val(response.order_display);
                            $('#edit-project-image').val('');

                            const imageUrl = response.image ?
                                `{{ asset('upload/projects') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-project-image-preview').attr('src', imageUrl).show();
                                $('#edit-project-image-preview-canvas').hide();
                            } else {
                                $('#edit-project-image-preview').hide();
                                $('#edit-project-image-preview-canvas').hide();
                            }

                            $('#editProjectModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-project-error-message');
                    }
                });
            });

            $('#edit-project-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const projectId = $('#edit-project-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-project-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('projects.update', ':id') }}`.replace(':id', projectId),
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
                            $('#editProjectModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-project-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            $(document).on('click', '.btn-show-project', function() {
                const projectId = $(this).data('id');
                $('#show-project-error-message').html('');
                const url = `{{ route('projects.show', ':id') }}`.replace(':id', projectId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#show-project-name').val(response.name || '');
                            $('#show-project-category').val(response.category?.name || 'Tidak ada');
                            $('#show-project-location').val(response.location || 'Tidak ada');
                            $('#show-project-start-date').val(response.start_date ? new Date(response.start_date).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }) : 'Tidak ada');
                            $('#show-project-end-date').val(response.end_date ? new Date(response.end_date).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }) : 'Tidak ada');
                            $('#show-project-description').val(response.description || 'Tidak ada');
                            $('#show-project-status').val(response.status === 'active' ? 'Aktif' : 'Nonaktif');
                            $('#show-project-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ?
                                `{{ asset('upload/projects') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-project-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Project"></a>`
                                );
                            } else {
                                $('#show-project-image').html('Tidak ada gambar');
                            }

                            $('#showProjectModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-project-error-message');
                    }
                });
            });

            window.confirmDelete = function(projectId) {
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
                            url: `{{ route('projects.destroy', ':id') }}`.replace(':id', projectId),
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