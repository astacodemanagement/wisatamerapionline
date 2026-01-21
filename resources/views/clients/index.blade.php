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
                                        @can('client-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addClientModal">
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
                                            <th>Email</th>
                                            <th>Nomor Telepon</th>
                                            <th>Alamat</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_client as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->email }}</td>
                                                <td>{{ $p->phone_number ?? 'Tidak ada' }}</td>
                                                <td>{{ $p->address ? Str::limit($p->address, 50) : 'N/A' }}</td>
                                                <td>{{ Str::limit($p->description, 50) }}</td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/clients/' . $p->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-client"
                                                        data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('client-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-client"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('client-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST"
                                                            action="{{ route('clients.destroy', $p->id) }}"
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

                                <!-- Modal Tambah Client -->
                                <div class="modal fade" id="addClientModal" tabindex="-1"
                                    aria-labelledby="addClientModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-client-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addClientModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Client
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="client-name" class="form-label">Nama Client
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="client-name"
                                                                    name="name" placeholder="Contoh: John Doe" required>
                                                                <div class="invalid-feedback" id="client-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="client-email" class="form-label">Email <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="email" class="form-control"
                                                                    id="client-email" name="email"
                                                                    placeholder="Contoh: john@example.com" required>
                                                                <div class="invalid-feedback" id="client-email-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="client-phone-number" class="form-label">Nomor
                                                                    Telepon</label>
                                                                <input type="text" class="form-control"
                                                                    id="client-phone-number" name="phone_number"
                                                                    placeholder="Contoh: +628123456789">
                                                                <div class="invalid-feedback"
                                                                    id="client-phone-number-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="client-address"
                                                                    class="form-label">Alamat</label>
                                                                <textarea class="form-control" id="client-address" name="address" rows="4" placeholder="Masukkan alamat"></textarea>
                                                                <div class="invalid-feedback" id="client-address-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="client-description"
                                                                    class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="client-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="client-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="client-status" class="form-label">Status <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="client-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="client-status-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="client-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="client-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="client-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="client-image" class="form-label">Gambar Client
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="client-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateClientImageUpload()">
                                                                <div class="invalid-feedback" id="client-image-error">
                                                                </div>
                                                                <img id="client-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="client-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="client-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Client -->
                                <div class="modal fade" id="editClientModal" tabindex="-1"
                                    aria-labelledby="editClientModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-client-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-client-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editClientModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Client
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-client-error-message" class="text-danger small mb-2">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-client-name" class="form-label">Nama
                                                                    Client <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-client-name" name="name"
                                                                    placeholder="Contoh: John Doe" required>
                                                                <div class="invalid-feedback" id="edit-client-name-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-client-email" class="form-label">Email
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control"
                                                                    id="edit-client-email" name="email"
                                                                    placeholder="Contoh: john@example.com" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-client-email-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-client-phone-number"
                                                                    class="form-label">Nomor Telepon</label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-client-phone-number" name="phone_number"
                                                                    placeholder="Contoh: +628123456789">
                                                                <div class="invalid-feedback"
                                                                    id="edit-client-phone-number-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-client-address"
                                                                    class="form-label">Alamat</label>
                                                                <textarea class="form-control" id="edit-client-address" name="address" rows="4"
                                                                    placeholder="Masukkan alamat"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-client-address-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-client-description"
                                                                    class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-client-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-client-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-client-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-client-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback"
                                                                    id="edit-client-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-client-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-client-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="edit-client-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-client-image" class="form-label">Gambar
                                                                    Client (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="edit-client-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateEditClientImageUpload()">
                                                                <div class="invalid-feedback"
                                                                    id="edit-client-image-error"></div>
                                                                <img id="edit-client-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-client-image-preview-canvas"
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

                                <!-- Modal Tampil Client -->
                                <div class="modal fade" id="showClientModal" tabindex="-1"
                                    aria-labelledby="showClientModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showClientModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Client
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-client-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-client-name" class="form-label">Nama
                                                                Client</label>
                                                            <input type="text" class="form-control"
                                                                id="show-client-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-client-email"
                                                                class="form-label">Email</label>
                                                            <input type="email" class="form-control"
                                                                id="show-client-email" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-client-phone-number" class="form-label">Nomor
                                                                Telepon</label>
                                                            <input type="text" class="form-control"
                                                                id="show-client-phone-number" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-client-address"
                                                                class="form-label">Alamat</label>
                                                            <textarea class="form-control" id="show-client-address" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-client-description"
                                                                class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-client-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-client-status"
                                                                class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                id="show-client-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-client-order-display"
                                                                class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-client-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-client-image" class="form-label">Gambar
                                                                Client</label>
                                                            <div id="show-client-image"></div>
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
        function validateClientImageUpload() {
            const fileInput = document.getElementById('client-image');
            const errorDiv = document.getElementById('client-image-error');
            const previewImage = document.getElementById('client-image-preview');
            const previewCanvas = document.getElementById('client-image-preview-canvas');
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

        function validateEditClientImageUpload() {
            const fileInput = document.getElementById('edit-client-image');
            const errorDiv = document.getElementById('edit-client-image-error');
            const previewImage = document.getElementById('edit-client-image-preview');
            const previewCanvas = document.getElementById('edit-client-image-preview-canvas');
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
                        // Tampilkan error pada field spesifik
                        $.each(errors, function(key, value) {
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}-error`).text(value[0]);
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}`).addClass(
                                'is-invalid');
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

            // Submit Tambah Client
            $('#add-client-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#client-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('clients.store') }}",
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
                            $('#addClientModal').modal('hide');
                            form[0].reset();
                            $('#client-image-preview').hide();
                            $('#client-image-preview-canvas').hide();
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#client-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit: ambil data dan tampilkan modal edit
            $(document).on('click', '.btn-edit-client', function() {
                const clientId = $(this).data('id');
                $('#edit-client-error-message').html('');
                $('#edit-client-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-client-image-preview').hide();
                $('#edit-client-image-preview-canvas').hide();

                const url = `{{ route('clients.edit', ':id') }}`.replace(':id', clientId);
                console.log('Request URL:', url); // Debug URL
                console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content')); // Debug CSRF

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Response:', response); // Debug respons
                        if (response && response.id) {
                            $('#edit-client-id').val(response.id);
                            $('#edit-client-name').val(response.name);
                            $('#edit-client-email').val(response.email);
                            $('#edit-client-phone-number').val(response.phone_number || '');
                            $('#edit-client-address').val(response.address || '');
                            $('#edit-client-description').val(response.description || '');
                            $('#edit-client-status').val(response.status);
                            $('#edit-client-order-display').val(response.order_display);
                            $('#edit-client-image').val('');

                            const imageUrl = response.image ?
                                `{{ asset('upload/clients') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-client-image-preview').attr('src', imageUrl).show();
                                $('#edit-client-image-preview-canvas').hide();
                            } else {
                                $('#edit-client-image-preview').hide();
                                $('#edit-client-image-preview-canvas').hide();
                            }

                            console.log('Opening modal with data:',
                            response); // Debug sebelum buka modal
                            $('#editClientModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr); // Debug error
                        handleAjaxError(xhr, '#edit-client-error-message');
                    }
                });
            });

            // Submit form Edit Client
            $('#edit-client-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const clientId = $('#edit-client-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-client-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('clients.update', ':id') }}`.replace(':id', clientId),
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
                            $('#editClientModal').modal('hide');
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-client-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Klik tombol Show: ambil data dan tampilkan modal show
            $(document).on('click', '.btn-show-client', function() {
                const clientId = $(this).data('id');
                $('#show-client-error-message').html('');
                console.log('Fetching client ID:', clientId); // Debug ID
                const url = `{{ route('clients.show', ':id') }}`.replace(':id', clientId);
                console.log('Request URL:', url); // Debug URL

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Response:', response); // Debug respons
                        if (response && response.id) {
                            $('#show-client-name').val(response.name || '');
                            $('#show-client-email').val(response.email || '');
                            $('#show-client-phone-number').val(response.phone_number ||
                                'Tidak ada');
                            $('#show-client-address').val(response.address || 'Tidak ada');
                            $('#show-client-description').val(response.description ||
                                'Tidak ada');
                            $('#show-client-status').val(response.status === 'active' ?
                                'Active' : 'Nonactive');
                            $('#show-client-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ?
                                `{{ asset('upload/clients') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-client-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Client"></a>`
                                );
                            } else {
                                $('#show-client-image').html('Tidak ada gambar');
                            }
                            console.log('Opening modal with data:',
                                response); // Debug sebelum buka modal
                            $('#showClientModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr); // Debug error
                        handleAjaxError(xhr, '#show-client-error-message');
                    }
                });
            });

            // Konfirmasi Hapus Client
            window.confirmDelete = function(clientId) {
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
                            url: `{{ route('clients.destroy', ':id') }}`.replace(':id',
                                clientId),
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
                                    location.reload(); // Reload halaman
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
