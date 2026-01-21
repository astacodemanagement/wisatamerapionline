@extends('layouts.app')


@push('css')
<link rel="stylesheet" href="{{ asset('template/back') }}/dist/libs/select2/dist/css/select2.min.css">
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
                            <li class="breadcrumb-item" aria-current="page"><a class="text-muted text-decoration-none" href="{{ route('menu_items.index') }}">Halaman Menu Item</a></li>
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

                            <form method="POST" action="{{ route('menu_items.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="form-item mb-3">
                                        <label for="menu_group_id">Menu Group</label>
                                        <select
                                            name="menu_group_id"
                                            class="select2 form-control"
                                            style="height: 36px; width: 100%"
                                            placeholder="--Pilih Menu Group--">

                                            <option></option>  
                                            @foreach ($data_menu_group as $id => $name)  
                                            <option value="{{ $id }}" {{ old('menu_group_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-item mb-3">
                                        <label for="name">Nama Menu Item</label>
                                        <input type="text" name="name" class="form-control" id="name" required>
                                    </div>

                                    <div class="form-item mb-3">
                                        <label for="icon">Ikon</label>
                                        <div class="input-group">
                                            <input type="text" name="icon" class="form-control" id="icon" required>
                                            <button type="button" class="btn btn-primary" id="openIconPicker">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="iconPickerModalLabel">Pilih Ikon</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="embed-container">
                                                        <iframe id="iconFrame" src="about:blank" width="100%" height="600px" frameborder="0"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <style>
                                        .embed-container {
                                            position: relative;
                                            width: 100%;
                                            height: 600px;
                                            overflow: hidden;
                                        }

                                        .embed-container iframe {
                                            width: 100%;
                                            height: 100%;
                                            border: none;
                                        }
                                    </style>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const modal = new bootstrap.Modal(document.getElementById('iconPickerModal'));
                                            const iconFrame = document.getElementById('iconFrame');

                                            // Buka modal dengan halaman FontAwesome
                                            document.getElementById('openIconPicker').addEventListener('click', function() {
                                                iconFrame.src = 'https://fontawesome.com/v4/icons/';
                                                modal.show();
                                            });
                                        });
                                    </script>




                                    <div class="form-item mb-3">
                                        <label for="route">Route</label>
                                        <select name="route" class="select2 form-control" id="route" required>
                                            <option></option> <!-- Placeholder option for select2 -->
                                            @foreach ($data_routes as $route)
                                            <option value="{{ $route }}" {{ old('route') == $route ? 'selected' : '' }}>
                                                {{ $route }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-item mb-3">
                                        <label for="permission_name">Permission</label>
                                        <select
                                            name="permission_name"
                                            class="select2 form-control"
                                            style="height: 36px; width: 100%"
                                            placeholder="--Pilih Permission--">

                                            <option></option> <!-- Placeholder option for select2 -->
                                            <optgroup label="--Pilih Permission--">
                                                @foreach ($data_permission as $id => $name)
                                                <option value="{{ $name }}" {{ in_array($name, old('permission_name', [])) ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>



                                    <div class="form-item mb-3">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="Aktif">Aktif</option>
                                            <option value="Non Aktif">Non Aktif</option>
                                        </select>
                                    </div>

                                    <div class="form-item mb-3">
                                        <label for="position">Urutan</label>
                                        <input type="number" name="position" class="form-control" id="position" required>
                                    </div>

                                    <div class="form-item mb-3">
                                        <label for="parent_id">Parent Menu</label>
                                        <select name="parent_id" class="select2 form-control" style="height: 36px; width: 100%">
                                            <option value="">-- Pilih Parent Menu --</option>
                                            @foreach ($data_menu_items as $id => $name)
                                            <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-3">
                                        <button type="submit" class="btn btn-primary btn-md mb-3"><i class="fa fa-save"></i> Simpan</button>
                                        <a class="btn btn-warning btn-md mb-3" href="{{ route('menu_items.index') }}"><i class="fa fa-undo"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>

            </div>
        </div>


    </section>


</div>
@endsection

@push('script')
<script src="{{ asset('template/back') }}/dist/libs/select2/dist/js/select2.full.min.js"></script>
<script src="{{ asset('template/back') }}/dist/libs/select2/dist/js/select2.min.js"></script>
<script src="{{ asset('template/back') }}/dist/js/forms/select2.init.js"></script>



@endpush