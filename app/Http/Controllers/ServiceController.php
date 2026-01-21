<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Service;
use Illuminate\Support\Str;

use App\Services\ImageService;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:service-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:service-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:service-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:service-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Service";
        $subtitle = "Menu Service";
        $data_service = Service::all();
        return view('services.index', compact('data_service', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'description' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama layanan wajib diisi.',
            'name.unique' => 'Nama layanan sudah terdaftar.',
            'name.max' => 'Nama layanan tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'description.required' => 'Deskripsi layanan wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik
            $existingService = Service::where('slug', $input['slug'])->first();
            if ($existingService) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            // Handle image upload
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/services')
                : null;

            $service = Service::create($input);

            // Log action using LogHelper
            LogHelper::logAction(
                'services',
                $service->id,
                'Create',
                null,
                $service->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Layanan berhasil ditambahkan.',
                    'data' => $service
                ], 200);
            }

            return redirect()->route('services.index')->with('success', 'Layanan berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan layanan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan layanan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $service = Service::findOrFail($id);
            return response()->json($service, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data layanan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data layanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $service = Service::findOrFail($id);
            return response()->json($service, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data layanan untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data layanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'description' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama layanan wajib diisi.',
            'name.unique' => 'Nama layanan sudah terdaftar.',
            'name.max' => 'Nama layanan tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'description.required' => 'Deskripsi layanan wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $service = Service::findOrFail($id);
            $oldData = $service->toArray();
            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik
            $existingService = Service::where('slug', $input['slug'])->where('id', '!=', $id)->first();
            if ($existingService) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/services',
                    $service->image
                );
            } else {
                $input['image'] = $service->image;
            }

            $service->update($input);

            // Log action
            LogHelper::logAction(
                'services',
                $service->id,
                'Update',
                $oldData,
                $service->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Layanan berhasil diperbarui.',
                    'data' => $service
                ], 200);
            }

            return redirect()->route('services.index')->with('success', 'Layanan berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui layanan: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui layanan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui layanan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $service = Service::findOrFail($id);
            $oldData = $service->toArray();

            // Delete associated image if exists
            if ($service->image) {
                $filePath = public_path('upload/services/' . $service->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $service->delete();

            // Log action
            LogHelper::logAction(
                'services',
                $service->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Layanan berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('services.index')->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus layanan: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus layanan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus layanan.');
        }
    }
}
