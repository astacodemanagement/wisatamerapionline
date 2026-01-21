<?php

namespace App\Http\Controllers;

use App\Models\PostingJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class PostingJobController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:posting-job-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:posting-job-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:posting-job-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:posting-job-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Lowongan Pekerjaan";
        $subtitle = "Menu Lowongan Pekerjaan";
        $postingJobs = PostingJob::orderBy('order_display')->get();
        return view('posting_jobs.index', compact('postingJobs', 'title', 'subtitle'));
    }

    public function create(): View
    {
        $title = "Tambah Lowongan Pekerjaan";
        $subtitle = "Form Tambah Lowongan Pekerjaan";
        return view('posting_jobs.create', compact('title', 'subtitle'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:posting_jobs,name',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
                'deadline' => 'nullable|date|after_or_equal:today',
            ], [
                'name.required' => 'Nama lowongan wajib diisi.',
                'name.unique' => 'Nama lowongan sudah terdaftar.',
                'name.max' => 'Nama lowongan tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
                'deadline.date' => 'Tanggal deadline harus valid.',
                'deadline.after_or_equal' => 'Tanggal deadline tidak boleh sebelum hari ini.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/posting_jobs')
                : null;

            $postingJob = PostingJob::create($input);
            LogHelper::logAction('posting_jobs', $postingJob->id, 'Create', null, $postingJob->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil ditambahkan.',
                'data' => $postingJob
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan lowongan pekerjaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan lowongan pekerjaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $postingJob = PostingJob::findOrFail($id);
            return response()->json($postingJob, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data lowongan pekerjaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data lowongan pekerjaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $postingJob = PostingJob::findOrFail($id);
            return response()->json([
                'postingJob' => $postingJob
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data lowongan pekerjaan untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data lowongan pekerjaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $postingJob = PostingJob::findOrFail($id);
            $oldData = $postingJob->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:posting_jobs,name,' . $id,
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
                'deadline' => 'nullable|date|after_or_equal:today',
            ], [
                'name.required' => 'Nama lowongan wajib diisi.',
                'name.unique' => 'Nama lowongan sudah terdaftar.',
                'name.max' => 'Nama lowongan tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
                'deadline.date' => 'Tanggal deadline harus valid.',
                'deadline.after_or_equal' => 'Tanggal deadline tidak boleh sebelum hari ini.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/posting_jobs', $postingJob->image)
                : $postingJob->image;

            $postingJob->update($input);
            LogHelper::logAction('posting_jobs', $postingJob->id, 'Update', $oldData, $postingJob->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil diperbarui.',
                'data' => $postingJob
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui lowongan pekerjaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui lowongan pekerjaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $postingJob = PostingJob::findOrFail($id);
            $oldData = $postingJob->toArray();

            if ($postingJob->image) {
                $filePath = public_path('upload/posting_jobs/' . $postingJob->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $postingJob->delete();
            LogHelper::logAction('posting_jobs', $postingJob->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus lowongan pekerjaan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus lowongan pekerjaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}