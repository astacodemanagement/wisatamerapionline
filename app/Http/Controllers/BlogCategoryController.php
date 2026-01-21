<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\BlogCategory;
use App\Services\ImageService;

class BlogCategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:blog-category-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:blog-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:blog-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:blog-category-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Kategori Blog";
        $subtitle = "Menu Kategori Blog";
        $data_category = BlogCategory::all();
        return view('blog_categories.index', compact('data_category', 'title', 'subtitle'));
    }

    public function create(): View
    {
         
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:100|unique:blog_categories,category_name',
            'category_slug' => 'required|string|max:120|unique:blog_categories,category_slug',
            'category_description' => 'nullable|string',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.unique' => 'Nama kategori sudah terdaftar.',
            'category_name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'category_slug.required' => 'Slug kategori wajib diisi.',
            'category_slug.unique' => 'Slug kategori sudah terdaftar.',
            'category_slug.max' => 'Slug kategori tidak boleh lebih dari 120 karakter.',
            'icon.mimes' => 'Ikon harus berupa file dengan format JPG, JPEG, atau PNG.',
            'icon.max' => 'Ukuran ikon tidak boleh lebih dari 4MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['icon'] = $request->hasFile('icon')
                ? $this->imageService->handleImageUpload($request->file('icon'), 'upload/blog_categories')
                : null;

            $category = BlogCategory::create($input);

            LogHelper::logAction(
                'blog_categories',
                $category->id,
                'Create',
                null,
                $category->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Kategori blog berhasil ditambahkan.',
                    'data' => $category
                ], 200);
            }

            return redirect()->route('blog-categories.index')->with('success', 'Kategori blog berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan kategori blog: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan kategori blog.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            return response()->json($category, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kategori blog: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            return response()->json($category, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kategori blog untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:100|unique:blog_categories,category_name,' . $id,
            'category_slug' => 'required|string|max:120|unique:blog_categories,category_slug,' . $id,
            'category_description' => 'nullable|string',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.unique' => 'Nama kategori sudah terdaftar.',
            'category_name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'category_slug.required' => 'Slug kategori wajib diisi.',
            'category_slug.unique' => 'Slug kategori sudah terdaftar.',
            'category_slug.max' => 'Slug kategori tidak boleh lebih dari 120 karakter.',
            'icon.mimes' => 'Ikon harus berupa file dengan format JPG, JPEG, atau PNG.',
            'icon.max' => 'Ukuran ikon tidak boleh lebih dari 4MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $category = BlogCategory::findOrFail($id);
            $oldData = $category->toArray();
            $input = $validated;

            if ($request->hasFile('icon')) {
                $input['icon'] = $this->imageService->handleImageUpload(
                    $request->file('icon'),
                    'upload/blog_categories',
                    $category->icon
                );
            } else {
                $input['icon'] = $category->icon;
            }

            $category->update($input);

            LogHelper::logAction(
                'blog_categories',
                $category->id,
                'Update',
                $oldData,
                $category->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Kategori blog berhasil diperbarui.',
                    'data' => $category
                ], 200);
            }

            return redirect()->route('blog-categories.index')->with('success', 'Kategori blog berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui kategori blog: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui kategori blog: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui kategori blog.',
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

            $category = BlogCategory::findOrFail($id);
            $oldData = $category->toArray();

            if ($category->icon) {
                $filePath = public_path('upload/blog_categories/' . $category->icon);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $category->delete();

            LogHelper::logAction(
                'blog_categories',
                $category->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Kategori blog berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('blog-categories.index')->with('success', 'Kategori blog berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus kategori blog: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus kategori blog.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus kategori blog.');
        }
    }
}