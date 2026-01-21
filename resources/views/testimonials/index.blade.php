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
                                        @can('testimonial-create')
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
                                        @foreach ($data_testimonial as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->email }}</td>
                                                <td>{{ $p->phone_number ?? 'Tidak ada' }}</td>
                                                <td>{{ $p->address ? Str::limit($p->address, 50) : 'N/A' }}</td>
                                                <td>{{ $p->description ? Str::limit($p->description, 50) : 'N/A' }}</td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/testimonies/' . $p->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-testimonial"
                                                        data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('testimonial-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-testimonial"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('testimonial-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST"
                                                            action="{{ route('testimonials.destroy', $p->id) }}"
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
                                            <form id="add-testimonial-form" enctype="multipart/form-data">
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
                                                                <label for="testimonial-name" class="form-label">Nama Client
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="testimonial-name"
                                                                    name="name" placeholder="Contoh: John Doe" required>
                                                                <div class="invalid-feedback" id="testimonial-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="testimonial-email" class="form-label">Email <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="email" class="form-control"
                                                                    id="testimonial-email" name="email"
                                                                    placeholder="Contoh: john@example.com" required>
                                                                <div class="invalid-feedback" id="testimonial-email-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="testimonial-phone-number" class="form-label">Nomor
                                                                    Telepon</label>
                                                                <input type="text" class="form-control"
                                                                    id="testimonial-phone-number" name="phone_number"
                                                                    placeholder="Contoh: +628123456789">
                                                                <div class="invalid-feedback"
                                                                    id="testimonial-phone-number-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="testimonial-address"
                                                                    class="form-label">Alamat</label>
                                                                <textarea class="form-control" id="testimonial-address" name="address" rows="4" placeholder="Masukkan alamat"></textarea>
                                                                <div class="invalid-feedback" id="testimonial-address-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="testimonial-description"
                                                                    class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="testimonial-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="testimonial-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="testimonial-status" class="form-label">Status <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="testimonial-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="testimonial-status-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="testimonial-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="testimonial-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="testimonial-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="testimonial-image" class="form-label">Gambar Client
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="testimonial-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateClientImageUpload()">
                                                                <div class="invalid-feedback" id="testimonial-image-error">
                                                                </div>
                                                                <img id="testimonial-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="testimonial-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="testimonial-error-message" class="text-danger small"></div>
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
                                            <form id="edit-testimonial-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-testimonial-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editClientModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Client
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-testimonial-error-message" class="text-danger small mb-2">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-name" class="form-label">Nama
                                                                    Client <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-testimonial-name" name="name"
                                                                    placeholder="Contoh: John Doe" required>
                                                                <div class="invalid-feedback" id="edit-testimonial-name-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-email" class="form-label">Email
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control"
                                                                    id="edit-testimonial-email" name="email"
                                                                    placeholder="Contoh: john@example.com" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-testimonial-email-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-phone-number"
                                                                    class="form-label">Nomor Telepon</label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-testimonial-phone-number" name="phone_number"
                                                                    placeholder="Contoh: +628123456789">
                                                                <div class="invalid-feedback"
                                                                    id="edit-testimonial-phone-number-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-address"
                                                                    class="form-label">Alamat</label>
                                                                <textarea class="form-control" id="edit-testimonial-address" name="address" rows="4"
                                                                    placeholder="Masukkan alamat"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-testimonial-address-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-description"
                                                                    class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-testimonial-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-testimonial-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-testimonial-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback"
                                                                    id="edit-testimonial-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-testimonial-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="edit-testimonial-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-testimonial-image" class="form-label">Gambar
                                                                    Client (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="edit-testimonial-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateEditClientImageUpload()">
                                                                <div class="invalid-feedback"
                                                                    id="edit-testimonial-image-error"></div>
                                                                <img id="edit-testimonial-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-testimonial-image-preview-canvas"
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
                                                <div id="show-testimonial-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-name" class="form-label">Nama
                                                                Client</label>
                                                            <input type="text" class="form-control"
                                                                id="show-testimonial-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-email"
                                                                class="form-label">Email</label>
                                                            <input type="email" class="form-control"
                                                                id="show-testimonial-email" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-phone-number" class="form-label">Nomor
                                                                Telepon</label>
                                                            <input type="text" class="form-control"
                                                                id="show-testimonial-phone-number" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-address"
                                                                class="form-label">Alamat</label>
                                                            <textarea class="form-control" id="show-testimonial-address" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-description"
                                                                class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-testimonial-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-status"
                                                                class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                id="show-testimonial-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-order-display"
                                                                class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-testimonial-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-testimonial-image" class="form-label">Gambar
                                                                Client</label>
                                                            <div id="show-testimonial-image"></div>
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
            const fileInput = document.getElementById('testimonial-image');
            const errorDiv = document.getElementById('testimonial-image-error');
            const previewImage = document.getElementById('testimonial-image-preview');
            const previewCanvas = document.getElementById('testimonial-image-preview-canvas');
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
            const fileInput = document.getElementById('edit-testimonial-image');
            const errorDiv = document.getElementById('edit-testimonial-image-error');
            const previewImage = document.getElementById('edit-testimonial-image-preview');
            const previewCanvas = document.getElementById('edit-testimonial-image-preview-canvas');
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
            $('#add-testimonial-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#testimonial-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('testimonials.store') }}",
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
                            $('#testimonial-image-preview').hide();
                            $('#testimonial-image-preview-canvas').hide();
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#testimonial-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit: ambil data dan tampilkan modal edit
            $(document).on('click', '.btn-edit-testimonial', function() {
                const testimonialId = $(this).data('id');
                $('#edit-testimonial-error-message').html('');
                $('#edit-testimonial-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-testimonial-image-preview').hide();
                $('#edit-testimonial-image-preview-canvas').hide();

                const url = `{{ route('testimonials.edit', ':id') }}`.replace(':id', testimonialId);
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
                            $('#edit-testimonial-id').val(response.id);
                            $('#edit-testimonial-name').val(response.name);
                            $('#edit-testimonial-email').val(response.email);
                            $('#edit-testimonial-phone-number').val(response.phone_number || '');
                            $('#edit-testimonial-address').val(response.address || '');
                            $('#edit-testimonial-description').val(response.description || '');
                            $('#edit-testimonial-status').val(response.status);
                            $('#edit-testimonial-order-display').val(response.order_display);
                            $('#edit-testimonial-image').val('');

                            const imageUrl = response.image ?
                                `{{ asset('upload/testimonies') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-testimonial-image-preview').attr('src', imageUrl).show();
                                $('#edit-testimonial-image-preview-canvas').hide();
                            } else {
                                $('#edit-testimonial-image-preview').hide();
                                $('#edit-testimonial-image-preview-canvas').hide();
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
                        handleAjaxError(xhr, '#edit-testimonial-error-message');
                    }
                });
            });

            // Submit form Edit Client
            $('#edit-testimonial-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const testimonialId = $('#edit-testimonial-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-testimonial-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('testimonials.update', ':id') }}`.replace(':id', testimonialId),
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
                        handleAjaxError(xhr, '#edit-testimonial-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Klik tombol Show: ambil data dan tampilkan modal show
            $(document).on('click', '.btn-show-testimonial', function() {
                const testimonialId = $(this).data('id');
                $('#show-testimonial-error-message').html('');
                console.log('Fetching testimonial ID:', testimonialId); // Debug ID
                const url = `{{ route('testimonials.show', ':id') }}`.replace(':id', testimonialId);
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
                            $('#show-testimonial-name').val(response.name || '');
                            $('#show-testimonial-email').val(response.email || '');
                            $('#show-testimonial-phone-number').val(response.phone_number ||
                                'Tidak ada');
                            $('#show-testimonial-address').val(response.address || 'Tidak ada');
                            $('#show-testimonial-description').val(response.description ||
                                'Tidak ada');
                            $('#show-testimonial-status').val(response.status === 'active' ?
                                'Active' : 'Nonactive');
                            $('#show-testimonial-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ?
                                `{{ asset('upload/testimonies') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-testimonial-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Client"></a>`
                                );
                            } else {
                                $('#show-testimonial-image').html('Tidak ada gambar');
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
                        handleAjaxError(xhr, '#show-testimonial-error-message');
                    }
                });
            });

            // Konfirmasi Hapus Client
            window.confirmDelete = function(testimonialId) {
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
                            url: `{{ route('testimonials.destroy', ':id') }}`.replace(':id',
                                testimonialId),
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
