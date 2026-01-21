<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\SubService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class DesignController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:design-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:design-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:design-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:design-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Desain";
        $subtitle = "Menu Desain";
        $designs = Design::with(['sub_service.service'])->get();
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('designs.index', compact('designs', 'sub_services', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Desain";
        $subtitle = "Form Tambah Desain";
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('designs.create', compact('sub_services', 'title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:designs,name',
                'image' => 'required|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'name.required' => 'Nama desain wajib diisi.',
                'name.unique' => 'Nama desain sudah terdaftar.',
                'name.max' => 'Nama desain tidak boleh lebih dari 255 karakter.',
                'image.required' => 'Gambar desain wajib diunggah.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $imagePath = $this->imageService->handleImageUpload(
                $request->file('image'),
                'upload/designs'
            );

            $design = Design::create([
                'name' => $validated['name'],
                'image' => $imagePath,
                'description' => $validated['description'],
                'sub_service_id' => $validated['sub_service_id'],
            ]);

            LogHelper::logAction('designs', $design->id, 'Create', null, $design->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Desain berhasil ditambahkan.',
                'data' => $design
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan desain: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan desain.',
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
            $design = Design::with(['sub_service.service'])->findOrFail($id);
            return response()->json($design, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data desain: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data desain.',
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
            $design = Design::findOrFail($id);
            $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
            return response()->json([
                'design' => $design,
                'sub_services' => $sub_services
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data desain untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data desain.',
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
            $design = Design::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:designs,name,' . $id,
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'name.required' => 'Nama desain wajib diisi.',
                'name.unique' => 'Nama desain sudah terdaftar.',
                'name.max' => 'Nama desain tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $imagePath = $design->image;
            if ($request->hasFile('image')) {
                $imagePath = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/designs',
                    $design->image
                );
            }

            $design->update([
                'name' => $validated['name'],
                'image' => $imagePath,
                'description' => $validated['description'],
                'sub_service_id' => $validated['sub_service_id'],
            ]);

            LogHelper::logAction('designs', $design->id, 'Update', $design->getOriginal(), $design->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Desain berhasil diperbarui.',
                'data' => $design
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui desain: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui desain.',
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

            $design = Design::findOrFail($id);
            $oldData = $design->toArray();

            // Delete associated image if exists
            if ($design->image) {
                $filePath = public_path('upload/designs/' . $design->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $design->delete();

            // Log action
            LogHelper::logAction(
                'designs',
                $design->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Desain berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('design.index')->with('success', 'Desain berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus design: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus design.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus design.');
        }
    }
}