<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\LogHelper;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Services\ImageService;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:project-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:project-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:project-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:project-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Project";
        $subtitle = "Menu Project";
        $data_project = Project::all();
        $data_categories = ProjectCategory::all();
        return view('projects.index', compact('data_project', 'data_categories', 'title', 'subtitle'));
    }


    public function create(): View {}

   public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'project_category_id' => 'nullable|exists:project_categories,id',
        ], [
            'name.required' => 'Nama proyek wajib diisi.',
            'name.string' => 'Nama proyek harus berupa teks.',
            'name.max' => 'Nama proyek tidak boleh lebih dari 255 karakter.',
            'location.string' => 'Lokasi harus berupa teks.',
            'location.max' => 'Lokasi tidak boleh lebih dari 255 karakter.',
            'start_date.date' => 'Tanggal mulai harus berupa format tanggal yang valid.',
            'end_date.date' => 'Tanggal selesai harus berupa format tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "Aktif" atau "Nonaktif".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4 MB.',
            'project_category_id.exists' => 'Kategori proyek tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik
            $existingProject = Project::where('slug', $input['slug'])->first();
            if ($existingProject) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/projects')
                : null;

            $project = Project::create($input);

            LogHelper::logAction(
                'projects',
                $project->id,
                'Create',
                null,
                $project->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Project berhasil ditambahkan.',
                    'data' => $project
                ], 200);
            }

            return redirect()->route('project.index')->with('success', 'Project berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan project: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan project.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

   



    public function show($id)
    {
        try {
            $project = Project::findOrFail($id);
            return response()->json($project, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data project: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data project.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $project = Project::findOrFail($id);
            return response()->json($project, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data project untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data project.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'project_category_id' => 'nullable|exists:project_categories,id',
        ], [
            'name.required' => 'Nama proyek wajib diisi.',
            'name.string' => 'Nama proyek harus berupa teks.',
            'name.max' => 'Nama proyek tidak boleh lebih dari 255 karakter.',
            'location.string' => 'Lokasi harus berupa teks.',
            'location.max' => 'Lokasi tidak boleh lebih dari 255 karakter.',
            'start_date.date' => 'Tanggal mulai harus berupa format tanggal yang valid.',
            'end_date.date' => 'Tanggal selesai harus berupa format tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "Aktif" atau "Nonaktif".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4 MB.',
            'project_category_id.exists' => 'Kategori proyek tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($id);
            $oldData = $project->toArray();
            $input = $validated;
            $input['slug'] = Str::slug($validated['name']); // Generate slug from name

            // Validasi slug unik, kecuali untuk proyek ini sendiri
            $existingProject = Project::where('slug', $input['slug'])->where('id', '!=', $id)->first();
            if ($existingProject) {
                throw ValidationException::withMessages([
                    'name' => 'Nama ini menghasilkan slug yang sudah digunakan. Gunakan nama lain.',
                ]);
            }

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/projects',
                    $project->image
                );
            } else {
                $input['image'] = $project->image;
            }

            $project->update($input);

            LogHelper::logAction(
                'projects',
                $project->id,
                'Update',
                $oldData,
                $project->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Project berhasil diperbarui.',
                    'data' => $project
                ], 200);
            }

            return redirect()->route('project.index')->with('success', 'Project berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui project: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui project: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui project.',
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

            $project = Project::findOrFail($id);
            $oldData = $project->toArray();

            // Delete associated image if exists
            if ($project->image) {
                $filePath = public_path('upload/projects/' . $project->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $project->delete();

            // Log action
            LogHelper::logAction(
                'projects',
                $project->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Project berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('project.index')->with('success', 'Project berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus project: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus project.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus project.');
        }
    }
}
