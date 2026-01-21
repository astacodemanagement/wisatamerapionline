<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class ProjectCategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:project-category-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:project-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:project-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:project-category-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Kategori Proyek";
        $subtitle = "Menu Kategori Proyek";
        $categories = ProjectCategory::all();
        return view('project_categories.index', compact('categories', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Kategori Proyek";
        $subtitle = "Form Tambah Kategori Proyek";
        return view('project_categories.create', compact('title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama kategori wajib diisi.',
                'name.string' => 'Nama kategori harus berupa teks.',
                'name.max' => 'Nama kategori tidak boleh lebih dari 255 karakter.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'status.required' => 'Status wajib diisi.',
                'status.in' => 'Status harus "active" atau "nonactive".',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik
            $existingCategory = ProjectCategory::where('slug', $input['slug'])->first();
            if ($existingCategory) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/project_categories')
                : null;

            $category = ProjectCategory::create($input);
            LogHelper::logAction('project_categories', $category->id, 'Create', null, $category->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Kategori proyek berhasil ditambahkan.',
                'data' => $category
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan kategori proyek: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan kategori proyek.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $category = ProjectCategory::findOrFail($id);
            return response()->json($category, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kategori proyek: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori proyek.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $category = ProjectCategory::findOrFail($id);
            return response()->json([
                'category' => $category
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kategori proyek untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kategori proyek.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $category = ProjectCategory::findOrFail($id);
            $oldData = $category->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama kategori wajib diisi.',
                'name.string' => 'Nama kategori harus berupa teks.',
                'name.max' => 'Nama kategori tidak boleh lebih dari 255 karakter.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'status.required' => 'Status wajib diisi.',
                'status.in' => 'Status harus "active" atau "nonactive".',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik, kecuali untuk kategori ini sendiri
            $existingCategory = ProjectCategory::where('slug', $input['slug'])->where('id', '!=', $id)->first();
            if ($existingCategory) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/project_categories', $category->image)
                : $category->image;

            $category->update($input);
            LogHelper::logAction('project_categories', $category->id, 'Update', $oldData, $category->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Kategori proyek berhasil diperbarui.',
                'data' => $category
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui kategori proyek: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui kategori proyek.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $category = ProjectCategory::findOrFail($id);
            $oldData = $category->toArray();

            if ($category->image) {
                $filePath = public_path('upload/project_categories/' . $category->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $category->delete();
            LogHelper::logAction('project_categories', $category->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Kategori proyek berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus kategori proyek: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus kategori proyek.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}