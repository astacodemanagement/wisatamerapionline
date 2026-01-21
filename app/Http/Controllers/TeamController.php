<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class TeamController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:team-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:team-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:team-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:team-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Tim";
        $subtitle = "Menu Tim";
        $teams = Team::orderBy('order_display')->get();
        return view('teams.index', compact('teams', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Anggota Tim";
        $subtitle = "Form Tambah Anggota Tim";
        return view('teams.create', compact('title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:teams,name',
                'position' => 'required|string|max:255',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama anggota tim wajib diisi.',
                'name.unique' => 'Nama anggota tim sudah terdaftar.',
                'name.max' => 'Nama anggota tim tidak boleh lebih dari 255 karakter.',
                'position.required' => 'Posisi wajib diisi.',
                'position.max' => 'Posisi tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/teams')
                : null;

            $team = Team::create($input);
            LogHelper::logAction('teams', $team->id, 'Create', null, $team->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Anggota tim berhasil ditambahkan.',
                'data' => $team
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan anggota tim: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan anggota tim.',
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
            $team = Team::findOrFail($id);
            return response()->json($team, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data anggota tim: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data anggota tim.',
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
            $team = Team::findOrFail($id);
            return response()->json([
                'team' => $team
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data anggota tim untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data anggota tim.',
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
            $team = Team::findOrFail($id);
            $oldData = $team->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:teams,name,' . $id,
                'position' => 'required|string|max:255',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama anggota tim wajib diisi.',
                'name.unique' => 'Nama anggota tim sudah terdaftar.',
                'name.max' => 'Nama anggota tim tidak boleh lebih dari 255 karakter.',
                'position.required' => 'Posisi wajib diisi.',
                'position.max' => 'Posisi tidak boleh lebih dari 255 karakter.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/teams', $team->image)
                : $team->image;

            $team->update($input);
            LogHelper::logAction('teams', $team->id, 'Update', $oldData, $team->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Anggota tim berhasil diperbarui.',
                'data' => $team
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui anggota tim: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui anggota tim.',
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
            $team = Team::findOrFail($id);
            $oldData = $team->toArray();

            if ($team->image) {
                $filePath = public_path('upload/teams/' . $team->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $team->delete();
            LogHelper::logAction('teams', $team->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Anggota tim berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus anggota tim: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus anggota tim.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}