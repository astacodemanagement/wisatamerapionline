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
            background: #fff;
        }
        .invalid-feedback {
            display: none;
        }
        .is-invalid + .invalid-feedback {
            display: block;
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
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    @can('job_vacancy-create')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVacancyModal">
                                            <i class="fa fa-plus"></i> Tambah Lowongan
                                        </button>
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
                                        <th>Status</th>
                                        <th>Gambar</th>
                                        <th>Urutan</th>
                                        <th width="280px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_vacancy as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $p->name }}</td>
                                            <td>{{ Str::limit($p->description, 50) }}</td>
                                            <td>
                                                <span class="badge {{ $p->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($p->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($p->image)
                                                    <a href="{{ asset('upload/job_vacancies/' . $p->image) }}" target="_blank" class="text-primary">
                                                        Lihat Gambar
                                                    </a>
                                                @else
                                                    <span class="text-muted">Tidak ada</span>
                                                @endif
                                            </td>
                                            <td>{{ $p->order_display }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-show-vacancy" data-id="{{ $p->id }}" title="Lihat Detail">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @can('job_vacancy-edit')
                                                    <button class="btn btn-success btn-sm btn-edit-vacancy" data-id="{{ $p->id }}" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                @endcan
                                                @can('job_vacancy-delete')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})" title="Hapus">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $p->id }}" method="POST" action="{{ route('job_vacancies.destroy', $p->id) }}" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Modal Tambah Lowongan -->
                            <div class="modal fade" id="addVacancyModal" tabindex="-1" aria-labelledby="addVacancyModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <form id="add-vacancy-form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="addVacancyModalLabel">
                                                    <i class="bi bi-plus-circle me-2"></i> Tambah Lowongan Kerja
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="vacancy-error-message" class="text-danger small mb-3"></div>

                                                <div class="mb-3">
                                                    <label for="vacancy-name" class="form-label">Nama Lowongan <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="vacancy-name" name="name" placeholder="Contoh: Front-end Developer" required>
                                                    <div class="invalid-feedback" id="vacancy-name-error"></div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="vacancy-description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="vacancy-description" name="description" rows="6" placeholder="Tulis deskripsi lengkap lowongan..." required></textarea>
                                                    <div class="invalid-feedback" id="vacancy-description-error"></div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="vacancy-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="vacancy-status" name="status" required>
                                                        <option value="active">Aktif</option>
                                                        <option value="nonactive">Tidak Aktif</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="vacancy-status-error"></div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="vacancy-order-display" class="form-label">Urutan Tampilan</label>
                                                    <input type="number" class="form-control" id="vacancy-order-display" name="order_display" value="0" min="0">
                                                    <div class="invalid-feedback" id="vacancy-order-display-error"></div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="vacancy-image" class="form-label">Gambar (JPG, JPEG, PNG)</label>
                                                    <input type="file" class="form-control" id="vacancy-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateVacancyImageUpload()">
                                                    <div class="invalid-feedback" id="vacancy-image-error"></div>
                                                    <canvas id="vacancy-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px;"></canvas>
                                                </div>
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

                            <!-- Modal Edit Lowongan -->
                            <div class="modal fade" id="editVacancyModal" tabindex="-1" aria-labelledby="editVacancyModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <form id="edit-vacancy-form" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" id="edit-vacancy-id" name="id">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="editVacancyModalLabel">
                                                    <i class="bi bi-pencil-square me-2"></i> Edit Lowongan Kerja
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="edit-vacancy-error-message" class="text-danger small mb-3"></div>

                                                <div class="mb-3">
                                                    <label for="edit-vacancy-name" class="form-label">Nama Lowongan <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edit-vacancy-name" name="name" required>
                                                    <div class="invalid-feedback" id="edit-vacancy-name-error"></div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit-vacancy-description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="edit-vacancy-description" name="description" rows="6" required></textarea>
                                                    <div class="invalid-feedback" id="edit-vacancy-description-error"></div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit-vacancy-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="edit-vacancy-status" name="status" required>
                                                        <option value="active">Aktif</option>
                                                        <option value="nonactive">Tidak Aktif</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit-vacancy-order-display" class="form-label">Urutan Tampilan</label>
                                                    <input type="number" class="form-control" id="edit-vacancy-order-display" name="order_display" min="0">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit-vacancy-image" class="form-label">Gambar Baru (JPG, JPEG, PNG)</label>
                                                    <input type="file" class="form-control" id="edit-vacancy-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateEditVacancyImageUpload()">
                                                    <div class="invalid-feedback" id="edit-vacancy-image-error"></div>
                                                    <div id="edit-current-image" class="mt-2"></div>
                                                    <canvas id="edit-vacancy-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px;"></canvas>
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

                            <!-- Modal Detail Lowongan -->
                            <div class="modal fade" id="showVacancyModal" tabindex="-1" aria-labelledby="showVacancyModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white" id="showVacancyModalLabel">
                                                <i class="bi bi-eye me-2"></i> Detail Lowongan Kerja
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Lowongan</label>
                                                        <input type="text" class="form-control" id="show-vacancy-name" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea class="form-control" id="show-vacancy-description" rows="6" readonly></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <input type="text" class="form-control" id="show-vacancy-status" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Urutan Tampilan</label>
                                                        <input type="text" class="form-control" id="show-vacancy-order-display" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Gambar</label>
                                                        <div id="show-vacancy-image"></div>
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
    // Validasi Upload Gambar (Tambah)
    function validateVacancyImageUpload() {
        const fileInput = document.getElementById('vacancy-image');
        const errorDiv = document.getElementById('vacancy-image-error');
        const previewCanvas = document.getElementById('vacancy-image-preview-canvas');
        const file = fileInput.files[0];
        const maxSize = 4 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png'];

        errorDiv.style.display = 'none';
        previewCanvas.style.display = 'none';
        fileInput.classList.remove('is-invalid');

        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            errorDiv.textContent = 'File harus JPEG atau PNG.';
            errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid');
            fileInput.value = '';
            return;
        }
        if (file.size > maxSize) {
            errorDiv.textContent = 'Ukuran maksimum 4MB.';
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
                const ctx = previewCanvas.getContext('2d');
                const maxW = 120;
                const scale = maxW / img.width;
                previewCanvas.width = maxW;
                previewCanvas.height = img.height * scale;
                ctx.drawImage(img, 0, 0, maxW, img.height * scale);
                previewCanvas.style.display = 'block';
            };
        };
        reader.readAsDataURL(file);
    }

    // Validasi Upload Gambar (Edit)
    function validateEditVacancyImageUpload() {
        const fileInput = document.getElementById('edit-vacancy-image');
        const errorDiv = document.getElementById('edit-vacancy-image-error');
        const previewCanvas = document.getElementById('edit-vacancy-image-preview-canvas');
        const file = fileInput.files[0];
        const maxSize = 4 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png'];

        errorDiv.style.display = 'none';
        previewCanvas.style.display = 'none';
        fileInput.classList.remove('is-invalid');

        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            errorDiv.textContent = 'File harus JPEG atau PNG.';
            errorDiv.style.display = 'block';
            fileInput.classList.add('is-invalid');
            fileInput.value = '';
            return;
        }
        if (file.size > maxSize) {
            errorDiv.textContent = 'Ukuran maksimum 4MB.';
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
                const ctx = previewCanvas.getContext('2d');
                const maxW = 120;
                const scale = maxW / img.width;
                previewCanvas.width = maxW;
                previewCanvas.height = img.height * scale;
                ctx.drawImage(img, 0, 0, maxW, img.height * scale);
                previewCanvas.style.display = 'block';
            };
        };
        reader.readAsDataURL(file);
    }

    $(document).ready(function() {
        // Loading Button
        function setButtonLoading(btn, loading, text = 'Menyimpan...') {
            if (loading) {
                btn.data('html', btn.html()).prop('disabled', true).html(`<span class="spinner-border spinner-border-sm"></span> ${text}`);
            } else {
                btn.prop('disabled', false).html(btn.data('html') || btn.html());
            }
        }

        // Handle Error
        function handleAjaxError(xhr, target) {
            let msg = "Terjadi kesalahan.";
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                const errors = xhr.responseJSON.errors;
                msg = Object.values(errors).flat().join('<br>');
                if (target) {
                    $(target).html(msg);
                    $.each(errors, (k, v) => {
                        const field = k.replace('.', '-');
                        $(`#${field}-error`).text(v[0]).show();
                        $(`#${field}`).addClass('is-invalid');
                    });
                }
            } else if (xhr.responseJSON?.message) {
                msg = xhr.responseJSON.message;
                if (target) $(target).html(msg);
            }
            Swal.fire({ icon: 'error', title: 'Error', html: msg });
        }

        // === TAMBAH ===
        $('#add-vacancy-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btn-save');
            const data = new FormData(form[0]);

            setButtonLoading(btn, true);
            $('#vacancy-error-message').empty();
            $('.invalid-feedback').hide().text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: "{{ route('job_vacancies.store') }}",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        $('#addVacancyModal').modal('hide');
                        location.reload();
                    });
                },
                error: function(xhr) { handleAjaxError(xhr, '#vacancy-error-message'); },
                complete: () => setButtonLoading(btn, false)
            });
        });

        // === EDIT: Ambil Data ===
        $(document).on('click', '.btn-edit-vacancy', function() {
            const id = $(this).data('id');
            $('#edit-vacancy-error-message').empty();
            $('#edit-vacancy-form')[0].reset();
            $('.invalid-feedback').hide().text('');
            $('.form-control').removeClass('is-invalid');
            $('#edit-vacancy-image-preview-canvas').hide();

            $.ajax({
                url: `{{ route('job_vacancies.edit', ':id') }}`.replace(':id', id),
                type: 'GET',
                success: function(res) {
                    $('#edit-vacancy-id').val(res.id);
                    $('#edit-vacancy-name').val(res.name);
                    $('#edit-vacancy-description').val(res.description);
                    $('#edit-vacancy-status').val(res.status);
                    $('#edit-vacancy-order-display').val(res.order_display);

                    const imgUrl = res.image ? `{{ asset('upload/job_vacancies') }}/${res.image}` : null;
                    if (imgUrl) {
                        $('#edit-current-image').html(`<small class="text-success">Gambar saat ini: </small><a href="${imgUrl}" target="_blank">Lihat</a>`);
                    } else {
                        $('#edit-current-image').html('<small class="text-muted">Tidak ada gambar</small>');
                    }

                    $('#editVacancyModal').modal('show');
                },
                error: function(xhr) { handleAjaxError(xhr, '#edit-vacancy-error-message'); }
            });
        });

        // === EDIT: Submit ===
        $('#edit-vacancy-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btn-update');
            const id = $('#edit-vacancy-id').val();
            const data = new FormData(form[0]);
            data.append('_method', 'PUT');

            setButtonLoading(btn, true, 'Memperbarui...');
            $('#edit-vacancy-error-message').empty();
            $('.invalid-feedback').hide().text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: `{{ route('job_vacancies.update', ':id') }}`.replace(':id', id),
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        $('#editVacancyModal').modal('hide');
                        location.reload();
                    });
                },
                error: function(xhr) { handleAjaxError(xhr, '#edit-vacancy-error-message'); },
                complete: () => setButtonLoading(btn, false)
            });
        });

        // === SHOW ===
        $(document).on('click', '.btn-show-vacancy', function() {
            const id = $(this).data('id');
            $.ajax({
                url: `{{ route('job_vacancies.show', ':id') }}`.replace(':id', id),
                type: 'GET',
                success: function(res) {
                    $('#show-vacancy-name').val(res.name);
                    $('#show-vacancy-description').val(res.description);
                    $('#show-vacancy-status').val(res.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                    $('#show-vacancy-order-display').val(res.order_display);

                    const imgUrl = res.image ? `{{ asset('upload/job_vacancies') }}/${res.image}` : null;
                    $('#show-vacancy-image').html(
                        imgUrl
                            ? `<a href="${imgUrl}" target="_blank"><img src="${imgUrl}" class="img-fluid rounded" style="max-height: 200px;"></a>`
                            : '<span class="text-muted">Tidak ada gambar</span>'
                    );

                    $('#showVacancyModal').modal('show');
                },
                error: function(xhr) { handleAjaxError(xhr); }
            });
        });

        // === HAPUS ===
        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('job_vacancies.destroy', ':id') }}`.replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Dihapus!', res.message, 'success').then(() => location.reload());
                        },
                        error: function(xhr) { handleAjaxError(xhr); }
                    });
                }
            });
        };
    });
</script>
@endpush