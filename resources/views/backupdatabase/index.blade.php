@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
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
                    <img src="{{ asset('template/back/dist/images/breadcrumb/ChatBc.png') }}" alt="" class="img-fluid mb-n4">
                </div>
            </div>
        </div>
    </div>

    <section class="datatables">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">


                        <h2>Backup Database</h2>

                        <div class="row">


                            <!-- Back Up Section -->
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">Silahkan Back Up Database Anda</div>
                                    <div class="card-body">
                                        <a href="{{ route('backup.manual') }}" class="btn btn-primary"><i class="fa fa-download"></i> Backup & Download</a>

                                    </div>
                                </div>
                            </div>

                            <!-- Restore Section -->
                            
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