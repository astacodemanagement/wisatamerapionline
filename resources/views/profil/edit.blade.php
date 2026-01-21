@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
    <style>
        .ql-editor {
            min-height: 120px;
            font-size: 14px;
        }
        .nav-pills .nav-link.active {
            background-color: #007bff !important;
            color: white !important;
        }
        .image-preview {
            max-height: 80px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }
        .preview-canvas {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: none;
        }
        .form-label {
            font-weight: 600;
        }
        .existing-image {
            max-height: 100px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .image-container {
            position: relative;
            display: inline-block;
        }
        .btn-delete-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4d4d;
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            line-height: 22px;
            text-align: center;
            cursor: pointer;
            display: none;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .image-container:hover .btn-delete-image {
            display: block;
        }
        .image-container.marked-deleted {
            opacity: 0.3;
            filter: grayscale(1);
        }
        .image-container.marked-deleted .btn-delete-image {
            background: #666;
            display: block;
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
                            <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('profil.index') }}">Halaman Profil</a></li>
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
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <ul class="nav nav-pills user-profile-tab" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent fs-3 py-4" id="tab-umum-tab" data-bs-toggle="pill" data-bs-target="#tab-umum" type="button" role="tab">
                                <i class="ti ti-user-circle me-2 fs-6"></i>
                                <span class="d-none d-md-block">Umum</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-4" id="tab-display-tab" data-bs-toggle="pill" data-bs-target="#tab-display" type="button" role="tab">
                                <i class="ti ti-camera me-2 fs-6"></i>
                                <span class="d-none d-md-block">Display</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-4" id="tab-company-tab" data-bs-toggle="pill" data-bs-target="#tab-company" type="button" role="tab">
                                <i class="ti ti-building me-2 fs-6"></i>
                                <span class="d-none d-md-block">Company</span>
                            </button>
                        </li>
                    </ul>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profil.update', $profil->id) }}" enctype="multipart/form-data" id="profil-form">
                            @csrf
                            @method('PUT')

                            @php
                                function renderImageInput($id, $label, $profil, $colClass = 'col-lg-4') {
                                    $imageValue = $profil->$id;
                                    $exists = $imageValue && file_exists(public_path('upload/profil/' . $imageValue));
                                    @endphp
                                    <div class="{{ $colClass }}">
                                        <div class="mb-4">
                                            <label for="{{ $id }}" class="form-label fw-semibold">{{ $label }}</label>
                                            @if ($exists)
                                                <div class="mb-2">
                                                    <div class="image-container" id="container_{{ $id }}">
                                                        <a href="{{ asset('upload/profil/' . $imageValue) }}" target="_blank">
                                                            <img src="{{ asset('upload/profil/' . $imageValue) }}" alt="{{ $label }}" class="existing-image">
                                                        </a>
                                                        <button type="button" class="btn-delete-image" onclick="toggleDeleteImage('{{ $id }}')" title="Hapus Gambar">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                        <input type="hidden" name="delete_{{ $id }}" id="delete_{{ $id }}" value="0">
                                                    </div>
                                                </div>
                                            @endif
                                            <input type="file" class="form-control" id="{{ $id }}" name="{{ $id }}" onchange="previewImage('{{ $id }}', 'preview_canvas_{{ $id }}')">
                                            <canvas id="preview_canvas_{{ $id }}" class="preview-canvas"></canvas>
                                        </div>
                                    </div>
                                    @php
                                }
                            @endphp

                            <div class="tab-content" id="pills-tabContent">
                                <!-- TAB UMUM -->
                                <div class="tab-pane fade show active" id="tab-umum" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-4">
                                                <label for="nama_profil" class="form-label fw-semibold">Nama Perusahaan</label>
                                                <input type="text" class="form-control" id="nama_profil" name="nama_profil" value="{{ $profil->nama_profil }}" placeholder="Ex: Jaya Saputra">
                                            </div>
                                            <div class="mb-4">
                                                <label for="no_telp" class="form-label fw-semibold">No Telp</label>
                                                <input type="number" class="form-control" id="no_telp" name="no_telp" value="{{ $profil->no_telp }}" placeholder="08500000000">
                                            </div>
                                        </div>
                                         <div class="col-lg-6">
                                            <div class="mb-4">
                                                <label for="email" class="form-label fw-semibold">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="{{ $profil->email }}" placeholder="maxima@gmail.com">
                                            </div>
                                            <div class="mb-4">
                                                <label for="no_wa" class="form-label fw-semibold">No WA</label>
                                                <input type="number" class="form-control" id="no_wa" name="no_wa" value="{{ $profil->no_wa }}" placeholder="08500000000">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-4">
                                                <label for="alamat" class="form-label fw-semibold">Alamat</label>
                                                <textarea class="form-control" name="alamat" id="alamat" rows="4">{{ $profil->alamat }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-4">
                                                <label for="instagram" class="form-label fw-semibold">Instagram</label>
                                                <input type="text" class="form-control" id="instagram" name="instagram" value="{{ $profil->instagram }}" placeholder="@master.kit">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-4">
                                                <label for="facebook" class="form-label fw-semibold">Facebook</label>
                                                <input type="text" class="form-control" id="facebook" name="facebook" value="{{ $profil->facebook }}" placeholder="Master Kit">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-4">
                                                <label for="youtube" class="form-label fw-semibold">Youtube</label>
                                                <input type="text" class="form-control" id="youtube" name="youtube" value="{{ $profil->youtube }}" placeholder="Master Kit">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-4">
                                                <label for="tiktok" class="form-label fw-semibold">Tiktok</label>
                                                <input type="text" class="form-control" id="tiktok" name="tiktok" value="{{ $profil->tiktok }}" placeholder="master.kit">
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="mb-4">
                                                <label for="website" class="form-label fw-semibold">Website</label>
                                                <input type="text" class="form-control" id="website" name="website" value="{{ $profil->website }}" placeholder="https://masterkit.com">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="ketua" class="form-label fw-semibold">Ketua</label>
                                                <input type="text" class="form-control" id="ketua" name="ketua" value="{{ $profil->ketua }}" placeholder="Dr. Hermawan">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="tempat_ttd" class="form-label fw-semibold">Kota</label>
                                                <input type="text" class="form-control" id="tempat_ttd" name="tempat_ttd" value="{{ $profil->tempat_ttd }}" placeholder="Jakarta">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="deskripsi_1" class="form-label fw-semibold">Deskripsi 1</label>
                                                <div id="deskripsi_1-editor"></div>
                                                <input type="hidden" name="deskripsi_1" id="deskripsi_1">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="deskripsi_2" class="form-label fw-semibold">Deskripsi 2</label>
                                                <div id="deskripsi_2-editor"></div>
                                                <input type="hidden" name="deskripsi_2" id="deskripsi_2">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="deskripsi_3" class="form-label fw-semibold">Deskripsi 3</label>
                                                <div id="deskripsi_3-editor"></div>
                                                <input type="hidden" name="deskripsi_3" id="deskripsi_3">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-4">
                                                <label for="no_rekening" class="form-label fw-semibold">No Rekening</label>
                                                <input type="text" class="form-control" id="no_rekening" name="no_rekening" value="{{ $profil->no_rekening }}" placeholder="75246728">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-4">
                                                <label for="bank" class="form-label fw-semibold">Bank</label>
                                                <input type="text" class="form-control" id="bank" name="bank" value="{{ $profil->bank }}" placeholder="Bank Madun">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-4">
                                                <label for="atas_nama" class="form-label fw-semibold">Atas Nama</label>
                                                <input type="text" class="form-control" id="atas_nama" name="atas_nama" value="{{ $profil->atas_nama }}" placeholder="Rahadi">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="term" class="form-label fw-semibold">Term Of Use</label>
                                                <div id="term-editor"></div>
                                                <input type="hidden" name="term" id="term">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB DISPLAY -->
                                <div class="tab-pane fade" id="tab-display" role="tabpanel">
                                    <div class="row">
                                        {{ renderImageInput('logo', 'Logo', $profil) }}
                                        {{ renderImageInput('logo_dark', 'Logo Dark', $profil) }}
                                        {{ renderImageInput('favicon', 'Favicon', $profil) }}
                                        {{ renderImageInput('banner', 'Banner', $profil) }}
                                        {{ renderImageInput('bg_login', 'Background Login', $profil) }}
                                        {{ renderImageInput('ttd', 'TTD Ketua', $profil) }}

                                        <!-- Display Images 1-6 -->
                                        @php
                                            $displayGambarLabels = [
                                                1 => 'breadcrumb about',
                                                2 => 'breadcrumb tour',
                                                3 => 'breadcrumb gallery',
                                                4 => 'breadcrumb blog',
                                                5 => 'breadcrumb contact',
                                                6 => 'breadcrumb destination',
                                            ];
                                        @endphp
                                        @foreach ($displayGambarLabels as $i => $label)
                                            {{ renderImageInput("breadcrumb_$i", $label, $profil) }}
                                        @endforeach

                                        <!-- Gambar 1-4 -->
                                        @php
                                            $gambarLabels = [1 => 'Gambar Iklan', 2 => 'Gambar 2', 3 => 'Gambar 3', 4 => 'Gambar 4'];
                                        @endphp
                                        @foreach ($gambarLabels as $i => $label)
                                            {{ renderImageInput("gambar_$i", $label, $profil) }}
                                        @endforeach
                                    </div>
                                </div>

                                <!-- TAB COMPANY -->
                                <div class="tab-pane fade" id="tab-company" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="main_products" class="form-label fw-semibold">Main Products</label>
                                                <input type="text" class="form-control" id="main_products" name="main_products" value="{{ $profil->main_products }}" placeholder="Solar Panel, Inverter">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="address" class="form-label fw-semibold">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" value="{{ $profil->address }}" placeholder="Jl. Putri Ayu B13 no 4-5">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="main_markets" class="form-label fw-semibold">Main Markets</label>
                                                <input type="text" class="form-control" id="main_markets" name="main_markets" value="{{ $profil->main_markets }}" placeholder="Asia, Europe">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="incoterms" class="form-label fw-semibold">Incoterms</label>
                                                <input type="text" class="form-control" id="incoterms" name="incoterms" value="{{ $profil->incoterms }}" placeholder="FOB, CIF">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="terms_of_payment" class="form-label fw-semibold">Terms of Payment</label>
                                                <input type="text" class="form-control" id="terms_of_payment" name="terms_of_payment" value="{{ $profil->terms_of_payment }}" placeholder="T/T 30%">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="average_lead_time" class="form-label fw-semibold">Average Lead Time</label>
                                                <input type="text" class="form-control" id="average_lead_time" name="average_lead_time" value="{{ $profil->average_lead_time }}" placeholder="15 days">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="sgs_audit_report_no" class="form-label fw-semibold">SGS Audit Report No</label>
                                                <input type="text" class="form-control" id="sgs_audit_report_no" name="sgs_audit_report_no" value="{{ $profil->sgs_audit_report_no }}" placeholder="SGS2023IDN001">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="rating" class="form-label fw-semibold">Rating</label>
                                                <input type="text" class="form-control" id="rating" name="rating" value="{{ $profil->rating }}" placeholder="4.8/5.0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="average_response_time" class="form-label fw-semibold">Average Response Time</label>
                                                <input type="text" class="form-control" id="average_response_time" name="average_response_time" value="{{ $profil->average_response_time }}" placeholder="2 hours">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="transactions_6_months" class="form-label fw-semibold">Transactions (6 Months)</label>
                                                <input type="number" class="form-control" id="transactions_6_months" name="transactions_6_months" value="{{ $profil->transactions_6_months }}" placeholder="125">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="embed_map" class="form-label fw-semibold">Map</label>
                                                <textarea class="form-control" name="embed_map" id="embed_map" rows="4">{{ $profil->embed_map }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="embed_youtube" class="form-label fw-semibold">Embed Youtube</label>
                                                <textarea class="form-control" name="embed_youtube" id="embed_youtube" rows="4">{{ $profil->embed_youtube }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="keyword" class="form-label fw-semibold">Keyword</label>
                                                <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $profil->keyword }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="deskripsi_keyword" class="form-label fw-semibold">Deskripsi Keyword</label>
                                                <textarea class="form-control" name="deskripsi_keyword" id="deskripsi_keyword" rows="3">{{ $profil->deskripsi_keyword }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Quill Editors -->
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="description" class="form-label fw-semibold">Company Description</label>
                                                <div id="description-editor"></div>
                                                <input type="hidden" name="description" id="description">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="vision" class="form-label fw-semibold">Vision</label>
                                                <div id="vision-editor"></div>
                                                <input type="hidden" name="vision" id="vision">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="mision" class="form-label fw-semibold">Mission</label>
                                                <div id="mision-editor"></div>
                                                <input type="hidden" name="mision" id="mision">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end mt-4 gap-3">
                                <button type="submit" id="updateButton" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Konfigurasi Quill
    const quillConfig = {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link'],
                [{ 'header': [1, 2, 3, false] }],
                ['clean']
            ]
        }
    };

    // Inisialisasi editor
    const editors = {
        deskripsi_1: new Quill('#deskripsi_1-editor', quillConfig),
        deskripsi_2: new Quill('#deskripsi_2-editor', quillConfig),
        deskripsi_3: new Quill('#deskripsi_3-editor', quillConfig),
        term: new Quill('#term-editor', quillConfig),
        description: new Quill('#description-editor', quillConfig),
        vision: new Quill('#vision-editor', quillConfig),
        mision: new Quill('#mision-editor', quillConfig)
    };

    // Isi data dari DB
    @php
        $quillFields = ['deskripsi_1', 'deskripsi_2', 'deskripsi_3', 'term', 'description', 'vision', 'mision'];
    @endphp
    @foreach($quillFields as $field)
        if (editors.{{ $field }}) {
            editors.{{ $field }}.root.innerHTML = `{!! addslashes($profil->$field ?? '') !!}`;
            editors.{{ $field }}.on('text-change', function() {
                document.getElementById('{{ $field }}').value = editors.{{ $field }}.root.innerHTML;
            });
        }
    @endforeach

    // Sinkronkan sebelum submit
    document.getElementById('profil-form').addEventListener('submit', function() {
        Object.keys(editors).forEach(key => {
            document.getElementById(key).value = editors[key].root.innerHTML;
        });
    });

    // Preview gambar (canvas)
    window.previewImage = function(inputId, canvasId) {
        const input = document.getElementById(inputId);
        const canvas = document.getElementById(canvasId);
        if (!input || !canvas) return;
        const file = input.files[0];
        if (!file) {
            canvas.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.src = e.target.result;
            img.onload = function() {
                const ctx = canvas.getContext('2d');
                const maxWidth = 200;
                const scale = maxWidth / img.width;
                const height = img.height * scale;
                canvas.width = maxWidth;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, maxWidth, height);
                canvas.style.display = 'block';

                // Jika ada container existing image, hapus mark deleted jika user pilih file baru
                const container = document.getElementById('container_' + inputId);
                if (container) {
                    container.classList.remove('marked-deleted');
                    const hiddenInput = document.getElementById('delete_' + inputId);
                    if (hiddenInput) hiddenInput.value = '0';
                }
            };
        };
        reader.readAsDataURL(file);
    };

    // Toggle delete image
    window.toggleDeleteImage = function(id) {
        const container = document.getElementById('container_' + id);
        const hiddenInput = document.getElementById('delete_' + id);
        const fileInput = document.getElementById(id);

        if (hiddenInput.value === '0') {
            hiddenInput.value = '1';
            container.classList.add('marked-deleted');
            // Jika ada file baru yang dipilih, hapus pilihannya
            if (fileInput) fileInput.value = '';
            const canvas = document.getElementById('preview_canvas_' + id);
            if (canvas) canvas.style.display = 'none';
        } else {
            hiddenInput.value = '0';
            container.classList.remove('marked-deleted');
        }
    };
});
</script>
@endpush
