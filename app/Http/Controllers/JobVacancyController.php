<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\JobVacancy;
use App\Services\ImageService;
use Illuminate\Support\Str;

class JobVacancyController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
       $this->middleware('permission:job_vacancy-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:job_vacancy-create', ['only' => ['store']]);
        $this->middleware('permission:job_vacancy-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:job_vacancy-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = "Halaman Lowongan Kerja";
        $subtitle = "Menu Lowongan Kerja";
        $data_vacancy = JobVacancy::orderBy('order_display', 'asc')->get();
        return view('job_vacancies.index', compact('data_vacancy', 'title', 'subtitle'));
    }

    public function create(): View
    {
 
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_vacancies,name',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama lowongan wajib diisi.',
            'name.unique' => 'Nama lowongan sudah terdaftar.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'description.required' => 'Deskripsi wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['slug'] = Str::slug($input['name']);

            // Handle image upload
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/job_vacancies')
                : null;

            $vacancy = JobVacancy::create($input);

            LogHelper::logAction(
                'job_vacancies',
                $vacancy->id,
                'Create',
                null,
                $vacancy->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Lowongan kerja berhasil ditambahkan.',
                    'data' => $vacancy
                ], 200);
            }

            return redirect()->route('job_vacancies.index')->with('success', 'Lowongan kerja berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan lowongan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan lowongan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $vacancy = JobVacancy::findOrFail($id);
            return response()->json($vacancy, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data lowongan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data lowongan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $vacancy = JobVacancy::findOrFail($id);
            return response()->json($vacancy, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data lowongan untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data lowongan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_vacancies,name,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama lowongan wajib diisi.',
            'name.unique' => 'Nama lowongan sudah terdaftar.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'description.required' => 'Deskripsi wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $vacancy = JobVacancy::findOrFail($id);
            $oldData = $vacancy->toArray();
            $input = $validated;
            $input['slug'] = Str::slug($input['name']);

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/job_vacancies',
                    $vacancy->image
                );
            } else {
                $input['image'] = $vacancy->image;
            }

            $vacancy->update($input);

            LogHelper::logAction(
                'job_vacancies',
                $vacancy->id,
                'Update',
                $oldData,
                $vacancy->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Lowongan kerja berhasil diperbarui.',
                    'data' => $vacancy
                ], 200);
            }

            return redirect()->route('job_vacancies.index')->with('success', 'Lowongan kerja berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui lowongan: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui lowongan: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui lowongan.',
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

            $vacancy = JobVacancy::findOrFail($id);
            $oldData = $vacancy->toArray();

            if ($vacancy->image) {
                $filePath = public_path('upload/job_vacancies/' . $vacancy->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $vacancy->delete();

            LogHelper::logAction(
                'job_vacancies',
                $vacancy->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Lowongan kerja berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('job_vacancies.index')->with('success', 'Lowongan kerja berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus lowongan: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus lowongan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus lowongan.');
        }
    }
}