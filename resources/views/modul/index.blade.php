@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active">{{ $subtitle }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $title }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

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

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('modul.store') }}" method="POST" id="create-module-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi Kebutuhan Module <span
                                            class="text-danger">*</span></label>
                                    <textarea name="description" id="description" class="form-control" rows="4"
                                        placeholder="Masukkan deskripsi kebutuhan module, misalnya: 'Buat schema untuk tabel products dengan kolom nama, harga, deskripsi, status'">{{ old('description', "Masukkan deskripsi kebutuhan module, misalnya: 'Buat schema untuk tabel products dengan kolom nama, harga, deskripsi, status'") }}</textarea>
                                    <div class="invalid-feedback" id="description-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="schema" class="form-label">Schema Migration <span
                                            class="text-danger">*</span></label>
                                    <textarea name="schema" id="schema" class="form-control" rows="10" required>{{ old('schema', "Schema::create('blogs', function (Blueprint \$table) {\n    \$table->id();\n    \$table->string('name');\n    \$table->string('slug')->unique();\n    \$table->string('image')->nullable();\n    \$table->text('description');\n    \$table->enum('status', ['active', 'nonactive'])->default('active');\n    \$table->integer('order_display')->default(0);\n    \$table->timestamps();\n});") }}</textarea>
                                    <div class="invalid-feedback" id="schema-error"></div>
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-info btn-sm" id="generate-schema-btn"><i
                                            class="fa fa-robot"></i> Generate Schema with AI</button>
                                    <button type="button" class="btn btn-warning btn-sm" id="validate-schema-btn"><i
                                            class="fa fa-check"></i> Validate Schema with AI</button>
                                    <button class="btn btn-success btn-sm" type="submit" id="create-module-btn"><i
                                            class="fa fa-save"></i> Create Module</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            function setButtonLoading(button, isLoading, loadingText = 'Processing...') {
                if (!button || button.length === 0) return;
                if (isLoading) {
                    button.data('original-html', button.html());
                    button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> ' +
                        loadingText);
                } else {
                    const original = button.data('original-html') || '<i class="fa fa-save"></i> Simpan';
                    button.prop('disabled', false).html(original);
                }
            }

            function handleAjaxError(xhr, target = null) {
                let message = 'Terjadi kesalahan.';
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors).map(e => e[0]).join('<br>');
                    if (target) {
                        $(target).html(message);
                        $.each(errors, function(key, value) {
                            $('#' + target.replace('#', '') + '-' + key.replace('.', '-') + '-error').text(
                                value[0]);
                            $('#' + target.replace('#', '') + '-' + key.replace('.', '-')).addClass(
                                'is-invalid');
                        });
                    }
                } else if (xhr.status === 403) {
                    message = 'Anda tidak memiliki izin.';
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

            // Generate Schema with AI
            $('#generate-schema-btn').click(function() {
                const description = $('#description').val();
                const btn = $(this);
                if (!description) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Deskripsi kebutuhan module harus diisi!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                setButtonLoading(btn, true, 'Generating...');
                $('#description-error').text('');
                $('#schema-error').text('');
                $('#description').removeClass('is-invalid');
                $('#schema').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('modul.generate-schema') }}",
                    type: 'POST',
                    data: {
                        description: description,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.schema) {
                            $('#schema').val(response.schema);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Schema migration telah dihasilkan oleh AI.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menghasilkan schema. Silakan coba lagi.'
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#description-error');
                    },
                    complete: function() {
                        setButtonLoading(btn, false,
                            '<i class="fa fa-robot"></i> Generate Schema with AI');
                    }
                });
            });

            // Validate Schema with AI
            $('#validate-schema-btn').click(function() {
                const schema = $('#schema').val();
                const btn = $(this);
                if (!schema) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Schema migration harus diisi!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                setButtonLoading(btn, true, 'Validating...');
                $('#schema-error').text('');
                $('#schema').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('modul.validate-schema') }}",
                    type: 'POST',
                    data: {
                        schema: schema,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.valid) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Valid!',
                                text: response.message ||
                                    'Schema migration valid dan dapat digunakan.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid!',
                                text: response.message ||
                                    'Schema migration tidak valid. Silakan periksa kembali.'
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, '#schema-error');
                    },
                    complete: function() {
                        setButtonLoading(btn, false,
                            '<i class="fa fa-check"></i> Validate Schema with AI');
                    }
                });
            });

            // Client-side validation before submit
            $('#create-module-form').submit(function(e) {
                const schema = $('#schema').val();
                const btn = $('#create-module-btn');

                if (!schema.includes('Schema::create')) {
                    e.preventDefault();
                    $('#schema-error').text('Schema harus mengandung Schema::create!');
                    $('#schema').addClass('is-invalid');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Schema harus mengandung Schema::create!'
                    });
                    return false;
                }

                // Set tombol Create Module ke loading
                setButtonLoading(btn, true, 'Creating Module...');

                // Biarkan form lanjut submit ke server
            });

        });
    </script>
@endpush
