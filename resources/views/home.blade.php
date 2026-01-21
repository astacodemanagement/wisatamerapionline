@extends('layouts.app')

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
        <style>
            /* Pastikan modal-body memiliki scroll dan tombol footer tetap terlihat */
            .modal-dialog-scrollable .modal-body {
                max-height: 60vh;
                overflow-y: auto;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .modal-footer {
                position: sticky;
                bottom: 0;
                z-index: 1;
            }

            /* Styling untuk card */
            .card {
                border-radius: 10px;
                transition: transform 0.3s;
            }

            .card:hover {
                transform: translateY(-5px);
            }

            .round {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background-color: #f8f9fa;
            }

            /* Responsif untuk card */
            @media (max-width: 576px) {
                .card {
                    margin-bottom: 1rem;
                }
            }
        </style>
    @endpush

    <div class="card bg-light-info shadow-none position-relative overflow-hidden" style="border: solid 0.5px #ccc;">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">{{ $title ?? 'Dashboard' }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item" aria-current="page">{{ $subtitle ?? 'Overview' }}</li>
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


    



    <div class="container-fluid">
        <div class="ms-auto mb-3 d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary">
                <i class="ti ti-filter"></i>
            </button>
            <select class="form-select w-auto" id="yearFilter">
                <option value="2025" {{ ($year ?? 2025) == 2025 ? 'selected' : '' }}>Tahun 2025</option>
                <option value="2024" {{ ($year ?? 2025) == 2024 ? 'selected' : '' }}>Tahun 2024</option>
            </select>
        </div>

        <div class="row">
            <!-- Card 1: Total Tours -->
            <div class="col-sm-6 col-xl-4">
                <div class="card text-white shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="round rounded d-flex align-items-center justify-content-center">
                                <i class="fa fa-plane text-primary fs-7" title="Total Tours"></i>
                            </div>
                            <h6 class="mb-0 ms-3">Total Tour ({{ $year ?? 2025 }})</h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h3 class="mb-0 fw-semibold fs-7">{{ $totalTours ?? 0 }}</h3>
                            <span class="fw-bold text-black">Tour Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 2: Total Destinations -->
            <div class="col-sm-6 col-xl-4">
                <div class="card text-white shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="round rounded d-flex align-items-center justify-content-center">
                                <i class="fa fa-map text-danger fs-7" title="Total Destinations"></i>
                            </div>
                            <h6 class="mb-0 ms-3">Total Destinasi ({{ $year ?? 2025 }})</h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h3 class="mb-0 fw-semibold fs-7">{{ $totalDestinations ?? 0 }}</h3>
                            <span class="fw-bold text-black">Destinasi Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 3: Total Galleries -->
            <div class="col-sm-6 col-xl-4">
                <div class="card text-white shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="round rounded d-flex align-items-center justify-content-center">
                                <i class="fa fa-images text-success fs-7" title="Total Galleries"></i>
                            </div>
                            <h6 class="mb-0 ms-3">Total Galeri ({{ $year ?? 2025 }})</h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h3 class="mb-0 fw-semibold fs-7">{{ $totalGalleries ?? 0 }}</h3>
                            <span class="fw-bold text-black">Galeri Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 4: Total Testimonials -->
            <div class="col-sm-6 col-xl-4">
                <div class="card text-white shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="round rounded d-flex align-items-center justify-content-center">
                                <i class="fa fa-comments text-warning fs-7" title="Total Testimonials"></i>
                            </div>
                            <h6 class="mb-0 ms-3">Total Testimoni ({{ $year ?? 2025 }})</h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h3 class="mb-0 fw-semibold fs-7">{{ $totalTestimonials ?? 0 }}</h3>
                            <span class="fw-bold text-black">Testimoni Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 5: Total Agents -->
            <div class="col-sm-6 col-xl-4">
                <div class="card text-white shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="round rounded d-flex align-items-center justify-content-center">
                                <i class="fa fa-users text-info fs-7" title="Total Agents"></i>
                            </div>
                            <h6 class="mb-0 ms-3">Total Agent ({{ $year ?? 2025 }})</h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h3 class="mb-0 fw-semibold fs-7">{{ $totalAgents ?? 0 }}</h3>
                            <span class="fw-bold text-black">Agent Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 6: Total Services -->
            <div class="col-sm-6 col-xl-4">
                <div class="card text-white shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="round rounded d-flex align-items-center justify-content-center">
                                <i class="fa fa-cog text-dark fs-7" title="Total Services"></i>
                            </div>
                            <h6 class="mb-0 ms-3">Total Layanan ({{ $year ?? 2025 }})</h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <h3 class="mb-0 fw-semibold fs-7">{{ $totalServices ?? 0 }}</h3>
                            <span class="fw-bold text-black">Layanan Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart: Data Distribution -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Distribusi Data ({{ $year ?? 2025 }})</h5>
                <canvas id="dataDistributionChart"></canvas>
            </div>
        </div>

        <!-- User Profile Card -->
        {{-- <div class="card mt-3 p-3 d-flex flex-row align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="position-relative">
                        <div class="border border-2 border-primary rounded-circle">
                            <img src="{{ optional(Auth::user())->image ? asset('/upload/users/' . Auth::user()->image) : 'https://img.freepik.com/premium-vector/character-avatar-isolated_729149-194801.jpg?semt=ais_hybrid&w=740' }}"
                                class="rounded-circle m-1" alt="user" width="60" />
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-semibold"><span class="text-dark">{{ Auth::user()->name }}</span></h3>
                        <span><b>Anggota Utama</b></span>
                        <p class="mb-1">No. Anggota: {{ Auth::user()->member_number ?? 'N/A' }}</p>
                        <p class="mb-1">Izin: {{ Auth::user()->certification_number ?? 'KEP-5502/IP.A/PJ/2019' }}
                            (Sertifikasi: {{ Auth::user()->certification_level ?? 'N/A' }})</p>
                        <p class="mb-0 fw-bold text-primary">Status: {{ Auth::user()->member_status ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('users.profile.edit', Auth::user()) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> Ubah Profil
                </a>
            </div>
        </div> --}}
    </div>
@endsection

@push('script')
    <script src="{{ asset('template/back/dist/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/back/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/back/dist/libs/owl.carousel/dist/owl.carousel.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Inisialisasi Chart.js untuk distribusi data
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('dataDistributionChart').getContext('2d');
            const dataDistributionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Tour', 'Destinasi', 'Galeri', 'Testimoni', 'Agent', 'Layanan'],
                    datasets: [{
                        label: 'Total Data ({{ $year ?? 2025 }})',
                        data: [
                            {{ $totalTours ?? 0 }},
                            {{ $totalDestinations ?? 0 }},
                            {{ $totalGalleries ?? 0 }},
                            {{ $totalTestimonials ?? 0 }},
                            {{ $totalAgents ?? 0 }},
                            {{ $totalServices ?? 0 }}
                        ],
                        backgroundColor: ['#007bff', '#dc3545', '#28a745', '#ffc107', '#17a2b8', '#343a40'],
                        borderColor: ['#0056b3', '#a71d2a', '#1e7e34', '#d39e00', '#117a8b', '#1d2124'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Kategori'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Event listener untuk filter tahun
            $('#yearFilter').on('change', function() {
                const selectedYear = $(this).val();
                window.location.href = `{{ route('home') }}?year=${selectedYear}`;
            });
        });
    </script>
@endpush
