<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SubService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class SubServiceController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:subservice-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:subservice-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:subservice-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:subservice-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Sub Service";
        $subtitle = "Menu Sub Service";
        $data_sub_services = SubService::with('service')->get();
        $services = Service::where('status', 'active')->orderBy('order_display')->get(['id', 'name', 'slug', 'status', 'order_display']);
        return view('sub_services.index', compact('data_sub_services', 'services', 'title', 'subtitle')); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View {}

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_services,name',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'description' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'service_id' => 'required|exists:services,id',
        ], [
            'name.required' => 'Nama sub layanan wajib diisi.',
            'name.unique' => 'Nama sub layanan sudah terdaftar.',
            'name.max' => 'Nama sub layanan tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'description.required' => 'Deskripsi sub layanan wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'service_id.required' => 'Layanan utama wajib dipilih.',
            'service_id.exists' => 'Layanan utama tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik
            $existingSubService = SubService::where('slug', $input['slug'])->first();
            if ($existingSubService) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            // Handle image upload
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/subservices')
                : null;

            $subService = SubService::create($input);

            // Log action using LogHelper
            LogHelper::logAction(
                'sub_services',
                $subService->id,
                'Create',
                null,
                $subService->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Sub layanan berhasil ditambahkan.',
                    'data' => $subService
                ], 200);
            }

            return redirect()->route('admin.sub_services.index')->with('success', 'Sub layanan berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan sub layanan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan sub layanan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

  

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $subService = SubService::with('service')->findOrFail($id);
            return response()->json($subService, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data sub layanan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data sub layanan.',
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
            $subService = SubService::findOrFail($id);
            $services = Service::where('status', 'active')->get();
            return response()->json([
                'sub_service' => $subService,
                'services' => $services
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data sub layanan untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data sub layanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_services,name,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'description' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'service_id' => 'required|exists:services,id',
        ], [
            'name.required' => 'Nama sub layanan wajib diisi.',
            'name.unique' => 'Nama sub layanan sudah terdaftar.',
            'name.max' => 'Nama sub layanan tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'description.required' => 'Deskripsi sub layanan wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'service_id.required' => 'Layanan utama wajib dipilih.',
            'service_id.exists' => 'Layanan utama tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $subService = SubService::findOrFail($id);
            $oldData = $subService->toArray();
            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik
            $existingSubService = SubService::where('slug', $input['slug'])->where('id', '!=', $id)->first();
            if ($existingSubService) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/subservices',
                    $subService->image
                );
            } else {
                $input['image'] = $subService->image;
            }

            $subService->update($input);

            // Log action
            LogHelper::logAction(
                'sub_services',
                $subService->id,
                'Update',
                $oldData,
                $subService->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Sub layanan berhasil diperbarui.',
                    'data' => $subService
                ], 200);
            }

            return redirect()->route('admin.sub_services.index')->with('success', 'Sub layanan berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui sub layanan: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui sub layanan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui sub layanan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $subService = SubService::findOrFail($id);
            $oldData = $subService->toArray();

            // Delete associated image if exists
            if ($subService->image) {
                $filePath = public_path('upload/subservices/' . $subService->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $subService->delete();

            // Log action
            LogHelper::logAction(
                'sub_services',
                $subService->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Sub layanan berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('subservices.index')->with('success', 'Sub layanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus sub layanan: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus sub layanan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus sub layanan.');
        }
    }
}
