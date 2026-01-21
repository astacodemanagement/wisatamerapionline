@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
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

        .nav-tabs .nav-link {
            color: #000;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        .ql-editor {
            min-height: 100px;
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .image-preview {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
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
                                        @can('product-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addProductModal">
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
                                            <th>Kode</th>
                                            <th>Kategori</th>
                                            <th>Satuan</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th>Stok</th>
                                            <th>Stok Minimum</th>
                                            <th>Status</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_product as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->code }}</td>
                                                <td>{{ $p->category ? $p->category->name : 'Tidak ada' }}</td>
                                                <td>{{ $p->unit ? $p->unit->name : 'Tidak ada' }}</td>
                                                <td>{{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                                                <td>{{ number_format($p->selling_price, 0, ',', '.') }}</td>
                                                <td>{{ $p->stock }}</td>
                                                <td>{{ $p->min_stock }}</td>
                                                <td>{{ $p->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-product"
                                                        data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('product-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-product"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-info btn-sm btn-manage-images"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-image"></i> Kelola Gambar
                                                        </button>
                                                    @endcan
                                                    @can('product-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST"
                                                            action="{{ route('products.destroy', $p->id) }}"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Tambah Produk -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <form id="add-product-form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="addProductModalLabel">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- BARU: Field pencarian produk untuk duplikasi -->
                            <div class="form-group mb-3">
                                <label for="search-product">Cari Produk untuk Duplikasi (Opsional) masukan minimal 3
                                    karakter</label>
                                <input type="text" id="search-product" class="form-control"
                                    placeholder="Ketik nama atau kode produk...">
                                <div id="search-results" class="list-group" style="display: none;"></div>
                            </div>
                            <ul class="nav nav-tabs" id="addProductTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general"
                                        role="tab" aria-controls="general" aria-selected="true">General</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="attribute-tab" data-bs-toggle="tab" href="#attribute"
                                        role="tab" aria-controls="attribute" aria-selected="false">Atribut</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="review-tab" data-bs-toggle="tab" href="#review"
                                        role="tab" aria-controls="review" aria-selected="false">Ulasan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="description-tab" data-bs-toggle="tab" href="#description"
                                        role="tab" aria-controls="description" aria-selected="false">Deskripsi</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-3" id="addProductTabContent">
                                <div class="tab-pane fade show active" id="general" role="tabpanel"
                                    aria-labelledby="general-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="name">Nama Produk <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" id="name"
                                                    value="{{ old('name') }}" required>
                                                <div class="invalid-feedback" id="name-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                                <input type="text" name="slug" class="form-control" id="slug"
                                                    value="{{ old('slug') }}" required readonly>
                                                <div class="invalid-feedback" id="slug-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="code">Kode Produk <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="code" class="form-control" id="code"
                                                    value="{{ old('code') }}" required>
                                                <div class="invalid-feedback" id="code-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="category_id">Kategori Produk <span
                                                        class="text-danger">*</span></label>
                                                <select id="category_id" name="category_id" class="form-control"
                                                    required>
                                                    <option value="" disabled selected>--Pilih Kategori Produk--
                                                    </option>
                                                    @foreach ($product_categories as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ old('category_id') == $p->id ? 'selected' : '' }}>
                                                            {{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id="category_id-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="sub_category_id">Sub Kategori Produk (Opsional)</label>
                                                <select id="sub_category_id" name="sub_category_id" class="form-control">
                                                    <option value="">--Tidak ada sub kategori--</option>
                                                    @foreach ($product_sub_categories as $sub)
                                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id="sub_category_id-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="main-image" class="form-label">Gambar Utama (JPG, JPEG, PNG,
                                                    Maks 4MB)</label>
                                                <input type="file" class="form-control" id="main-image"
                                                    name="image" accept=".jpg,.jpeg,.png"
                                                    onchange="validateImageUpload()">
                                                <div class="invalid-feedback" id="image-error"></div>
                                                <div id="image-preview" class="image-preview-container"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="status_display">Status Tampilan <span
                                                        class="text-danger">*</span></label>
                                                <select id="status_display" name="status_display" class="form-control"
                                                    required>
                                                    <option value="" disabled selected>--Pilih Status Tampilan--
                                                    </option>
                                                    <option value="active"
                                                        {{ old('status_display') == 'active' ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="nonactive"
                                                        {{ old('status_display') == 'nonactive' ? 'selected' : '' }}>Non
                                                        Active</option>
                                                </select>
                                                <div class="invalid-feedback" id="status_display-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="unit_id">Satuan <span class="text-danger">*</span></label>
                                                <select id="unit_id" name="unit_id" class="form-control" required>
                                                    <option value="" disabled selected>--Pilih Satuan--</option>
                                                    @foreach ($units as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id="unit_id-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="purchase_price">Harga Beli <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="purchase_price" class="form-control"
                                                    id="purchase_price" value="{{ old('purchase_price', '0') }}" required
                                                    oninput="formatPrice(this)">
                                                <div class="invalid-feedback" id="purchase_price-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="selling_price">Harga Jual <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="selling_price" class="form-control"
                                                    id="selling_price" value="{{ old('selling_price', '0') }}" required
                                                    oninput="formatPrice(this)">
                                                <div class="invalid-feedback" id="selling_price-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="stock">Stok <span class="text-danger">*</span></label>
                                                <input type="number" name="stock" class="form-control" id="stock"
                                                    value="{{ old('stock', 0) }}" required min="0">
                                                <div class="invalid-feedback" id="stock-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="min_stock">Stok Minimum <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="min_stock" class="form-control"
                                                    id="min_stock" value="{{ old('min_stock', 0) }}" required
                                                    min="0">
                                                <div class="invalid-feedback" id="min_stock-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="status">Status <span class="text-danger">*</span></label>
                                                <div>
                                                    <label class="me-3">
                                                        <input type="radio" name="status" value="active" required
                                                            {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                                        Active
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="nonactive" required
                                                            {{ old('status', 'active') == 'nonactive' ? 'checked' : '' }}>
                                                        Non Active
                                                    </label>
                                                </div>
                                                <div class="invalid-feedback" id="status-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="order_display">Urutan</label>
                                                <input type="number" name="order_display" class="form-control"
                                                    id="order_display" value="{{ old('order_display', 0) }}"
                                                    min="0">
                                                <div class="invalid-feedback" id="order_display-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="attribute" role="tabpanel"
                                    aria-labelledby="attribute-tab">
                                    <div class="form-group mb-3">
                                        <label for="attribute">Atribut</label>
                                        <div id="attribute-editor"></div>
                                        <input type="hidden" name="attribute" id="attribute">
                                        <div class="invalid-feedback" id="attribute-error"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                    <div class="form-group mb-3">
                                        <label for="review">Ulasan</label>
                                        <div id="review-editor"></div>
                                        <input type="hidden" name="review" id="review">
                                        <div class="invalid-feedback" id="review-error"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="description" role="tabpanel"
                                    aria-labelledby="description-tab">
                                    <div class="form-group mb-3">
                                        <label for="description">Deskripsi</label>
                                        <div id="description-editor"></div>
                                        <input type="hidden" name="description" id="description">
                                        <div class="invalid-feedback" id="description-error"></div>
                                    </div>
                                    <div class="form-group mb-3" style="display:none;">
                                        <label for="supplier">Supplier</label>
                                        <div id="supplier-editor"></div>
                                        <input type="hidden" name="supplier" id="supplier">
                                        <div class="invalid-feedback" id="supplier-error"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="product-error-message" class="text-danger small mt-3"></div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="submit" class="btn btn-primary" id="btn-save"><i class="fa fa-save"></i>
                                Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i> Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Produk -->
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <form id="edit-product-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-product-id" name="id" />
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="editProductModalLabel">
                                <i class="bi bi-pencil-square me-2"></i>Edit Produk
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" id="editProductTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="edit-general-tab" data-bs-toggle="tab"
                                        href="#edit-general" role="tab" aria-controls="edit-general"
                                        aria-selected="true">General</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-attribute-tab" data-bs-toggle="tab"
                                        href="#edit-attribute" role="tab" aria-controls="edit-attribute"
                                        aria-selected="false">Atribut</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-review-tab" data-bs-toggle="tab" href="#edit-review"
                                        role="tab" aria-controls="edit-review" aria-selected="false">Ulasan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-description-tab" data-bs-toggle="tab"
                                        href="#edit-description" role="tab" aria-controls="edit-description"
                                        aria-selected="false">Deskripsi</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-3" id="editProductTabContent">
                                <div class="tab-pane fade show active" id="edit-general" role="tabpanel"
                                    aria-labelledby="edit-general-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="edit_name">Nama Produk <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" id="edit_name"
                                                    required>
                                                <div class="invalid-feedback" id="edit_name-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_slug">Slug <span class="text-danger">*</span></label>
                                                <input type="text" name="slug" class="form-control" id="edit_slug"
                                                    required readonly>
                                                <div class="invalid-feedback" id="edit_slug-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_code">Kode Produk <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="code" class="form-control" id="edit_code"
                                                    required>
                                                <div class="invalid-feedback" id="edit_code-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_category_id">Kategori Produk <span
                                                        class="text-danger">*</span></label>
                                                <select id="edit_category_id" name="category_id" class="form-control"
                                                    required>
                                                    <option value="" disabled>--Pilih Kategori Produk--</option>
                                                    @foreach ($product_categories as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id="edit_category_id-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_sub_category_id">Sub Kategori Produk (Opsional)</label>
                                                <select id="edit_sub_category_id" name="sub_category_id"
                                                    class="form-control">
                                                    <option value="">--Tidak ada sub kategori--</option>
                                                    @foreach ($product_sub_categories as $sub)
                                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id="edit_sub_category_id-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit-main-image" class="form-label">Gambar Utama (JPG, JPEG,
                                                    PNG, Maks 4MB)</label>
                                                <input type="file" class="form-control" id="edit-main-image"
                                                    name="image" accept=".jpg,.jpeg,.png"
                                                    onchange="validateEditImageUpload()">
                                                <div class="invalid-feedback" id="edit_image-error"></div>
                                                <div id="edit_image-preview" class="image-preview-container"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_status_display">Status Tampilan <span
                                                        class="text-danger">*</span></label>
                                                <select id="edit_status_display" name="status_display"
                                                    class="form-control" required>
                                                    <option value="" disabled selected>--Pilih Status Tampilan--
                                                    </option>
                                                    <option value="active">Active</option>
                                                    <option value="nonactive">Non Active</option>
                                                </select>
                                                <div class="invalid-feedback" id="edit_status_display-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="edit_unit_id">Satuan <span
                                                        class="text-danger">*</span></label>
                                                <select id="edit_unit_id" name="unit_id" class="form-control" required>
                                                    <option value="" disabled>--Pilih Satuan--</option>
                                                    @foreach ($units as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id="edit_unit_id-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_purchase_price">Harga Beli <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="purchase_price" class="form-control"
                                                    id="edit_purchase_price" required oninput="formatPrice(this)">
                                                <div class="invalid-feedback" id="edit_purchase_price-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_selling_price">Harga Jual <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="selling_price" class="form-control"
                                                    id="edit_selling_price" required oninput="formatPrice(this)">
                                                <div class="invalid-feedback" id="edit_selling_price-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_stock">Stok <span class="text-danger">*</span></label>
                                                <input type="number" name="stock" class="form-control"
                                                    id="edit_stock" required min="0">
                                                <div class="invalid-feedback" id="edit_stock-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_min_stock">Stok Minimum <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="min_stock" class="form-control"
                                                    id="edit_min_stock" required min="0">
                                                <div class="invalid-feedback" id="edit_min_stock-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="edit_status">Status <span class="text-danger">*</span></label>
                                                <div>
                                                    <label class="me-3">
                                                        <input type="radio" name="status" value="active" required>
                                                        Active
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="nonactive" required>
                                                        Non Active
                                                    </label>
                                                </div>
                                                <div class="invalid-feedback" id="edit_status-error"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_order_display">Urutan</label>
                                                <input type="number" name="order_display" class="form-control"
                                                    id="edit_order_display" min="0">
                                                <div class="invalid-feedback" id="edit_order_display-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="edit-attribute" role="tabpanel"
                                    aria-labelledby="edit-attribute-tab">
                                    <div class="form-group mb-3">
                                        <label for="edit_attribute">Atribut</label>
                                        <div id="edit-attribute-editor"></div>
                                        <input type="hidden" name="attribute" id="edit_attribute">
                                        <div class="invalid-feedback" id="edit_attribute-error"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="edit-review" role="tabpanel"
                                    aria-labelledby="edit-review-tab">
                                    <div class="form-group mb-3">
                                        <label for="edit_review">Ulasan</label>
                                        <div id="edit-review-editor"></div>
                                        <input type="hidden" name="review" id="edit_review">
                                        <div class="invalid-feedback" id="edit_review-error"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="edit-description" role="tabpanel"
                                    aria-labelledby="edit-description-tab">
                                    <div class="form-group mb-3">
                                        <label for="edit_description">Deskripsi</label>
                                        <div id="edit-description-editor"></div>
                                        <input type="hidden" name="description" id="edit_description">
                                        <div class="invalid-feedback" id="edit_description-error"></div>
                                    </div>
                                    <div class="form-group mb-3" style="display:none;">
                                        <label for="edit_supplier">Supplier</label>
                                        <div id="edit-supplier-editor"></div>
                                        <input type="hidden" name="supplier" id="edit_supplier">
                                        <div class="invalid-feedback" id="edit_supplier-error"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="edit-product-error-message" class="text-danger small mt-3"></div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="submit" class="btn btn-primary" id="btn-update"><i class="fa fa-save"></i>
                                Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i> Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Tampil Produk -->
        <div class="modal fade" id="showProductModal" tabindex="-1" aria-labelledby="showProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white" id="showProductModalLabel">
                            <i class="bi bi-eye me-2"></i>Detail Produk
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="show-product-error-message" class="text-danger small mb-2"></div>
                        <ul class="nav nav-tabs" id="showProductTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="show-general-tab" data-bs-toggle="tab"
                                    href="#show-general" role="tab" aria-controls="show-general"
                                    aria-selected="true">General</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="show-images-tab" data-bs-toggle="tab" href="#show-images"
                                    role="tab" aria-controls="show-images" aria-selected="false">Images</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="show-attribute-tab" data-bs-toggle="tab" href="#show-attribute"
                                    role="tab" aria-controls="show-attribute" aria-selected="false">Atribut</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="show-review-tab" data-bs-toggle="tab" href="#show-review"
                                    role="tab" aria-controls="show-review" aria-selected="false">Ulasan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="show-description-tab" data-bs-toggle="tab" href="#show-description"
                                    role="tab" aria-controls="show-description" aria-selected="false">Deskripsi</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="showProductTabContent">
                            <div class="tab-pane fade show active" id="show-general" role="tabpanel"
                                aria-labelledby="show-general-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="show_name">Nama Produk</label>
                                            <input type="text" class="form-control" id="show_name" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_slug">Slug</label>
                                            <input type="text" class="form-control" id="show_slug" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_code">Kode Produk</label>
                                            <input type="text" class="form-control" id="show_code" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_category_id">Kategori Produk</label>
                                            <input type="text" class="form-control" id="show_category_id" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_sub_category_id">Sub Kategori Produk</label>
                                            <input type="text" class="form-control" id="show_sub_category_id"
                                                readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_image">Gambar Utama</label>
                                            <div id="show_image" class="image-preview-container"></div>
                                            <div class="text-muted small" id="show_image_error" style="display: none;">
                                                Tidak ada gambar utama tersedia.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="show_unit_id">Satuan</label>
                                            <input type="text" class="form-control" id="show_unit_id" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_purchase_price">Harga Beli</label>
                                            <input type="text" class="form-control" id="show_purchase_price" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_selling_price">Harga Jual</label>
                                            <input type="text" class="form-control" id="show_selling_price" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_stock">Stok</label>
                                            <input type="text" class="form-control" id="show_stock" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_min_stock">Stok Minimum</label>
                                            <input type="text" class="form-control" id="show_min_stock" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="show_description">Deskripsi</label>
                                            <div id="show-description-viewer" class="form-control"
                                                style="min-height: 100px; background: #f8f9fa;"></div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_status">Status</label>
                                            <input type="text" class="form-control" id="show_status" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="show_order_display">Urutan</label>
                                            <input type="text" class="form-control" id="show_order_display" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="show-images" role="tabpanel"
                                aria-labelledby="show-images-tab">
                                <div class="form-group mb-3">
                                    <label for="show_images">Gambar Tambahan</label>
                                    <div id="show_images" class="image-preview-container"></div>
                                    <div class="text-muted small" id="show_images_error" style="display: none;">Tidak ada
                                        gambar tambahan tersedia.</div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="show-attribute" role="tabpanel"
                                aria-labelledby="show-attribute-tab">
                                <div class="form-group mb-3">
                                    <label for="show_attribute">Atribut</label>
                                    <div id="show-attribute-viewer" class="form-control"
                                        style="min-height: 100px; background: #f8f9fa;"></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="show-review" role="tabpanel"
                                aria-labelledby="show-review-tab">
                                <div class="form-group mb-3">
                                    <label for="show_review">Ulasan</label>
                                    <div id="show-review-viewer" class="form-control"
                                        style="min-height: 100px; background: #f8f9fa;"></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="show-description" role="tabpanel"
                                aria-labelledby="show-description-tab">
                                <div class="form-group mb-3">
                                    <label for="show_description">Deskripsi</label>
                                    <div id="show-description-viewer" class="form-control"
                                        style="min-height: 100px; background: #f8f9fa;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fa fa-undo"></i> Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Kelola Gambar Tambahan -->
        <div class="modal fade" id="manageImagesModal" tabindex="-1" aria-labelledby="manageImagesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <form id="manage-images-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="manage-product-id" name="product_id" />
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="manageImagesModalLabel">
                                <i class="bi bi-image me-2"></i>Kelola Gambar Tambahan
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="additional-images" class="form-label">Unggah Gambar Tambahan (JPG, JPEG, PNG,
                                    Maks 4MB)</label>
                                <input type="file" class="form-control" id="additional-images"
                                    name="additional_images[]" accept=".jpg,.jpeg,.png" multiple
                                    onchange="validateAdditionalImagesUpload()">
                                <div class="invalid-feedback" id="additional-images-error"></div>
                                <div id="additional-images-preview" class="image-preview-container"></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table border table-striped table-bordered" id="additional-images-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="additional-images-table-body">
                                    </tbody>
                                </table>
                            </div>
                            <div id="manage-images-error-message" class="text-danger small mt-3"></div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="submit" class="btn btn-primary" id="btn-save-images"><i
                                    class="fa fa-save"></i> Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i> Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('template/back/dist/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/back/dist/js/datatable/datatable-basic.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>



    <script>
        function formatPrice(input) {
            let value = input.value.replace(/[^0-9.]/g, '');
            let parts = value.split('.');
            if (parts.length > 2) parts = [parts[0], parts[1]];
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.value = parts.join('.');
        }

        function validateImageUpload() {
            const fileInput = document.getElementById('main-image');
            const errorDiv = document.getElementById('image-error');
            const previewContainer = document.getElementById('image-preview');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB
            const allowedTypes = ['image/jpeg', 'image/png'];

            // Reset
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewContainer.innerHTML = '';
            fileInput.classList.remove('is-invalid');

            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    errorDiv.textContent = 'File harus berupa JPG, JPEG, atau PNG.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = '';
                    return;
                }
                if (file.size > maxSize) {
                    errorDiv.textContent = 'Ukuran file maksimal 4MB.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        }

        function validateEditImageUpload() {
            const fileInput = document.getElementById('edit-main-image');
            const errorDiv = document.getElementById('edit_image-error');
            const previewContainer = document.getElementById('edit_image-preview');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB
            const allowedTypes = ['image/jpeg', 'image/png'];

            // Reset
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewContainer.innerHTML = '';
            fileInput.classList.remove('is-invalid');

            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    errorDiv.textContent = 'File harus berupa JPG, JPEG, atau PNG.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = '';
                    return;
                }
                if (file.size > maxSize) {
                    errorDiv.textContent = 'Ukuran file maksimal 4MB.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        }

        function validateAdditionalImagesUpload() {
            const fileInput = document.getElementById('additional-images');
            const errorDiv = document.getElementById('additional-images-error');
            const previewContainer = document.getElementById('additional-images-preview');
            const files = fileInput.files;
            const maxSize = 4 * 1024 * 1024; // 4 MB
            const allowedTypes = ['image/jpeg', 'image/png'];

            // Reset
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewContainer.innerHTML = '';
            fileInput.classList.remove('is-invalid');

            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!allowedTypes.includes(file.type)) {
                        errorDiv.textContent = 'Semua file harus berupa JPG, JPEG, atau PNG.';
                        errorDiv.style.display = 'block';
                        fileInput.classList.add('is-invalid');
                        fileInput.value = '';
                        previewContainer.innerHTML = '';
                        return;
                    }
                    if (file.size > maxSize) {
                        errorDiv.textContent = 'Ukuran setiap file maksimal 4MB.';
                        errorDiv.style.display = 'block';
                        fileInput.classList.add('is-invalid');
                        fileInput.value = '';
                        previewContainer.innerHTML = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = new Image();
                        img.src = e.target.result;
                        img.className = 'image-preview';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus produk ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(`delete-form-${id}`);
                    const formData = new FormData(form);

                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
        }

        function confirmDeleteImage(productId, imageId) {
            Swal.fire({
                title: 'Konfirmasi Hapus Gambar',
                text: 'Apakah Anda yakin ingin menghapus gambar ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('products.deleteAdditionalImage', ['id' => ':product_id', 'imageId' => ':image_id']) }}`
                            .replace(':product_id', productId)
                            .replace(':image_id', imageId),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message
                            }).then(() => {
                                // Refresh the images table
                                loadAdditionalImages(productId);
                            });
                        },
                        error: function(xhr) {
                            handleAjaxError(xhr, '#manage-images-error-message');
                        }
                    });
                }
            });
        }

        function loadAdditionalImages(productId) {
            $.ajax({
                url: `{{ route('products.manageImages', ':id') }}`.replace(':id', productId),
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const tbody = $('#additional-images-table-body');
                    tbody.empty();
                    if (response.images && response.images.length > 0) {
                        response.images.forEach((image, index) => {
                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><img src="{{ asset('upload/products/additional') }}/${image.image_name}" class="image-preview" alt="Gambar"></td>
                                    <td>${image.order_display}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-image" data-product-id="${productId}" data-image-id="${image.id}">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>`;
                            tbody.append(row);
                        });
                    } else {
                        tbody.append(
                            '<tr><td colspan="4" class="text-center">Tidak ada gambar tambahan tersedia.</td></tr>'
                            );
                    }
                },
                error: function(xhr) {
                    handleAjaxError(xhr, '#manage-images-error-message');
                }
            });
        }

        $(document).ready(function() {
            // Set CSRF token for all AJAX requests globally
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // QuillJS configuration
            const quillConfig = {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'direction': 'rtl'
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'font': []
                        }],
                        [{
                            'align': []
                        }],
                        ['clean']
                    ]
                }
            };

            // CKEditor configuration for show modal
            const readOnlyConfig = {
                toolbar: []
            };

            let descriptionQuill, attributeQuill, reviewQuill, supplierQuill;
            let editDescriptionQuill, editAttributeQuill, editReviewQuill, editSupplierQuill;

            // Initialize QuillJS for add modal
            descriptionQuill = new Quill('#description-editor', quillConfig);
            descriptionQuill.on('text-change', function() {
                $('#description').val(descriptionQuill.root.innerHTML);
            });

            attributeQuill = new Quill('#attribute-editor', quillConfig);
            attributeQuill.on('text-change', function() {
                $('#attribute').val(attributeQuill.root.innerHTML);
            });

            reviewQuill = new Quill('#review-editor', quillConfig);
            reviewQuill.on('text-change', function() {
                $('#review').val(reviewQuill.root.innerHTML);
            });

            supplierQuill = new Quill('#supplier-editor', quillConfig);
            supplierQuill.on('text-change', function() {
                $('#supplier').val(supplierQuill.root.innerHTML);
            });

            // Initialize QuillJS for edit modal
            editDescriptionQuill = new Quill('#edit-description-editor', quillConfig);
            editDescriptionQuill.on('text-change', function() {
                $('#edit_description').val(editDescriptionQuill.root.innerHTML);
            });

            editAttributeQuill = new Quill('#edit-attribute-editor', quillConfig);
            editAttributeQuill.on('text-change', function() {
                $('#edit_attribute').val(editAttributeQuill.root.innerHTML);
            });

            editReviewQuill = new Quill('#edit-review-editor', quillConfig);
            editReviewQuill.on('text-change', function() {
                $('#edit_review').val(editReviewQuill.root.innerHTML);
            });

            editSupplierQuill = new Quill('#edit-supplier-editor', quillConfig);
            editSupplierQuill.on('text-change', function() {
                $('#edit_supplier').val(editSupplierQuill.root.innerHTML);
            });

            // Initialize CKEditors for show modal in read-only mode
            ClassicEditor
                .create(document.querySelector('#show-description-viewer'), readOnlyConfig)
                .then(editor => {
                    editor.isReadOnly = true;
                })
                .catch(error => console.error('Error initializing show description viewer:', error));

            ClassicEditor
                .create(document.querySelector('#show-attribute-viewer'), readOnlyConfig)
                .then(editor => {
                    editor.isReadOnly = true;
                })
                .catch(error => console.error('Error initializing show attribute viewer:', error));

            ClassicEditor
                .create(document.querySelector('#show-review-viewer'), readOnlyConfig)
                .then(editor => {
                    editor.isReadOnly = true;
                })
                .catch(error => console.error('Error initializing show review viewer:', error));



            // Generate slug otomatis untuk tambah produk
            $('#name').on('input', function() {
                const name = $(this).val();
                const slug = generateSlug(name);
                $('#slug').val(slug);
            });

            // Generate slug otomatis untuk edit produk
            $('#edit_name').on('input', function() {
                const name = $(this).val();
                const slug = generateSlug(name);
                $('#edit_slug').val(slug);
            });

            // Fungsi untuk menghasilkan slug
            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }

            // Fungsi untuk menangani loading tombol
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
                            let field = key.replace(/\./g, '_');
                            $(`#${target.replace('#', '')}-${field}-error`).text(value[0]).show();
                            $(`#${target.replace('#', '')}-${field}`).addClass('is-invalid');
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

            // Submit form tambah produk
            $('#add-product-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                // Konversi harga ke format numerik untuk pengiriman
                ['purchase_price', 'selling_price'].forEach(field => {
                    let value = $(`#${field}`).val().replace(/[^0-9]/g, '');
                    formData.set(field, value);
                });

                // Set Quill contents to hidden inputs
                formData.set('description', descriptionQuill.root.innerHTML);
                formData.set('attribute', attributeQuill.root.innerHTML);
                formData.set('review', reviewQuill.root.innerHTML);
                formData.set('supplier', supplierQuill.root.innerHTML);

                setButtonLoading(btn, true);
                $('#product-error-message').html('');

                $.ajax({
                    url: "{{ route('products.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        setButtonLoading(btn, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            $('#addProductModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        setButtonLoading(btn, false);
                        handleAjaxError(xhr, '#product-error-message');
                    }
                });
            });
  // Show product
            $(document).on('click', '.btn-show-product', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ route('products.show', ':id') }}`.replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#show_name').val(response.name);
                        $('#show_slug').val(response.slug);
                        $('#show_code').val(response.code);
                        $('#show_category_id').val(response.category ? response.category.name :
                            'Tidak ada');
                        $('#show_sub_category_id').val(response.sub_category ? response
                            .sub_category.name : 'Tidak ada');
                        $('#show_unit_id').val(response.unit ? response.unit.name :
                        'Tidak ada');
                        $('#show_purchase_price').val(new Intl.NumberFormat('id-ID').format(
                            response.purchase_price));
                        $('#show_selling_price').val(new Intl.NumberFormat('id-ID').format(
                            response.selling_price));
                        $('#show_stock').val(response.stock);
                        $('#show_min_stock').val(response.min_stock);
                        $('#show_status').val(response.status === 'active' ? 'Aktif' :
                            'Tidak Aktif');
                        $('#show_order_display').val(response.order_display || '0');

                        // Show main image
                        const showImageContainer = $('#show_image');
                        const showImageError = $('#show_image_error');
                        showImageContainer.empty();
                        if (response.image) {
                            showImageError.hide();
                            const img =
                                `<img src="{{ asset('upload/products') }}/${response.image}" class="image-preview" alt="Gambar Utama">`;
                            showImageContainer.append(img);
                        } else {
                            showImageError.show();
                        }

                        // Show additional images
                        const showImagesContainer = $('#show_images');
                        const showImagesError = $('#show_images_error');
                        showImagesContainer.empty();
                        if (response.images && response.images.length > 0) {
                            showImagesError.hide();
                            response.images.forEach(image => {
                                const img =
                                    `<img src="{{ asset('upload/products/additional') }}/${image.image_name}" class="image-preview" alt="Gambar Tambahan">`;
                                showImagesContainer.append(img);
                            });
                        } else {
                            showImagesError.show();
                        }

                        // Set CKEditor content
                        $('#show-description-viewer').html(response.description || '');
                        $('#show-attribute-viewer').html(response.attribute || '');
                        $('#show-review-viewer').html(response.review || '');

                        $('#showProductModal').modal('show');
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-product-error-message');
                    }
                });
            });
          

            // Edit product
            $(document).on('click', '.btn-edit-product', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ route('products.edit', ':id') }}`.replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#edit-product-id').val(response.id);
                        $('#edit_name').val(response.name);
                        $('#edit_slug').val(response.slug);
                        $('#edit_code').val(response.code);
                        $('#edit_category_id').val(response.category_id);
                        $('#edit_sub_category_id').val(response.sub_category_id);
                        $('#edit_unit_id').val(response.unit_id);
                        $('#edit_purchase_price').val(new Intl.NumberFormat('id-ID').format(
                            response.purchase_price));
                        $('#edit_selling_price').val(new Intl.NumberFormat('id-ID').format(
                            response.selling_price));
                        $('#edit_stock').val(response.stock);
                        $('#edit_min_stock').val(response.min_stock);
                        $(`input[name="status"][value="${response.status}"]`).prop('checked',
                            true);
                        $('#edit_order_display').val(response.order_display || '0');
                        $('#edit_status_display').val(response.status_display);

                        // Set Quill content
                        editDescriptionQuill.setContents([]);
                        editDescriptionQuill.root.innerHTML = response.description || '';
                        editAttributeQuill.setContents([]);
                        editAttributeQuill.root.innerHTML = response.attribute || '';
                        editReviewQuill.setContents([]);
                        editReviewQuill.root.innerHTML = response.review || '';
                        editSupplierQuill.setContents([]);
                        editSupplierQuill.root.innerHTML = response.supplier || '';

                        // Show existing main image
                        const editImagePreview = $('#edit_image-preview');
                        editImagePreview.empty();
                        if (response.image) {
                            const img =
                                `<img src="{{ asset('upload/products') }}/${response.image}" class="image-preview" alt="Gambar Utama">`;
                            editImagePreview.append(img);
                        }

                        $('#editProductModal').modal('show');
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    }
                });
            });

            // Submit form edit produk
            $('#edit-product-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const formData = new FormData(form[0]);
                const id = $('#edit-product-id').val();

                // Konversi harga ke format numerik untuk pengiriman
                ['purchase_price', 'selling_price'].forEach(field => {
                    let value = $(`#edit_${field}`).val().replace(/[^0-9]/g, '');
                    formData.set(field, value);
                });

                // Set Quill contents to hidden inputs
                formData.set('description', editDescriptionQuill.root.innerHTML);
                formData.set('attribute', editAttributeQuill.root.innerHTML);
                formData.set('review', editReviewQuill.root.innerHTML);
                formData.set('supplier', editSupplierQuill.root.innerHTML);

                setButtonLoading(btn, true, 'Mengupdate...');
                $('#edit-product-error-message').html('');

                $.ajax({
                    url: `{{ route('products.update', ':id') }}`.replace(':id', id),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        setButtonLoading(btn, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            $('#editProductModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        setButtonLoading(btn, false);
                        handleAjaxError(xhr, '#edit-product-error-message');
                    }
                });
            });



            // Manage additional images
            $(document).on('click', '.btn-manage-images', function() {
                const productId = $(this).data('id');
                $('#manage-product-id').val(productId);
                loadAdditionalImages(productId);
                $('#manageImagesModal').modal('show');
            });

            // Submit form manage images
            $('#manage-images-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save-images');
                const formData = new FormData(form[0]);
                const productId = $('#manage-product-id').val();

                setButtonLoading(btn, true);
                $('#manage-images-error-message').html('');

                $.ajax({
                    url: `{{ route('products.storeAdditionalImages', ':id') }}`.replace(':id',
                        productId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        setButtonLoading(btn, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            loadAdditionalImages(productId);
                            $('#additional-images').val('');
                            $('#additional-images-preview').empty();
                        });
                    },
                    error: function(xhr) {
                        setButtonLoading(btn, false);
                        handleAjaxError(xhr, '#manage-images-error-message');
                    }
                });
            });

            // Handle delete image button click
            $(document).on('click', '.btn-delete-image', function() {
                const productId = $(this).data('product-id');
                const imageId = $(this).data('image-id');
                confirmDeleteImage(productId, imageId);
            });

            // Kode search (diperbaiki dan dipindah ke sini)
            let searchTimeout;
            $('#search-product').on('input', function() {
                console.log('Input detected:', $(this).val()); // Debug: Lihat apakah event terdeteksi
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();
                if (query.length < 2) {
                    $('#search-results').hide().empty();
                    console.log('Query too short, hiding results'); // Debug
                    return;
                }

                searchTimeout = setTimeout(() => {
                    console.log('Sending AJAX for query:',
                    query); // Debug: Lihat apakah AJAX dipanggil
                    $.ajax({
                        url: "{{ route('products.search') }}",
                        type: 'GET',
                        data: {
                            query: query
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Search response:',
                            response); // Debug: Lihat response dari server
                            const resultsContainer = $('#search-results');
                            resultsContainer.empty().css('display',
                            'block'); // Force tampil dengan CSS
                            if (response.length > 0) {
                                response.forEach(product => {
                                    const item = `<div class="search-result-item list-group-item" data-id="${product.id}">
                                        ${product.name} (Kode: ${product.code})
                                    </div>`;
                                    resultsContainer.append(item);
                                    console.log('Added item:', product
                                    .name); // Debug
                                });
                            } else {
                                resultsContainer.append(
                                    '<div class="list-group-item">Tidak ditemukan produk.</div>'
                                    );
                                console.log('No products found'); // Debug
                            }
                        },
                        error: function(xhr) {
                            console.log('AJAX Error:', xhr.status, xhr
                            .responseText); // Debug: Lihat error detail
                            handleAjaxError(xhr);
                        }
                    });
                }, 300); // Debounce 300ms
            });

            // Handle klik hasil search untuk copy data
            $(document).on('click', '.search-result-item', function() {
                console.log('Item clicked:', $(this).data('id')); // Debug: Lihat apakah klik terdeteksi
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ route('products.show', ':id') }}`.replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Show response for copy:', response); // Debug
                        // Populate form fields (kecuali gambar)
                        $('#name').val(response.name + ' (Copy)');
                        $('#slug').val(generateSlug(response.name) + '-copy');
                        $('#code').val(response.code + '-COPY');
                        $('#category_id').val(response.category_id);
                        $('#sub_category_id').val(response.sub_category_id || '');
                        $('#unit_id').val(response.unit_id);
                        $('#purchase_price').val(new Intl.NumberFormat('id-ID').format(response
                            .purchase_price));
                        $('#selling_price').val(new Intl.NumberFormat('id-ID').format(response
                            .selling_price));
                        $('#stock').val(response.stock);
                        $('#min_stock').val(response.min_stock);
                        $(`input[name="status"][value="${response.status}"]`).prop('checked',
                            true);
                        $('#order_display').val(response.order_display || 0);
                        $('#status_display').val(response.status_display);

                        // Populate Quill editors
                        descriptionQuill.root.innerHTML = response.description || '';
                        attributeQuill.root.innerHTML = response.attribute || '';
                        reviewQuill.root.innerHTML = response.review || '';
                        supplierQuill.root.innerHTML = response.supplier || '';

                        // Kosongkan preview gambar (tidak copy)
                        $('#image-preview').empty();
                        $('#main-image').val('');

                        // Sembunyikan hasil search
                        $('#search-results').hide().empty();
                        $('#search-product').val(''); // Reset search input
                    },
                    error: function(xhr) {
                        console.log('Show AJAX Error:', xhr.status, xhr.responseText); // Debug
                        handleAjaxError(xhr);
                    }
                });
            });

            // Klik di luar hasil search untuk sembunyikan
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search-product, #search-results').length) {
                    $('#search-results').hide();
                    console.log('Hiding search results'); // Debug
                }
            });
        });
    </script>
@endpush
