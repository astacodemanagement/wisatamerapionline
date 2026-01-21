@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
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
                                        @can('tour-create')
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
                                            <th>Slug</th>
                                            <th>Harga</th>
                                            <th>Durasi</th>
                                            <th>Max Peserta</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_tour as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->slug }}</td>
                                                <td>Rp {{ number_format($p->price, 0, ',', '.') }} {{ $p->price_label }}</td>
                                                <td>{{ $p->duration_minutes }} menit</td>
                                                <td>{{ $p->max_participants }} orang</td>
                                                <td>{{ $p->location }}</td>
                                                <td>{{ $p->status }}</td>
                                                <td>
                                                    @if ($p->image)
                                                        <a href="{{ asset('upload/tours/' . $p->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $p->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-tour"
                                                        data-id="{{ $p->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('tour-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-tour"
                                                            data-id="{{ $p->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('tour-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $p->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $p->id }}" method="POST"
                                                            action="{{ route('tours.destroy', $p->id) }}"
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

                                <!-- Modal Tambah Tour -->
                                <div class="modal fade" id="addClientModal" tabindex="-1"
                                    aria-labelledby="addClientModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-tour-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addClientModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Tour
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="tour-name" class="form-label">Nama Tour
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="tour-name"
                                                                    name="name" placeholder="Contoh: Wisata Gunung Bromo" required>
                                                                <div class="invalid-feedback" id="tour-name-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-slug" class="form-label">Slug
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="tour-slug" name="slug"
                                                                    placeholder="Contoh: wisata-gunung-bromo" required>
                                                                <div class="invalid-feedback" id="tour-slug-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-price" class="form-label">Harga
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="tour-price" name="price" step="0.01"
                                                                    placeholder="Contoh: 500000" required>
                                                                <div class="invalid-feedback" id="tour-price-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-price-label" class="form-label">Label Harga</label>
                                                                <input type="text" class="form-control"
                                                                    id="tour-price-label" name="price_label"
                                                                    value="/ Per Ticket" placeholder="Contoh: / Per Ticket">
                                                                <div class="invalid-feedback" id="tour-price-label-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-duration" class="form-label">Durasi (menit)
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="tour-duration" name="duration_minutes"
                                                                    placeholder="Contoh: 480" required>
                                                                <div class="invalid-feedback" id="tour-duration-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-max-participants" class="form-label">Maksimal Peserta
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="tour-max-participants" name="max_participants"
                                                                    placeholder="Contoh: 20" required>
                                                                <div class="invalid-feedback" id="tour-max-participants-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-location" class="form-label">Lokasi
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="tour-location" name="location"
                                                                    placeholder="Contoh: Malang, Jawa Timur" required>
                                                                <div class="invalid-feedback" id="tour-location-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-short-description"
                                                                    class="form-label">Deskripsi Singkat</label>
                                                                <div id="tour-short-description-editor" style="height: 150px;"></div>
                                                                <textarea name="short_description" id="tour-short-description" style="display:none;"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="tour-short-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-status" class="form-label">Status <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="tour-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="inactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="tour-status-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="tour-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="tour-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="tour-image" class="form-label">Gambar Tour
                                                                    (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="tour-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateClientImageUpload()">
                                                                <div class="invalid-feedback" id="tour-image-error">
                                                                </div>
                                                                <img id="tour-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="tour-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>

                                                            <hr>
                                                            <div class="mb-2 d-flex align-items-center justify-content-between">
                                                                <label class="form-label mb-0">Routes</label>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-route">
                                                                    <i class="fa fa-plus"></i> Add Route
                                                                </button>
                                                            </div>
                                                            <div id="routes-container"></div>

                                                            <template id="route-row-template">
                                                                <div class="route-row border rounded p-2 mb-2 bg-light">
                                                                    <div class="d-flex gap-2 align-items-center">
                                                                        <div class="flex-grow-1">
                                                                            <input type="hidden" name="route_tours[__INDEX__][id]" class="route-id" />
                                                                            <input type="text" class="form-control route-name-input" name="route_tours[__INDEX__][route_name]" placeholder="Nama Route" required>
                                                                        </div>
                                                                        <button type="button" class="btn btn-outline-danger btn-remove-route">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="invalid-feedback d-block route-name-error"></div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div id="tour-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Tour -->
                                <div class="modal fade" id="editClientModal" tabindex="-1"
                                    aria-labelledby="editClientModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-tour-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-tour-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editClientModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Tour
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-tour-error-message" class="text-danger small mb-2">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-tour-name" class="form-label">Nama
                                                                    Tour <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-tour-name" name="name"
                                                                    placeholder="Contoh: Wisata Gunung Bromo" required>
                                                                <div class="invalid-feedback" id="edit-tour-name-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-slug" class="form-label">Slug
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-tour-slug" name="slug"
                                                                    placeholder="Contoh: wisata-gunung-bromo" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-slug-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-price" class="form-label">Harga
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-tour-price" name="price" step="0.01"
                                                                    placeholder="Contoh: 500000" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-price-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-price-label" class="form-label">Label Harga</label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-tour-price-label" name="price_label"
                                                                    placeholder="Contoh: / Per Ticket">
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-price-label-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-duration" class="form-label">Durasi (menit)
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-tour-duration" name="duration_minutes"
                                                                    placeholder="Contoh: 480" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-duration-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-max-participants" class="form-label">Maksimal Peserta
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-tour-max-participants" name="max_participants"
                                                                    placeholder="Contoh: 20" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-max-participants-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-location" class="form-label">Lokasi
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-tour-location" name="location"
                                                                    placeholder="Contoh: Malang, Jawa Timur" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-location-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-short-description"
                                                                    class="form-label">Deskripsi Singkat</label>
                                                                <div id="edit-tour-short-description-editor" style="height: 150px;"></div>
                                                                <textarea name="short_description" id="edit-tour-short-description" style="display:none;"></textarea>
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-short-description-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-tour-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="inactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-tour-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-tour-image" class="form-label">Gambar
                                                                    Tour (JPG, JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="edit-tour-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateEditClientImageUpload()">
                                                                <div class="invalid-feedback"
                                                                    id="edit-tour-image-error"></div>
                                                                <img id="edit-tour-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-tour-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>

                                                            <hr>
                                                            <div class="mb-2 d-flex align-items-center justify-content-between">
                                                                <label class="form-label mb-0">Routes</label>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-route-edit">
                                                                    <i class="fa fa-plus"></i> Add Route
                                                                </button>
                                                            </div>
                                                            <div id="routes-container-edit"></div>

                                                            <template id="route-row-template-edit">
                                                                <div class="route-row border rounded p-2 mb-2 bg-light">
                                                                    <div class="d-flex gap-2 align-items-center">
                                                                        <div class="flex-grow-1">
                                                                            <input type="hidden" name="route_tours[__INDEX__][id]" class="route-id" />
                                                                            <input type="text" class="form-control route-name-input" name="route_tours[__INDEX__][route_name]" placeholder="Nama Route" required>
                                                                        </div>
                                                                        <button type="button" class="btn btn-outline-danger btn-remove-route">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="invalid-feedback d-block route-name-error"></div>
                                                                </div>
                                                            </template>
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

                                <!-- Modal Tampil Tour -->
                                <div class="modal fade" id="showClientModal" tabindex="-1"
                                    aria-labelledby="showClientModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showClientModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Tour
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-tour-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-tour-name" class="form-label">Nama
                                                                Tour</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-slug"
                                                                class="form-label">Slug</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-slug" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-price" class="form-label">Harga</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-price" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-price-label" class="form-label">Label Harga</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-price-label" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-duration" class="form-label">Durasi (menit)</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-duration" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-max-participants" class="form-label">Maksimal Peserta</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-max-participants" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-location"
                                                                class="form-label">Lokasi</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-location" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-short-description"
                                                                class="form-label">Deskripsi Singkat</label>
                                                            <div id="show-tour-short-description" class="border rounded p-2 bg-light" style="min-height: 100px;"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-status"
                                                                class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-order-display"
                                                                class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-tour-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-tour-image" class="form-label">Gambar
                                                                Tour</label>
                                                            <div id="show-tour-image"></div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Routes</label>
                                                            <ul id="show-tour-routes" class="list-group"></ul>
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
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        function validateClientImageUpload() {
            const fileInput = document.getElementById('tour-image');
            const errorDiv = document.getElementById('tour-image-error');
            const previewImage = document.getElementById('tour-image-preview');
            const previewCanvas = document.getElementById('tour-image-preview-canvas');
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
            const fileInput = document.getElementById('edit-tour-image');
            const errorDiv = document.getElementById('edit-tour-image-error');
            const previewImage = document.getElementById('edit-tour-image-preview');
            const previewCanvas = document.getElementById('edit-tour-image-preview-canvas');
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

            let shortDescriptionQuill = new Quill('#tour-short-description-editor', quillConfig);
            let editShortDescriptionQuill = new Quill('#edit-tour-short-description-editor', quillConfig);

            // Auto generate slug from name
            $('#tour-name').on('input', function() {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
                    .trim('-'); // Remove leading/trailing hyphens
                $('#tour-slug').val(slug);
            });

            $('#edit-tour-name').on('input', function() {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
                    .trim('-'); // Remove leading/trailing hyphens
                $('#edit-tour-slug').val(slug);
            });

            // Helpers Routes UI
            function addRouteRow(container, template, index) {
                const html = template.html().replaceAll('__INDEX__', index);
                const $row = $(html);
                container.append($row);
                return $row;
            }

            let routeIndexCreate = 0;
            $('#btn-add-route').on('click', function() {
                addRouteRow($('#routes-container'), $('#route-row-template'), routeIndexCreate++);
            });
            // init default first row
            addRouteRow($('#routes-container'), $('#route-row-template'), routeIndexCreate++);

            $(document).on('click', '.btn-remove-route', function() {
                $(this).closest('.route-row').remove();
            });

            let routeIndexEdit = 0;
            $('#btn-add-route-edit').on('click', function() {
                addRouteRow($('#routes-container-edit'), $('#route-row-template-edit'), routeIndexEdit++);
            });

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

            // Submit Tambah Tour
            $('#add-tour-form').submit(function(e) {
                e.preventDefault();
                $('#tour-short-description').val(shortDescriptionQuill.root.innerHTML);
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#tour-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('tours.store') }}",
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
                            $('#tour-image-preview').hide();
                            $('#tour-image-preview-canvas').hide();
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#tour-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit: ambil data dan tampilkan modal edit
            $(document).on('click', '.btn-edit-tour', function() {
                const clientId = $(this).data('id');
                $('#edit-tour-error-message').html('');
                $('#edit-tour-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-tour-image-preview').hide();
                $('#edit-tour-image-preview-canvas').hide();

                const url = `{{ route('tours.edit', ':id') }}`.replace(':id', clientId);
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
                            $('#edit-tour-id').val(response.id);
                            $('#edit-tour-name').val(response.name);
                            $('#edit-tour-slug').val(response.slug);
                            $('#edit-tour-price').val(response.price);
                            $('#edit-tour-price-label').val(response.price_label || '/ Per Ticket');
                            $('#edit-tour-duration').val(response.duration_minutes);
                            $('#edit-tour-max-participants').val(response.max_participants);
                            $('#edit-tour-location').val(response.location);
                            editShortDescriptionQuill.root.innerHTML = response.short_description || '';
                            $('#edit-tour-status').val(response.status);
                            $('#edit-tour-order-display').val(response.order_display);
                            $('#edit-tour-image').val('');

                            const imageUrl = response.image ?
                                `{{ asset('upload/tours') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-tour-image-preview').attr('src', imageUrl).show();
                                $('#edit-tour-image-preview-canvas').hide();
                            } else {
                                $('#edit-tour-image-preview').hide();
                                $('#edit-tour-image-preview-canvas').hide();
                            }

                            // Populate Routes
                            $('#routes-container-edit').empty();
                            routeIndexEdit = 0;
                            const routes = Array.isArray(response.route_tours) ? response.route_tours : [];
                            if (routes.length === 0) {
                                addRouteRow($('#routes-container-edit'), $('#route-row-template-edit'), routeIndexEdit++);
                            } else {
                                routes.forEach(function(r) {
                                    const $row = addRouteRow($('#routes-container-edit'), $('#route-row-template-edit'), routeIndexEdit++);
                                    $row.find('.route-id').val(r.id || '');
                                    $row.find('.route-name-input').val(r.route_name || '');
                                });
                            }
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
                        handleAjaxError(xhr, '#edit-tour-error-message');
                    }
                });
            });

            // Submit form Edit Tour
            $('#edit-tour-form').submit(function(e) {
                e.preventDefault();
                $('#edit-tour-short-description').val(editShortDescriptionQuill.root.innerHTML);
                const form = $(this);
                const btn = $('#btn-update');
                const clientId = $('#edit-tour-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-tour-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('tours.update', ':id') }}`.replace(':id', clientId),
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
                        handleAjaxError(xhr, '#edit-tour-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Reset preview saat modal ditutup
            $('#addClientModal').on('hidden.bs.modal', function() {
                $('#add-tour-form')[0].reset();
                shortDescriptionQuill.setContents([]);
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#tour-image-preview').hide();
                $('#tour-image-preview-canvas').hide();
            });

            $('#editClientModal').on('hidden.bs.modal', function() {
                $('#edit-tour-form')[0].reset();
                editShortDescriptionQuill.setContents([]);
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-tour-image-preview').hide();
                $('#edit-tour-image-preview-canvas').hide();
            });

            // Klik tombol Show: ambil data dan tampilkan modal show
            $(document).on('click', '.btn-show-tour', function() {
                const clientId = $(this).data('id');
                $('#show-tour-error-message').html('');
                console.log('Fetching tour ID:', clientId); // Debug ID
                const url = `{{ route('tours.show', ':id') }}`.replace(':id', clientId);
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
                            $('#show-tour-name').val(response.name || '');
                            $('#show-tour-slug').val(response.slug || '');
                            $('#show-tour-price').val('Rp ' + new Intl.NumberFormat('id-ID').format(response.price) + ' ' + (response.price_label || ''));
                            $('#show-tour-price-label').val(response.price_label || '');
                            $('#show-tour-duration').val((response.duration_minutes || 0) + ' menit');
                            $('#show-tour-max-participants').val((response.max_participants || 0) + ' orang');
                            $('#show-tour-location').val(response.location || 'Tidak ada');
                            $('#show-tour-short-description').html(response.short_description || 'Tidak ada');
                            $('#show-tour-status').val(response.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                            $('#show-tour-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ?
                                `{{ asset('upload/tours') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-tour-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar Tour"></a>`
                                );
                            } else {
                                $('#show-tour-image').html('Tidak ada gambar');
                            }

                            // Render routes
                            const routes = Array.isArray(response.route_tours) ? response.route_tours : [];
                            const list = $('#show-tour-routes');
                            list.empty();
                            if (routes.length === 0) {
                                list.append('<li class="list-group-item">Tidak ada route</li>');
                            } else {
                                routes.forEach(function(r){
                                    list.append(`<li class="list-group-item">${r.route_name || '-'}</li>`);
                                });
                            }

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
                        handleAjaxError(xhr, '#show-tour-error-message');
                    }
                });
            });

            // Konfirmasi Hapus Tour
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
                            url: `{{ route('tours.destroy', ':id') }}`.replace(':id',
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
