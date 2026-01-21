@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back') }}/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/select2/dist/css/select2.min.css') }}">
    <style>
        .nav-tabs .nav-link.active {
            background: linear-gradient(to right, #00923f, #fff000) !important;
            color: white !important;
        }
    </style>
    <style>
        .card-member {
            position: relative;
        }
        .card-member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #cece16;
            z-index: -1;
            border-radius: 10px;
        }
        @media print {
            .card-member::before {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: #cece16 !important;
            }
            body * {
                visibility: hidden;
            }
            .card-member,
            .card-member * {
                visibility: visible;
            }
            .card-member {
                position: absolute;
                top: 0;
                left: 0;
                width: 350px;
                height: 200px;
            }
            .col-md-12.mt-3,
            h4,
            span {
                display: none;
            }
            .d-flex {
                display: block !important;
            }
            .card-member+.card-member {
                top: 220px;
            }
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
                                <li class="breadcrumb-item" aria-current="page">{{ $subtitle }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-3 text-center mb-n5">
                        <img src="{{ asset('template/back/dist/images/breadcrumb/ChatBc.png') }}" alt=""
                             class="img-fluid mb-n4">
                    </div>
                </div>
            </div>
        </div>

        <!-- Show Section -->
        <section class="datatables">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="userTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="data-diri-tab" data-bs-toggle="tab"
                                            data-bs-target="#data-diri" type="button" role="tab"
                                            aria-controls="data-diri" aria-selected="true">
                                        Data Diri
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="data-anggota-tab" data-bs-toggle="tab"
                                            data-bs-target="#data-anggota" type="button" role="tab"
                                            aria-controls="data-anggota" aria-selected="false">
                                        Data Anggota
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="dokumen-persyaratan-tab" data-bs-toggle="tab"
                                            data-bs-target="#dokumen-persyaratan" type="button" role="tab"
                                            aria-controls="dokumen-persyaratan" aria-selected="false">
                                        Dokumen Persyaratan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="kartu-anggota-tab" data-bs-toggle="tab"
                                            data-bs-target="#kartu-anggota" type="button" role="tab"
                                            aria-controls="kartu-anggota" aria-selected="false">
                                        Kartu Anggota
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="userTabsContent">
                                <div class="tab-pane fade show active" id="data-diri" role="tabpanel"
                                     aria-labelledby="data-diri-tab">
                                    <div class="row">
                                        <!-- Gambar Diri -->
                                        <div class="col-md-12 mb-3">
                                            <div class="linear-gradient d-flex align-items-center justify-content-center rounded-circle"
                                                 style="width: 110px; height: 110px;">
                                                <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                                                     style="width: 100px; height: 100px;">
                                                    <a href="{{ $data_user->image ? asset('/upload/users/' . $data_user->image) : 'https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg?format=1500w' }}">
                                                        <img src="{{ $data_user->image ? asset('/upload/users/' . $data_user->image) : 'https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg?format=1500w' }}"
                                                             alt="" class="w-100 h-100">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <h4 class="mt-3"><i class="fa fa-user-circle" style="color: #00923f;"></i> Data Diri</h4>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nama:</strong></label>
                                            <p>{{ $data_user->name }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>User:</strong></label>
                                            <p>{{ $data_user->user }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Email:</strong></label>
                                            <p>{{ $data_user->email }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nomor Telepon:</strong></label>
                                            <p>{{ $data_user->phone_number ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Tempat Lahir:</strong></label>
                                            <p>{{ $data_user->birth_place }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Tanggal Lahir:</strong></label>
                                            <p>{{ $data_user->birth_date ? \Carbon\Carbon::parse($data_user->birth_date)->translatedFormat('d F Y') : '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Role:</strong></label>
                                            <p>{{ implode(', ', $usersRole) ?: '-' }}</p>
                                        </div>

                                        <!-- Alamat Sesuai KTP -->
                                        <h4 class="mt-3"><i class="fa fa-address-card" style="color: #00923f;"></i> Alamat Sesuai KTP</h4>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Alamat Jalan:</strong></label>
                                            <p>{{ $data_user->address_by_card ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>RT/RW:</strong></label>
                                            <p>{{ $data_user->rt_rw ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Desa/Kelurahan:</strong></label>
                                            <p>{{ $data_user->subdistrict ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Kecamatan:</strong></label>
                                            <p>{{ $data_user->district ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Kota/Kabupaten:</strong></label>
                                            <p>{{ $data_user->city ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Provinsi:</strong></label>
                                            <p>{{ $data_user->province ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Kode POS:</strong></label>
                                            <p>{{ $data_user->postal_code ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Gunakan Alamat Domisili:</strong></label>
                                            <p>{{ $data_user->use_address == '1' ? 'Ya' : 'Tidak' }}</p>
                                        </div>

                                        <!-- Informasi Pekerjaan -->
                                        <h4 class="mt-3"><i class="fa fa-briefcase" style="color: #00923f;"></i> Informasi Pekerjaan</h4>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Status Pekerjaan:</strong></label>
                                            <p>{{ $data_user->occupation_type ? ucfirst($data_user->occupation_type) : '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nama Perusahaan/Instansi:</strong></label>
                                            <p>{{ $data_user->company_name ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Jabatan:</strong></label>
                                            <p>{{ $data_user->position ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Alamat Perusahaan/Instansi:</strong></label>
                                            <p>{{ $data_user->company_address ?? '-' }}</p>
                                        </div>

                                        <!-- Alamat Korespondensi -->
                                        <h4 class="mt-3"><i class="fa fa-location-arrow" style="color: #00923f;"></i> Alamat Korespondensi</h4>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Alamat untuk Surat Menyurat:</strong></label>
                                            <p>{{ $data_user->correspondence_address ?? '-' }}</p>
                                        </div>

                                        <!-- Pendidikan -->
                                        <h4 class="mt-3"><i class="fa fa-graduation-cap" style="color: #00923f;"></i> Pendidikan</h4>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Tingkat Pendidikan Terakhir:</strong></label>
                                            <p>{{ $data_user->last_education ? ucfirst($data_user->last_education) : '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Jurusan:</strong></label>
                                            <p>{{ $data_user->study_program ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nama Sekolah/Universitas:</strong></label>
                                            <p>{{ $data_user->university_name ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Tahun Lulus:</strong></label>
                                            <p>{{ $data_user->graduation_year ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="data-anggota" role="tabpanel" aria-labelledby="data-anggota-tab">
                                    <div class="row">
                                        <h4 class="mt-3"><i class="fa fa-book" style="color: #00923f;"></i> Informasi Dasar Keanggotaan</h4>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nomor Anggota:</strong></label>
                                            <p>{{ $data_user->member_number ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Tanggal Bergabung:</strong></label>
                                            <p>{{ $data_user->join_date ? \Carbon\Carbon::parse($data_user->join_date)->translatedFormat('d F Y') : '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Jenis Keanggotaan:</strong></label>
                                            <p>{{ $data_user->member_type ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Status Keanggotaan:</strong></label>
                                            <p>{{ $data_user->member_status ? ucfirst($data_user->member_status) : '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Terdaftar pada Cabang:</strong></label>
                                            <p>{{ $data_user->branch_id ? $branches[$data_user->branch_id] : '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Memiliki Izin Kuasa Hukum dari Pengadilan Pajak?</strong></label>
                                            <p>{{ $data_user->legal_authorization_permission == '1' ? 'Ya' : 'Tidak' }}</p>
                                        </div>
                                        @if ($data_user->legal_authorization_permission == '1')
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label"><strong>Kartu Izin Kuasa Hukum:</strong></label>
                                                <p>
                                                    @if ($data_user->legal_authorization_file)
                                                        <a href="{{ asset('/upload/legal_authorizations/' . $data_user->legal_authorization_file) }}"
                                                           target="_blank">Lihat file</a>
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                        @endif

                                        <hr>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Pernah Mengabdikan diri sebagai atau seorang pensiunan pegawai Direktorat Jenderal Pajak?</strong></label>
                                            <p>{{ $data_user->retired_tax_officer == '1' ? 'Ya' : 'Tidak' }}</p>
                                        </div>
                                        @if ($data_user->retired_tax_officer == '1')
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><strong>Pangkat Golongan:</strong></label>
                                                <p>{{ $data_user->position_tax ?? '-' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><strong>Tahun Pensiun:</strong></label>
                                                <p>{{ $data_user->retirement_year ?? '-' }}</p>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label"><strong>Surat Keputusan Pensiun:</strong></label>
                                                <p>
                                                    @if ($data_user->retirement_decision_letter)
                                                        <a href="{{ asset('/upload/retirement_letters/' . $data_user->retirement_decision_letter) }}"
                                                           target="_blank">Lihat file</a>
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                        @endif

                                        <h4 class="mt-3"><i class="fa fa-credit-card" style="color: #00923f;"></i> Nomor Identitas</h4>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nomor KTP:</strong></label>
                                            <p>{{ $data_user->nik }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nomor NPWP:</strong></label>
                                            <p>{{ $data_user->npwp }}</p>
                                        </div>

                                        <h4 class="mt-3"><i class="fa fa-universal-access" style="color: #00923f;"></i> Informasi Lisensi Praktik (Khusus Anggota Utama)</h4>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nomor Izin Praktik:</strong></label>
                                            <p>{{ $data_user->practice_license_number ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Tingkat Sertifikasi:</strong></label>
                                            <p>{{ $data_user->certification_level ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Tanggal Terbit Izin Praktik:</strong></label>
                                            <p>{{ $data_user->practice_license_issue_date ? \Carbon\Carbon::parse($data_user->practice_license_issue_date)->translatedFormat('d F Y') : '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="dokumen-persyaratan" role="tabpanel" aria-labelledby="dokumen-persyaratan-tab">
                                    <div class="row">
                                        <h4 class="mt-3"><i class="fa fa-upload" style="color: #00923f;"></i> Daftar Dokumen Terunggah</h4>
                                        <span>Daftar dokumen yang telah diunggah oleh pengguna.</span>

                                        <div class="table-responsive">
                                            <table id="scroll_hor" class="table border table-striped table-bordered display nowrap" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama</th>
                                                        <th>Link</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data_user->documents as $index => $document)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $document->document_name }}</td>
                                                            <td><a href="{{ asset('/upload/documents/' . $document->document_file) }}"
                                                                   target="_blank">Lihat file</a></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="kartu-anggota" role="tabpanel" aria-labelledby="kartu-anggota-tab">
                                    <div class="row" style="padding: 10px;">
                                        <h4 class="mt-3">
                                            <i class="fa fa-id-card" style="color: #00923f;"></i> Kartu Anggota
                                        </h4>
                                        <span>Berikut adalah kartu anggota untuk pengguna yang sedang dilihat.</span>

                                        <div class="col-md-12 mt-4">
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; width: 100%;">
                                                <div style="width: 100%; max-width: 350px; overflow-x: auto;">
                                                    @include('kartu_anggota.depan')
                                                </div>
                                                <div style="width: 100%; max-width: 350px; overflow-x: auto;">
                                                    @include('kartu_anggota.belakang')
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3" style="display: flex; justify-content: center; gap: 10px;">
                                                <a href="{{ route('kartu.cetak', $data_user->id) }}" target="_blank"
                                                   class="btn btn-primary btn-sm cetak-kartu">
                                                    <i class="fa fa-print"></i> Cetak Kartu
                                                </a>
                                                <a href="{{ route('kartu.unduh', $data_user->id) }}"
                                                   class="btn btn-success btn-sm unduh-pdf">
                                                    <i class="fa fa-download"></i> Unduh PDF
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <a href="{{ route('daftar.index') }}" class="btn btn-warning btn-sm"><i class="fa fa-undo"></i> Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('script')
    <script src="{{ asset('template/back/dist/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('template/back') }}/dist/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('template/back') }}/dist/js/datatable/datatable-basic.init.js"></script>
@endpush