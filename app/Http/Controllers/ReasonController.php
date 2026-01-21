<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Reason;
use App\Services\ImageService;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:reason-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:reason-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:reason-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:reason-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Reason";
        $subtitle = "Menu Reason";
        $data_reason = Reason::all();
        return view('reasons.index', compact('data_reason', 'title', 'subtitle'));
    }


    public function create(): View {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:reasons,name',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description_1' => 'required|string',
            'description_2' => 'required|string',
            'description_3' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama reason wajib diisi.',
            'name.unique' => 'Nama reason sudah terdaftar.',
            'name.max' => 'Nama reason tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'icon.mimes' => 'Icon harus berupa file dengan format JPG, JPEG, atau PNG.',
            'icon.max' => 'Ukuran icon tidak boleh lebih dari 4MB.',
            'description_1.required' => 'Deskripsi 1 wajib diisi.',
            'description_2.required' => 'Deskripsi 2 wajib diisi.',
            'description_3.required' => 'Deskripsi 3 wajib diisi.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/reasons')
                : null;
            $input['icon'] = $request->hasFile('icon')
                ? $this->imageService->handleImageUpload($request->file('icon'), 'upload/reasons/icons')
                : null;

            $reason = Reason::create($input);

            // Log action using LogHelper
            LogHelper::logAction(
                'reasons',
                $reason->id,
                'Create',
                null,
                $reason->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Reason berhasil ditambahkan.',
                    'data' => $reason
                ], 200);
            }

            return redirect()->route('reason.index')->with('success', 'Reason berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan reason: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan reason.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $reason = Reason::findOrFail($id);
            return response()->json($reason, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data reason: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data reason.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $reason = Reason::findOrFail($id);
            return response()->json($reason, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data reason untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data reason.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:reasons,name,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'description_1' => 'required|string',
            'description_2' => 'required|string',
            'description_3' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama reason wajib diisi.',
            'name.unique' => 'Nama reason sudah terdaftar.',
            'name.max' => 'Nama reason tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'icon.mimes' => 'Icon harus berupa file dengan format JPG, JPEG, atau PNG.',
            'icon.max' => 'Ukuran icon tidak boleh lebih dari 4MB.',
            'description_1.required' => 'Deskripsi 1 wajib diisi.',
            'description_2.required' => 'Deskripsi 2 wajib diisi.',
            'description_3.required' => 'Deskripsi 3 wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $reason = Reason::findOrFail($id);
            $oldData = $reason->toArray();
            $input = $validated;

            // Handle image upload
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/reasons',
                    $reason->image
                );
            } else {
                $input['image'] = $reason->image;
            }

            // Handle icon upload
            if ($request->hasFile('icon')) {
                $input['icon'] = $this->imageService->handleImageUpload(
                    $request->file('icon'),
                    'upload/reasons/icons',
                    $reason->icon
                );
            } else {
                $input['icon'] = $reason->icon;
            }

            $reason->update($input);

            // Log action
            LogHelper::logAction(
                'reasons',
                $reason->id,
                'Update',
                $oldData,
                $reason->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Reason berhasil diperbarui.',
                    'data' => $reason
                ], 200);
            }

            return redirect()->route('reason.index')->with('success', 'Reason berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui reason: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui reason: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui reason.',
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

            $reason = Reason::findOrFail($id);
            $oldData = $reason->toArray();

            // Delete associated image if exists
            if ($reason->image) {
                $filePath = public_path('upload/reasons/' . $reason->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }


            if ($reason->icon) {
                $filePath = public_path('upload/reasons/icons' . $reason->icon);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }


            $reason->delete();

            // Log action
            LogHelper::logAction(
                'reasons',
                $reason->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Reason berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('reason.index')->with('success', 'Reason berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus reason: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus reason.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus reason.');
        }
    }
}
