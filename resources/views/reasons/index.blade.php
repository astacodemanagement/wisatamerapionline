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
                                        @can('reason-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addReasonModal">
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
                                            <th>Deskripsi 1</th>
                                            <th>Deskripsi 2</th>
                                            <th>Deskripsi 3</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Icon</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_reason as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ Str::limit($p->description_1, 50) }}</td>
                                                <td>{{ Str::limit($p->description_2, 50) }}</td>
                                                <td>{{ Str::limit($p->description_3, 50) }}</td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/reasons/' . $p->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($p->icon)
                                                        <a href="{{ asset('upload/reasons/icons/' . $p->icon) }}"
                                                            target="_blank">Lihat Icon</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-reason"
                                                        data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('reason-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-reason"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('reason-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST"
                                                            action="{{ route('reasons.destroy', $p->id) }}"
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

                                <!-- Modal Tambah Reason -->
                                <div class="modal fade" id="addReasonModal" tabindex="-1"
                                    aria-labelledby="addReasonModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-reason-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addReasonModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Reason
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="reason-name" class="form-label">Nama Reason
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="reason-name"
                                                                    name="name" placeholder="Contoh: Reason 1" required>
                                                                <div class="invalid-feedback" id="reason-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="reason-description-1"
                                                                    class="form-label">Deskripsi 1
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="reason-description-1" name="description_1" rows="4"
                                                                    placeholder="Masukkan deskripsi 1" required></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="reason-description-1-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="reason-description-2"
                                                                    class="form-label">Deskripsi 2
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="reason-description-2" name="description_2" rows="4"
                                                                    placeholder="Masukkan deskripsi 2" required></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="reason-description-2-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="reason-description-3"
                                                                    class="form-label">Deskripsi 3
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="reason-description-3" name="description_3" rows="4"
                                                                    placeholder="Masukkan deskripsi 3" required></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="reason-description-3-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="reason-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="reason-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="reason-status-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="reason-order-display"
                                                                    class="form-label">Urutan
                                                                    Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="reason-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="reason-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="reason-image" class="form-label">Gambar Reason
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="reason-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateReasonImageUpload()">
                                                                <div class="invalid-feedback" id="reason-image-error">
                                                                </div>
                                                                <img id="reason-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="reason-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="reason-icon" class="form-label">Icon Reason
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="reason-icon" name="icon"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateReasonIconUpload()">
                                                                <div class="invalid-feedback" id="reason-icon-error">
                                                                </div>
                                                                <img id="reason-icon-preview" src="#"
                                                                    alt="Icon Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="reason-icon-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="reason-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Reason -->
                                <div class="modal fade" id="editReasonModal" tabindex="-1"
                                    aria-labelledby="editReasonModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-reason-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-reason-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editReasonModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Reason
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-reason-error-message" class="text-danger small mb-2">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-reason-name" class="form-label">Nama
                                                                    Reason
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-reason-name" name="name"
                                                                    placeholder="Contoh: Reason 1" required>
                                                                <div class="invalid-feedback" id="edit-reason-name-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-reason-description-1"
                                                                    class="form-label">Deskripsi 1
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-reason-description-1" name="description_1" rows="4"
                                                                    placeholder="Masukkan deskripsi 1" required></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-reason-description-1-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-reason-description-2"
                                                                    class="form-label">Deskripsi 2
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-reason-description-2" name="description_2" rows="4"
                                                                    placeholder="Masukkan deskripsi 2" required></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-reason-description-2-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-reason-description-3"
                                                                    class="form-label">Deskripsi 3
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-reason-description-3" name="description_3" rows="4"
                                                                    placeholder="Masukkan deskripsi 3" required></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-reason-description-3-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-reason-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-reason-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback"
                                                                    id="edit-reason-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-reason-order-display"
                                                                    class="form-label">Urutan
                                                                    Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-reason-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="edit-reason-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-reason-image" class="form-label">Gambar
                                                                    Reason
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="edit-reason-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateEditReasonImageUpload()">
                                                                <div class="invalid-feedback"
                                                                    id="edit-reason-image-error"></div>
                                                                <img id="edit-reason-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-reason-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-reason-icon" class="form-label">Icon
                                                                    Reason
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="edit-reason-icon" name="icon"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateEditReasonIconUpload()">
                                                                <div class="invalid-feedback" id="edit-reason-icon-error">
                                                                </div>
                                                                <img id="edit-reason-icon-preview" src="#"
                                                                    alt="Icon Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-reason-icon-preview-canvas"
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

                                <!-- Modal Tampil Reason -->
                                <div class="modal fade" id="showReasonModal" tabindex="-1"
                                    aria-labelledby="showReasonModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showReasonModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Reason
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-reason-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-reason-name" class="form-label">Nama
                                                                Reason</label>
                                                            <input type="text" class="form-control"
                                                                id="show-reason-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-reason-description-1"
                                                                class="form-label">Deskripsi
                                                                1</label>
                                                            <textarea class="form-control" id="show-reason-description-1" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-reason-description-2"
                                                                class="form-label">Deskripsi
                                                                2</label>
                                                            <textarea class="form-control" id="show-reason-description-2" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-reason-description-3"
                                                                class="form-label">Deskripsi
                                                                3</label>
                                                            <textarea class="form-control" id="show-reason-description-3" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-reason-status"
                                                                class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                id="show-reason-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-reason-order-display"
                                                                class="form-label">Urutan
                                                                Tampilan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-reason-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-reason-image" class="form-label">Gambar
                                                                Reason</label>
                                                            <div id="show-reason-image"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-reason-icon" class="form-label">Icon
                                                                Reason</label>
                                                            <div id="show-reason-icon"></div>
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
        function validateReasonImageUpload() {
            const fileInput = document.getElementById('reason-image');
            const errorDiv = document.getElementById('reason-image-error');
            const previewImage = document.getElementById('reason-image-preview');
            const previewCanvas = document.getElementById('reason-image-preview-canvas');
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

        function validateReasonIconUpload() {
            const fileInput = document.getElementById('reason-icon');
            const errorDiv = document.getElementById('reason-icon-error');
            const previewImage = document.getElementById('reason-icon-preview');
            const previewCanvas = document.getElementById('reason-icon-preview-canvas');
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

        function validateEditReasonImageUpload() {
            const fileInput = document.getElementById('edit-reason-image');
            const errorDiv = document.getElementById('edit-reason-image-error');
            const previewImage = document.getElementById('edit-reason-image-preview');
            const previewCanvas = document.getElementById('edit-reason-image-preview-canvas');
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

        function validateEditReasonIconUpload() {
            const fileInput = document.getElementById('edit-reason-icon');
            const errorDiv = document.getElementById('edit-reason-icon-error');
            const previewImage = document.getElementById('edit-reason-icon-preview');
            const previewCanvas = document.getElementById('edit-reason-icon-preview-canvas');
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

            // Submit Tambah Reason
            $('#add-reason-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#reason-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('reasons.store') }}",
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
                            $('#addReasonModal').modal('hide');
                            form[0].reset();
                            $('#reason-image-preview').hide();
                            $('#reason-image-preview-canvas').hide();
                            $('#reason-icon-preview').hide();
                            $('#reason-icon-preview-canvas').hide();
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#reason-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit: ambil data dan tampilkan modal edit
            $(document).on('click', '.btn-edit-reason', function() {
                const reasonId = $(this).data('id');
                $('#edit-reason-error-message').html('');
                $('#edit-reason-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-reason-image-preview').hide();
                $('#edit-reason-image-preview-canvas').hide();
                $('#edit-reason-icon-preview').hide();
                $('#edit-reason-icon-preview-canvas').hide();

                const url = `{{ route('reasons.edit', ':id') }}`.replace(':id', reasonId);
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
                            $('#edit-reason-id').val(response.id);
                            $('#edit-reason-name').val(response.name || '');
                            $('#edit-reason-description-1').val(response.description_1 || '');
                            $('#edit-reason-description-2').val(response.description_2 || '');
                            $('#edit-reason-description-3').val(response.description_3 || '');
                            $('#edit-reason-status').val(response.status || '');
                            $('#edit-reason-order-display').val(response.order_display || '0');
                            $('#edit-reason-image').val('');
                            $('#edit-reason-icon').val('');

                            const imageUrl = response.image ?
                                `{{ asset('upload/reasons') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-reason-image-preview').attr('src', imageUrl).show();
                                $('#edit-reason-image-preview-canvas').hide();
                            } else {
                                $('#edit-reason-image-preview').hide();
                                $('#edit-reason-image-preview-canvas').hide();
                            }

                            const iconUrl = response.icon ?
                                `{{ asset('upload/reasons/icons') }}/${response.icon}` : null;
                            if (iconUrl && /\.(jpg|jpeg|png|webp)$/i.test(iconUrl)) {
                                $('#edit-reason-icon-preview').attr('src', iconUrl).show();
                                $('#edit-reason-icon-preview-canvas').hide();
                            } else {
                                $('#edit-reason-icon-preview').hide();
                                $('#edit-reason-icon-preview-canvas').hide();
                            }

                            console.log('Opening modal with data:',
                            response); // Debug sebelum buka modal
                            $('#editReasonModal').modal('show');
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
                        handleAjaxError(xhr, '#edit-reason-error-message');
                    }
                });
            });

            // Submit form Edit Reason
            $('#edit-reason-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const reasonId = $('#edit-reason-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-reason-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('reasons.update', ':id') }}`.replace(':id', reasonId),
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
                            $('#editReasonModal').modal('hide');
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-reason-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Klik tombol Show: ambil data dan tampilkan modal show
            $(document).on('click', '.btn-show-reason', function() {
                const reasonId = $(this).data('id');
                $('#show-reason-error-message').html('');

                const url = `{{ route('reasons.show', ':id') }}`.replace(':id', reasonId);
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
                            $('#show-reason-name').val(response.name || '');
                            $('#show-reason-description-1').val(response.description_1 || '');
                            $('#show-reason-description-2').val(response.description_2 || '');
                            $('#show-reason-description-3').val(response.description_3 || '');
                            $('#show-reason-status').val(response.status === 'active' ?
                                'Active' : 'Nonactive');
                            $('#show-reason-order-display').val(response.order_display || '0');

                            const imageUrl = response.image ?
                                `{{ asset('upload/reasons') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-reason-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Reason"></a>`
                                );
                            } else {
                                $('#show-reason-image').html('Tidak ada gambar');
                            }

                            const iconUrl = response.icon ?
                                `{{ asset('upload/reasons/icons') }}/${response.icon}` : null;
                            if (iconUrl && /\.(jpg|jpeg|png|webp)$/i.test(iconUrl)) {
                                $('#show-reason-icon').html(
                                    `<a href="${iconUrl}" target="_blank"><img src="${iconUrl}" class="img-fluid" style="max-width: 50%;" alt="Icon Reason"></a>`
                                );
                            } else {
                                $('#show-reason-icon').html('Tidak ada icon');
                            }

                            console.log('Opening modal with data:',
                                response); // Debug sebelum buka modal
                            $('#showReasonModal').modal('show');
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
                        handleAjaxError(xhr, '#show-reason-error-message');
                    }
                });
            });

            // Konfirmasi Hapus Reason
            window.confirmDelete = function(reasonId) {
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
                            url: `{{ route('reasons.destroy', ':id') }}`.replace(':id',
                                reasonId),
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
