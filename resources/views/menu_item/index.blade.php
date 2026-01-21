@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.css" />
    <style>
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .submenu-item {
            padding-left: 20px;
        }

        .kanban .row {
            display: flex;
            flex-wrap: wrap;
        }

        .submenu {
            list-style: none;
            display: flex;
            flex-direction: column;
            padding-left: 0;
        }

        .submenu-item {
            cursor: pointer;
            padding: 5px;
            margin: 5px 0;
            background: #f0f0f0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .submenu-item:hover {
            background-color: #e0e0e0;
        }

        .card {
            border: solid 1px #ddd;
            margin-bottom: 10px;
            padding: 5px;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            outline: 2px solid #007bff;
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
                    <div class="col-3">
                        <div class="text-center mb-n5">
                            <img src="{{ asset('template/back') }}/dist/images/breadcrumb/ChatBc.png" alt=""
                                class="img-fluid mb-n4">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4 mt-4">
            <div class="col-lg-12 margin-tb">
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
                <div class="pull-right">
                    @can('menuitem-create')
                        <a class="btn btn-primary mb-2" href="{{ route('menu_items.create') }}"><i class="fa fa-plus"></i>
                            Tambah Data</a>
                    @endcan
                </div>
            </div>
        </div>


        <!-- Input Pencarian -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form action="{{ route('menu_items.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari Menu Item"
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('menu_items.index') }}" class="btn btn-warning">Clear</a> <!-- Tombol Clear -->
                    </div>
                </form>
            </div>
        </div>



        <!-- Kanban -->
        <div class="kanban">
            <div class="row">
                @foreach ($data_menu_item as $menu_item)
                    <div class="col-12 mb-4" data-id="{{ $menu_item->id }}" data-parent-id="{{ $menu_item->parent_id }}">
                        <div class="card" data-id="{{ $menu_item->id }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>{{ $menu_item->name }}</h5>
                                <!-- Tombol Aktif / Nonaktif -->
                                <div class="ml-auto d-flex gap-2">

                                    <a class="btn btn-sm {{ $menu_item->status == 'Aktif' ? 'btn-success' : 'btn-warning' }}"
                                        href="{{ route('menu_items.show', $menu_item->id) }}" title="Status">
                                        <i
                                            class="fa {{ $menu_item->status == 'Aktif' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        <!-- {{ $menu_item->status }} -->
                                    </a>
                                    @can('menuitem-edit')
                                        <a class="btn btn-secondary btn-sm" href="{{ route('menu_items.edit', $menu_item->id) }}"
                                            title="Edit Data">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('menuitem-delete')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $menu_item->id }})" title="Hapus Data">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $menu_item->id }}" method="POST"
                                            action="{{ route('menu_items.destroy', $menu_item->id) }}" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                </div>
                            </div>

                            @if ($menu_item->children->isNotEmpty())
                                <div class="card-body">
                                    <ul class="submenu" data-id="{{ $menu_item->id }}">
                                        @foreach ($menu_item->children as $sub_item)
                                            <li class="submenu-item" data-id="{{ $sub_item->id }}"
                                                data-parent-id="{{ $menu_item->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <!-- Nama submenu di sebelah kiri -->
                                                    <span>{{ $sub_item->name }}</span>

                                                    <!-- Tombol-tombol di sebelah kanan -->
                                                    <div class="d-flex gap-2">

                                                        <a class="btn btn-sm {{ $sub_item->status == 'Aktif' ? 'btn-success' : 'btn-warning' }}"
                                                            href="{{ route('menu_items.show', $sub_item->id) }}"
                                                            title="Status">
                                                            <i
                                                                class="fa {{ $sub_item->status == 'Aktif' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                                            <!-- {{ $sub_item->status }} -->
                                                        </a>
                                                        @can('menuitem-edit')
                                                            <!-- Edit menu submenu dengan ID sub_item -->
                                                            <a class="btn btn-primary btn-sm"
                                                                href="{{ route('menu_items.edit', $sub_item->id) }}"
                                                                title="Edit Data">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endcan
                                                        @can('menuitem-delete')
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="confirmDelete({{ $sub_item->id }})"
                                                                title="Hapus Data">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                            <form id="delete-form-{{ $sub_item->id }}" method="POST"
                                                                action="{{ route('menu_items.destroy', $sub_item->id) }}"
                                                                style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            @endif


                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $data_menu_item->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

    <script>
        function confirmDelete(permissionId) {
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
                    document.getElementById('delete-form-' + permissionId).submit();
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kanbanContainer = document.querySelector('.kanban .row');
            const submenuContainers = document.querySelectorAll('.submenu');

            // Membuat Sortable untuk kanban utama (menu)
            new Sortable(kanbanContainer, {
                group: 'kanban', // Set group untuk menu
                animation: 150,
                onEnd: function(evt) {
                    let sortedItems = [];
                    kanbanContainer.querySelectorAll('.card').forEach(function(card) {
                        sortedItems.push(card.getAttribute('data-id'));
                    });

                    // Kirimkan update hanya untuk menu utama (parent_id === null)
                    fetch("{{ route('menu_items.update_positions') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                positions: sortedItems,
                                parent_id: null // Pastikan parent_id null untuk menu utama
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Posisi item berhasil diperbarui');
                                location.reload();
                            } else {
                                alert('Terjadi kesalahan');
                            }
                        });
                }
            });


            // Menambahkan Sortable untuk submenu (menu child)
            submenuContainers.forEach(submenu => {
                new Sortable(submenu, {
                    group: 'kanban', // Set group untuk submenu
                    animation: 150,
                    onEnd: function(evt) {
                        let sortedSubItems = [];
                        submenu.querySelectorAll('.submenu-item').forEach(function(subItem) {
                            sortedSubItems.push(subItem.getAttribute('data-id'));
                        });

                        fetch("{{ route('menu_items.update_positions') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                },
                                body: JSON.stringify({
                                    positions: sortedSubItems,
                                    parent_id: submenu.getAttribute(
                                        'data-id') // Kirimkan parent ID untuk sub-menu yang baru
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Posisi sub-menu berhasil diperbarui');
                                    location.reload();
                                } else {
                                    alert('Terjadi kesalahan');
                                }
                            });
                    }
                });
            });
        });
    </script>
@endpush
