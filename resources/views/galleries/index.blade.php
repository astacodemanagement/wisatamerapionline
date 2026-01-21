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
        .modal-body { padding: 1.5rem; }
        .modal-footer { position: sticky; bottom: 0; z-index: 1; background: #f8f9fa; }
        .preview-container {
            margin-top: 10px;
            max-width: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
        }
        .badge-active { background-color: #28a745; color: white; }
        .badge-nonactive { background-color: #dc3545; color: white; }
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
                            <div class="row mb-3">
                                <div class="col-lg-12 text-start">
                                    @can('gallery-create')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                                            <i class="fa fa-plus"></i> Tambah Gallery
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Berhasil!</strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Gagal!</strong> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <table id="scroll_hor" class="table border table-striped table-bordered display nowrap" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kategori</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Gambar</th>
                                        <th>Deskripsi</th>
                                        <th>Urutan</th>
                                        <th>Status</th>
                                        <th width="280px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($galleries as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $p->category?->name ?? '<em>Tanpa Kategori</em>' }}</td>
                                            <td>{{ $p->name }}</td>
                                            <td>{{ $p->slug }}</td>
                                            <td>
                                                @if ($p->image)
                                                    <a href="{{ asset('upload/galleries/' . $p->image) }}" target="_blank">Lihat</a>
                                                @else
                                                    Tidak ada
                                                @endif
                                            </td>
                                            <td>{{ $p->description ? Str::limit($p->description, 50) : '<em>Tanpa deskripsi</em>' }}</td>
                                            <td>{{ $p->order_display }}</td>
                                            <td>
                                                <span class="badge {{ $p->status == 'active' ? 'badge-active' : 'badge-nonactive' }}">
                                                    {{ ucfirst($p->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-show-gallery" data-id="{{ $p->id }}">
                                                    <i class="fas fa-eye"></i> Show
                                                </button>
                                                @can('gallery-edit')
                                                    <button class="btn btn-success btn-sm btn-edit-gallery" data-id="{{ $p->id }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                @endcan
                                                @can('gallery-delete')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                    <form id="delete-form-{{ $p->id }}" action="{{ route('galleries.destroy', $p->id) }}" method="POST" style="display:none;">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Modal Tambah -->
                            <div class="modal fade" id="addGalleryModal" tabindex="-1" aria-labelledby="addGalleryModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <form id="add-gallery-form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white">Tambah Gallery</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="gallery-error-message" class="text-danger small mb-2"></div>

                                                <div class="mb-3">
                                                    <label class="form-label">Kategori</label>
                                                    <select class="form-select" name="gallery_category_id">
                                                        <option value="">-- Pilih Kategori --</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" id="gallery-name" placeholder="Masukkan nama" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="slug" id="gallery-slug" placeholder="otomatis dari nama" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Gambar (JPG/PNG, max 4MB)</label>
                                                    <input type="file" class="form-control" name="image" id="gallery-image" accept=".jpg,.jpeg,.png"
                                                        onchange="validateGalleryImageUpload()">
                                                    <div class="invalid-feedback" id="gallery-image-error"></div>
                                                    <canvas id="gallery-image-preview-canvas" class="preview-container" style="display:none;"></canvas>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea class="form-control" name="description" rows="3" placeholder="Opsional"></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Urutan Tampilan <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="order_display" value="0" min="0" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="active">Active</option>
                                                        <option value="nonactive">Nonactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="submit" class="btn btn-primary" id="btn-save">
                                                    <i class="fas fa-save"></i> Simpan
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times"></i> Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editGalleryModal" tabindex="-1" aria-labelledby="editGalleryModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <form id="edit-gallery-form" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="id" id="edit-gallery-id">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white">Edit Gallery</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="edit-gallery-error-message" class="text-danger small mb-2"></div>

                                                <div class="mb-3">
                                                    <label class="form-label">Kategori</label>
                                                    <select class="form-select" name="gallery_category_id" id="edit-gallery-category">
                                                        <option value="">-- Pilih Kategori --</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" id="edit-gallery-name" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="slug" id="edit-gallery-slug" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Gambar (JPG/PNG, max 4MB)</label>
                                                    <input type="file" class="form-control" name="image" id="edit-gallery-image" accept=".jpg,.jpeg,.png"
                                                        onchange="validateEditGalleryImageUpload()">
                                                    <div class="invalid-feedback" id="edit-gallery-image-error"></div>
                                                    <canvas id="edit-gallery-image-preview-canvas" class="preview-container" style="display:none;"></canvas>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea class="form-control" name="description" id="edit-gallery-description" rows="3"></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Urutan <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="order_display" id="edit-gallery-order_display" min="0" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="status" id="edit-gallery-status" required>
                                                        <option value="active">Active</option>
                                                        <option value="nonactive">Nonactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="submit" class="btn btn-primary" id="btn-update">
                                                    <i class="fas fa-save"></i> Update
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times"></i> Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Show -->
                            <div class="modal fade" id="showGalleryModal" tabindex="-1" aria-labelledby="showGalleryModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white">Detail Gallery</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="show-gallery-error-message" class="text-danger small mb-2"></div>
                                            <table class="table table-bordered">
                                                <tr><th>Kategori</th><td id="show-gallery-category">-</td></tr>
                                                <tr><th>Nama</th><td id="show-gallery-name">-</td></tr>
                                                <tr><th>Slug</th><td id="show-gallery-slug">-</td></tr>
                                                <tr><th>Gambar</th><td id="show-gallery-image">-</td></tr>
                                                <tr><th>Deskripsi</th><td id="show-gallery-description">-</td></tr>
                                                <tr><th>Urutan</th><td id="show-gallery-order_display">-</td></tr>
                                                <tr><th>Status</th><td id="show-gallery-status">-</td></tr>
                                            </table>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="fas fa-times"></i> Tutup
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
    function generateSlug(text) {
        return text.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function validateGalleryImageUpload() {
        const fileInput = document.getElementById('gallery-image');
        const errorDiv = document.getElementById('gallery-image-error');
        const canvas = document.getElementById('gallery-image-preview-canvas');
        const file = fileInput.files[0];
        const maxSize = 4 * 1024 * 1024;
        const allowed = ['image/jpeg', 'image/png'];

        errorDiv.style.display = 'none'; errorDiv.textContent = '';
        canvas.style.display = 'none';
        fileInput.classList.remove('is-invalid');

        if (!file) return;
        if (!allowed.includes(file.type)) {
            errorDiv.textContent = 'Hanya JPG/PNG'; errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid'); fileInput.value = ''; return;
        }
        if (file.size > maxSize) {
            errorDiv.textContent = 'Maksimal 4MB'; errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid'); fileInput.value = ''; return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                const ctx = canvas.getContext('2d');
                const maxW = 100;
                const scale = maxW / img.width;
                canvas.width = maxW; canvas.height = img.height * scale;
                ctx.drawImage(img, 0, 0, maxW, img.height * scale);
                canvas.style.display = 'block';
            };
        };
        reader.readAsDataURL(file);
    }

    function validateEditGalleryImageUpload() {
        const fileInput = document.getElementById('edit-gallery-image');
        const errorDiv = document.getElementById('edit-gallery-image-error');
        const canvas = document.getElementById('edit-gallery-image-preview-canvas');
        const file = fileInput.files[0];
        const maxSize = 4 * 1024 * 1024;
        const allowed = ['image/jpeg', 'image/png'];

        errorDiv.style.display = 'none'; errorDiv.textContent = '';
        canvas.style.display = 'none';
        fileInput.classList.remove('is-invalid');

        if (!file) return;
        if (!allowed.includes(file.type)) {
            errorDiv.textContent = 'Hanya JPG/PNG'; errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid'); fileInput.value = ''; return;
        }
        if (file.size > maxSize) {
            errorDiv.textContent = 'Maksimal 4MB'; errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid'); fileInput.value = ''; return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                const ctx = canvas.getContext('2d');
                const maxW = 100;
                const scale = maxW / img.width;
                canvas.width = maxW; canvas.height = img.height * scale;
                ctx.drawImage(img, 0, 0, maxW, img.height * scale);
                canvas.style.display = 'block';
            };
        };
        reader.readAsDataURL(file);
    }

    function resetPreview(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        canvas.style.display = 'none';
    }

    $(document).ready(function() {
        $('#gallery-name, #edit-gallery-name').on('input', function() {
            const target = $(this).attr('id').includes('edit') ? '#edit-gallery-slug' : '#gallery-slug';
            $(target).val(generateSlug($(this).val()));
        });

        $('#addGalleryModal, #editGalleryModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('.invalid-feedback').text(''); $('.form-control').removeClass('is-invalid');
            resetPreview('gallery-image-preview-canvas');
            resetPreview('edit-gallery-image-preview-canvas');
        });

        function setButtonLoading(btn, loading, text = 'Menyimpan...') {
            if (loading) {
                btn.data('html', btn.html()).prop('disabled', true).html(`<span class="spinner-border spinner-border-sm"></span> ${text}`);
            } else {
                btn.prop('disabled', false).html(btn.data('html') || btn.html());
            }
        }

        function handleAjaxError(xhr, target) {
            let msg = 'Terjadi kesalahan.';
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                const errors = xhr.responseJSON.errors;
                msg = Object.values(errors).flat().join('<br>');
                if (target) $(target).html(msg);
            }
            Swal.fire({ icon: 'error', title: 'Error', html: msg });
        }

        // === TAMBAH ===
        $('#add-gallery-form').submit(function(e) {
            e.preventDefault();
            const btn = $('#btn-save');
            const formData = new FormData(this);
            setButtonLoading(btn, true);
            $.ajax({
                url: "{{ route('galleries.store') }}",
                type: "POST",
                data: formData,
                processData: false, contentType: false,
                success: res => Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload()),
                error: xhr => handleAjaxError(xhr, '#gallery-error-message'),
                complete: () => setButtonLoading(btn, false)
            });
        });

        // === EDIT ===
        $(document).on('click', '.btn-edit-gallery', function() {
            const id = $(this).data('id');
            $.get("{{ route('galleries.edit', ':id') }}".replace(':id', id), res => {
                $('#edit-gallery-id').val(res.id);
                $('#edit-gallery-category').val(res.gallery_category_id ?? '');
                $('#edit-gallery-name').val(res.name);
                $('#edit-gallery-slug').val(res.slug);
                $('#edit-gallery-description').val(res.description ?? '');
                $('#edit-gallery-order_display').val(res.order_display);
                $('#edit-gallery-status').val(res.status);

                if (res.image) {
                    const img = new Image();
                    img.src = `{{ asset('upload/galleries') }}/${res.image}`;
                    img.onload = () => {
                        const canvas = document.getElementById('edit-gallery-image-preview-canvas');
                        const ctx = canvas.getContext('2d');
                        const maxW = 100;
                        const scale = maxW / img.width;
                        canvas.width = maxW; canvas.height = img.height * scale;
                        ctx.drawImage(img, 0, 0, maxW, img.height * scale);
                        canvas.style.display = 'block';
                    };
                }
                $('#editGalleryModal').modal('show');
            }).fail(xhr => handleAjaxError(xhr));
        });

        $('#edit-gallery-form').submit(function(e) {
            e.preventDefault();
            const btn = $('#btn-update');
            const id = $('#edit-gallery-id').val();
            const formData = new FormData(this);
            formData.append('_method', 'PUT');
            setButtonLoading(btn, true, 'Memperbarui...');
            $.ajax({
                url: "{{ route('galleries.update', ':id') }}".replace(':id', id),
                type: "POST",
                data: formData,
                processData: false, contentType: false,
                success: res => Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload()),
                error: xhr => handleAjaxError(xhr, '#edit-gallery-error-message'),
                complete: () => setButtonLoading(btn, false)
            });
        });

        // === SHOW ===
        $(document).on('click', '.btn-show-gallery', function() {
            const id = $(this).data('id');
            $.get("{{ route('galleries.show', ':id') }}".replace(':id', id), res => {
                $('#show-gallery-category').text(res.category?.name || 'Tanpa Kategori');
                $('#show-gallery-name').text(res.name);
                $('#show-gallery-slug').text(res.slug);
                $('#show-gallery-description').text(res.description || 'Tidak ada');
                $('#show-gallery-order_display').text(res.order_display);
                $('#show-gallery-status').html(
                    `<span class="badge ${res.status === 'active' ? 'bg-success' : 'bg-danger'}">
                        ${res.status.charAt(0).toUpperCase() + res.status.slice(1)}
                     </span>`
                );
                $('#show-gallery-image').html(res.image ?
                    `<a href="{{ asset('upload/galleries') }}/${res.image}" target="_blank">
                        <img src="{{ asset('upload/galleries') }}/${res.image}" class="img-fluid" style="max-height:200px;">
                     </a>` : 'Tidak ada'
                );
                $('#showGalleryModal').modal('show');
            }).fail(xhr => handleAjaxError(xhr));
        });

        // === HAPUS ===
        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Yakin?', text: 'Data akan dihapus!', icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Ya, hapus!'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('galleries.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: res => Swal.fire('Terhapus!', res.message, 'success').then(() => location.reload()),
                        error: xhr => handleAjaxError(xhr)
                    });
                }
            });
        };
    });
</script>
@endpush