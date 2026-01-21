
<?php

cek
Standarisasi Penulisan Controller :
1. Awali fungsi dengan validasi :
$this->validate($request, [
    'name' => 'required|unique:units,name',
], [
    'name.required' => 'Nama wajib diisi.',
    'name.unique' => 'Nama sudah terdaftar.',
]);

2. Untuk Kode Penyimpanan bisa opsional tambahkan try catch :

  public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $this->validate($request, [
            'name' => 'required|unique:units,name',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.unique' => 'Nama sudah terdaftar.',
        ]);

        try {
            // Menyimpan data unit ke dalam database
            $unit = Unit::create($request->all());

            // Mendapatkan ID pengguna yang sedang login
            $loggedInUserId = Auth::id();

            // Menyimpan log histori
            $this->simpanLogHistori('Create', 'Satuan', $unit->id, $loggedInUserId, null, json_encode($unit));

            // Redirect dengan pesan sukses
            return redirect()->route('units.index')
                ->with('success', 'Satuan berhasil dibuat.');
        } catch (\Exception $e) {
            // Menangkap kesalahan dan menampilkan pesan error
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    dimana awalnya 

public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:units,name',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.unique' => 'Nama sudah terdaftar.',
        ]);


        $unit = Unit::create($request->all());

        $loggedInUserId = Auth::id();

        $this->simpanLogHistori('Create', 'Satuan', $unit->id, $loggedInUserId, null, json_encode($unit));

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil dibuat.');
    }

3. Tambahkan Penyimpanan Ke log history :
    $loggedInUserId = Auth::id();
    $this->simpanLogHistori('Create', 'Satuan', $unit->id, $loggedInUserId, null, json_encode($unit));

4. Untuk Di Index alert error dan sukses :
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

5. dan untuk di create dan edit diatas form :
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

                           
