<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Models\GalleryCategory;

class GalleryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:gallery-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:gallery-create', ['only' => ['store']]);
        $this->middleware('permission:gallery-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:gallery-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

public function index(Request $request): View
{
    $title = 'Halaman Gallery';
    $subtitle = 'Menu Gallery';
    $galleries = Gallery::with('category')->get();
    $categories = GalleryCategory::orderBy('order_display')->get();
    return view('galleries.index', compact('galleries', 'title', 'subtitle', 'categories'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gallery_category_id' => 'nullable|exists:gallery_categories,id',
            'slug' => 'required|string|max:255|unique:galleries,slug',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Name wajib diisi.',
            'gallery_category_id.exists' => 'Kategori tidak valid.',
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
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/galleries');
            }

            $gallery = Gallery::create($input);

            LogHelper::logAction('galleries', $gallery->id, 'Create', null, $gallery->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Gallery berhasil ditambahkan.', 'data' => $gallery], 200);
            }
            return redirect()->route('galleries.index')->with('success', 'Gallery berhasil ditambahkan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan galleries: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal menambahkan galleries.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

   public function show($id)
{
    try {
        $gallery = Gallery::with('category')->findOrFail($id);
        return response()->json($gallery, 200);
    } catch (\Exception $e) {
        Log::error('Gagal mengambil data galleries: ' . $e->getMessage());
        return response()->json(['message' => 'Gagal mengambil data galleries.', 'error' => $e->getMessage()], 500);
    }
}

   public function edit($id)
{
    try {
        $gallery = Gallery::with('category')->findOrFail($id);
        return response()->json($gallery, 200);
    } catch (\Exception $e) {
        Log::error('Gagal mengambil data galleries untuk edit: ' . $e->getMessage());
        return response()->json(['message' => 'Gagal mengambil data galleries.', 'error' => $e->getMessage()], 500);
    }
}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
           'slug' => 'required|string|max:255|unique:galleries,slug,' . $id . ',id',
           'gallery_category_id' => 'nullable|exists:gallery_categories,id',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Name wajib diisi.',
            'name.max' => 'Name tidak boleh lebih dari 255 karakter.',
            'gallery_category_id.exists' => 'Kategori tidak valid.',
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

            $gallery = Gallery::findOrFail($id);
            $oldData = $gallery->toArray();
            $input = $validated;
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/galleries', $gallery->image);
            } else {
                $input['image'] = $gallery->image;
            }

            $gallery->update($input);

            LogHelper::logAction('galleries', $gallery->id, 'Update', $oldData, $gallery->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Gallery berhasil diperbarui.', 'data' => $gallery], 200);
            }
            return redirect()->route('galleries.index')->with('success', 'Gallery berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui galleries: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui galleries: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal memperbarui galleries.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $gallery = Gallery::findOrFail($id);
            $oldData = $gallery->toArray();
            if ($gallery->image) {
                $filePath = public_path('upload/galleries/' . $gallery->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $gallery->delete();

            LogHelper::logAction('galleries', $gallery->id, 'Delete', $oldData, null);

            DB::commit();

            if (request()->ajax()) {
                return response()->json(['message' => 'Gallery berhasil dihapus.'], 200);
            }
            return redirect()->route('galleries.index')->with('success', 'Gallery berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus galleries: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['message' => 'Gagal menghapus galleries.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus galleries.');
        }
    }
}
