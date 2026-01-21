<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Destination;
use App\Services\ImageService;

class DestinationController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:destination-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:destination-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:destination-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:destination-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Destinasi";
        $subtitle = "Menu Destinasi";
        $data_destination = Destination::all();
        return view('destinations.index', compact('data_destination', 'title', 'subtitle'));
    }

    public function create(): View {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:300|unique:destinations,slug',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'location_details' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'views' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Nama destinasi wajib diisi.',
            'name.max' => 'Nama destinasi tidak boleh lebih dari 255 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.unique' => 'Slug sudah terdaftar.',
            'slug.max' => 'Slug tidak boleh lebih dari 300 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'thumbnail.mimes' => 'Thumbnail harus berupa file dengan format JPG, JPEG, atau PNG.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari 4MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'views.integer' => 'Jumlah views harus berupa angka.',
            'views.min' => 'Jumlah views tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['thumbnail'] = $request->hasFile('thumbnail')
                ? $this->imageService->handleImageUpload($request->file('thumbnail'), 'upload/destinations')
                : null;

            $destination = Destination::create($input);

            LogHelper::logAction(
                'destinations',
                $destination->id,
                'Create',
                null,
                $destination->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Destinasi berhasil ditambahkan.',
                    'data' => $destination
                ], 200);
            }

            return redirect()->route('destinations.index')->with('success', 'Destinasi berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan destinasi: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan destinasi.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $destination = Destination::findOrFail($id);
            return response()->json($destination, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data destinasi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data destinasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $destination = Destination::findOrFail($id);
            return response()->json($destination, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data destinasi untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data destinasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:300|unique:destinations,slug,' . $id,
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'location_details' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'views' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Nama destinasi wajib diisi.',
            'name.max' => 'Nama destinasi tidak boleh lebih dari 255 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.unique' => 'Slug sudah terdaftar.',
            'slug.max' => 'Slug tidak boleh lebih dari 300 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'thumbnail.mimes' => 'Thumbnail harus berupa file dengan format JPG, JPEG, atau PNG.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari 4MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'views.integer' => 'Jumlah views harus berupa angka.',
            'views.min' => 'Jumlah views tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $destination = Destination::findOrFail($id);
            $oldData = $destination->toArray();
            $input = $validated;

            if ($request->hasFile('thumbnail')) {
                $input['thumbnail'] = $this->imageService->handleImageUpload(
                    $request->file('thumbnail'),
                    'upload/destinations',
                    $destination->thumbnail
                );
            } else {
                $input['thumbnail'] = $destination->thumbnail;
            }

            $destination->update($input);

            LogHelper::logAction(
                'destinations',
                $destination->id,
                'Update',
                $oldData,
                $destination->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Destinasi berhasil diperbarui.',
                    'data' => $destination
                ], 200);
            }

            return redirect()->route('destinations.index')->with('success', 'Destinasi berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui destinasi: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui destinasi: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui destinasi.',
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

            $destination = Destination::findOrFail($id);
            $oldData = $destination->toArray();

            if ($destination->thumbnail) {
                $filePath = public_path('upload/destinations/' . $destination->thumbnail);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $destination->delete();

            LogHelper::logAction(
                'destinations',
                $destination->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Destinasi berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('destinations.index')->with('success', 'Destinasi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus destinasi: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus destinasi.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus destinasi.');
        }
    }
}