@extends('layouts.app')

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
                            <li class="breadcrumb-item" aria-current="page"><a class="text-muted text-decoration-none" href="{{ route('roles.index') }}">Halaman Role</a></li>
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


                            <form method="POST" action="{{ route('roles.update', $data_role->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <strong>Nama: <span class="text-danger">*</span></strong>
                                            <input type="text" name="name" placeholder="Nama" class="form-control" value="{{ $data_role->name }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Permission:</strong>
                                            <br />

                                            @php
                                            $groupedPermissions = [];

                                            foreach ($permission as $value) {
                                            $category = explode('-', $value->name)[0]; 
                                            $groupedPermissions[$category][] = $value;
                                            }
                                            @endphp

                                            <label style="display: inline-block; margin-bottom: 15px;">
                                                <input type="checkbox" id="check-all-global"> Check All Permissions
                                            </label>

                                            @foreach($groupedPermissions as $category => $permissions)
                                            <h3>{{ ucfirst($category) }}</h3> 

                                         
                                            <label style="display: inline-block; margin-right: 15px;">
                                                <input type="checkbox" class="check-all" data-category="{{ $category }}"> Check All in {{ ucfirst($category) }}
                                            </label>
                                            <div style="display: flex; flex-wrap: wrap;" class="permission-group-{{ $category }}">  
                                                @foreach($permissions as $value)
                                                <label style="display: inline-block; margin-right: 15px;">
                                                    <input type="checkbox" name="permission[{{ $value->id }}]" value="{{ $value->id }}" class="name permission-checkbox permission-{{ $category }}"
                                                        {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                    {{ $value->name }}
                                                </label>
                                                @endforeach
                                            </div>
                                            <br />
                                            @endforeach

                                            <script>
                                                document.getElementById('check-all-global').addEventListener('change', function() {
                                                    const allCheckboxes = document.querySelectorAll('.permission-checkbox');
                                                    allCheckboxes.forEach(checkbox => checkbox.checked = this.checked);

                                                    document.querySelectorAll('.check-all').forEach(checkAll => checkAll.checked = this.checked);
                                                });

                                                document.querySelectorAll('.check-all').forEach(checkAll => {
                                                    checkAll.addEventListener('change', function() {
                                                        const category = this.getAttribute('data-category');
                                                        const checkboxes = document.querySelectorAll(`.permission-${category}`);
                                                        checkboxes.forEach(checkbox => checkbox.checked = this.checked);

                                                        const allCategoryCheckAll = document.querySelectorAll('.check-all');
                                                        document.getElementById('check-all-global').checked = Array.from(allCategoryCheckAll).every(el => el.checked);
                                                    });
                                                });

                                                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                                                    checkbox.addEventListener('change', function() {
                                                        const category = this.classList[1].split('-')[1];
                                                        const categoryCheckboxes = document.querySelectorAll(`.permission-${category}`);
                                                        const checkAllCategory = document.querySelector(`.check-all[data-category="${category}"]`);

                                                        checkAllCategory.checked = Array.from(categoryCheckboxes).every(cb => cb.checked);

                                                        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
                                                        document.getElementById('check-all-global').checked = Array.from(allCheckboxes).every(cb => cb.checked);
                                                    });
                                                });
                                            </script>



                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-3">
                                        <button type="submit" class="btn btn-primary btn-md mb-3"><i class="fa fa-save"></i> Update</button>
                                        <a class="btn btn-warning btn-md mb-3" href="{{ route('roles.index') }}"><i class="fa fa-undo"></i> Kembali</a>
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

@endpush