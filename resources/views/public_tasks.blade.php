<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF token for AJAX -->
    <title>Public Tasks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            padding: 40px;
        }
        .container {
            max-width: 1300px;
            margin: 0 auto;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            padding: 30px;
            transition: box-shadow 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
        }
        .table {
            width: 100%;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .btn-primary {
            background-color: #4e73df;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 6px;
        }
        .btn-info {
            background-color: #17a2b8;
            border: none;
            border-radius: 6px;
        }
        .btn-info:hover {
            background-color: #138496;
        }
        .btn-warning {
            background-color: #f6c23e;
            border: none;
            border-radius: 6px;
        }
        .btn-warning:hover {
            background-color: #e4b12a;
        }
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .modal-header {
            background-color: #4e73df;
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        #calendar {
            margin-top: 30px;
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .fc-event {
            cursor: pointer;
            font-size: 14px;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 6px;
            background-color: #f8f9fa;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .text-danger {
            font-size: 0.875rem;
        }
        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
            vertical-align: middle;
        }
        .badge {
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 500;
        }
        .status-task-belum {
            background-color: #dc3545;
            color: white;
        }
        .status-task-proses {
            background-color: #ffc107;
            color: black;
        }
        .status-task-selesai {
            background-color: #28a745;
            color: white;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            .container {
                padding: 0 10px;
            }
            .card {
                padding: 15px;
            }
            .table th, .table td {
                padding: 10px;
                font-size: 14px;
            }
            .action-buttons {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-sm {
                width: 100%;
                margin-bottom: 5px;
            }
            h2, h4 {
                font-size: 1.5rem;
            }
            .breadcrumb {
                font-size: 0.9rem;
            }
            #calendar {
                padding: 10px;
            }
            .fc-event {
                font-size: 12px;
                padding: 8px;
                margin-bottom: 4px;
            }
           .fc-list-event .fc-list-event-title {
    color: #000000;
    font-weight: 600;
    text-shadow: 0 0 1px #ffffff;
}
.fc-list-event .fc-list-event-desc {
    color: #000000;
    font-weight: 500;
    text-shadow: 0 0 1px #ffffff;
}
.fc-list-event {
    background-color: #ffffff;
    border-left: 4px solid #4e73df;
}
            
            .modal-dialog {
                margin: 10px;
            }
            .modal-body {
                padding: 15px;
            }
            .btn-primary, .btn-secondary {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            .table th, .table td {
                font-size: 12px;
            }
            h2, h4 {
                font-size: 1.2rem;
            }
            .btn-primary, .btn-secondary {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            .modal-header h5 {
                font-size: 1rem;
            }
            .fc-list-event {
                font-size: 11px;
                padding: 4px;
                margin-bottom: 3px;
            }
            .fc-list-event .fc-list-event-desc {
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="mb-4"><i class="fas fa-tasks me-2"></i>Public Tasks</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/" class="text-muted text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Public Tasks</li>
                </ol>
            </nav>
        </div>

        <div class="card">
            <h4 class="mb-3"><i class="fas fa-sitemap me-2"></i>List Divisions</h4>
            <table id="divisions-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($divisions as $division)
                        <tr>
                            <td>{{ $division->name }}</td>
                            <td>{{ $division->description ?? 'N/A' }}</td>
                            <td>{{ ucfirst($division->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4 class="mt-5 mb-3"><i class="fas fa-list-ul me-2"></i>List Tasks</h4>
            <table id="tasks-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Division</th>
                        <th>Description</th>
                        <th>Status Task</th>
                        <th>PJ</th>
                        <th>Deadline</th>
                        <th>Reminder</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->division->name ?? 'N/A' }}</td>
                            <td>{{ $task->description ?? 'N/A' }}</td>
                            <td>
                                <span class="badge status-task-{{ $task->status_task }}">
                                    {{ ucfirst($task->status_task) }}
                                </span>
                            </td>
                            <td>{{ $task->pj ?? 'N/A' }}</td>
                            <td>{{ $task->deadline ? date('d-m-Y', strtotime($task->deadline)) : 'N/A' }}</td>
                            <td>
                                @if ($task->deadline)
                                    @php
                                        $deadline = new DateTime($task->deadline);
                                        $today = new DateTime();
                                        $interval = $today->diff($deadline);
                                        $days = $interval->invert ? '-' . $interval->days : $interval->days;
                                        $reminder = $interval->invert ? 'Overdue' : ($days == 0 ? 'Today' : "$days days left");
                                    @endphp
                                    {{ $reminder }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ ucfirst($task->status) }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-info btn-sm show-task-btn" data-id="{{ $task->id }}">
                                        <i class="fas fa-eye"></i> Show
                                    </button>
                                    <button class="btn btn-warning btn-sm edit-task-btn" data-id="{{ $task->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Buttons for Adding Division and Task -->
            <div class="mt-5 d-flex justify-content-start gap-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDivisionModal">
                    <i class="fas fa-plus me-2"></i>Tambah Divisi
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="fas fa-plus me-2"></i>Tambah Task
                </button>
            </div>

            <!-- Calendar -->
            <div class="mt-5">
                <h4 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Calendar Tasks</h4>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Modal Tambah Divisi -->
        <div class="modal fade" id="addDivisionModal" tabindex="-1" aria-labelledby="addDivisionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="add-division-form" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDivisionModalLabel"><i class="fas fa-plus me-2"></i>Tambah Divisi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="division-name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="division-name" name="name" required>
                                <div class="invalid-feedback" id="division-name-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="division-description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="division-description" name="description"></textarea>
                                <div class="invalid-feedback" id="division-description-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="division-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="division-status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="nonactive">Nonaktif</option>
                                </select>
                                <div class="invalid-feedback" id="division-status-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="division-order_display" class="form-label">Urutan Tampilan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="division-order_display" name="order_display" min="0" value="0" required>
                                <div class="invalid-feedback" id="division-order_display-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="division-image" class="form-label">Gambar (opsional)</label>
                                <input type="file" class="form-control" id="division-image" name="image" accept=".jpg,.jpeg,.png">
                                <div class="invalid-feedback" id="division-image-error"></div>
                            </div>
                            <div id="division-error-message" class="text-danger small"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Batal</button>
                            <button type="submit" class="btn btn-primary" id="division-submit-btn">
                                <span class="submit-text"><i class="fas fa-save me-2"></i>Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Task -->
        <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="add-task-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addTaskModalLabel"><i class="fas fa-plus me-2"></i>Tambah Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="task-title" class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="task-title" name="title" required>
                                <div class="invalid-feedback" id="task-title-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="task-division_id" class="form-label">Divisi <span class="text-danger">*</span></label>
                                <select class="form-control" id="task-division_id" name="division_id" required>
                                    <option value="">Pilih Divisi</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="task-division_id-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="task-description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="task-description" name="description"></textarea>
                                <div class="invalid-feedback" id="task-description-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="task-status_task" class="form-label">Status Task <span class="text-danger">*</span></label>
                                <select class="form-control" id="task-status_task" name="status_task" required>
                                    <option value="belum">Belum</option>
                                    <option value="proses">Proses</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                                <div class="invalid-feedback" id="task-status_task-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="task-pj" class="form-label">Penanggung Jawab</label>
                                <input type="text" class="form-control" id="task-pj" name="pj">
                                <div class="invalid-feedback" id="task-pj-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="task-deadline" class="form-label">Deadline</label>
                                <input type="date" class="form-control" id="task-deadline" name="deadline">
                                <div class="invalid-feedback" id="task-deadline-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="task-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="task-status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="nonactive">Nonaktif</option>
                                </select>
                                <div class="invalid-feedback" id="task-status-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="task-order_display" class="form-label">Urutan Tampilan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="task-order_display" name="order_display" min="0" value="0" required>
                                <div class="invalid-feedback" id="task-order_display-error"></div>
                            </div>
                            <div id="task-error-message" class="text-danger small"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Batal</button>
                            <button type="submit" class="btn btn-primary" id="task-submit-btn">
                                <span class="submit-text"><i class="fas fa-save me-2"></i>Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Task -->
        <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="edit-task-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" id="edit-task-id">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTaskModalLabel"><i class="fas fa-edit me-2"></i>Edit Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit-task-title" class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-task-title" name="title" required>
                                <div class="invalid-feedback" id="edit-task-title-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-task-division_id" class="form-label">Divisi <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit-task-division_id" name="division_id" required>
                                    <option value="">Pilih Divisi</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="edit-task-division_id-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-task-description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="edit-task-description" name="description"></textarea>
                                <div class="invalid-feedback" id="edit-task-description-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-task-status_task" class="form-label">Status Task <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit-task-status_task" name="status_task" required>
                                    <option value="belum">Belum</option>
                                    <option value="proses">Proses</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                                <div class="invalid-feedback" id="edit-task-status_task-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-task-pj" class="form-label">Penanggung Jawab</label>
                                <input type="text" class="form-control" id="edit-task-pj" name="pj">
                                <div class="invalid-feedback" id="edit-task-pj-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-task-deadline" class="form-label">Deadline</label>
                                <input type="date" class="form-control" id="edit-task-deadline" name="deadline">
                                <div class="invalid-feedback" id="edit-task-deadline-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-task-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit-task-status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="nonactive">Nonaktif</option>
                                </select>
                                <div class="invalid-feedback" id="edit-task-status-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-task-order_display" class="form-label">Urutan Tampilan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit-task-order_display" name="order_display" min="0" value="0" required>
                                <div class="invalid-feedback" id="edit-task-order_display-error"></div>
                            </div>
                            <div id="edit-task-error-message" class="text-danger small"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Batal</button>
                            <button type="submit" class="btn btn-primary" id="edit-task-submit-btn">
                                <span class="submit-text"><i class="fas fa-save me-2"></i>Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Show Task Info -->
        <div class="modal fade" id="showTaskInfoModal" tabindex="-1" aria-labelledby="showTaskInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showTaskInfoModalLabel"><i class="fas fa-info-circle me-2"></i>Task Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Title:</strong> <span id="show-title"></span></p>
                        <p><strong>Division:</strong> <span id="show-division"></span></p>
                        <p><strong>Description:</strong> <span id="show-description"></span></p>
                        <p><strong>Status Task:</strong> <span id="show-status-task"></span></p>
                        <p><strong>PJ:</strong> <span id="show-pj"></span></p>
                        <p><strong>Deadline:</strong> <span id="show-deadline"></span></p>
                        <p><strong>Reminder:</strong> <span id="show-reminder"></span></p>
                        <p><strong>Status:</strong> <span id="show-status"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTables for divisions and tasks
        $(document).ready(function() {
            $('#divisions-table').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: false,
                pageLength: 10
            });

            $('#tasks-table').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: false,
                pageLength: 10,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // Title
                    { responsivePriority: 2, targets: 8 }, // Actions
                    { responsivePriority: 3, targets: 3 }  // Status Task
                ]
            });
        });

        // Store tasks in JS array for quick lookup
        const tasks = [
            @foreach ($tasks as $task)
                {
                    id: '{{ $task->id }}',
                    title: '{{ $task->title }}',
                    division: '{{ $task->division->name ?? "N/A" }}',
                    description: '{{ $task->description ?? "N/A" }}',
                    status_task: '{{ ucfirst($task->status_task) }}',
                    pj: '{{ $task->pj ?? "N/A" }}',
                    deadline: '{{ $task->deadline ? date("d-m-Y", strtotime($task->deadline)) : "N/A" }}',
                    status: '{{ ucfirst($task->status) }}',
                    start: '{{ $task->deadline }}',
                    days_until_deadline: @if ($task->deadline)
                        @php
                            $deadline = new DateTime($task->deadline);
                            $today = new DateTime();
                            $interval = $today->diff($deadline);
                            $days = $interval->invert ? '-' . $interval->days : $interval->days;
                            echo json_encode($interval->invert ? 'Overdue' : ($days == 0 ? 'Today' : "$days days left"));
                        @endphp
                    @else
                        'N/A'
                    @endif
                },
            @endforeach
        ];

        // Initialize FullCalendar
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth <= 768 ? 'list' : 'dayGridMonth',
                views: {
                    list: {
                        type: 'list',
                        duration: { days: 30 },
                        eventMaxHeight: 40
                    },
                    dayGridMonth: {
                        eventMaxHeight: 60
                    }
                },
                events: tasks,
                eventContent: function(arg) {
                    return {
                        html: `
                            <div class="fc-list-event" style="color:black">
                                <div class="fc-list-event-title" >${arg.event.title}</div>
                                <div class="fc-list-event-desc">
                                    <span class="badge status-task-${arg.event.extendedProps.status_task.toLowerCase()}">${arg.event.extendedProps.status_task}</span>
                                    (${arg.event.extendedProps.days_until_deadline})
                                </div>
                            </div>`
                    };
                },
                dateClick: function(info) {
                    // Open add task modal with pre-filled deadline
                    $('#task-deadline').val(info.dateStr);
                    $('#addTaskModal').modal('show');
                },
                eventClick: function(info) {
                    // Show task info in modal
                    const task = tasks.find(t => t.id === info.event.id);
                    if (task) {
                        $('#show-title').text(task.title);
                        $('#show-division').text(task.division);
                        $('#show-description').text(task.description);
                        $('#show-status-task').text(task.status_task);
                        $('#show-pj').text(task.pj);
                        $('#show-deadline').text(task.deadline);
                        $('#show-reminder').text(task.days_until_deadline);
                        $('#show-status').text(task.status);
                        $('#showTaskInfoModal').modal('show');
                    }
                }
            });
            calendar.render();

            // Re-render calendar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768 && calendar.view.type !== 'list') {
                    calendar.changeView('list');
                } else if (window.innerWidth > 768 && calendar.view.type !== 'dayGridMonth') {
                    calendar.changeView('dayGridMonth');
                }
            });
        });

        // Handle form submission for adding division
        $('#add-division-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#division-submit-btn');
            const submitText = submitBtn.find('.submit-text');
            const spinner = submitBtn.find('.spinner-border');
            submitBtn.prop('disabled', true);
            submitText.addClass('d-none');
            spinner.removeClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#division-error-message').text('');

            const formData = new FormData(this);
            $.ajax({
                url: '{{ route("public-tasks.store-division") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#addDivisionModal').modal('hide');
                        form[0].reset();
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessages = '';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`#division-${key.replace('.', '-')}-error`).text(value[0]).show();
                            $(`#division-${key.replace('.', '-')}`).addClass('is-invalid');
                            errorMessages += value[0] + '<br>';
                        });
                        $('#division-error-message').html(errorMessages);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: xhr.responseJSON.message,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitText.removeClass('d-none');
                    spinner.addClass('d-none');
                }
            });
        });

        // Handle form submission for adding task
        $('#add-task-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#task-submit-btn');
            const submitText = submitBtn.find('.submit-text');
            const spinner = submitBtn.find('.spinner-border');
            submitBtn.prop('disabled', true);
            submitText.addClass('d-none');
            spinner.removeClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#task-error-message').text('');

            const formData = new FormData(this);
            $.ajax({
                url: '{{ route("public-tasks.store-task") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#addTaskModal').modal('hide');
                        form[0].reset();
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessages = '';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`#task-${key.replace('.', '-')}-error`).text(value[0]).show();
                            $(`#task-${key.replace('.', '-')}`).addClass('is-invalid');
                            errorMessages += value[0] + '<br>';
                        });
                        $('#task-error-message').html(errorMessages);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: xhr.responseJSON.message,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitText.removeClass('d-none');
                    spinner.addClass('d-none');
                }
            });
        });

        // Handle show task
        $(document).on('click', '.show-task-btn', function() {
            const taskId = $(this).data('id');
            $.ajax({
                url: '{{ route("public-tasks.show", ":id") }}'.replace(':id', taskId),
                type: 'GET',
                success: function(response) {
                    const task = response.data;
                    $('#show-title').text(task.title);
                    $('#show-division').text(task.division);
                    $('#show-description').text(task.description);
                    $('#show-status-task').text(task.status_task);
                    $('#show-pj').text(task.pj);
                    $('#show-deadline').text(task.deadline);
                    $('#show-reminder').text(task.days_until_deadline);
                    $('#show-status').text(task.status);
                    $('#showTaskInfoModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON.message || 'Gagal mengambil data tugas.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Handle edit task
        $(document).on('click', '.edit-task-btn', function() {
            const taskId = $(this).data('id');
            $.ajax({
                url: '{{ route("public-tasks.edit", ":id") }}'.replace(':id', taskId),
                type: 'GET',
                success: function(response) {
                    const task = response.data;
                    $('#edit-task-id').val(task.id);
                    $('#edit-task-title').val(task.title);
                    $('#edit-task-division_id').val(task.division_id);
                    $('#edit-task-description').val(task.description);
                    $('#edit-task-status_task').val(task.status_task);
                    $('#edit-task-pj').val(task.pj);
                    $('#edit-task-deadline').val(task.deadline);
                    $('#edit-task-status').val(task.status);
                    $('#edit-task-order_display').val(task.order_display);
                    $('#editTaskModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON.message || 'Gagal mengambil data tugas.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Handle form submission for editing task
        $('#edit-task-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#edit-task-submit-btn');
            const submitText = submitBtn.find('.submit-text');
            const spinner = submitBtn.find('.spinner-border');
            submitBtn.prop('disabled', true);
            submitText.addClass('d-none');
            spinner.removeClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#edit-task-error-message').text('');

            const formData = new FormData(this);
            const taskId = $('#edit-task-id').val();
            $.ajax({
                url: '{{ route("public-tasks.update", ":id") }}'.replace(':id', taskId),
                type: 'POST', // Using POST with _method=PUT for Laravel
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#editTaskModal').modal('hide');
                        form[0].reset();
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessages = '';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`#edit-task-${key.replace('.', '-')}-error`).text(value[0]).show();
                            $(`#edit-task-${key.replace('.', '-')}`).addClass('is-invalid');
                            errorMessages += value[0] + '<br>';
                        });
                        $('#edit-task-error-message').html(errorMessages);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: xhr.responseJSON.message,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitText.removeClass('d-none');
                    spinner.addClass('d-none');
                }
            });
        });
    </script>
</body>
</html>