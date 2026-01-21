<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Unit;
 

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:unit-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:unit-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:unit-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:unit-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $title = "Halaman Satuan";
        $subtitle = "Menu Satuan";
        $data_unit = Unit::orderBy('order_display', 'asc')->get();
        return view('units.index', compact('data_unit', 'title', 'subtitle'));
    }

    public function create(): View
    {
        // Tidak digunakan karena form tambah ada di modal (AJAX)
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:units,name',
            'abbreviation' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.unique' => 'Nama satuan sudah terdaftar.',
            'name.max' => 'Nama satuan tidak boleh lebih dari 100 karakter.',
            'abbreviation.max' => 'Singkatan tidak boleh lebih dari 50 karakter.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
           

            $unit = Unit::create($input);

            LogHelper::logAction(
                'units',
                $unit->id,
                'Create',
                null,
                $unit->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Satuan berhasil ditambahkan.',
                    'data' => $unit
                ], 200);
            }

            return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan satuan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan satuan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            return response()->json($unit, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data satuan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data satuan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            return response()->json($unit, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data satuan untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data satuan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:units,name,' . $id,
            'abbreviation' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.unique' => 'Nama satuan sudah terdaftar.',
            'name.max' => 'Nama satuan tidak boleh lebih dari 100 karakter.',
            'abbreviation.max' => 'Singkatan tidak boleh lebih dari 50 karakter.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $unit = Unit::findOrFail($id);
            $oldData = $unit->toArray();
            $input = $validated;
            

            $unit->update($input);

            LogHelper::logAction(
                'units',
                $unit->id,
                'Update',
                $oldData,
                $unit->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Satuan berhasil diperbarui.',
                    'data' => $unit
                ], 200);
            }

            return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui satuan: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui satuan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui satuan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $unit = Unit::findOrFail($id);
            $oldData = $unit->toArray();
            $unit->delete();

            LogHelper::logAction(
                'units',
                $unit->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Satuan berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus satuan: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus satuan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus satuan.');
        }
    }
}
