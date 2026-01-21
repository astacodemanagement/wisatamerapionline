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
                                        @can('portfolio-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addPortfolioModal">
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
                                            <th>Sub Layanan</th>
                                            <th>Layanan Utama</th>
                                            <th>Deskripsi 1</th>
                                            <th>Gambar</th>
                                            <th>Ikon</th>
                                            <th>Status</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($portfolios as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->sub_service ? $p->sub_service->name : 'N/A' }}</td>
                                                <td>{{ $p->sub_service && $p->sub_service->service ? $p->sub_service->service->name : 'N/A' }}</td>
                                                <td>{{ $p->description_1 ? Str::limit($p->description_1, 50) : 'N/A' }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/portfolios/' . $p->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($p->icon)
                                                        <a href="{{ asset('upload/portfolios/icons/' . $p->icon) }}"
                                                            target="_blank">Lihat Ikon</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->status }}</td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-portfolio"
                                                        data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('portfolio-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-portfolio"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('portfolio-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST"
                                                            action="{{ route('portfolios.destroy', $p->id) }}"
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

                                <!-- Modal Tambah Portfolio -->
                                <div class="modal fade" id="addPortfolioModal" tabindex="-1"
                                    aria-labelledby="addPortfolioModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-portfolio-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addPortfolioModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Portfolio
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="portfolio-sub-service-id" class="form-label">Sub Layanan
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="portfolio-sub-service-id"
                                                                    name="sub_service_id" required>
                                                                    <option value="">Pilih Sub Layanan</option>
                                                                    @foreach ($sub_services as $sub_service)
                                                                        <option value="{{ $sub_service->id }}">{{ $sub_service->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="portfolio-sub-service-id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-name" class="form-label">Nama Portfolio
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="portfolio-name"
                                                                    name="name" placeholder="Contoh: Portfolio Proyek" required>
                                                                <div class="invalid-feedback" id="portfolio-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-description-1" class="form-label">Deskripsi 1
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="portfolio-description-1" name="description_1" rows="4"
                                                                    placeholder="Masukkan deskripsi 1" required></textarea>
                                                                <div class="invalid-feedback" id="portfolio-description-1-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-description-2" class="form-label">Deskripsi 2
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="portfolio-description-2" name="description_2" rows="4"
                                                                    placeholder="Masukkan deskripsi 2" required></textarea>
                                                                <div class="invalid-feedback" id="portfolio-description-2-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-description-3" class="form-label">Deskripsi 3
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="portfolio-description-3" name="description_3" rows="4"
                                                                    placeholder="Masukkan deskripsi 3" required></textarea>
                                                                <div class="invalid-feedback" id="portfolio-description-3-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="portfolio-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="portfolio-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="portfolio-order-display"
                                                                    name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="portfolio-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-image" class="form-label">Gambar Portfolio
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="portfolio-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="portfolio-image-error"></div>
                                                                <img id="portfolio-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="portfolio-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="portfolio-icon" class="form-label">Ikon Portfolio
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="portfolio-icon"
                                                                    name="icon" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="portfolio-icon-error"></div>
                                                                <img id="portfolio-icon-preview" src="#" alt="Ikon Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="portfolio-icon-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="portfolio-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Portfolio -->
                                <div class="modal fade" id="editPortfolioModal" tabindex="-1"
                                    aria-labelledby="editPortfolioModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-portfolio-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-portfolio-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editPortfolioModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Portfolio
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-portfolio-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-sub-service-id" class="form-label">Sub Layanan
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-portfolio-sub-service-id"
                                                                    name="sub_service_id" required>
                                                                    <option value="">Pilih Sub Layanan</option>
                                                                    @foreach ($sub_services as $sub_service)
                                                                        <option value="{{ $sub_service->id }}">{{ $sub_service->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-portfolio-sub-service-id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-name" class="form-label">Nama Portfolio
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-portfolio-name"
                                                                    name="name" placeholder="Contoh: Portfolio Proyek" required>
                                                                <div class="invalid-feedback" id="edit-portfolio-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-description-1" class="form-label">Deskripsi 1
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-portfolio-description-1" name="description_1" rows="4"
                                                                    placeholder="Masukkan deskripsi 1" required></textarea>
                                                                <div class="invalid-feedback" id="edit-portfolio-description-1-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-description-2" class="form-label">Deskripsi 2
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-portfolio-description-2" name="description_2" rows="4"
                                                                    placeholder="Masukkan deskripsi 2" required></textarea>
                                                                <div class="invalid-feedback" id="edit-portfolio-description-2-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-description-3" class="form-label">Deskripsi 3
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-portfolio-description-3" name="description_3" rows="4"
                                                                    placeholder="Masukkan deskripsi 3" required></textarea>
                                                                <div class="invalid-feedback" id="edit-portfolio-description-3-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-portfolio-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-portfolio-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="edit-portfolio-order-display"
                                                                    name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="edit-portfolio-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-image" class="form-label">Gambar Portfolio
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-portfolio-image"
                                                                    name="image" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="edit-portfolio-image-error"></div>
                                                                <img id="edit-portfolio-image-preview" src="#" alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-portfolio-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-portfolio-icon" class="form-label">Ikon Portfolio
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-portfolio-icon"
                                                                    name="icon" accept=".jpg,.jpeg,.png">
                                                                <div class="invalid-feedback" id="edit-portfolio-icon-error"></div>
                                                                <img id="edit-portfolio-icon-preview" src="#" alt="Ikon Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-portfolio-icon-preview-canvas"
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

                                <!-- Modal Tampil Portfolio -->
                                <div class="modal fade" id="showPortfolioModal" tabindex="-1"
                                    aria-labelledby="showPortfolioModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showPortfolioModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Portfolio
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-portfolio-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-sub-service-name" class="form-label">Sub Layanan</label>
                                                            <input type="text" class="form-control" id="show-portfolio-sub-service-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-service-name" class="form-label">Layanan Utama</label>
                                                            <input type="text" class="form-control" id="show-portfolio-service-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-name" class="form-label">Nama Portfolio</label>
                                                            <input type="text" class="form-control" id="show-portfolio-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-description-1" class="form-label">Deskripsi 1</label>
                                                            <textarea class="form-control" id="show-portfolio-description-1" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-description-2" class="form-label">Deskripsi 2</label>
                                                            <textarea class="form-control" id="show-portfolio-description-2" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-description-3" class="form-label">Deskripsi 3</label>
                                                            <textarea class="form-control" id="show-portfolio-description-3" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-portfolio-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-portfolio-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-image" class="form-label">Gambar Portfolio</label>
                                                            <div id="show-portfolio-image"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-portfolio-icon" class="form-label">Ikon Portfolio</label>
                                                            <div id="show-portfolio-icon"></div>
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
            function validatePortfolioImageUpload(inputId, errorId, previewId, canvasId, maxSize) {
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
            $('#portfolio-image').on('change', function() {
                validatePortfolioImageUpload(
                    'portfolio-image',
                    'portfolio-image-error',
                    'portfolio-image-preview',
                    'portfolio-image-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            $('#portfolio-icon').on('change', function() {
                validatePortfolioImageUpload(
                    'portfolio-icon',
                    'portfolio-icon-error',
                    'portfolio-icon-preview',
                    'portfolio-icon-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            // Binding event untuk modal edit
            $('#edit-portfolio-image').on('change', function() {
                validatePortfolioImageUpload(
                    'edit-portfolio-image',
                    'edit-portfolio-image-error',
                    'edit-portfolio-image-preview',
                    'edit-portfolio-image-preview-canvas',
                    4 * 1024 * 1024 // 4MB
                );
            });

            $('#edit-portfolio-icon').on('change', function() {
                validatePortfolioImageUpload(
                    'edit-portfolio-icon',
                    'edit-portfolio-icon-error',
                    'edit-portfolio-icon-preview',
                    'edit-portfolio-icon-preview-canvas',
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

            // Submit form tambah portfolio
            $('#add-portfolio-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#portfolio-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('portfolios.store') }}",
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
                            $('#addPortfolioModal').modal('hide');
                            form[0].reset();
                            $('#portfolio-image-preview').hide();
                            $('#portfolio-image-preview-canvas').hide();
                            $('#portfolio-icon-preview').hide();
                            $('#portfolio-icon-preview-canvas').hide();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#portfolio-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Event untuk tombol edit
            $(document).on('click', '.btn-edit-portfolio', function() {
                const portfolioId = $(this).data('id');
                $('#edit-portfolio-error-message').html('');
                $('#edit-portfolio-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-portfolio-image-preview').hide();
                $('#edit-portfolio-image-preview-canvas').hide();
                $('#edit-portfolio-icon-preview').hide();
                $('#edit-portfolio-icon-preview-canvas').hide();

                $.ajax({
                    url: `{{ route('portfolios.edit', ':id') }}`.replace(':id', portfolioId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.portfolio.id) {
                            $('#edit-portfolio-id').val(response.portfolio.id);
                            $('#edit-portfolio-sub-service-id').val(response.portfolio.sub_service_id || '');
                            $('#edit-portfolio-name').val(response.portfolio.name);
                            $('#edit-portfolio-description-1').val(response.portfolio.description_1);
                            $('#edit-portfolio-description-2').val(response.portfolio.description_2);
                            $('#edit-portfolio-description-3').val(response.portfolio.description_3);
                            $('#edit-portfolio-status').val(response.portfolio.status);
                            $('#edit-portfolio-order-display').val(response.portfolio.order_display);
                            $('#edit-portfolio-image').val('');
                            $('#edit-portfolio-icon').val('');
                            const imageUrl = response.portfolio.image ? `{{ asset('upload/portfolios') }}/${response.portfolio.image}` : null;
                            const iconUrl = response.portfolio.icon ? `{{ asset('upload/portfolios/icons') }}/${response.portfolio.icon}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-portfolio-image-preview').attr('src', imageUrl).show();
                                $('#edit-portfolio-image-preview-canvas').hide();
                            } else {
                                $('#edit-portfolio-image-preview').hide();
                                $('#edit-portfolio-image-preview-canvas').hide();
                            }
                            if (iconUrl && /\.(jpg|jpeg|png|webp)$/i.test(iconUrl)) {
                                $('#edit-portfolio-icon-preview').attr('src', iconUrl).show();
                                $('#edit-portfolio-icon-preview-canvas').hide();
                            } else {
                                $('#edit-portfolio-icon-preview').hide();
                                $('#edit-portfolio-icon-preview-canvas').hide();
                            }
                            $('#editPortfolioModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-portfolio-error-message');
                    }
                });
            });

            // Submit form edit portfolio
            $('#edit-portfolio-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const portfolioId = $('#edit-portfolio-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-portfolio-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('portfolios.update', ':id') }}`.replace(':id', portfolioId),
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
                            $('#editPortfolioModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-portfolio-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Event untuk tombol show
            $(document).on('click', '.btn-show-portfolio', function() {
                const portfolioId = $(this).data('id');
                $('#show-portfolio-error-message').html('');

                $.ajax({
                    url: `{{ route('portfolios.show', ':id') }}`.replace(':id', portfolioId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            $('#show-portfolio-sub-service-name').val(response.sub_service ? response.sub_service.name : 'N/A');
                            $('#show-portfolio-service-name').val(response.sub_service && response.sub_service.service ? response.sub_service.service.name : 'N/A');
                            $('#show-portfolio-name').val(response.name || '');
                            $('#show-portfolio-description-1').val(response.description_1 || '');
                            $('#show-portfolio-description-2').val(response.description_2 || '');
                            $('#show-portfolio-description-3').val(response.description_3 || '');
                            $('#show-portfolio-status').val(response.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                            $('#show-portfolio-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ? `{{ asset('upload/portfolios') }}/${response.image}` : null;
                            const iconUrl = response.icon ? `{{ asset('upload/portfolios/icons') }}/${response.icon}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-portfolio-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Portfolio"></a>`
                                );
                            } else {
                                $('#show-portfolio-image').html('Tidak ada gambar');
                            }
                            if (iconUrl && /\.(jpg|jpeg|png|webp)$/i.test(iconUrl)) {
                                $('#show-portfolio-icon').html(
                                    `<a href="${iconUrl}" target="_blank"><img src="${iconUrl}" class="img-fluid" style="max-width: 50%;" alt="Ikon Portfolio"></a>`
                                );
                            } else {
                                $('#show-portfolio-icon').html('Tidak ada ikon');
                            }
                            $('#showPortfolioModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-portfolio-error-message');
                    }
                });
            });

            // Fungsi untuk konfirmasi hapus
            window.confirmDelete = function(portfolioId) {
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
                            url: `{{ route('portfolios.destroy', ':id') }}`.replace(':id', portfolioId),
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