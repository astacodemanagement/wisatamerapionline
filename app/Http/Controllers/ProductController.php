<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\CustomerCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:product-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update', 'manageImages', 'storeAdditionalImages', 'deleteAdditionalImage']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index()
    {
        $data_product = Product::with(['category', 'unit', 'images'])->get();
        $product_categories = ProductCategory::all();
        $customer_categories = CustomerCategory::all();
        $units = Unit::all();
        $product_sub_categories = \App\Models\ProductSubCategory::all();
        return view('products.index', [
            'title' => 'Produk',
            'subtitle' => 'Daftar Produk',
            'data_product' => $data_product,
            'product_categories' => $product_categories,
            'customer_categories' => $customer_categories,
            'units' => $units,
            'product_sub_categories' => $product_sub_categories
        ]);
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = Product::with(['category', 'unit', 'subCategory'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->limit(10) // Batasi hasil untuk performa
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'code' => 'required|string|max:255|unique:products,code',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'category_id' => 'required|exists:product_categories,id',
            'sub_category_id' => 'nullable|exists:product_sub_categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'attribute' => 'nullable|string',
            'review' => 'nullable|string',
            'supplier' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'nullable|integer|min:0',
            'status_display' => 'required|in:active,nonactive', // Tambahkan validasi untuk status_display
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.unique' => 'Slug sudah digunakan.',
            'code.required' => 'Kode produk wajib diisi.',
            'code.unique' => 'Kode produk sudah digunakan.',
            'image.mimes' => 'Gambar utama harus berupa file JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar utama tidak boleh lebih dari 4MB.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori produk tidak valid.',
            'sub_category_id.exists' => 'Sub kategori produk tidak valid.',
            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan tidak valid.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'min_stock.required' => 'Stok minimum wajib diisi.',
            'min_stock.integer' => 'Stok minimum harus berupa angka bulat.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'status_display.required' => 'Status tampilan wajib diisi.', // Pesan validasi untuk status_display
            'status_display.in' => 'Status tampilan harus "active" atau "nonactive".',
        ]);

        try {
            DB::beginTransaction();

            // Handle main image upload with WebP conversion
            $validated['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/products')
                : null;

            // Create product
            $product = Product::create($validated);

            // Log action
            LogHelper::logAction('products', $product->id, 'Create', null, $product->toArray());

            DB::commit();

            return response()->json([
                'message' => 'Produk berhasil ditambahkan.',
                'data' => $product
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan produk: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with(['category', 'unit', 'images', 'subCategory'])->findOrFail($id);
            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat menampilkan produk: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menampilkan produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::with(['category', 'unit', 'images'])->findOrFail($id);
            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengambil data produk untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'code' => 'required|string|max:255|unique:products,code,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'category_id' => 'required|exists:product_categories,id',
            'sub_category_id' => 'nullable|exists:product_sub_categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'attribute' => 'nullable|string',
            'review' => 'nullable|string',
            'supplier' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'nullable|integer|min:0',
            'status_display' => 'required|in:active,nonactive', // Tambahkan validasi untuk status_display
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.unique' => 'Slug sudah digunakan.',
            'code.required' => 'Kode produk wajib diisi.',
            'code.unique' => 'Kode produk sudah digunakan.',
            'image.mimes' => 'Gambar utama harus berupa file JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar utama tidak boleh lebih dari 4MB.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori produk tidak valid.',
            'sub_category_id.exists' => 'Sub kategori produk tidak valid.',
            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan tidak valid.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'min_stock.required' => 'Stok minimum wajib diisi.',
            'min_stock.integer' => 'Stok minimum harus berupa angka bulat.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'status_display.required' => 'Status tampilan wajib diisi.', // Pesan validasi untuk status_display
            'status_display.in' => 'Status tampilan harus "active" atau "nonactive".',
        ]);

        try {
            DB::beginTransaction();

            // Simpan data produk sebelum pembaruan untuk logging
            $oldData = $product->toArray();

            // Handle main image upload with WebP conversion
            if ($request->hasFile('image')) {
                $validated['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/products',
                    $product->image
                );
            } else {
                $validated['image'] = $product->image;
            }

            // Update product
            $product->update($validated);

            // Log action
            LogHelper::logAction('products', $product->id, 'Update', $oldData, $product->toArray());

            DB::commit();

            return response()->json([
                'message' => 'Produk berhasil diperbarui.',
                'data' => $product
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui produk: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $oldData = $product->toArray();

            // Delete main image
            if ($product->image && file_exists(public_path('upload/products/' . $product->image))) {
                unlink(public_path('upload/products/' . $product->image));
            }

            // Delete additional images
            foreach ($product->images as $image) {
                if (file_exists(public_path('upload/products/additional/' . $image->image_name))) {
                    unlink(public_path('upload/products/additional/' . $image->image_name));
                }
                $image->delete();
            }

            $product->delete();

            // Log action
            LogHelper::logAction('products', $product->id, 'Delete', $oldData, null);

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Produk berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus produk: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus produk.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus produk.');
        }
    }

    public function manageImages($id)
    {
        try {
            $product = Product::with('images')->findOrFail($id);
            return response()->json([
                'product' => $product,
                'images' => $product->images
            ], 200);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengambil data gambar produk: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data gambar produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeAdditionalImages(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'additional_images.*' => 'required|file|mimes:jpg,jpeg,png|max:4096',
        ], [
            'additional_images.*.required' => 'Gambar tambahan wajib diisi.',
            'additional_images.*.mimes' => 'Gambar tambahan harus berupa file JPG, JPEG, atau PNG.',
            'additional_images.*.max' => 'Ukuran setiap gambar tambahan tidak boleh lebih dari 4MB.',
        ]);

        try {
            DB::beginTransaction();

            // Get the highest order_display for existing images
            $maxOrder = ProductImage::where('product_id', $product->id)->max('order_display') ?? 0;
            $order = $maxOrder + 1;

            // Handle additional images upload
            foreach ($request->file('additional_images') as $image) {
                if ($image->isValid()) {
                    $filename = $this->imageService->handleImageUpload(
                        $image,
                        'upload/products/additional'
                    );
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_name' => $filename,
                        'order_display' => $order++,
                    ]);
                }
            }

            // Log action
            LogHelper::logAction('product_images', $product->id, 'Create', null, $request->file('additional_images'));

            DB::commit();

            return response()->json([
                'message' => 'Gambar tambahan berhasil ditambahkan.',
                'data' => $product->images
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan gambar tambahan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan gambar tambahan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAdditionalImage($id, $imageId)
    {
        try {
            DB::beginTransaction();

            $image = ProductImage::where('product_id', $id)->where('id', $imageId)->firstOrFail();
            $oldData = $image->toArray();

            // Delete image file
            if (file_exists(public_path('upload/products/additional/' . $image->image_name))) {
                unlink(public_path('upload/products/additional/' . $image->image_name));
            }

            $image->delete();

            // Log action
            LogHelper::logAction('product_images', $imageId, 'Delete', $oldData, null);

            DB::commit();

            return response()->json([
                'message' => 'Gambar tambahan berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menghapus gambar tambahan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus gambar tambahan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}