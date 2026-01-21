@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <style>
        /* Pastikan modal-body memiliki scroll dan tombol footer tetap terlihat */
        .modal-dialog-scrollable .modal-body {
            max-height: 60vh;
            /* Batasi tinggi modal-body agar scroll muncul */
            overflow-y: auto;
        }

        /* Optional: Tambahkan padding agar konten tidak terlalu mepet */
        .modal-body {
            padding: 1.5rem;
        }

        /* Pastikan footer tidak ikut scroll */
        .modal-footer {
            position: sticky;
            bottom: 0;
            z-index: 1;
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
                    <div class="col-3 text-center mb-n5">
                        <img src="{{ asset('template/back/dist/images/breadcrumb/ChatBc.png') }}" alt=""
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
                                <div class="row">
                                    <div class="col-lg-12 margin-tb">
                                        @can('faq-create')
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#addFaqModal">
                                                    <i class="fa fa-plus"></i> Tambah FAQ
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
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

                                <table id="scroll_hor" class="table border table-striped table-bordered display nowrap"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Pertanyaan</th>
                                            <th>Jawaban</th>
                                            <th>Status</th>
                                            <th>Gambar</th>
                                            <th>Urutan</th>
                                            <th width="280px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_faq as $faq)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($faq->question, 50) }}</td>
                                                <td>{{ Str::limit($faq->answer, 50) }}</td>
                                                <td>{{ $faq->status }}</td>
                                                <td>
                                                    @if ($faq->image)
                                                        <a href="{{ asset('upload/faqs/' . $faq->image) }}"
                                                            target="_blank">Lihat Gambar</a>
                                                    @else
                                                        Tidak ada
                                                    @endif
                                                </td>
                                                <td>{{ $faq->order_display }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-show-faq"
                                                        data-id="{{ $faq->id }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </button>
                                                    @can('faq-edit')
                                                        <button class="btn btn-success btn-sm btn-edit-faq"
                                                            data-id="{{ $faq->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    @endcan
                                                    @can('faq-delete')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $faq->id }})">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                        <form id="delete-form-{{ $faq->id }}" method="POST"
                                                            action="{{ route('faqs.destroy', $faq->id) }}"
                                                            style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal Tambah FAQ -->
                                <div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="add-faq-form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="addFaqModalLabel">
                                                        <i class="bi bi-plus-circle me-2"></i>Tambah FAQ
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="faq-question" class="form-label">Pertanyaan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="faq-question"
                                                                    name="question" placeholder="Masukkan pertanyaan"
                                                                    required>
                                                                <div class="invalid-feedback" id="faq-question-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="faq-answer" class="form-label">Jawaban <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="faq-answer" name="answer" rows="4" placeholder="Masukkan jawaban"
                                                                    required></textarea>
                                                                <div class="invalid-feedback" id="faq-answer-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="faq-status" class="form-label">Status <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="faq-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="faq-status-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="faq-order-display" class="form-label">Urutan
                                                                    Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="faq-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="faq-order-display-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="faq-image" class="form-label">Gambar FAQ (JPG,
                                                                    JPEG,
                                                                    PNG)</label>
                                                                <input type="file" class="form-control" id="faq-image"
                                                                    name="image" accept=".jpg,.jpeg,.png"
                                                                    onchange="validateFaqImageUpload()">
                                                                <div class="invalid-feedback" id="faq-image-error"></div>
                                                                <img id="faq-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="faq-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="faq-error-message" class="text-danger small"></div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-save">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Edit FAQ -->
                                <div class="modal fade" id="editFaqModal" tabindex="-1"
                                    aria-labelledby="editFaqModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <form id="edit-faq-form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" id="edit-faq-id" name="id" />
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white" id="editFaqModalLabel">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit FAQ
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="edit-faq-error-message" class="text-danger small mb-2"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit-faq-question"
                                                                    class="form-label">Pertanyaan
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit-faq-question" name="question"
                                                                    placeholder="Masukkan pertanyaan" required>
                                                                <div class="invalid-feedback"
                                                                    id="edit-faq-question-error"></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-faq-answer" class="form-label">Jawaban
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="edit-faq-answer" name="answer" rows="4" placeholder="Masukkan jawaban"
                                                                    required></textarea>
                                                                <div class="invalid-feedback" id="edit-faq-answer-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-faq-status" class="form-label">Status
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="edit-faq-status"
                                                                    name="status" required>
                                                                    <option value="active">Aktif</option>
                                                                    <option value="nonactive">Tidak Aktif</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="edit-faq-status-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-faq-order-display"
                                                                    class="form-label">Urutan Tampilan</label>
                                                                <input type="number" class="form-control"
                                                                    id="edit-faq-order-display" name="order_display"
                                                                    placeholder="Contoh: 1" value="0"
                                                                    min="0">
                                                                <div class="invalid-feedback"
                                                                    id="edit-faq-order-display-error">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit-faq-image" class="form-label">Gambar FAQ
                                                                    (JPG,
                                                                    JPEG, PNG)</label>
                                                                <input type="file" class="form-control"
                                                                    id="edit-faq-image" name="image"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    onchange="validateEditFaqImageUpload()">
                                                                <div class="invalid-feedback" id="edit-faq-image-error">
                                                                </div>
                                                                <img id="edit-faq-image-preview" src="#"
                                                                    alt="Gambar Preview"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;">
                                                                <canvas id="edit-faq-image-preview-canvas"
                                                                    style="display: none; max-width: 100%; margin-top: 10px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="submit" class="btn btn-primary" id="btn-update">
                                                        <i class="fa fa-save"></i> Update
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="fa fa-undo"></i> Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Tampil FAQ -->
                                <div class="modal fade" id="showFaqModal" tabindex="-1"
                                    aria-labelledby="showFaqModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white" id="showFaqModalLabel">
                                                    <i class="bi bi-eye me-2"></i>Detail FAQ
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="show-faq-error-message" class="text-danger small mb-2"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="show-faq-question"
                                                                class="form-label">Pertanyaan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-faq-question" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-faq-answer"
                                                                class="form-label">Jawaban</label>
                                                            <textarea class="form-control" id="show-faq-answer" rows="4" readonly></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-faq-status" class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                id="show-faq-status" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-faq-order-display" class="form-label">Urutan
                                                                Tampilan</label>
                                                            <input type="text" class="form-control"
                                                                id="show-faq-order-display" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="show-faq-image" class="form-label">Gambar
                                                                FAQ</label>
                                                            <div id="show-faq-image"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fa fa-undo"></i> Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection




