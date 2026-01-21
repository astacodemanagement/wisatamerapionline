@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{ asset('template/back') }}/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
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
                    <div class="col-3">
                        <div class="text-center mb-n5">
                            <img src="{{ asset('template/back') }}/dist/images/breadcrumb/ChatBc.png" alt=""
                                class="img-fluid mb-n4">
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
                                        <strong>Whoops!</strong> Ada beberapa masalah dengan data yang anda
                                        masukkan.<br><br>
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

                                @can('visitor-deleteall')
                                    <form id="delete-all-form" action="{{ route('visitor.delete') }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger mb-3" onclick="confirmDeleteSweetAlert()">
                                            <i class="fa fa-trash"></i> Hapus Semua Data
                                        </button>
                                    </form>

                                    <script>
                                        function confirmDeleteSweetAlert() {
                                            Swal.fire({
                                                title: 'Apakah Anda yakin?',
                                                text: "Semua data visitor akan dihapus permanen!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#d33',
                                                cancelButtonColor: '#3085d6',
                                                confirmButtonText: 'Ya, hapus semua!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    document.getElementById('delete-all-form').submit();
                                                }
                                            });
                                        }
                                    </script>
                                @endcan


                                <table id="visitor_table" class="table border table-striped table-bordered display nowrap"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tipe</th>
                                            <th>Toko</th>
                                            <th>Waktu</th>
                                            <th>IP Address</th>
                                            <th>User Agent</th>
                                            <th>Platform</th>
                                            <th>Device</th>
                                            <th>Browser</th>
                                        </tr>
                                    </thead>
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
    <script src="{{ asset('template/back') }}/dist/js/datatable/datatable-basic.init.js"></script>


    <script>
        $(document).ready(function() {
            $('#visitor_table').DataTable({
                processing: true,  
                serverSide: true,  
                ajax: "{{ route('visitor.index') }}",  
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'page_type',
                        name: 'page_type'
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'visit_time',
                        name: 'visit_time'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    },
                    {
                        data: 'user_agent',
                        name: 'user_agent'
                    },
                    {
                        data: 'platform',
                        name: 'platform'
                    },
                    {
                        data: 'device',
                        name: 'device'
                    },
                    {
                        data: 'browser',
                        name: 'browser'
                    }
                ]
            });
        });
    </script>
@endpush
