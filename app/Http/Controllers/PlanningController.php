<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\SubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class PlanningController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:planning-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:planning-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:planning-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:planning-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Perencanaan";
        $subtitle = "Menu Perencanaan";
        $plannings = Planning::with(['sub_service.service'])->get();
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('plannings.index', compact('plannings', 'sub_services', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Perencanaan";
        $subtitle = "Form Tambah Perencanaan";
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('plannings.create', compact('sub_services', 'title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:plannings,name',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'required|string',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'name.required' => 'Nama perencanaan wajib diisi.',
                'name.unique' => 'Nama perencanaan sudah terdaftar.',
                'name.max' => 'Nama perencanaan tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.required' => 'Deskripsi wajib diisi.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/plannings')
                : null;

            $planning = Planning::create($input);
            LogHelper::logAction('plannings', $planning->id, 'Create', null, $planning->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Perencanaan berhasil ditambahkan.',
                'data' => $planning
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan perencanaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan perencanaan.',
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
            $planning = Planning::with(['sub_service.service'])->findOrFail($id);
            return response()->json($planning, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data perencanaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data perencanaan.',
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
            $planning = Planning::findOrFail($id);
            $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
            return response()->json([
                'planning' => $planning,
                'sub_services' => $sub_services
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data perencanaan untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data perencanaan.',
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
            $planning = Planning::findOrFail($id);
            $oldData = $planning->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:plannings,name,' . $id,
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'required|string',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'name.required' => 'Nama perencanaan wajib diisi.',
                'name.unique' => 'Nama perencanaan sudah terdaftar.',
                'name.max' => 'Nama perencanaan tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.required' => 'Deskripsi wajib diisi.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/plannings', $planning->image)
                : $planning->image;

            $planning->update($input);
            LogHelper::logAction('plannings', $planning->id, 'Update', $oldData, $planning->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Perencanaan berhasil diperbarui.',
                'data' => $planning
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui perencanaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui perencanaan.',
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
            $planning = Planning::findOrFail($id);
            $oldData = $planning->toArray();

            if ($planning->image) {
                $filePath = public_path('upload/plannings/' . $planning->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $planning->delete();
            LogHelper::logAction('plannings', $planning->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Perencanaan berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus perencanaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus perencanaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}