@push('script')
    <script src="{{ asset('template/back/dist/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/back/dist/js/datatable/datatable-basic.init.js') }}"></script>
    <script>
        function validateFaqImageUpload() {
            const fileInput = document.getElementById('faq-image');
            const errorDiv = document.getElementById('faq-image-error');
            const previewImage = document.getElementById('faq-image-preview');
            const previewCanvas = document.getElementById('faq-image-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB dalam bytes
            const allowedTypes = ['image/jpeg', 'image/png'];

            // Reset error dan pratinjau
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewImage.style.display = 'none';
            previewCanvas.style.display = 'none';
            fileInput.classList.remove('is-invalid');

            if (file) {
                // Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    errorDiv.textContent = 'File harus berupa JPEG atau PNG.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = ''; // Reset input file
                    return;
                }

                // Validasi ukuran file
                if (file.size > maxSize) {
                    errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = ''; // Reset input file
                    return;
                }

                // Pratinjau untuk JPEG atau PNG
                if (allowedTypes.includes(file.type)) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = new Image();
                        img.src = e.target.result;

                        img.onload = function() {
                            const canvasContext = previewCanvas.getContext('2d');
                            const maxWidth = 100; // Lebar maksimum pratinjau
                            const scaleFactor = maxWidth / img.width;
                            const newHeight = img.height * scaleFactor;

                            previewCanvas.width = maxWidth;
                            previewCanvas.height = newHeight;
                            canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);

                            previewCanvas.style.display = 'block';
                            previewImage.style.display = 'none';
                        };
                    };
                    reader.readAsDataURL(file);
                }
            }
        }

        function validateEditFaqImageUpload() {
            const fileInput = document.getElementById('edit-faq-image');
            const errorDiv = document.getElementById('edit-faq-image-error');
            const previewImage = document.getElementById('edit-faq-image-preview');
            const previewCanvas = document.getElementById('edit-faq-image-preview-canvas');
            const file = fileInput.files[0];
            const maxSize = 4 * 1024 * 1024; // 4 MB dalam bytes
            const allowedTypes = ['image/jpeg', 'image/png'];

            // Reset error dan pratinjau
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            previewImage.style.display = 'none';
            previewCanvas.style.display = 'none';
            fileInput.classList.remove('is-invalid');

            if (file) {
                // Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    errorDiv.textContent = 'File harus berupa JPEG atau PNG.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = ''; // Reset input file
                    return;
                }

                // Validasi ukuran file
                if (file.size > maxSize) {
                    errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';
                    errorDiv.style.display = 'block';
                    fileInput.classList.add('is-invalid');
                    fileInput.value = ''; // Reset input file
                    return;
                }

                // Pratinjau untuk JPEG atau PNG
                if (allowedTypes.includes(file.type)) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = new Image();
                        img.src = e.target.result;

                        img.onload = function() {
                            const canvasContext = previewCanvas.getContext('2d');
                            const maxWidth = 100; // Lebar maksimum pratinjau
                            const scaleFactor = maxWidth / img.width;
                            const newHeight = img.height * scaleFactor;

                            previewCanvas.width = maxWidth;
                            previewCanvas.height = newHeight;
                            canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);

                            previewCanvas.style.display = 'block';
                            previewImage.style.display = 'none';
                        };
                    };
                    reader.readAsDataURL(file);
                }
            }
        }

        $(document).ready(function() {
            // Fungsi spinner tombol (loading state)
            function setButtonLoading(button, isLoading, loadingText = 'Menyimpan...') {
                if (!button || button.length === 0) return;
                if (isLoading) {
                    button.data('original-html', button.html());
                    button.prop('disabled', true).html(
                        `<span class="spinner-border spinner-border-sm"></span> ${loadingText}`);
                } else {
                    const original = button.data('original-html') || '<i class="bi bi-save"></i> Simpan';
                    button.prop('disabled', false).html(original);
                }
            }

            // Fungsi handle error AJAX
            function handleAjaxError(xhr, target = null) {
                let message = "Terjadi kesalahan.";
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors).map(e => e[0]).join('<br>');
                    if (target) {
                        $(target).html(message);
                        // Tampilkan error pada field spesifik
                        $.each(errors, function(key, value) {
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}-error`).text(value[0]);
                            $(`#${target.replace('#', '')}-${key.replace('.', '-')}`).addClass(
                                'is-invalid');
                        });
                    }
                } else if (xhr.status === 403) {
                    message = "Anda tidak memiliki izin.";
                    if (target) $(target).html(message);
                } else if (xhr.responseJSON?.error) {
                    message = xhr.responseJSON.error;
                    if (target) $(target).html(message);
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: message,
                    confirmButtonText: 'OK'
                });
            }

            // Submit Tambah FAQ
            $('#add-faq-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-save');
                const formData = new FormData(form[0]);

                setButtonLoading(btn, true);
                $('#faq-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('faqs.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            $('#addFaqModal').modal('hide');
                            form[0].reset();
                            $('#faq-image-preview').hide();
                            $('#faq-image-preview-canvas').hide();
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#faq-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false);
                    }
                });
            });

            // Klik tombol Edit: ambil data dan tampilkan modal edit
            $(document).on('click', '.btn-edit-faq', function() {
                const faqId = $(this).data('id');
                $('#edit-faq-error-message').html('');
                $('#edit-faq-form')[0].reset();
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#edit-faq-image-preview').hide();
                $('#edit-faq-image-preview-canvas').hide();

                const url = `{{ route('faqs.edit', ':id') }}`.replace(':id', faqId);
                console.log('Request URL:', url); // Debug URL
                console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content')); // Debug CSRF

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Response:', response); // Debug respons
                        if (response && response.id) {
                            $('#edit-faq-id').val(response.id);
                            $('#edit-faq-question').val(response.question);
                            $('#edit-faq-answer').val(response.answer);
                            $('#edit-faq-status').val(response.status);
                            $('#edit-faq-order-display').val(response.order_display);
                            $('#edit-faq-image').val('');

                            const imageUrl = response.image ?
                                `{{ asset('upload/faqs') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#edit-faq-image-preview').attr('src', imageUrl).show();
                                $('#edit-faq-image-preview-canvas').hide();
                            } else {
                                $('#edit-faq-image-preview').hide();
                                $('#edit-faq-image-preview-canvas').hide();
                            }

                            console.log('Opening modal with data:',
                            response); // Debug sebelum buka modal
                            $('#editFaqModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr); // Debug error
                        handleAjaxError(xhr, '#edit-faq-error-message');
                    }
                });
            });

            // Submit form Edit FAQ
            $('#edit-faq-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-update');
                const faqId = $('#edit-faq-id').val();
                const formData = new FormData(form[0]);
                formData.append('_method', 'PUT');

                setButtonLoading(btn, true, 'Memperbarui...');
                $('#edit-faq-error-message').html('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `{{ route('faqs.update', ':id') }}`.replace(':id', faqId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            $('#editFaqModal').modal('hide');
                            location.reload(); // Reload halaman
                        });
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#edit-faq-error-message');
                    },
                    complete: function() {
                        setButtonLoading(btn, false, '<i class="fa fa-save"></i> Update');
                    }
                });
            });

            // Klik tombol Show: ambil data dan tampilkan modal show
            $(document).on('click', '.btn-show-faq', function() {
                const faqId = $(this).data('id');
                $('#show-faq-error-message').html('');
                console.log('Fetching FAQ ID:', faqId); // Debug ID
                const url = `{{ route('faqs.show', ':id') }}`.replace(':id', faqId);
                console.log('Request URL:', url); // Debug URL

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Response:', response); // Debug respons
                        if (response && response.id) {
                            $('#show-faq-question').val(response.question || '');
                            $('#show-faq-answer').val(response.answer || '');
                            $('#show-faq-status').val(response.status === 'active' ? 'Active' :
                                'Nonactive');
                            $('#show-faq-order-display').val(response.order_display || '0');
                            const imageUrl = response.image ?
                                `{{ asset('upload/faqs') }}/${response.image}` : null;
                            if (imageUrl && /\.(jpg|jpeg|png|webp)$/i.test(imageUrl)) {
                                $('#show-faq-image').html(
                                    `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="img-fluid" style="max-width: 50%;" alt="Gambar FAQ"></a>`
                                );
                            } else {
                                $('#show-faq-image').html('Tidak ada gambar');
                            }
                            console.log('Opening modal with data:',
                            response); // Debug sebelum buka modal
                            $('#showFaqModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Data tidak ditemukan atau respons tidak valid.',
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr); // Debug error
                        handleAjaxError(xhr, '#show-faq-error-message');
                    }
                });
            });

            // Konfirmasi Hapus FAQ
            window.confirmDelete = function(faqId) {
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
                        $.ajax({
                            url: `{{ route('faqs.destroy', ':id') }}`.replace(':id', faqId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                }).then(() => {
                                    $('#scroll_hor').DataTable().ajax
                                .reload(); // Reload DataTables
                                });
                            },
                            error: function(xhr) {
                                handleAjaxError(xhr);
                            }
                        });
                    }
                });
            };
        });
    </script>
@endpush
