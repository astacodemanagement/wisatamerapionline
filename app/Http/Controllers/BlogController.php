<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\ImageService;

class BlogController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:blog-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:blog-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:blog-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:blog-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Blog";
        $subtitle = "Menu Blog";
        $data_blog = Blog::with('category')->get();
        return view('blogs.index', compact('data_blog', 'title', 'subtitle'));
    }

    public function create(): View
    {
        
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'headline' => 'required|string|max:255',
            'news_slug' => 'required|string|max:300|unique:blogs,news_slug',
            'body' => 'required|string',
            'resume' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'author' => 'nullable|string|max:100',
            'publish_date' => 'nullable|date',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'views' => 'nullable|integer|min:0',
        ], [
            'headline.required' => 'Judul wajib diisi.',
            'headline.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'news_slug.required' => 'Slug wajib diisi.',
            'news_slug.unique' => 'Slug sudah terdaftar.',
            'news_slug.max' => 'Slug tidak boleh lebih dari 300 karakter.',
            'body.required' => 'Konten wajib diisi.',
            'category_id.exists' => 'Kategori tidak valid.',
            'thumbnail.mimes' => 'Thumbnail harus berupa file dengan format JPG, JPEG, atau PNG.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari 4MB.',
            'author.max' => 'Nama penulis tidak boleh lebih dari 100 karakter.',
            'publish_date.date' => 'Tanggal publikasi tidak valid.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'views.integer' => 'Jumlah tampilan harus berupa angka.',
            'views.min' => 'Jumlah tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['thumbnail'] = $request->hasFile('thumbnail')
                ? $this->imageService->handleImageUpload($request->file('thumbnail'), 'upload/blogs')
                : null;

            $blog = Blog::create($input);

            LogHelper::logAction(
                'blogs',
                $blog->id,
                'Create',
                null,
                $blog->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Blog berhasil ditambahkan.',
                    'data' => $blog
                ], 200);
            }

            return redirect()->route('blogs.index')->with('success', 'Blog berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan blog: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan blog.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $blog = Blog::with('category')->findOrFail($id);
            return response()->json($blog, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data blog: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $blog = Blog::with('category')->findOrFail($id);
            return response()->json($blog, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data blog untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'headline' => 'required|string|max:255',
            'news_slug' => 'required|string|max:300|unique:blogs,news_slug,' . $id,
            'body' => 'required|string',
            'resume' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'author' => 'nullable|string|max:100',
            'publish_date' => 'nullable|date',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'views' => 'nullable|integer|min:0',
        ], [
            'headline.required' => 'Judul wajib diisi.',
            'headline.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'news_slug.required' => 'Slug wajib diisi.',
            'news_slug.unique' => 'Slug sudah terdaftar.',
            'news_slug.max' => 'Slug tidak boleh lebih dari 300 karakter.',
            'body.required' => 'Konten wajib diisi.',
            'category_id.exists' => 'Kategori tidak valid.',
            'thumbnail.mimes' => 'Thumbnail harus berupa file dengan format JPG, JPEG, atau PNG.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari 4MB.',
            'author.max' => 'Nama penulis tidak boleh lebih dari 100 karakter.',
            'publish_date.date' => 'Tanggal publikasi tidak valid.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'views.integer' => 'Jumlah tampilan harus berupa angka.',
            'views.min' => 'Jumlah tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $blog = Blog::findOrFail($id);
            $oldData = $blog->toArray();
            $input = $validated;

            if ($request->hasFile('thumbnail')) {
                $input['thumbnail'] = $this->imageService->handleImageUpload(
                    $request->file('thumbnail'),
                    'upload/blogs',
                    $blog->thumbnail
                );
            } else {
                $input['thumbnail'] = $blog->thumbnail;
            }

            $blog->update($input);

            LogHelper::logAction(
                'blogs',
                $blog->id,
                'Update',
                $oldData,
                $blog->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Blog berhasil diperbarui.',
                    'data' => $blog
                ], 200);
            }

            return redirect()->route('blogs.index')->with('success', 'Blog berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui blog: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui blog: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui blog.',
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

            $blog = Blog::findOrFail($id);
            $oldData = $blog->toArray();

            if ($blog->thumbnail) {
                $filePath = public_path('upload/blogs/' . $blog->thumbnail);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $blog->delete();

            LogHelper::logAction(
                'blogs',
                $blog->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Blog berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('blogs.index')->with('success', 'Blog berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus blog: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus blog.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus blog.');
        }
    }
}