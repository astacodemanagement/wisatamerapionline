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
                                        @can('task-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addTaskModal">
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
                                            <th>Judul</th>
                                            <th>Divisi</th>
                                            <th>Deskripsi</th>
                                            <th>Status Tugas</th>
                                            <th>Penanggung Jawab</th>
                                            <th>Tenggat</th>
                                            <th>Status</th>
                                            <th>Urutan Tampilan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tasks as $task)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->division ? $task->division->name : 'N/A' }}</td>
                                                <td>{{ $task->description ? Str::limit($task->description, 50) : 'N/A' }}</td>
                                                <td>{{ ucfirst($task->status_task) }}</td>
                                                <td>{{ $task->pj ?? 'N/A' }}</td>
                                                 <td>{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d-m-Y') : 'Tidak ada' }}</td>
                                               
                                                <td>{{ ucfirst($task->status) }}</td>
                                                <td>{{ $task->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-task"
                                                        data-id="{{ $task->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('task-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-task"
                                                            data-id="{{ $task->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('task-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $task->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $task->id }}" method="POST"
                                                            action="{{ route('tasks.destroy', $task->id) }}"
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

                                <!-- Modal Tambah Tugas -->
                                <div class="modal fade" id="addTaskModal" tabindex="-1"
                                    aria-labelledby="addTaskModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-task-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addTaskModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah Tugas
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="task-title" class="form-label">Judul
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="task-title"
                                                                    name="title" placeholder="Contoh: Tugas Proyek" required>
                                                                <div class="invalid-feedback" id="task-title-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="task-division-id" class="form-label">Divisi
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="task-division-id"
                                                                    name="division_id" required>
                                                                    <option value="">Pilih Divisi</option>
                                                                    @foreach ($divisions as $division)
                                                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="task-division-id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="task-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="task-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="task-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="task-status-task" class="form-label">Status Tugas
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="task-status-task"
                                                                    name="status_task" required>
                                                                    <option value="belum">Belum</option>
                                                                    <option value="proses">Proses</option>
                                                                    <option value="selesai">Selesai</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="task-status-task-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="task-pj" class="form-label">Penanggung Jawab</label>
                                                                <input type="text" class="form-control" id="task-pj"
                                                                    name="pj" placeholder="Contoh: John Doe">
                                                                <div class="invalid-feedback" id="task-pj-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="task-deadline" class="form-label">Tenggat</label>
                                                                <input type="date" class="form-control" id="task-deadline"
                                                                    name="deadline">
                                                                <div class="invalid-feedback" id="task-deadline-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="task-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="task-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="task-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="task-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="task-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="task-order-display-error"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="task-error-message" class="text-danger small"></div>
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

                                <!-- Modal Edit Tugas -->
                                <div class="modal fade" id="editTaskModal" tabindex="-1"
                                    aria-labelledby="editTaskModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-task-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-task-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editTaskModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Tugas
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-task-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-task-title" class="form-label">Judul
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="edit-task-title"
                                                                    name="title" placeholder="Contoh: Tugas Proyek" required>
                                                                <div class="invalid-feedback" id="edit-task-title-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-task-division-id" class="form-label">Divisi
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-task-division-id"
                                                                    name="division_id" required>
                                                                    <option value="">Pilih Divisi</option>
                                                                    @foreach ($divisions as $division)
                                                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-task-division-id-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-task-description" class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" id="edit-task-description" name="description" rows="4"
                                                                    placeholder="Masukkan deskripsi"></textarea>
                                                                <div class="invalid-feedback" id="edit-task-description-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-task-status-task" class="form-label">Status Tugas
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-task-status-task"
                                                                    name="status_task" required>
                                                                    <option value="belum">Belum</option>
                                                                    <option value="proses">Proses</option>
                                                                    <option value="selesai">Selesai</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-task-status-task-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-task-pj" class="form-label">Penanggung Jawab</label>
                                                                <input type="text" class="form-control" id="edit-task-pj"
                                                                    name="pj" placeholder="Contoh: John Doe">
                                                                <div class="invalid-feedback" id="edit-task-pj-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-task-deadline" class="form-label">Tenggat</label>
                                                                <input type="date" class="form-control" id="edit-task-deadline"
                                                                    name="deadline">
                                                                <div class="invalid-feedback" id="edit-task-deadline-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-task-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-task-status" name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Nonaktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-task-status-error"></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit-task-order-display" class="form-label">Urutan Tampilan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="edit-task-order-display"
                                                                    name="order_display" value="0" min="0" required>
                                                                <div class="invalid-feedback" id="edit-task-order-display-error"></div>
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

                                <!-- Modal Tampil Tugas -->
                                <div class="modal fade" id="showTaskModal" tabindex="-1"
                                    aria-labelledby="showTaskModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showTaskModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail Tugas
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-task-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-task-title" class="form-label">Judul</label>
                                                            <input type="text" class="form-control" id="show-task-title" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-task-division-name" class="form-label">Divisi</label>
                                                            <input type="text" class="form-control" id="show-task-division-name" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-task-description" class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" id="show-task-description" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-task-status-task" class="form-label">Status Tugas</label>
                                                            <input type="text" class="form-control" id="show-task-status-task" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-task-pj" class="form-label">Penanggung Jawab</label>
                                                            <input type="text" class="form-control" id="show-task-pj" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-task-deadline" class="form-label">Tenggat</label>
                                                            <input type="text" class="form-control" id="show-task-deadline" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-task-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control" id="show-task-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-task-order-display" class="form-label">Urutan Tampilan</label>
                                                            <input type="text" class="form-control" id="show-task-order-display" readonly>
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

            // Submit form tambah tugas
            $('#add-task-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#task-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('tasks.store') }}",
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
                            $('#addTaskModal').modal('hide');
                            form[0].reset();
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#task-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Event untuk tombol edit
            $(document).on('click', '.btn-edit-task', function() {
                const taskId = $(this).data('id');
                $('#edit-task-error-message').html('');
                $('#edit-task-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('tasks.edit', ':id') }}`.replace(':id', taskId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Edit Task Response:', response); // Debugging
                        if (response && response.task && response.task.id) {
                            $('#edit-task-id').val(response.task.id);
                            $('#edit-task-title').val(response.task.title || '');
                            $('#edit-task-division-id').val(response.task.division_id || '');
                            $('#edit-task-description').val(response.task.description || '');
                            $('#edit-task-status-task').val(response.task.status_task || '');
                            $('#edit-task-pj').val(response.task.pj || '');
                            // Assign deadline directly, expecting YYYY-MM-DD or empty
                            $('#edit-task-deadline').val(response.task.deadline || '');
                            $('#edit-task-status').val(response.task.status || '');
                            $('#edit-task-order-display').val(response.task.order_display || '0');
                            $('#editTaskModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Edit Task Error:', xhr.responseText); // Debugging
                        handleAjaxError(xhr, '#edit-task-error-message');
                    }
                });
            });

            // Submit form edit tugas
            $('#edit-task-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const taskId = $('#edit-task-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-task-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('tasks.update', ':id') }}`.replace(':id', taskId),
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
                            $('#editTaskModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-task-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Event untuk tombol show
            $(document).on('click', '.btn-show-task', function() {
                const taskId = $(this).data('id');
                $('#show-task-error-message').html('');

                $.ajax({
                    url: `{{ route('tasks.show', ':id') }}`.replace(':id', taskId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.id) {
                            $('#show-task-title').val(response.title || '');
                            $('#show-task-division-name').val(response.division ? response.division.name : 'N/A');
                            $('#show-task-description').val(response.description || 'N/A');
                            $('#show-task-status-task').val(response.status_task ? response.status_task.charAt(0).toUpperCase() + response.status_task.slice(1) : '');
                            $('#show-task-pj').val(response.pj || 'N/A');
                            $('#show-task-deadline').val(response.deadline ? new Date(response.deadline).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric'
                            }) : 'N/A');
                            $('#show-task-status').val(response.status ? response.status.charAt(0).toUpperCase() + response.status.slice(1) : '');
                            $('#show-task-order-display').val(response.order_display || '0');
                            $('#showTaskModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#show-task-error-message');
                    }
                });
            });

            // Fungsi untuk konfirmasi hapus
            window.confirmDelete = function(taskId) {
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
                            url: `{{ route('tasks.destroy', ':id') }}`.replace(':id', taskId),
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