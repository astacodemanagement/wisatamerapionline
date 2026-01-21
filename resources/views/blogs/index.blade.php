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

        .preview-container {
            margin-top: 10px;
            max-width: 100px;
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
                                        @can('blog-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addBlogModal">
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
                                            <th>Judul</th>
                                            <th>Slug</th>
                                            <th>Kategori</th>
                                            <th>Penulis</th>
                                            <th>Tanggal Publikasi</th>
                                            <th>Status</th>
                                            <th>Thumbnail</th>
                                            <th>Urutan</th>
                                            <th>Views</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_blog as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($p->headline, 50) }}</td>
                                                <td>{{ $p->news_slug }}</td>
                                                <td>{{ $p->category ? $p->category->category_name : 'Tidak ada' }}</td>
                                                <td>{{ $p->author ?? 'Tidak ada' }}</td>
                                                <td>
                                                    @if ($p->publish_date)
                                                        {{ \Carbon\Carbon::parse($p->publish_date)->format('d-m-Y H:i') }}
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->thumbnail)
                                                        <a href="{{ asset('upload/blogs/' . $p->thumbnail) }}" target="_blank">Lihat Thumbnail</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>{{ $p->views ?? 0 }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-blog" data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('blog-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-blog" data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('blog-delete')
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST" action="{{ route('blogs.destroy', $p->id) }}" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah Blog -->
                                <div class="modal fade" id="addBlogModal" tabindex="-1" aria-labelledby="addBlogModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-blog-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addBlogModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Blog
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="blog-headline" class="form-label">Judul <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="blog-headline" name="headline" placeholder="Contoh: Judul Blog" required>
                                                                <div class="invalid-feedback" id="blog-headline-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="blog-slug" name="news_slug" placeholder="Contoh: judul-blog" required>
                                                                <div class="invalid-feedback" id="blog-slug-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-body" class="form-label">Konten <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="blog-body" name="body" required></textarea>
                                                                <div class="invalid-feedback" id="blog-body-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-resume" class="form-label">Ringkasan</label>
                                                                <textarea class="form-control" id="blog-resume" name="resume" rows="4" placeholder="Masukkan ringkasan"></textarea>
                                                                <div class="invalid-feedback" id="blog-resume-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-category" class="form-label">Kategori</label>
                                                                <select class="form-control" id="blog-category" name="category_id">
                                                                    <option value="">Pilih Kategori</option>
                                                                    @foreach (App\Models\BlogCategory::where('status', 'active')->get() as $category)
                                                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="blog-category-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-thumbnail" class="form-label">Thumbnail (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="blog-thumbnail" name="thumbnail" accept=".jpg,.jpeg,.png"
                                                                       onchange="validateImageUpload('blog-thumbnail', 'blog-thumbnail-error', 'blog-thumbnail-preview-canvas')">
                                                                <div class="invalid-feedback" id="blog-thumbnail-error"></div>
                                                                <canvas id="blog-thumbnail-preview-canvas" class="preview-container" style="display: none;"></canvas>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-author" class="form-label">Penulis</label>
                                                                <input type="text" class="form-control" id="blog-author" name="author" placeholder="Contoh: John Doe">
                                                                <div class="invalid-feedback" id="blog-author-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-publish-date" class="form-label">Tanggal Publikasi</label>
                                                                <input type="datetime-local" class="form-control" id="blog-publish-date" name="publish_date">
                                                                <div class="invalid-feedback" id="blog-publish-date-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="blog-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="blog-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="blog-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="blog-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="blog-order-display-error"></div>
                                                            </div>

                                                            
                                                        </div>
                                                    </div>
                                                    <div id="blog-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Blog -->
                                <div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-blog-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-blog-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editBlogModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Blog
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-blog-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-blog-headline" class="form-label">Judul <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-blog-headline" name="headline" placeholder="Contoh: Judul Blog" required>
                                                                <div class="invalid-feedback" id="edit-blog-headline-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-blog-slug" name="news_slug" placeholder="Contoh: judul-blog" required>
                                                                <div class="invalid-feedback" id="edit-blog-slug-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-body" class="form-label">Konten <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-blog-body" name="body" required></textarea>
                                                                <div class="invalid-feedback" id="edit-blog-body-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-resume" class="form-label">Ringkasan</label>
                                                                <textarea class="form-control" id="edit-blog-resume" name="resume" rows="4" placeholder="Masukkan ringkasan"></textarea>
                                                                <div class="invalid-feedback" id="edit-blog-resume-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-category" class="form-label">Kategori</label>
                                                                <select class="form-control" id="edit-blog-category" name="category_id">
                                                                    <option value="">Pilih Kategori</option>
                                                                    @foreach (App\Models\BlogCategory::where('status', 'active')->get() as $category)
                                                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-blog-category-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-thumbnail" class="form-label">Thumbnail (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-blog-thumbnail" name="thumbnail" accept=".jpg,.jpeg,.png"
                                                                       onchange="validateImageUpload('edit-blog-thumbnail', 'edit-blog-thumbnail-error', 'edit-blog-thumbnail-preview-canvas')">
                                                                <div class="invalid-feedback" id="edit-blog-thumbnail-error"></div>
                                                                <canvas id="edit-blog-thumbnail-preview-canvas" class="preview-container" style="display: none;"></canvas>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-author" class="form-label">Penulis</label>
                                                                <input type="text" class="form-control" id="edit-blog-author" name="author" placeholder="Contoh: John Doe">
                                                                <div class="invalid-feedback" id="edit-blog-author-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-publish-date" class="form-label">Tanggal Publikasi</label>
                                                                <input type="datetime-local" class="form-control" id="edit-blog-publish-date" name="publish_date">
                                                                <div class="invalid-feedback" id="edit-blog-publish-date-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-blog-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-blog-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-blog-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="edit-blog-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="edit-blog-order-display-error"></div>
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

                                <!-- Modal Tampil Blog -->
                                <div class="modal fade" id="showBlogModal" tabindex="-1" aria-labelledby="showBlogModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showBlogModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Blog
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-blog-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-blog-headline" class="form-label">Judul</label>
                                                            <input type="text" class="form-control" id="show-blog-headline" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-slug" class="form-label">Slug</label>
                                                            <input type="text" class="form-control" id="show-blog-slug" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-body" class="form-label">Konten</label>
                                                            <div id="show-blog-body" class="border p-3" style="min-height: 200px;"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-resume" class="form-label">Ringkasan</label>
                                                            <textarea class="form-control" id="show-blog-resume" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-category" class="form-label">Kategori</label>
                                                            <input type="text" class="form-control" id="show-blog-category" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-author" class="form-label">Penulis</label>
                                                            <input type="text" class="form-control" id="show-blog-author" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-publish-date" class="form-label">Tanggal Publikasi</label>
                                                            <input type="text" class="form-control" id="show-blog-publish-date" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-blog-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-blog-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-views" class="form-label">Jumlah Tampilan</label>
                                                            <input type="text" class="form-control" id="show-blog-views" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-blog-thumbnail" class="form-label">Thumbnail</label>
                                                            <div id="show-blog-thumbnail"></div>
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
    <script src="{{ asset('template/back/dist/libs/ckeditor/ckeditor.js') }}"></script>
    <script>
        // Fungsi untuk mengonversi teks menjadi slug
        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[\s+]/g, '-') // Ganti spasi dengan strip
                .replace(/[^a-z0-9-]/g, '') // Hapus karakter non-alphanumeric kecuali strip
                .replace(/-+/g, '-'); // Ganti multiple strip dengan satu strip
        }

        // Validasi dan preview gambar
        function validateImageUpload(inputId, errorId, canvasId) {
            const fileInput = document.getElementById(inputId);
            const errorDiv = document.getElementById(errorId);
            const previewCanvas = document.getElementById(canvasId);
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            const minWidth = 100;
            const minHeight = 100;

            // Reset state
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewCanvas.style.display = 'none';
            fileInput.classList.remove('is-invalid');

            if (!file) {
                return;
            }

            // Validasi tipe file
            if (!allowedTypes.includes(file.type)) {
                errorDiv.textContent = 'File harus berupa JPEG, JPG, atau PNG.';
                errorDiv.style.display = 'block';
                fileInput.classList.add('is-invalid');
                fileInput.value = '';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'File harus berupa JPEG, JPG, atau PNG.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validasi ukuran file
            if (file.size > maxSize) {
                errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';
                errorDiv.style.display = 'block';
                fileInput.classList.add('is-invalid');
                fileInput.value = '';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ukuran file terlalu besar. Maksimum 4 MB.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validasi dimensi gambar
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.src = e.target.result;

                img.onload = function() {
                    if (img.width < minWidth || img.height < minHeight) {
                        errorDiv.textContent = `Dimensi gambar minimal ${minWidth}x${minHeight} piksel.`;
                        errorDiv.style.display = 'block';
                        fileInput.classList.add('is-invalid');
                        fileInput.value = '';
                        previewCanvas.style.display = 'none';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `Dimensi gambar minimal ${minWidth}x${minHeight} piksel.`,
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Tampilkan preview
                    const canvasContext = previewCanvas.getContext('2d');
                    const maxWidth = 100;
                    const scaleFactor = maxWidth / img.width;
                    const newHeight = img.height * scaleFactor;

                    previewCanvas.width = maxWidth;
                    previewCanvas.height = newHeight;
                    canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);

                    previewCanvas.style.display = 'block';
                };
                img.onerror = function() {
                    errorDiv.textContent = 'Gagal memuat gambar. Pastikan file valid.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = '';
                    previewCanvas.style.display = 'none';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat gambar. Pastikan file valid.',
                        confirmButtonText: 'OK'
                    });
                };
            };
            reader.readAsDataURL(file);
        }

        // Reset preview saat modal ditutup
        function resetImagePreview(canvasId) {
            const previewCanvas = document.getElementById(canvasId);
            const context = previewCanvas.getContext('2d');
            context.clearRect(0, 0, previewCanvas.width, previewCanvas.height);
            previewCanvas.style.display = 'none';
        }

        $(document).ready(function() {
            // Inisialisasi CKEditor untuk modal tambah dan edit
            CKEDITOR.replace('blog-body', { height: 200 });
            CKEDITOR.replace('edit-blog-body', { height: 200 });

            // Generate slug otomatis
            $('#blog-headline').on('input', function() {
                const headline = $(this).val();
                const slug = generateSlug(headline);
                $('#blog-slug').val(slug);
            });

            $('#edit-blog-headline').on('input', function() {
                const headline = $(this).val();
                const slug = generateSlug(headline);
                $('#edit-blog-slug').val(slug);
            });

            // Reset preview saat modal ditutup
            $('#addBlogModal').on('hidden.bs.modal', function() {
                $('#add-blog-form')[0].reset();
                CKEDITOR.instances['blog-body'].setData('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#blog-error-message').html('');
                resetImagePreview('blog-thumbnail-preview-canvas');
            });

            $('#editBlogModal').on('hidden.bs.modal', function() {
                $('#edit-blog-form')[0].reset();
                CKEDITOR.instances['edit-blog-body'].setData('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-blog-error-message').html('');
                resetImagePreview('edit-blog-thumbnail-preview-canvas');
            });

            // Fungsi untuk mengatur status tombol loading
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

            // Submit form tambah blog
            $('#add-blog-form').submit(function(e) {
                e.preventDefault();
                CKEDITOR.instances['blog-body'].updateElement();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#blog-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('blogs.store') }}",
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
                            $('#addBlogModal').modal('hide');
                            form[0].reset();
                            resetImagePreview('blog-thumbnail-preview-canvas');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#blog-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Edit blog
            $(document).on('click', '.btn-edit-blog', function() {
                const blogId = $(this).data('id');
                $('#edit-blog-error-message').html('');
                $('#edit-blog-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                resetImagePreview('edit-blog-thumbnail-preview-canvas');

                const url = `{{ route('blogs.edit', ':id') }}`.replace(':id', blogId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#edit-blog-id').val(response.id);
                            $('#edit-blog-headline').val(response.headline);
                            $('#edit-blog-slug').val(response.news_slug);
                            CKEDITOR.instances['edit-blog-body'].setData(response.body || '');
                            $('#edit-blog-resume').val(response.resume || '');
                            $('#edit-blog-category').val(response.category_id || '');
                            $('#edit-blog-author').val(response.author || '');
                            $('#edit-blog-publish-date').val(response.publish_date ? response.publish_date.replace(' ', 'T') : '');
                            $('#edit-blog-status').val(response.status);
                            $('#edit-blog-order-display').val(response.order_display || 0);
                            $('#edit-blog-thumbnail').val('');

                            const thumbnailUrl = response.thumbnail ? `{{ asset('upload/blogs') }}/${response.thumbnail}` : null;
                            if (thumbnailUrl && /\.(jpg|jpeg|png|webp)$/i.test(thumbnailUrl)) {
                                const img = new Image();
                                img.src = thumbnailUrl;
                                img.onload = function() {
                                    const canvas = document.getElementById('edit-blog-thumbnail-preview-canvas');
                                    const context = canvas.getContext('2d');
                                    const maxWidth = 100;
                                    const scaleFactor = maxWidth / img.width;
                                    const newHeight = img.height * scaleFactor;

                                    canvas.width = maxWidth;
                                    canvas.height = newHeight;
                                    context.drawImage(img, 0, 0, maxWidth, newHeight);
                                    canvas.style.display = 'block';
                                };
                                img.onerror = function() {
                                    resetImagePreview('edit-blog-thumbnail-preview-canvas');
                                };
                            } else {
                                resetImagePreview('edit-blog-thumbnail-preview-canvas');
                            }

                            $('#editBlogModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-blog-error-message');
                    }
                });
            });

            // Submit form edit blog
            $('#edit-blog-form').submit(function(e) {
                e.preventDefault();
                CKEDITOR.instances['edit-blog-body'].updateElement();
                const form = $(this);
                const btn = $('#btn-update');
                const blogId = $('#edit-blog-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-blog-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('blogs.update', ':id') }}`.replace(':id', blogId),
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
                            $('#editBlogModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-blog-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Show blog
            $(document).on('click', '.btn-show-blog', function() {
                const blogId = $(this).data('id');
                $('#show-blog-error-message').html('');

                const url = `{{ route('blogs.show', ':id') }}`.replace(':id', blogId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#show-blog-headline').val(response.headline || 'Tidak ada');
                            $('#show-blog-slug').val(response.news_slug || 'Tidak ada');
                            $('#show-blog-body').html(response.body || 'Tidak ada');
                            $('#show-blog-resume').val(response.resume || 'Tidak ada');
                            $('#show-blog-category').val(response.category ? response.category.category_name : 'Tidak ada');
                            $('#show-blog-author').val(response.author || 'Tidak ada');
                            $('#show-blog-publish-date').val(response.publish_date ?
                                new Date(response.publish_date).toLocaleString('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                }) : 'Tidak ada');
                            $('#show-blog-status').val(response.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                            $('#show-blog-order-display').val(response.order_display || '0');
                            const thumbnailUrl = response.thumbnail ? `{{ asset('upload/blogs') }}/${response.thumbnail}` : null;
                            if (thumbnailUrl && /\.(jpg|jpeg|png|webp)$/i.test(thumbnailUrl)) {
                                $('#show-blog-thumbnail').html(
                                    `<a href="${thumbnailUrl}" target="_blank"><img src="${thumbnailUrl}" class="img-fluid preview-container" alt="Thumbnail"></a>`
                                );
                            } else {
                                $('#show-blog-thumbnail').html('Tidak ada thumbnail');
                            }
                            $('#showBlogModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-blog-error-message');
                    }
                });
            });

            // Hapus blog
            window.confirmDelete = function(blogId) {
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
                            url: `{{ route('blogs.destroy', ':id') }}`.replace(':id', blogId),
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
                                    window.location.reload();
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