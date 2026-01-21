<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class PrivacyPolicyController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:privacy-policy-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:privacy-policy-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:privacy-policy-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:privacy-policy-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Kebijakan Privasi";
        $subtitle = "Menu Kebijakan Privasi";
        $privacyPolicies = PrivacyPolicy::orderBy('order_display')->get();
        return view('privacy_policies.index', compact('privacyPolicies', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Kebijakan Privasi";
        $subtitle = "Form Tambah Kebijakan Privasi";
        return view('privacy_policies.create', compact('title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:privacy_policies,name',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama kebijakan wajib diisi.',
                'name.unique' => 'Nama kebijakan sudah terdaftar.',
                'name.max' => 'Nama kebijakan tidak boleh lebih dari 255 karakter.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/privacy_policies')
                : null;

            $privacyPolicy = PrivacyPolicy::create($input);
            LogHelper::logAction('privacy_policies', $privacyPolicy->id, 'Create', null, $privacyPolicy->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Kebijakan privasi berhasil ditambahkan.',
                'data' => $privacyPolicy
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan kebijakan privasi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan kebijakan privasi.',
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
            $privacyPolicy = PrivacyPolicy::findOrFail($id);
            return response()->json($privacyPolicy, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kebijakan privasi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kebijakan privasi.',
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
            $privacyPolicy = PrivacyPolicy::findOrFail($id);
            return response()->json([
                'privacyPolicy' => $privacyPolicy
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data kebijakan privasi untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data kebijakan privasi.',
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
            $privacyPolicy = PrivacyPolicy::findOrFail($id);
            $oldData = $privacyPolicy->toArray();

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:privacy_policies,name,' . $id,
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'description' => 'nullable|string',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'name.required' => 'Nama kebijakan wajib diisi.',
                'name.unique' => 'Nama kebijakan sudah terdaftar.',
                'name.max' => 'Nama kebijakan tidak boleh lebih dari 255 karakter.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/privacy_policies', $privacyPolicy->image)
                : $privacyPolicy->image;

            $privacyPolicy->update($input);
            LogHelper::logAction('privacy_policies', $privacyPolicy->id, 'Update', $oldData, $privacyPolicy->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Kebijakan privasi berhasil diperbarui.',
                'data' => $privacyPolicy
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui kebijakan privasi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui kebijakan privasi.',
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
            $privacyPolicy = PrivacyPolicy::findOrFail($id);
            $oldData = $privacyPolicy->toArray();

            if ($privacyPolicy->image) {
                $filePath = public_path('upload/privacy_policies/' . $privacyPolicy->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $privacyPolicy->delete();
            LogHelper::logAction('privacy_policies', $privacyPolicy->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Kebijakan privasi berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus kebijakan privasi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus kebijakan privasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}