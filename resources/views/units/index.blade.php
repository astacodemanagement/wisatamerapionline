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
                                        @can('unit-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                                                    <i class="fa fa-plus"></i> Tambah Satuan
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
                                            <th>Nama</th>
                                            <th>Singkatan</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Urutan</th>
                                            <th width="200px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_unit as $unit)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($unit->name, 50) }}</td>
                                                <td>{{ $unit->abbreviation ?? 'Tidak ada' }}</td>
                                                <td>{!! Str::limit($unit->description, 50) !!}</td>
                                                <td>{{ $unit->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                <td>{{ $unit->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-unit" data-id="{{ $unit->id }}">
                                                        <i class="fa fa-eye"></i> Lihat
                                                    </button>
                                                    @can('unit-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-unit" data-id="{{ $unit->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('unit-delete')
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $unit->id }})">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </button>
                                                        <form id="delete-form-{{ $unit->id }}" method="POST" action="{{ route('units.destroy', $unit->id) }}" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah Satuan -->
                                <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-unit-form">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addUnitModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Satuan
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="unit-name" class="form-label">Nama <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="unit-name" name="name" placeholder="Contoh: Kilogram" required>
                                                                <div class="invalid-feedback" id="unit-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="unit-abbreviation" class="form-label">Singkatan</label>
                                                                <input type="text" class="form-control" id="unit-abbreviation" name="abbreviation" placeholder="Contoh: kg">
                                                                <div class="invalid-feedback" id="unit-abbreviation-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="unit-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="unit-description" name="description" rows="4"></textarea>
                                                                <div class="invalid-feedback" id="unit-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="unit-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="unit-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="unit-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="unit-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="unit-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="unit-order-display-error"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="unit-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Satuan -->
                                <div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-unit-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-unit-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editUnitModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Satuan
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-unit-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-unit-name" class="form-label">Nama <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-unit-name" name="name" placeholder="Contoh: Kilogram" required>
                                                                <div class="invalid-feedback" id="edit-unit-name-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-unit-abbreviation" class="form-label">Singkatan</label>
                                                                <input type="text" class="form-control" id="edit-unit-abbreviation" name="abbreviation" placeholder="Contoh: kg">
                                                                <div class="invalid-feedback" id="edit-unit-abbreviation-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-unit-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-unit-description" name="description" rows="4"></textarea>
                                                                <div class="invalid-feedback" id="edit-unit-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-unit-status" class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-unit-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-unit-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-unit-order-display" class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control" id="edit-unit-order-display" name="order_display" placeholder="Contoh: 1" value="0" min="0">
                                                                <div class="invalid-feedback" id="edit-unit-order-display-error"></div>
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

                                <!-- Modal Tampil Satuan -->
                                <div class="modal fade" id="showUnitModal" tabindex="-1" aria-labelledby="showUnitModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showUnitModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Satuan
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-unit-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-unit-name" class="form-label">Nama</label>
                                                            <input type="text" class="form-control" id="show-unit-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-unit-abbreviation" class="form-label">Singkatan</label>
                                                            <input type="text" class="form-control" id="show-unit-abbreviation" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-unit-description" class="form-label">Deskripsi</label>
                                                            <div id="show-unit-description" class="border p-3" style="min-height: 100px;"></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-unit-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-unit-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-unit-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-unit-order-display" readonly>
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
            
            // Fungsi untuk menangani loading tombol
            function setButtonLoading(button, isLoading, text = 'Menyimpan...') {
                if (isLoading) {
                    button.data('original', button.html()).prop('disabled', true)
                        .html(`<span class="spinner-border spinner-border-sm"></span> ${text}`);
                } else {
                    button.prop('disabled', false).html(button.data('original'));
                }
            }

            // Fungsi untuk menangani error AJAX
            function handleAjaxError(xhr, target) {
                let message = 'Terjadi kesalahan.';
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    message = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>');
                    if (target) {
                        $(target).html(message);
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}-error`).text(value[0]);
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}`).addClass('is-invalid');
                        });
                    }
                } else if (xhr.status === 403) {
                    message = 'Anda tidak memiliki izin.';
                    if (target) $(target).html(message);
                } else if (xhr.responseJSON?.error) {
                    message = xhr.responseJSON.error;
                    if (target) $(target).html(message);
                }
                Swal.fire({ icon: 'error', title: 'Error', html: message });
            }

            // Form tambah satuan
            $('#add-unit-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#unit-error-message').html('');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: "{{ route('units.store') }}",
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
                            $('#addUnitModal').modal('hide');
                            form[0].reset();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#unit-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Tombol edit satuan
            $(document).on('click', '.btn-edit-unit', function() {
                const unitId = $(this).data('id');
                $('#edit-unit-error-message').html('');
                $('#edit-unit-form')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: `{{ route('units.edit', ':id') }}`.replace(':id', unitId),
                    type: 'GET',
                    success: function(response) {
                        $('#edit-unit-id').val(response.id);
                        $('#edit-unit-name').val(response.name);
                        $('#edit-unit-abbreviation').val(response.abbreviation || '');
                        $('#edit-unit-description').val(response.description || '');
                        $('#edit-unit-status').val(response.status);
                        $('#edit-unit-order-display').val(response.order_display || 0);
                        $('#editUnitModal').modal('show');
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-unit-error-message');
                    }
                });
            });

            // Form edit satuan
            $('#edit-unit-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const unitId = $('#edit-unit-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-unit-error-message').html('');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: `{{ route('units.update', ':id') }}`.replace(':id', unitId),
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
                            $('#editUnitModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-unit-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Tombol tampil satuan
            $(document).on('click', '.btn-show-unit', function() {
                const unitId = $(this).data('id');
                $('#show-unit-error-message').html('');

                $.ajax({
                    url: `{{ route('units.show', ':id') }}`.replace(':id', unitId),
                    type: 'GET',
                    success: function(response) {
                        $('#show-unit-name').val(response.name || 'Tidak ada');
                        $('#show-unit-abbreviation').val(response.abbreviation || 'Tidak ada');
                        $('#show-unit-description').html(response.description || 'Tidak ada');
                        $('#show-unit-status').val(response.status === 'active' ? 'Aktif' : 'Tidak Aktif');
                        $('#show-unit-order-display').val(response.order_display || '0');
                        $('#showUnitModal').modal('show');
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-unit-error-message');
                    }
                });
            });

            // Fungsi hapus satuan
            window.confirmDelete = function(unitId) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data satuan yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('units.destroy', ':id') }}`.replace(':id', unitId),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
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