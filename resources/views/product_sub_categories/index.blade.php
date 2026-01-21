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
                                        @can('product-sub-category-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addSubCategoryModal">
                                                    <i class="fa fa-plus"></i> Tambah Sub Kategori
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
                                            <th>Nama Sub Kategori</th>
                                            <th>Slug</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th>Kategori Utama</th>
                                            <th>Parent</th>
                                            <th width="200px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_sub_category as $subCategory)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($subCategory->name, 50) }}</td>
                                                <td>{{ $subCategory->slug }}</td>
                                                <td>{!! Str::limit($subCategory->description, 50) !!}</td>
                                                <td>{{ $subCategory->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                <td>
                                                    @if ($subCategory->image)
                                                        <a href="{{ asset('upload/product_sub_categories/' . $subCategory->image) }}" target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $subCategory->order_display }}</td>
                                                <td>{{ $subCategory->category->name ?? 'Tidak ada' }}</td>
                                                <td>{{ $subCategory->parent->name ?? 'Tidak ada' }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-sub-category" data-id="{{ $subCategory->id }}">
                                                        <i class="fa fa-eye"></i> Lihat
                                                    </button>
                                                    @can('product-sub-category-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-sub-category" data-id="{{ $subCategory->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('product-sub-category-delete')
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $subCategory->id }})">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </button>
                                                        <form id="delete-form-{{ $subCategory->id }}" method="POST" action="{{ route('product-sub-categories.destroy', $subCategory->id) }}" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah Sub Kategori -->
                                <div class="modal fade" id="addSubCategoryModal" tabindex="-1" aria-labelledby="addSubCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-sub-category-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addSubCategoryModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Sub Kategori Produk
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="sub-category-category_id" class="form-label">Kategori Utama <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="sub-category-category_id" name="category_id" required>
                                                                    <option value="">Pilih Kategori</option>
                                                                    @foreach ($data_category as $cat)
                                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="sub-category-category_id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="sub-category-parent_id" class="form-label">Parent Sub Kategori (Opsional)</label>
                                                                <select class="form-control" id="sub-category-parent_id" name="parent_id">
                                                                    <option value="">Tidak ada</option>
                                                                    @foreach ($data_sub_category as $sub)
                                                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="sub-category-parent_id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="sub-category-name" class="form-label">Nama Sub Kategori <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="sub-category-name" name="name" placeholder="Contoh: Handphone" required>
                                                                <div class="invalid-feedback" id="sub-category-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="sub-category-slug" class="form-label">Slug Sub Kategori <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="sub-category-slug" name="slug" placeholder="Contoh: handphone" required readonly>
                                                                <div class="invalid-feedback" id="sub-category-slug-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="sub-category-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="sub-category-description" name="description" rows="4" placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="sub-category-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="sub-category-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="sub-category-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="sub-category-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="sub-category-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="sub-category-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="sub-category-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="sub-category-image" class="form-label">Gambar Sub Kategori (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="sub-category-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateSubCategoryImageUpload()">
                                                                <div class="invalid-feedback" id="sub-category-image-error"></div>
                                                                <img id="sub-category-image-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="sub-category-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="sub-category-error-message" class="text-danger small"></div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-save-sub">
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

                                <!-- Modal Edit Sub Kategori -->
                                <div class="modal fade" id="editSubCategoryModal" tabindex="-1" aria-labelledby="editSubCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-sub-category-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-sub-category-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editSubCategoryModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Sub Kategori Produk
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-sub-category-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-category_id" class="form-label">Kategori Utama <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-sub-category-category_id" name="category_id" required>
                                                                    <option value="">Pilih Kategori</option>
                                                                    @foreach ($data_category as $cat)
                                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-sub-category-category_id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-parent_id" class="form-label">Parent Sub Kategori (Opsional)</label>
                                                                <select class="form-control" id="edit-sub-category-parent_id" name="parent_id">
                                                                    <option value="">Tidak ada</option>
                                                                    @foreach ($data_sub_category as $sub)
                                                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-sub-category-parent_id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-name" class="form-label">Nama Sub Kategori <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-sub-category-name" name="name" placeholder="Contoh: Handphone" required>
                                                                <div class="invalid-feedback" id="edit-sub-category-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-slug" class="form-label">Slug Sub Kategori <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-sub-category-slug" name="slug" placeholder="Contoh: handphone" required readonly>
                                                                <div class="invalid-feedback" id="edit-sub-category-slug-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-sub-category-description" name="description" rows="4" placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="edit-sub-category-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-sub-category-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-sub-category-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="edit-sub-category-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="edit-sub-category-order-display-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-sub-category-image" class="form-label">Gambar Sub Kategori (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control" id="edit-sub-category-image" name="image" accept=".jpg,.jpeg,.png" onchange="validateEditSubCategoryImageUpload()">
                                                                <div class="invalid-feedback" id="edit-sub-category-image-error"></div>
                                                                <img id="edit-sub-category-image-preview" src="#" alt="Gambar Preview" style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-sub-category-image-preview-canvas" style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-update-sub">
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

                                <!-- Modal Show Sub Kategori -->
                                <div class="modal fade" id="showSubCategoryModal" tabindex="-1" aria-labelledby="showSubCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showSubCategoryModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Sub Kategori Produk
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-sub-category-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-category_id" class="form-label">Kategori Utama</label>
                                                            <input type="text" class="form-control" id="show-sub-category-category_id" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-parent_id" class="form-label">Parent Sub Kategori</label>
                                                            <input type="text" class="form-control" id="show-sub-category-parent_id" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-name" class="form-label">Nama Sub Kategori</label>
                                                            <input type="text" class="form-control" id="show-sub-category-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-slug" class="form-label">Slug Sub Kategori</label>
                                                            <input type="text" class="form-control" id="show-sub-category-slug" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-sub-category-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-sub-category-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-sub-category-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-sub-category-image" class="form-label">Gambar Sub Kategori</label>
                                                            <div id="show-sub-category-image"></div>
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
        $(document).ready(function() {
            // Fungsi generate slug
            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '') // Hapus karakter non-alfanumerik kecuali spasi dan tanda hubung
                    .replace(/\s+/g, '-') // Ganti spasi dengan tanda hubung
                    .replace(/-+/g, '-'); // Ganti beberapa tanda hubung berurutan dengan satu tanda hubung
            }

            // Generate slug otomatis saat input name di modal tambah
            $('#sub-category-name').on('input', function() {
                const name = $(this).val();
                const slug = generateSlug(name);
                $('#sub-category-slug').val(slug);
            });

            // Generate slug otomatis saat input name di modal edit
            $('#edit-sub-category-name').on('input', function() {
                const name = $(this).val();
                const slug = generateSlug(name);
                $('#edit-sub-category-slug').val(slug);
            });

            // Fungsi spinner tombol (loading state)
            function setButtonLoading(button, isLoading, loadingText = 'Menyimpan...') {
                if (!button || button.length === 0) return;
                if (isLoading) {
                    button.data('original-html', button.html());
                    button.prop('disabled', true).html(`<span class="spinner-border spinner-border-sm"></span> ${loadingText}`);
                } else {
                    const original = button.data('original-html') || '<i class="fa fa-save"></i> Simpan';
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
                        $.each(errors, function(key, value) {
                            $(`#${target.replace('#', '')}-${key}-error`).text(value[0]);
                            $(`#${target.replace('#', '')}-${key}`).addClass('is-invalid');
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

            // Submit Tambah Sub Kategori
            $('#add-sub-category-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save-sub');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#sub-category-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('product-sub-categories.store') }}",
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
                            $('#addSubCategoryModal').modal('hide');
                            form[0].reset();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#sub-category-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik Edit Sub Kategori
            $(document).on('click', '.btn-edit-sub-category', function() {
                const id = $(this).data('id');
                $('#edit-sub-category-error-message').html('');
                $('#edit-sub-category-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('product-sub-categories.edit', ':id') }}`.replace(':id', id),
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response && response.id) {
                            $('#edit-sub-category-id').val(response.id);
                            $('#edit-sub-category-category_id').val(response.category_id);
                            $('#edit-sub-category-parent_id').val(response.parent_id);
                            $('#edit-sub-category-name').val(response.name);
                            $('#edit-sub-category-slug').val(response.slug); // Set slug awal dari response
                            $('#edit-sub-category-description').val(response.description);
                            $('#edit-sub-category-status').val(response.status);
                            $('#edit-sub-category-order-display').val(response.order_display);
                            const imageUrl = response.image ? `{{ asset('upload/product_sub_categories') }}/${response.image}` : null;
                            if (imageUrl) {
                                $('#edit-sub-category-image-preview').attr('src', imageUrl).show();
                            }
                            $('#editSubCategoryModal').modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Data tidak ditemukan.' });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-sub-category-error-message');
                    }
                });
            });

            // Submit Edit Sub Kategori
            $('#edit-sub-category-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update-sub');
                const id = $('#edit-sub-category-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-sub-category-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('product-sub-categories.update', ':id') }}`.replace(':id', id),
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
                            $('#editSubCategoryModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-sub-category-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Klik Show Sub Kategori
            $(document).on('click', '.btn-show-sub-category', function() {
                const id = $(this).data('id');
                $('#show-sub-category-error-message').html('');

                $.ajax({
                    url: `{{ route('product-sub-categories.show', ':id') }}`.replace(':id', id),
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response) {
                            $('#show-sub-category-category_id').val(response.category ? response.category.name : 'Tidak ada');
                            $('#show-sub-category-parent_id').val(response.parent ? response.parent.name : 'Tidak ada');
                            $('#show-sub-category-name').val(response.name || '');
                            $('#show-sub-category-slug').val(response.slug || '');
                            $('#show-sub-category-description').val(response.description || '');
                            $('#show-sub-category-status').val(response.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                            $('#show-sub-category-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ? `{{ asset('upload/product_sub_categories') }}/${response.image}` : null;
                            if (imageUrl) {
                                $('#show-sub-category-image').html(`<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;"></a>`);
                            } else {
                                $('#show-sub-category-image').html('Tidak ada gambar');
                            }
                            $('#showSubCategoryModal').modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Data tidak ditemukan.' });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-sub-category-error-message');
                    }
                });
            });

            // Konfirmasi Hapus
            window.confirmDelete = function(id) {
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
                            url: `{{ route('product-sub-categories.destroy', ':id') }}`.replace(':id', id),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
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

            // Validasi gambar untuk modal tambah
            function validateSubCategoryImageUpload() {
                const fileInput = document.getElementById('sub-category-image');
                const errorDiv = document.getElementById('sub-category-image-error');
                const previewImage = document.getElementById('sub-category-image-preview');
                const previewCanvas = document.getElementById('sub-category-image-preview-canvas');
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

            // Validasi gambar untuk modal edit
            function validateEditSubCategoryImageUpload() {
                const fileInput = document.getElementById('edit-sub-category-image');
                const errorDiv = document.getElementById('edit-sub-category-image-error');
                const previewImage = document.getElementById('edit-sub-category-image-preview');
                const previewCanvas = document.getElementById('edit-sub-category-image-preview-canvas');
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
        });
    </script>
@endpush