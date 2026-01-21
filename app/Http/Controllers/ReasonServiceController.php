<?php

namespace App\Http\Controllers;

use App\Models\ReasonService;
use App\Models\SubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class ReasonServiceController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:reason-service-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:reason-service-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:reason-service-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:reason-service-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Reason Service";
        $subtitle = "Menu Reason Service";
        $reason_services = ReasonService::with(['sub_service.service'])->get();
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('reason_services.index', compact('reason_services', 'sub_services', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Reason Service";
        $subtitle = "Form Tambah Reason Service";
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('reason-services.create', compact('sub_services', 'title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:reason_services,name',
                'description' => 'required|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'name.unique' => 'Nama sudah terdaftar.',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
                'description.required' => 'Deskripsi wajib diisi.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampil wajib diisi.',
                'order_display.integer' => 'Urutan tampil harus berupa angka.',
                'order_display.min' => 'Urutan tampil tidak boleh kurang dari 0.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/reason_services')
                : null;

            $reason_service = ReasonService::create($input);
            LogHelper::logAction('reason_services', $reason_service->id, 'Create', null, $reason_service->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Reason Service berhasil ditambahkan.',
                'data' => $reason_service
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan Reason Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan Reason Service.',
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
            $reason_service = ReasonService::with(['sub_service.service'])->findOrFail($id);
            return response()->json($reason_service, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data Reason Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data Reason Service.',
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
            $reason_service = ReasonService::findOrFail($id);
            $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
            return response()->json([
                'reason_service' => $reason_service,
                'sub_services' => $sub_services
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data Reason Service untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data Reason Service.',
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
            $reason_service = ReasonService::findOrFail($id);
            $oldData = $reason_service->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:reason_services,name,' . $id,
                'description' => 'required|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'name.unique' => 'Nama sudah terdaftar.',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
                'description.required' => 'Deskripsi wajib diisi.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampil wajib diisi.',
                'order_display.integer' => 'Urutan tampil harus berupa angka.',
                'order_display.min' => 'Urutan tampil tidak boleh kurang dari 0.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/reason_services', $reason_service->image)
                : $reason_service->image;

            $reason_service->update($input);
            LogHelper::logAction('reason_services', $reason_service->id, 'Update', $oldData, $reason_service->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Reason Service berhasil diperbarui.',
                'data' => $reason_service
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui Reason Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui Reason Service.',
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
            $reason_service = ReasonService::findOrFail($id);
            $oldData = $reason_service->toArray();

            if ($reason_service->image) {
                $filePath = public_path('upload/reason_services/' . $reason_service->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $reason_service->delete();
            LogHelper::logAction('reason_services', $reason_service->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Reason Service berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus Reason Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus Reason Service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}