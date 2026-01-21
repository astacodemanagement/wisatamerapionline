@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('template/back') }}/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
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
                    <div class="col-3">
                        <div class="text-center mb-n5">
                            <img src="{{ asset('template/back') }}/dist/images/breadcrumb/ChatBc.png" alt="" class="img-fluid mb-n4">
                        </div>
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

                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> Ada beberapa masalah dengan data yang anda masukkan.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @can('loghistori-deleteall')
                                    <button id="btn-clear-logs" class="btn btn-danger mb-3">
                                        <i class="fa fa-trash"></i> Hapus Semua Log
                                    </button>

                                    <a class="btn btn-success mb-3" href="{{ route('logs.show') }}"><i class="fa fa-plus"></i>
                                        Lihat Logs</a>
                                @endcan
                                <table id="scroll_hor" class="table border table-striped table-bordered display nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tabel</th>
                                            <th>ID Entitas</th>
                                            <th>Aksi</th>
                                            <th>Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Data Lama</th>
                                            <th>Data Baru</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_log_histori as $p)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $p->source_table }}</td>
                                                <td>{{ $p->entity_id }}</td>
                                                <td>{{ $p->action }}</td>
                                                <td>{{ $p->logged_at ? \Carbon\Carbon::parse($p->logged_at)->format('d-m-Y H:i:s') : '-' }}</td>
                                                <td>{{ $p->user }}</td>
                                                <td><pre>{{ $p->old_data }}</pre></td>
                                                <td><pre>{{ $p->new_data }}</pre></td>
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
    </div>
@endsection

@push('script')
    <script src="{{ asset('template/back') }}/dist/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(function() {
            $('#btn-clear-logs').on('click', function() {
                Swal.fire({
                    title: 'Yakin menghapus semua log?',
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus semua!',
                    cancelButtonText: 'Batal'
                }).then((res) => {
                    if (res.isConfirmed) {
                        let btn = $(this);
                        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menghapus...');

                        $.ajax({
                            url: "{{ route('log-histori.delete-all') }}",
                            method: "GET",
                            success: function(resp) {
                                Swal.fire({
                                    title: 'Sukses!',
                                    text: resp.success,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.error ?? 'Terjadi kesalahan.', 'error');
                            },
                            complete: function() {
                                btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Hapus Semua Log');
                            }
                        });
                    }
                });
            });

            // Destroy existing DataTable instance if it exists, then initialize
            if ($.fn.DataTable.isDataTable('#scroll_hor')) {
                $('#scroll_hor').DataTable().destroy();
            }
            $('#scroll_hor').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                scrollX: true,
                initComplete: function() {
                    // Ensure buttons are visible after initialization
                    $('.dt-button').addClass('btn btn-primary btn-sm').css('margin-right', '5px');
                }
            });
        });
    </script>
@endpush