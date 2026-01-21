<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\ProductCategory;
use App\Services\ImageService;

class ProductCategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:product-category-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-category-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Kategori Produk";
        $subtitle = "Menu Kategori Produk";
        $data_category = ProductCategory::all();
        return view('product_categories.index', compact('data_category', 'title', 'subtitle'));
    }

    public function create(): View {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_categories,name',
            'slug' => 'required|string|max:120|unique:product_categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/product_categories')
                : null;

            $category = ProductCategory::create($input);

            LogHelper::logAction(
                'product_categories',
                $category->id,
                'Create',
                null,
                $category->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Kategori Produk berhasil ditambahkan.',
                    'data' => $category
                ], 200);
            }

            return redirect()->route('product-categories.index')->with('success', 'Kategori Produk berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan kategori product: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan kategori product.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            \Log::info('Show category response: ' . json_encode($category));
            return response()->json($category, 200);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data kategori produk: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            \Log::info('Edit category response: ' . json_encode($category));
            return response()->json($category, 200);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data kategori produk untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_categories,name,' . $id,
            'slug' => 'required|string|max:120|unique:product_categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
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

            $category = ProductCategory::findOrFail($id);
            $oldData = $category->toArray();
            $input = $validated;

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/product_categories',
                    $category->image
                );
            } else {
                $input['image'] = $category->image;
            }

            $category->update($input);

            LogHelper::logAction(
                'product_categories',
                $category->id,
                'Update',
                $oldData,
                $category->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Kategori Produk berhasil diperbarui.',
                    'data' => $category
                ], 200);
            }

            return redirect()->route('product-categories.index')->with('success', 'Kategori Produk berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui kategori product: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui kategori product: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui kategori product.',
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

            $category = ProductCategory::findOrFail($id);
            $oldData = $category->toArray();

            if ($category->image) {
                $filePath = public_path('upload/product_categories/' . $category->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $category->delete();

            LogHelper::logAction(
                'product_categories',
                $category->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Kategori Produk berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('product-categories.index')->with('success', 'Kategori Produk berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus kategori product: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus kategori product.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus kategori product.');
        }
    }
}
