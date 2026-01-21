<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OtherSlider;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class OtherSliderController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:other_slider-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:other_slider-create', ['only' => ['store']]);
        $this->middleware('permission:other_slider-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:other_slider-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = 'Halaman OtherSlider';
        $subtitle = 'Menu OtherSlider';
        $other_sliders = OtherSlider::all();
        return view('other_sliders.index', compact('other_sliders', 'title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:other_sliders,slug',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Name wajib diisi.',
            'name.max' => 'Name tidak boleh lebih dari 255 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.max' => 'Slug tidak boleh lebih dari 255 karakter.',
            'slug.unique' => 'Slug sudah terdaftar.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
             
            'order_display.required' => 'OrderDisplay wajib diisi.',
            'order_display.integer' => 'OrderDisplay harus berupa angka.',
            'order_display.min' => 'OrderDisplay tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/other_sliders');
            }

            $other_slider = OtherSlider::create($input);

            LogHelper::logAction('other_sliders', $other_slider->id, 'Create', null, $other_slider->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'OtherSlider berhasil ditambahkan.', 'data' => $other_slider], 200);
            }
            return redirect()->route('other_sliders.index')->with('success', 'OtherSlider berhasil ditambahkan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan other_sliders: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal menambahkan other_sliders.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $other_slider = OtherSlider::findOrFail($id);
            return response()->json($other_slider, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data other_sliders: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data other_sliders.', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $other_slider = OtherSlider::findOrFail($id);
            return response()->json($other_slider, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data other_sliders untuk edit: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data other_sliders.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug'          => 'required|string|max:255|unique:other_sliders,slug,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Name wajib diisi.',
            'name.max' => 'Name tidak boleh lebih dari 255 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.max' => 'Slug tidak boleh lebih dari 255 karakter.',
            'slug.unique' => 'Slug sudah terdaftar.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
           
            'order_display.required' => 'OrderDisplay wajib diisi.',
            'order_display.integer' => 'OrderDisplay harus berupa angka.',
            'order_display.min' => 'OrderDisplay tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $other_slider = OtherSlider::findOrFail($id);
            $oldData = $other_slider->toArray();
            $input = $validated;
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/other_sliders', $other_slider->image);
            } else {
                $input['image'] = $other_slider->image;
            }

            $other_slider->update($input);

            LogHelper::logAction('other_sliders', $other_slider->id, 'Update', $oldData, $other_slider->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'OtherSlider berhasil diperbarui.', 'data' => $other_slider], 200);
            }
            return redirect()->route('other_sliders.index')->with('success', 'OtherSlider berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui other_sliders: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui other_sliders: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal memperbarui other_sliders.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $other_slider = OtherSlider::findOrFail($id);
            $oldData = $other_slider->toArray();
            if ($other_slider->image) {
                $filePath = public_path('upload/other_sliders/' . $other_slider->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $other_slider->delete();

            LogHelper::logAction('other_sliders', $other_slider->id, 'Delete', $oldData, null);

            DB::commit();

            if (request()->ajax()) {
                return response()->json(['message' => 'OtherSlider berhasil dihapus.'], 200);
            }
            return redirect()->route('other_sliders.index')->with('success', 'OtherSlider berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus other_sliders: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['message' => 'Gagal menghapus other_sliders.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus other_sliders.');
        }
    }
}
