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


                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Name:</strong>
                                        {{ $data_role->name }}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Permissions:</strong>
                                        @php
                                        $groupedPermissions = [];

                                        foreach ($rolePermissions as $permission) {
                                        $category = explode('-', $permission->name)[0];  
                                        $groupedPermissions[$category][] = $permission;
                                        }
                                        @endphp

                                        @if(!empty($rolePermissions))
                                        @foreach($groupedPermissions as $category => $permissions)
                                        <h3>{{ ucfirst($category) }}</h3> 
                                        <div style="display: flex; flex-wrap: wrap; margin-bottom: 10px;">  
                                            @foreach($permissions as $permission)
                                            <label class="label label-success" style="margin-right: 10px;">{{ $permission->name }}</label>
                                            @endforeach
                                        </div>
                                        @endforeach
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <a class="btn btn-warning mb-2 mt-3" href="{{ route('roles.index') }}"><i class="fa fa-undo"></i> Kembali</a>


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