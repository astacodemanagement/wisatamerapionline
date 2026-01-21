@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <style>
        /* Pastikan modal-body memiliki scroll dan tombol footer tetap terlihat */
        .modal-dialog-scrollable .modal-body {
            max-height: 60vh;
            /* Batasi tinggi modal-body agar scroll muncul */
            overflow-y: auto;
        }

        /* Optional: Tambahkan padding agar konten tidak terlalu mepet */
        .modal-body {
            padding: 1.5rem;
        }

        /* Pastikan footer tidak ikut scroll */
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
                                        @can('agent-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addAgentModal">
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
                                            <th>Telepon</th>
                                            <th>Kota</th>
                                            <th>Alamat</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_agent as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->phone ?? 'Tidak ada' }}</td>
                                                <td>{{ $p->city ?? 'Tidak ada' }}</td>
                                                <td>{{ $p->address ? Str::limit($p->address, 50) : 'N/A' }}</td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/agents/' . $p->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-agent"
                                                        data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('agent-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-agent"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('agent-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST"
                                                            action="{{ route('agents.destroy', $p->id) }}"
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

                                <!-- Modal Tambah Agent -->
                                <div class="modal fade" id="addAgentModal" tabindex="-1"
                                    aria-labelledby="addAgentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-agent-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addAgentModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Agent
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="agent-name" class="form-label">Nama Agent
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="agent-name"
                                                                    name="name" placeholder="Contoh: John Doe" required>
                                                                <div class="invalid-feedback" id="agent-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="agent-phone" class="form-label">Nomor
                                                                    Telepon</label>
                                                                <input type="text" class="form-control"
                                                                    id="agent-phone" name="phone"
                                                                    placeholder="Contoh: +628123456789">
                                                                <div class="invalid-feedback"
                                                                    id="agent-phone-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="agent-city" class="form-label">Kota</label>
                                                                <input type="text" class="form-control"
                                                                    id="agent-city" name="city"
                                                                    placeholder="Contoh: Jakarta">
                                                                <div class="invalid-feedback"
                                                                    id="agent-city-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="agent-address"
                                                                    class="form-label">Alamat</label>
                                                                <textarea class="form-control" id="agent-address" name="address" rows="4" placeholder="Masukkan alamat"></textarea>
                                                                <div class="invalid-feedback" id="agent-address-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="agent-status" class="form-label">Status <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="agent-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="agent-status-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="agent-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="agent-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="agent-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="agent-image" class="form-label">Gambar Agent</label>
                                                                <input type="file" class="form-control" id="agent-image"
                                                                    name="image" accept="image/*"
                                                                    onchange="validateAgentImageUpload()">
                                                                <div class="invalid-feedback" id="agent-image-error"></div>
                                                                <div class="mt-2">
                                                                    <img id="agent-image-preview" src="#" alt="Pratinjau Gambar"
                                                                        style="display:none; max-width: 100px;" class="img-thumbnail">
                                                                    <canvas id="agent-image-preview-canvas"
                                                                        style="display:none; max-width: 100px;"
                                                                        class="img-thumbnail"></canvas>
                                                                </div>
                                                                <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 4MB.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="agent-error-message" class="text-danger small mt-2"></div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                    <button type="submit" class="btn btn-primary" id="btn-save">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Edit Agent -->
                                <div class="modal fade" id="editAgentModal" tabindex="-1"
                                    aria-labelledby="editAgentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-agent-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-agent-id" name="id">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title text-white" id="editAgentModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Agent
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-agent-name" class="form-label">Nama Agent
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-agent-name"
                                                                    name="name" required>
                                                                <div class="invalid-feedback" id="edit-agent-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-agent-phone" class="form-label">Nomor
                                                                    Telepon</label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-agent-phone" name="phone">
                                                                <div class="invalid-feedback"
                                                                    id="edit-agent-phone-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-agent-city" class="form-label">Kota</label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-agent-city" name="city">
                                                                <div class="invalid-feedback"
                                                                    id="edit-agent-city-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-agent-address"
                                                                    class="form-label">Alamat</label>
                                                                <textarea class="form-control" id="edit-agent-address" name="address" rows="4"></textarea>
                                                                <div class="invalid-feedback" id="edit-agent-address-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-agent-status" class="form-label">Status <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-agent-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-agent-status-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-agent-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-agent-order-display" name="order_display"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="edit-agent-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-agent-image" class="form-label">Gambar Agent</label>
                                                                <input type="file" class="form-control" id="edit-agent-image"
                                                                    name="image" accept="image/*"
                                                                    onchange="validateEditAgentImageUpload()">
                                                                <div class="invalid-feedback" id="edit-agent-image-error"></div>
                                                                <div class="mt-2">
                                                                    <div id="edit-agent-image-current" class="mb-2"></div>
                                                                    <img id="edit-agent-image-preview" src="#" alt="Pratinjau Gambar"
                                                                        style="display:none; max-width: 100px;" class="img-thumbnail">
                                                                    <canvas id="edit-agent-image-preview-canvas"
                                                                        style="display:none; max-width: 100px;"
                                                                        class="img-thumbnail"></canvas>
                                                                </div>
                                                                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="edit-agent-error-message" class="text-danger small mt-2"></div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                    <button type="submit" class="btn btn-success" id="btn-update">
                                                        <i class="fa fa-save"></i> Perbarui
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Show Agent -->
                                <div class="modal fade" id="showAgentModal" tabindex="-1"
                                    aria-labelledby="showAgentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title text-white" id="showAgentModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Agent
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-agent-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-agent-name" class="form-label">Nama Agent</label>
                                                            <input type="text" class="form-control"
                                                                id="show-agent-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-agent-phone" class="form-label">Nomor Telepon</label>
                                                            <input type="text" class="form-control"
                                                                id="show-agent-phone" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-agent-city" class="form-label">Kota</label>
                                                            <input type="text" class="form-control"
                                                                id="show-agent-city" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-agent-address" class="form-label">Alamat</label>
                                                            <textarea class="form-control" id="show-agent-address" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-agent-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                id="show-agent-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-agent-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-agent-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-agent-image" class="form-label">Gambar Agent</label>
                                                            <div id="show-agent-image-container"></div>
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
        function validateAgentImageUpload() {
            const fileInput = document.getElementById('agent-image');
            const errorDiv = document.getElementById('agent-image-error');
            const previewImage = document.getElementById('agent-image-preview');
            const previewCanvas = document.getElementById('agent-image-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB
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
                    };
                };
                reader.readAsDataURL(file);
            }
        }

        function validateEditAgentImageUpload() {
            const fileInput = document.getElementById('edit-agent-image');
            const errorDiv = document.getElementById('edit-agent-image-error');
            const previewImage = document.getElementById('edit-agent-image-preview');
            const previewCanvas = document.getElementById('edit-agent-image-preview-canvas');
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
                    };
                };
                reader.readAsDataURL(file);
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
                    const original = button.data('original-html') || '<i class="fa fa-save"></i> Simpan';
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
                            $(`#${target.replace('#', '')}-${key}`).text(value[0]);
                            $(`[name="${key}"]`).addClass('is-invalid');
                        });
                    }
                } else if (xhr.status === 403) {
                    message = "Anda tidak memiliki izin.";
                } else if (xhr.responseJSON?.error) {
                    message = xhr.responseJSON.error;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: message,
                    confirmButtonText: 'OK'
                });
            }

            // Submit Tambah Agent
            $('#add-agent-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#agent-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('agents.store') }}",
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
                            $('#addAgentModal').modal('hide');
                            form[0].reset();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#agent-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit
            $(document).on('click', '.btn-edit-agent', function() {
                const agentId = $(this).data('id');
                $('#edit-agent-error-message').html('');
                $('#edit-agent-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-agent-image-preview-canvas').hide();

                const url = `{{ route('agents.edit', ':id') }}`.replace(':id', agentId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#edit-agent-id').val(response.id);
                        $('#edit-agent-name').val(response.name);
                        $('#edit-agent-phone').val(response.phone);
                        $('#edit-agent-city').val(response.city);
                        $('#edit-agent-address').val(response.address);
                        $('#edit-agent-status').val(response.status);
                        $('#edit-agent-order-display').val(response.order_display);
                        
                        if (response.image) {
                            $('#edit-agent-image-current').html(
                                `<small class="text-muted d-block">Gambar saat ini:</small><img src="{{ asset('upload/agents') }}/${response.image}" class="img-thumbnail" style="max-width: 100px;">`
                            );
                        } else {
                            $('#edit-agent-image-current').empty();
                        }
                        
                        $('#editAgentModal').modal('show');
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    }
                });
            });

            // Submit Update Agent
            $('#edit-agent-form').submit(function(e) {
                e.preventDefault();
                const agentId = $('#edit-agent-id').val();
                const form = $(this);
                const btn = $('#btn-update');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#edit-agent-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('agents.update', ':id') }}`.replace(':id', agentId),
                    type: "POST", // Use POST with @method('PUT')
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            $('#editAgentModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-agent-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Show
            $(document).on('click', '.btn-show-agent', function() {
                const agentId = $(this).data('id');
                const url = `{{ route('agents.show', ':id') }}`.replace(':id', agentId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#show-agent-name').val(response.name);
                        $('#show-agent-phone').val(response.phone || 'Tidak ada');
                        $('#show-agent-city').val(response.city || 'Tidak ada');
                        $('#show-agent-address').val(response.address || 'Tidak ada');
                        $('#show-agent-status').val(response.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                        $('#show-agent-order-display').val(response.order_display);
                        
                        if (response.image) {
                            const imageUrl = `{{ asset('upload/agents') }}/${response.image}`;
                            $('#show-agent-image-container').html(
                                `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid img-thumbnail" style="max-width: 200px;"></a>`
                            );
                        } else {
                            $('#show-agent-image-container').html('<span class="text-muted">Tidak ada gambar</span>');
                        }
                        
                        $('#showAgentModal').modal('show');
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    }
                });
            });

            // Konfirmasi Hapus
            window.confirmDelete = function(agentId) {
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
                            url: `{{ route('agents.destroy', ':id') }}`.replace(':id', agentId),
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
