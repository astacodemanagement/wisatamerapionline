<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class DivisionController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:division-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:division-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:division-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:division-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Divisi";
        $subtitle = "Menu Divisi";
        $divisions = Division::orderBy('order_display')->get();
        return view('divisions.index', compact('divisions', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Divisi";
        $subtitle = "Form Tambah Divisi";
        return view('divisions.create', compact('title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:divisions,name',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama divisi wajib diisi.',
                'name.unique' => 'Nama divisi sudah terdaftar.',
                'name.max' => 'Nama divisi tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/divisions')
                : null;

            $division = Division::create($input);
            LogHelper::logAction('divisions', $division->id, 'Create', null, $division->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Divisi berhasil ditambahkan.',
                'data' => $division
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan divisi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan divisi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $division = Division::findOrFail($id);
            return response()->json($division, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data divisi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data divisi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $division = Division::findOrFail($id);
            return response()->json([
                'division' => $division
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data divisi untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data divisi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $division = Division::findOrFail($id);
            $oldData = $division->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:divisions,name,' . $id,
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama divisi wajib diisi.',
                'name.unique' => 'Nama divisi sudah terdaftar.',
                'name.max' => 'Nama divisi tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/divisions', $division->image)
                : $division->image;

            $division->update($input);
            LogHelper::logAction('divisions', $division->id, 'Update', $oldData, $division->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Divisi berhasil diperbarui.',
                'data' => $division
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui divisi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui divisi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $division = Division::findOrFail($id);
            $oldData = $division->toArray();

            if ($division->image) {
                $filePath = public_path('upload/divisions/' . $division->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $division->delete();
            LogHelper::logAction('divisions', $division->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Divisi berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus divisi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus divisi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}