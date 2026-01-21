<?php

namespace App\Http\Controllers;

use App\Models\Legal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class LegalController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:legal-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:legal-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:legal-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:legal-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Legal";
        $subtitle = "Menu Legal";
        $legals = Legal::orderBy('order_display')->get();
        return view('legals.index', compact('legals', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Legal";
        $subtitle = "Form Tambah Legal";
        return view('legals.create', compact('title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:legals,name',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama legal wajib diisi.',
                'name.unique' => 'Nama legal sudah terdaftar.',
                'name.max' => 'Nama legal tidak boleh lebih dari 255 karakter.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/legals')
                : null;

            $legal = Legal::create($input);
            LogHelper::logAction('legals', $legal->id, 'Create', null, $legal->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Legal berhasil ditambahkan.',
                'data' => $legal
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan legal: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan legal.',
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
            $legal = Legal::findOrFail($id);
            return response()->json($legal, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data legal: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data legal.',
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
            $legal = Legal::findOrFail($id);
            return response()->json([
                'legal' => $legal
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data legal untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data legal.',
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
            $legal = Legal::findOrFail($id);
            $oldData = $legal->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:legals,name,' . $id,
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama legal wajib diisi.',
                'name.unique' => 'Nama legal sudah terdaftar.',
                'name.max' => 'Nama legal tidak boleh lebih dari 255 karakter.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/legals', $legal->image)
                : $legal->image;

            $legal->update($input);
            LogHelper::logAction('legals', $legal->id, 'Update', $oldData, $legal->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Legal berhasil diperbarui.',
                'data' => $legal
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui legal: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui legal.',
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
            $legal = Legal::findOrFail($id);
            $oldData = $legal->toArray();

            if ($legal->image) {
                $filePath = public_path('upload/legals/' . $legal->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $legal->delete();
            LogHelper::logAction('legals', $legal->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Legal berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus legal: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus legal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}