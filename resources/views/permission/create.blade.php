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
                            <li class="breadcrumb-item" aria-current="page">
                                <a class="text-muted text-decoration-none" href="{{ route('permissions.index') }}">Halaman Permission</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">{{ $subtitle }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3 text-center mb-n5">
                    <img src="{{ asset('template/back') }}/dist/images/breadcrumb/ChatBc.png" alt="" class="img-fluid mb-n4">
                </div>
            </div>
        </div>
    </div>

    <section class="datatables">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> Ada beberapa masalah dengan data yang anda masukkan.
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('permissions.store') }}">
                            @csrf
                            <div class="row">
                                @foreach (['name' => 'Nama', 'urutan' => 'Urutan', 'guard_name' => 'Guard'] as $field => $label)
                                <div class="col-12 mb-3">
                                    <div class="form-group">
                                        <strong>{{ $label }}:</strong>
                                        <input type="{{ $field === 'urutan' ? 'number' : 'text' }}"
                                            name="{{ $field }}"
                                            placeholder="{{ $label }}"
                                            class="form-control"
                                            value="{{ old($field, $field === 'guard_name' ? 'web' : '') }}">
                                    </div>
                                </div>
                                @endforeach

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary btn-md mt-2 mb-3"><i class="fa fa-save"></i> Simpan</button>
                                    <a class="btn btn-warning btn-md mt-2 mb-3" href="{{ route('permissions.index') }}"><i class="fa fa-undo"></i> Kembali</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection