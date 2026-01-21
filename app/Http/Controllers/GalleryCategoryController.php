<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class GalleryCategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:gallery_category-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:gallery_category-create', ['only' => ['store']]);
        $this->middleware('permission:gallery_category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:gallery_category-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Kategori Galeri";
        $subtitle = "Menu Kategori Galeri";
        $gallery_categories = GalleryCategory::orderBy('order_display')->get();

        return view('gallery_categories.index', compact('gallery_categories', 'title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:gallery_categories,name',
            'slug' => 'required|string|max:120|unique:gallery_categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'slug.required' => 'Slug kategori wajib diisi.',
            'slug.unique' => 'Slug kategori sudah terdaftar.',
            'slug.max' => 'Slug kategori tidak boleh lebih dari 120 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/gallery_categories')
                : null;

            $category = GalleryCategory::create($input);

            LogHelper::logAction(
                'gallery_categories',
                $category->id,
                'Create',
                null,
                $category->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Kategori galeri berhasil ditambahkan.',
                    'data' => $category
                ], 200);
            }

            return redirect()->route('gallery_categories.index')->with('success', 'Kategori galeri berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan kategori galeri: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan kategori galeri.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $category = GalleryCategory::findOrFail($id);
            return response()->json($category, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kategori galeri: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori galeri.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $category = GalleryCategory::findOrFail($id);
            return response()->json($category, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kategori galeri untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori galeri.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:gallery_categories,name,' . $id,
            'slug' => 'required|string|max:120|unique:gallery_categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'slug.required' => 'Slug kategori wajib diisi.',
            'slug.unique' => 'Slug kategori sudah terdaftar.',
            'slug.max' => 'Slug kategori tidak boleh lebih dari 120 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $category = GalleryCategory::findOrFail($id);
            $oldData = $category->toArray();
            $input = $validated;

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/gallery_categories',
                    $category->image
                );
            } else {
                $input['image'] = $category->image;
            }

            $category->update($input);

            LogHelper::logAction(
                'gallery_categories',
                $category->id,
                'Update',
                $oldData,
                $category->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Kategori galeri berhasil diperbarui.',
                    'data' => $category
                ], 200);
            }

            return redirect()->route('gallery_categories.index')->with('success', 'Kategori galeri berhasil diperbarui.');
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
            Log::error('Kesalahan saat memperbarui kategori galeri: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui kategori galeri.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $category = GalleryCategory::findOrFail($id);
            $oldData = $category->toArray();

            if ($category->image) {
                $filePath = public_path('upload/gallery_categories/' . $category->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $category->delete();

            LogHelper::logAction(
                'gallery_categories',
                $category->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Kategori galeri berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('gallery_categories.index')->with('success', 'Kategori galeri berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus kategori galeri: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus kategori galeri.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus kategori galeri.');
        }
    }
}