@extends('layouts.app')
@section('title', $title)
@section('subtitle', $subtitle)

@push('css')
    <link rel="stylesheet" href="{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <style>
        .modal-dialog-scrollable .modal-body { max-height: 60vh; overflow-y: auto; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { position: sticky; bottom: 0; z-index: 1; background: #f8f9fa; }
        .preview-container { margin-top: 10px; max-width: 100px; border: 1px solid #ddd; border-radius: 4px; }
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
                        <div class="table-responsive">
                            <div class="row mb-3">
                                <div class="col-lg-12 text-start">
                                    @can('other_slider-create')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOtherSliderModal">
                                            <i class="fa fa-plus"></i> Tambah Other Slider
                                        </button>
                                    @endcan
                                </div>
                            </div>

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

                            <table id="scroll_hor" class="table border table-striped table-bordered display nowrap" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Deskripsi</th>
                                        <th>Status</th>
                                        <th>Gambar</th>
                                        <th>Urutan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($other_sliders as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $p->name }}</td>
                                            <td>{{ $p->slug }}</td>
                                            <td>{{ Str::limit($p->description, 50) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $p->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($p->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($p->image)
                                                    <a href="{{ asset('upload/other_sliders/' . $p->image) }}" target="_blank">Lihat</a>
                                                @else
                                                    Tidak ada
                                                @endif
                                            </td>
                                            <td>{{ $p->order_display }}</td>
                                           <td>
    <button class="btn btn-warning btn-sm btn-show" data-id="{{ $p->id }}" title="Lihat Detail">
        <i class="fas fa-eye"></i> Lihat
    </button>
    @can('other_slider-edit')
        <button class="btn btn-success btn-sm btn-edit" data-id="{{ $p->id }}" title="Edit">
            <i class="fas fa-edit"></i> Edit
        </button>
    @endcan
    @can('other_slider-delete')
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})" title="Hapus">
            <i class="fas fa-trash"></i> Hapus
        </button>
        <form id="delete-form-{{ $p->id }}" action="{{ route('other_sliders.destroy', $p->id) }}" method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
    @endcan
</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Modal Tambah -->
                            <div class="modal fade" id="addOtherSliderModal" tabindex="-1" aria-labelledby="addOtherSliderModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <form id="add-form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white">Tambah Other Slider</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="error-message" class="text-danger small mb-2"></div>

                                                <div class="mb-3">
                                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan nama" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="slug" id="slug" placeholder="otomatis" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea class="form-control" name="description" rows="3" placeholder="Opsional..."></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="status" required>
                                                        <option value="active">Aktif</option>
                                                        <option value="nonactive">Tidak Aktif</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Urutan <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="order_display" value="0" min="0" required>
                                                </div>

                                                <!-- GAMBAR DIPINDAH KE BAWAH -->
                                                <div class="mb-3">
                                                    <label class="form-label">Gambar (JPG/PNG, max 4MB)</label>
                                                    <input type="file" class="form-control" name="image" id="image" accept=".jpg,.jpeg,.png" onchange="validateImageUpload(this)">
                                                    <div class="invalid-feedback"></div>
                                                    <canvas id="image-preview-canvas" class="preview-container" style="display:none;"></canvas>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="submit" class="btn btn-primary" id="btn-save"><i class="fas fa-save"></i> Simpan</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editOtherSliderModal" tabindex="-1" aria-labelledby="editOtherSliderModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <form id="edit-form" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="id" id="edit-id">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white">Edit Other Slider</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="edit-error-message" class="text-danger small mb-2"></div>

                                                <div class="mb-3">
                                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" id="edit-name" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="slug" id="edit-slug" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea class="form-control" name="description" id="edit-description" rows="3" placeholder="Opsional..."></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="status" id="edit-status" required>
                                                        <option value="active">Aktif</option>
                                                        <option value="nonactive">Tidak Aktif</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Urutan <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="order_display" id="edit-order_display" min="0" required>
                                                </div>

                                                <!-- GAMBAR DIPINDAH KE BAWAH -->
                                                <div class="mb-3">
                                                    <label class="form-label">Gambar (Ganti jika perlu)</label>
                                                    <input type="file" class="form-control" name="image" id="edit-image" accept=".jpg,.jpeg,.png" onchange="validateImageUpload(this, true)">
                                                    <canvas id="edit-image-preview-canvas" class="preview-container" style="display:none;"></canvas>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="submit" class="btn btn-primary" id="btn-update"><i class="fas fa-save"></i> Update</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Show -->
                            <div class="modal fade" id="showOtherSliderModal" tabindex="-1" aria-labelledby="showOtherSliderModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white">Detail Other Slider</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered">
                                                <tr><th>Nama</th><td id="show-name">-</td></tr>
                                                <tr><th>Slug</th><td id="show-slug">-</td></tr>
                                                <tr><th>Deskripsi</th><td id="show-description">-</td></tr>
                                                <tr><th>Status</th><td id="show-status">-</td></tr>
                                                <tr><th>Gambar</th><td id="show-image">-</td></tr>
                                                <tr><th>Urutan</th><td id="show-order_display">-</td></tr>
                                            </table>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Tutup</button>
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
    function generateSlug(text) {
        return text.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function validateImageUpload(input, isEdit = false) {
        const file = input.files[0];
        const errorDiv = input.parentElement.querySelector('.invalid-feedback');
        const canvas = isEdit ? document.getElementById('edit-image-preview-canvas') : document.getElementById('image-preview-canvas');
        errorDiv.textContent = ''; errorDiv.style.display = 'none';
        canvas.style.display = 'none';
        input.classList.remove('is-invalid');

        if (!file) return;
        const allowed = ['image/jpeg', 'image/png'];
        const maxSize = 4 * 1024 * 1024;

        if (!allowed.includes(file.type)) {
            errorDiv.textContent = 'Hanya JPG/PNG'; errorDiv.style.display = 'block';
            input.classList.add('is-invalid'); input.value = ''; return;
        }
        if (file.size > maxSize) {
            errorDiv.textContent = 'Maksimal 4MB'; errorDiv.style.display = 'block';
            input.classList.add('is-invalid'); input.value = ''; return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                const ctx = canvas.getContext('2d');
                const maxW = 100;
                const scale = maxW / img.width;
                canvas.width = maxW; canvas.height = img.height * scale;
                ctx.drawImage(img, 0, 0, maxW, img.height * scale);
                canvas.style.display = 'block';
            };
        };
        reader.readAsDataURL(file);
    }

    $(document).ready(function() {
        $('#name, #edit-name').on('input', function() {
            const isEdit = $(this).attr('id') === 'edit-name';
            const slugField = isEdit ? '#edit-slug' : '#slug';
            $(slugField).val(generateSlug($(this).val()));
        });

        $('#addOtherSliderModal, #editOtherSliderModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('.invalid-feedback').text(''); $('.form-control').removeClass('is-invalid');
            document.querySelectorAll('canvas').forEach(c => c.style.display = 'none');
        });

        function setLoading(btn, loading, text = 'Menyimpan...') {
            if (loading) {
                btn.data('html', btn.html()).prop('disabled', true).html(`<span class="spinner-border spinner-border-sm"></span> ${text}`);
            } else {
                btn.prop('disabled', false).html(btn.data('html') || btn.html());
            }
        }

        function handleError(xhr, target) {
            let msg = 'Terjadi kesalahan.';
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                if (target) $(target).html(msg);
            }
            Swal.fire({ icon: 'error', title: 'Error', html: msg });
        }

        $('#add-form').submit(function(e) {
            e.preventDefault();
            const btn = $('#btn-save');
            const formData = new FormData(this);
            setLoading(btn, true);
            $.ajax({
                url: "{{ route('other_sliders.store') }}",
                type: "POST",
                data: formData,
                processData: false, contentType: false,
                success: res => Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload()),
                error: xhr => handleError(xhr, '#error-message'),
                complete: () => setLoading(btn, false)
            });
        });

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            $.get("{{ route('other_sliders.edit', ':id') }}".replace(':id', id), res => {
                $('#edit-id').val(res.id);
                $('#edit-name').val(res.name);
                $('#edit-slug').val(res.slug);
                $('#edit-description').val(res.description || '');
                $('#edit-status').val(res.status);
                $('#edit-order_display').val(res.order_display);

                if (res.image) {
                    const img = new Image();
                    img.src = `{{ asset('upload/other_sliders') }}/${res.image}`;
                    img.onload = () => {
                        const canvas = document.getElementById('edit-image-preview-canvas');
                        const ctx = canvas.getContext('2d');
                        const maxW = 100;
                        const scale = maxW / img.width;
                        canvas.width = maxW; canvas.height = img.height * scale;
                        ctx.drawImage(img, 0, 0, maxW, img.height * scale);
                        canvas.style.display = 'block';
                    };
                }
                $('#editOtherSliderModal').modal('show');
            }).fail(xhr => handleError(xhr));
        });

        $('#edit-form').submit(function(e) {
            e.preventDefault();
            const btn = $('#btn-update');
            const id = $('#edit-id').val();
            const formData = new FormData(this);
            formData.append('_method', 'PUT');
            setLoading(btn, true, 'Memperbarui...');
            $.ajax({
                url: "{{ route('other_sliders.update', ':id') }}".replace(':id', id),
                type: "POST",
                data: formData,
                processData: false, contentType: false,
                success: res => Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload()),
                error: xhr => handleError(xhr, '#edit-error-message'),
                complete: () => setLoading(btn, false)
            });
        });

        $(document).on('click', '.btn-show', function() {
            const id = $(this).data('id');
            $.get("{{ route('other_sliders.show', ':id') }}".replace(':id', id), res => {
                $('#show-name').text(res.name);
                $('#show-slug').text(res.slug);
                $('#show-description').text(res.description || '-');
                $('#show-status').html(`<span class="badge bg-${res.status === 'active' ? 'success' : 'secondary'}">${res.status}</span>`);
                $('#show-order_display').text(res.order_display);
                $('#show-image').html(res.image ?
                    `<a href="{{ asset('upload/other_sliders') }}/${res.image}" target="_blank">
                        <img src="{{ asset('upload/other_sliders') }}/${res.image}" class="img-fluid" style="max-height:200px;">
                     </a>` : 'Tidak ada'
                );
                $('#showOtherSliderModal').modal('show');
            }).fail(xhr => handleError(xhr));
        });

        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Yakin?', text: 'Data akan dihapus!', icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Ya, hapus!'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('other_sliders.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: res => Swal.fire('Terhapus!', res.message, 'success').then(() => location.reload()),
                        error: xhr => handleError(xhr)
                    });
                }
            });
        };
    });
</script>
@endpush