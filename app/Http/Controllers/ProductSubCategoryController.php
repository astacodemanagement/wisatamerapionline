<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\ProductSubCategory;
use App\Models\ProductCategory;
use App\Services\ImageService;

class ProductSubCategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:product-sub-category-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-sub-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-sub-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-sub-category-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Sub Kategori Produk";
        $subtitle = "Menu Sub Kategori Produk";
        $data_sub_category = ProductSubCategory::with(['category', 'parent'])->get();
        $data_category = ProductCategory::all(); // Untuk filter atau select
        return view('product_sub_categories.index', compact('data_sub_category', 'data_category', 'title', 'subtitle'));
    }

    public function create(): View
    {
        // Jika butuh view create terpisah, tapi karena pakai modal, skip atau kosongkan
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_sub_categories,name',
            'slug' => 'required|string|max:120|unique:product_sub_categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'category_id' => 'required|exists:product_categories,id',
            'parent_id' => 'nullable|exists:product_sub_categories,id',
        ], [
            'name.required' => 'Nama sub kategori wajib diisi.',
             'name.unique' => 'Nama kategori sudah terdaftar.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'slug.required' => 'Slug kategori wajib diisi.',
            'slug.unique' => 'Slug kategori sudah terdaftar.',
            'slug.max' => 'Slug kategori tidak boleh lebih dari 120 karakter.',
            'image.mimes' => 'Ikon harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran ikon tidak boleh lebih dari 4MB.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/product_sub_categories')
                : null;

            $subCategory = ProductSubCategory::create($input);

            LogHelper::logAction(
                'product_sub_categories',
                $subCategory->id,
                'Create',
                null,
                $subCategory->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Sub Kategori Produk berhasil ditambahkan.',
                    'data' => $subCategory
                ], 200);
            }

            return redirect()->route('product-sub-categories.index')->with('success', 'Sub Kategori Produk berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan sub kategori product: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan sub kategori product.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $subCategory = ProductSubCategory::findOrFail($id);
            \Log::info('Show sub category response: ' . json_encode($subCategory));
            return response()->json($subCategory, 200);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data sub kategori produk: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data sub kategori produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $subCategory = ProductSubCategory::findOrFail($id);
            \Log::info('Edit sub category response: ' . json_encode($subCategory));
            return response()->json($subCategory, 200);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data sub kategori produk untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data sub kategori produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_sub_categories,name,' . $id,
            'slug' => 'required|string|max:120|unique:product_sub_categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'category_id' => 'required|exists:product_categories,id',
            'parent_id' => 'nullable|exists:product_sub_categories,id',
        ], [
            'name.required' => 'Nama sub kategori wajib diisi.',
            'name.unique' => 'Nama sub kategori sudah terdaftar.',
            'name.max' => 'Nama sub kategori tidak boleh lebih dari 100 karakter.',
            'slug.required' => 'Slug kategori wajib diisi.',
            'slug.unique' => 'Slug kategori sudah terdaftar.',
            'slug.max' => 'Slug kategori tidak boleh lebih dari 120 karakter.',
            'image.mimes' => 'Ikon harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran ikon tidak boleh lebih dari 4MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $subCategory = ProductSubCategory::findOrFail($id);
            $oldData = $subCategory->toArray();
            $input = $validated;

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/product_sub_categories',
                    $subCategory->image
                );
            } else {
                $input['image'] = $subCategory->image;
            }

            $subCategory->update($input);

            LogHelper::logAction(
                'product_sub_categories',
                $subCategory->id,
                'Update',
                $oldData,
                $subCategory->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Sub Kategori Produk berhasil diperbarui.',
                    'data' => $subCategory
                ], 200);
            }

            return redirect()->route('product-sub-categories.index')->with('success', 'Sub Kategori Produk berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui sub kategori product: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui sub kategori product: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui sub kategori product.',
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

            $subCategory = ProductSubCategory::findOrFail($id);
            $oldData = $subCategory->toArray();

            if ($subCategory->image) {
                $filePath = public_path('upload/product_sub_categories/' . $subCategory->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $subCategory->delete();

            LogHelper::logAction(
                'product_sub_categories',
                $subCategory->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Sub Kategori Produk berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('product-sub-categories.index')->with('success', 'Sub Kategori Produk berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus sub kategori product: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus sub kategori product.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus sub kategori product.');
        }
    }
}