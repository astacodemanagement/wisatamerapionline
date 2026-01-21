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
                                        @can('team-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addTeamModal">
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
                                            <th>Posisi</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Urutan Tampilan</th>
                                            <th>Gambar</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($teams as $team)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $team->name }}</td>
                                                <td>{{ $team->position }}</td>
                                                 <td>{{ $team->description ? Str::limit($team->description, 50) : 'N/A' }}</td>
                                              
                                                <td>{{ $team->status }}</td>
                                                <td>{{ $team->order_display }}</td>
                                                <td>
                                                    @if ($team->image)
                                                        <a href="{{ asset('upload/teams/' . $team->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-team"
                                                        data-id="{{ $team->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('team-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-team"
                                                            data-id="{{ $team->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('team-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $team->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $team->id }}" method="POST"
                                                            action="{{ route('teams.destroy', $team->id) }}"
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

                                <!-- Modal Tambah Team -->
                                <div class="modal fade" id="addTeamModal" tabindex="-1"
                                    aria-labelledby="addTeamModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-team-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addTeamModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Anggota Tim
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="team-name" class="form-label">Nama Anggota Tim
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="team-name"
                                                                    name="name" placeholder="Contoh: John Doe" required>
                                                                <div class="invalid-feedback" id="team-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="team-position" class="form-label">Posisi
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="team-position"
                                                                    name="position" placeholder="Contoh: Manager" required>
                                                                <div class="invalid-feedback" id="team-position-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="team-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="team-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="team-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="team-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="team-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="team-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="team-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="team-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="team-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="team-image" class="form-label">Gambar Anggota Tim
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="team-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="team-image-error"></div>
                                                                <img id="team-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="team-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="team-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Team -->
                                <div class="modal fade" id="editTeamModal" tabindex="-1"
                                    aria-labelledby="editTeamModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-team-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-team-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editTeamModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Anggota Tim
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-team-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-team-name" class="form-label">Nama Anggota Tim
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-team-name"
                                                                    name="name" placeholder="Contoh: John Doe" required>
                                                                <div class="invalid-feedback" id="edit-team-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-team-position" class="form-label">Posisi
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-team-position"
                                                                    name="position" placeholder="Contoh: Manager" required>
                                                                <div class="invalid-feedback" id="edit-team-position-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-team-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-team-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="edit-team-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-team-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-team-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-team-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-team-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="edit-team-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="edit-team-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-team-image" class="form-label">Gambar Anggota Tim
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-team-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="edit-team-image-error"></div>
                                                                <img id="edit-team-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-team-image-preview-canvas"
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

                                <!-- Modal Tampil Team -->
                                <div class="modal fade" id="showTeamModal" tabindex="-1"
                                    aria-labelledby="showTeamModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showTeamModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Anggota Tim
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-team-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-team-name" class="form-label">Nama Anggota Tim</label>
                                                            <input type="text" class="form-control" id="show-team-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-team-position" class="form-label">Posisi</label>
                                                            <input type="text" class="form-control" id="show-team-position" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-team-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-team-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-team-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-team-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-team-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-team-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-team-image" class="form-label">Gambar Anggota Tim</label>
                                                            <div id="show-team-image"></div>
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
            function validateTeamImageUpload(inputId, errorId, previewId, canvasId, maxSize) {
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
            $('#team-image').on('change', function() {
                validateTeamImageUpload(
                    'team-image',
                    'team-image-error',
                    'team-image-preview',
                    'team-image-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            // Binding event untuk modal edit
            $('#edit-team-image').on('change', function() {
                validateTeamImageUpload(
                    'edit-team-image',
                    'edit-team-image-error',
                    'edit-team-image-preview',
                    'edit-team-image-preview-canvas',
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

            // Submit form tambah team
            $('#add-team-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#team-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('teams.store') }}",
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
                            $('#addTeamModal').modal('hide');
                            form[0].reset();
                            $('#team-image-preview').hide();
                            $('#team-image-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#team-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Event untuk tombol edit
            $(document).on('click', '.btn-edit-team', function() {
                const teamId = $(this).data('id');
                $('#edit-team-error-message').html('');
                $('#edit-team-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-team-image-preview').hide();
                $('#edit-team-image-preview-canvas').hide();

                $.ajax({
                    url: `{{ route('teams.edit', ':id') }}`.replace(':id', teamId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.team.id) {
                            $('#edit-team-id').val(response.team.id);
                            $('#edit-team-name').val(response.team.name);
                            $('#edit-team-position').val(response.team.position);
                            $('#edit-team-description').val(response.team.description || '');
                            $('#edit-team-status').val(response.team.status);
                            $('#edit-team-order-display').val(response.team.order_display);
                            $('#edit-team-image').val('');
                            const imageUrl = response.team.image ? `{{ asset('upload/teams') }}/${response.team.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-team-image-preview').attr('src', imageUrl).show();
                                $('#edit-team-image-preview-canvas').hide();
                            } else {
                                $('#edit-team-image-preview').hide();
                                $('#edit-team-image-preview-canvas').hide();
                            }
                            $('#editTeamModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-team-error-message');
                    }
                });
            });

            // Submit form edit team
            $('#edit-team-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const teamId = $('#edit-team-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-team-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('teams.update', ':id') }}`.replace(':id', teamId),
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
                            $('#editTeamModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-team-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Event untuk tombol show
            $(document).on('click', '.btn-show-team', function() {
                const teamId = $(this).data('id');
                $('#show-team-error-message').html('');

                $.ajax({
                    url: `{{ route('teams.show', ':id') }}`.replace(':id', teamId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            $('#show-team-name').val(response.name || '');
                            $('#show-team-position').val(response.position || '');
                            $('#show-team-description').val(response.description || 'N/A');
                            $('#show-team-status').val(response.status || '');
                            $('#show-team-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ? `{{ asset('upload/teams') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-team-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Anggota Tim"></a>`
                                );
                            } else {
                                $('#show-team-image').html('Tidak ada gambar');
                            }
                            $('#showTeamModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-team-error-message');
                    }
                });
            });

            // Fungsi untuk konfirmasi hapus
            window.confirmDelete = function(teamId) {
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
                            url: `{{ route('teams.destroy', ':id') }}`.replace(':id', teamId),
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