<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Slider;
use App\Services\ImageService;

class SliderController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:slider-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:slider-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:slider-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:slider-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Slider";
        $subtitle = "Menu Slider";
        $data_slider = Slider::all();
        return view('sliders.index', compact('data_slider', 'title', 'subtitle'));
    }

    public function create(): View
    {
        $title = "Tambah Slider";
        $subtitle = "Form Tambah Slider";
        return view('sliders.create', compact('title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sliders,name',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'link' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama slider wajib diisi.',
            'name.unique' => 'Nama slider sudah terdaftar.',
            'name.max' => 'Nama slider tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'link.max' => 'Link tidak boleh lebih dari 255 karakter.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;

            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/sliders')
                : null;

            $slider = Slider::create($input);

            LogHelper::logAction(
                'sliders',
                $slider->id,
                'Create',
                null,
                $slider->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Slider berhasil ditambahkan.',
                    'data' => $slider
                ], 200);
            }

            return redirect()->route('sliders.index')->with('success', 'Slider berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan slider: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan slider.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $slider = Slider::findOrFail($id);
            return response()->json($slider, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data slider: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data slider.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $slider = Slider::findOrFail($id);
            return response()->json($slider, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data slider untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data slider.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sliders,name,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'link' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama slider wajib diisi.',
            'name.unique' => 'Nama slider sudah terdaftar.',
            'name.max' => 'Nama slider tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'link.max' => 'Link tidak boleh lebih dari 255 karakter.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $slider = Slider::findOrFail($id);
            $oldData = $slider->toArray();
            $input = $validated;

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/sliders',
                    $slider->image
                );
            } else {
                $input['image'] = $slider->image;
            }

            $slider->update($input);

            LogHelper::logAction(
                'sliders',
                $slider->id,
                'Update',
                $oldData,
                $slider->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Slider berhasil diperbarui.',
                    'data' => $slider
                ], 200);
            }

            return redirect()->route('sliders.index')->with('success', 'Slider berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui slider: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui slider: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui slider.',
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

        $slider = Slider::findOrFail($id);
        $oldData = $slider->toArray();

        if ($slider->image) {
            $filePath = public_path('upload/sliders/' . $slider->image);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $slider->delete();

        LogHelper::logAction(
            'sliders',
            $slider->id,
            'Delete',
            $oldData,
            null
        );

        DB::commit(); // Commit the transaction

        if (request()->ajax()) {
            return response()->json([
                'message' => 'Slider berhasil dihapus.'
            ], 200);
        }

        return redirect()->route('sliders.index')->with('success', 'Slider berhasil dihapus.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal menghapus slider: ' . $e->getMessage());
        if (request()->ajax()) {
            return response()->json([
                'message' => 'Gagal menghapus slider.',
                'error' => $e->getMessage()
            ], 500);
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus slider.');
    }
}
}