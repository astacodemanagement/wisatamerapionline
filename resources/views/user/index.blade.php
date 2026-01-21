@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb Section -->
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
                        <img src="{{ asset('template/back') }}/dist/images/breadcrumb/ChatBc.png" alt=""
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
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    @can('user-create')
                                        <a class="btn btn-primary mb-2" href="{{ route('users.create') }}">
                                            <i class="fa fa-plus"></i> Tambah Data
                                        </a>
                                    @endcan
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


                            

                                <!-- Modal Update Role -->
                                <div class="modal fade" id="updateRoleModal" tabindex="-1"
                                    aria-labelledby="updateRoleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" id="updateRoleForm">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="user_id" id="updateRoleUserId">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateRoleModalLabel">Update Role User
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="roles" class="form-label">Pilih Role</label>
                                                        <select name="role" id="updateRoleSelect" class="form-select">
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->name }}">{{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                     <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan
                                                        Perubahan</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                   
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- JavaScript for handling modal data -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const updateRoleModal = document.getElementById('updateRoleModal');
                                        updateRoleModal.addEventListener('show.bs.modal', function(event) {
                                            const button = event.relatedTarget;
                                            const userId = button.getAttribute('data-user-id');
                                            const currentRole = button.getAttribute('data-user-role');

                                            const form = updateRoleModal.querySelector('#updateRoleForm');
                                            const userIdInput = updateRoleModal.querySelector('#updateRoleUserId');
                                            const roleSelect = updateRoleModal.querySelector('#updateRoleSelect');

                                            // Set the form action dynamically with the user ID
                                            form.action = `/users/${userId}/roles`;
                                            userIdInput.value = userId;

                                            // Set the selected role
                                            Array.from(roleSelect.options).forEach(option => {
                                                option.selected = option.value === currentRole;
                                            });
                                        });
                                    });
                                </script>



                                <table id="scroll_hor" class="table border table-striped table-bordered display nowrap"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Roles</th>
                                            <th>Gambar</th>
                                            <th width="280px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_user as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @foreach ($user->getRoleNames() as $role)
                                                        <label class="badge bg-primary">{{ $role }}</label>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if (!empty($user->image))
                                                        <a href="/upload/users/{{ $user->image }}" target="_blank">
                                                            <img style="max-width:50px; max-height:50px"
                                                                src="/upload/users/{{ $user->image }}" alt="Image">
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No Data</span>
                                                    @endif
                                                </td>
                                               


                                                <td>

                                                    {{-- <a class="btn btn-warning btn-sm"
                                                        href="{{ route('users.show', $user->id) }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </a> --}}


                                                    @can('user-edit')
                                                        <a class="btn btn-success btn-sm"
                                                            href="{{ route('users.edit', $user->id) }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>
                                                    @endcan
 

                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#updateRoleModal"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-role="{{ $user->getRoleNames()->first() ?? '' }}">
                                                        <i class="fa fa-user"></i> Update Role
                                                    </button>


                                                    {{-- <a class="btn btn-info btn-sm"
                                                        href="{{ route('users.links', $user->id) }}">
                                                        <i class="fa fa-file"></i> Dokumen
                                                    </a> --}}

                                                    @can('user-delete')
                                                        @if ($user->id !== auth()->id())
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="confirmDelete({{ $user->id }})">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </button>
                                                            <form id="delete-form-{{ $user->id }}" method="POST"
                                                                action="{{ route('users.destroy', $user->id) }}"
                                                                style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endif
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
    </div>
@endsection

@push('script')
    <script src="{{ asset('template/back') }}/dist/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/datatable/datatable-basic.init.js"></script>



    <script>
        function confirmDelete(userId) {
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
                    document.getElementById('delete-form-' + userId).submit();
                }
            });
        }
    </script>


  
@endpush
