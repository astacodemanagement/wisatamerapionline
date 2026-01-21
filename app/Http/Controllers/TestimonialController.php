<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Testimonial;
use App\Services\ImageService;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:testimonial-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:testimonial-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:testimonial-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:testimonial-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Testimonial";
        $subtitle = "Menu Testimonial";
        $data_testimonial = Testimonial::all();
        return view('testimonials.index', compact('data_testimonial', 'title', 'subtitle'));
    }


    public function create(): View {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:testimonies,name',
            'email' => 'required|email|max:255|unique:testimonies,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama testimonial wajib diisi.',
            'name.unique' => 'Nama testimonial sudah terdaftar.',
            'name.max' => 'Nama testimonial tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/testimonies')
                : null;
            

            $testimonial = Testimonial::create($input);

            // Log action using LogHelper
            LogHelper::logAction(
                'testimonial',
                $testimonial->id,
                'Create',
                null,
                $testimonial->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Testimonial berhasil ditambahkan.',
                    'data' => $testimonial
                ], 200);
            }

            return redirect()->route('testimonial.index')->with('success', 'Testimonial berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan testimonial: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan testimonial.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            return response()->json($testimonial, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data testimonial: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data testimonial.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            return response()->json($testimonial, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data testimonial untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data testimonial.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:testimonies,name,' . $id,
            'email' => 'required|email|max:255|unique:testimonies,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama testimonial wajib diisi.',
            'name.unique' => 'Nama testimonial sudah terdaftar.',
            'name.max' => 'Nama testimonial tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

       try {
        DB::beginTransaction();

        $testimonial = Testimonial::findOrFail($id);
        $oldData = $testimonial->toArray();
        $input = $validated;

        // Handle image upload
        if ($request->hasFile('image')) {
            $input['image'] = $this->imageService->handleImageUpload( // Perbaikan di sini
                $request->file('image'),
                'upload/testimonies',
                $testimonial->image
            );
        } else {
            $input['image'] = $testimonial->image;
        }

        $testimonial->update($input);

            // Log action
            LogHelper::logAction(
                'testimonial',
                $testimonial->id,
                'Update',
                $oldData,
                $testimonial->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Testimonial berhasil diperbarui.',
                    'data' => $testimonial
                ], 200);
            }

            return redirect()->route('testimonial.index')->with('success', 'Testimonial berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui testimonial: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui testimonial: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui testimonial.',
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

            $testimonial = Testimonial::findOrFail($id);
            $oldData = $testimonial->toArray();

             // Delete associated image if exists
            if ($testimonial->image) {
                $filePath = public_path('upload/testimonies/' . $testimonial->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $testimonial->delete();

            // Log action
            LogHelper::logAction(
                'testimonial',
                $testimonial->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Testimonial berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('testimonial.index')->with('success', 'Testimonial berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus testimonial: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus testimonial.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus testimonial.');
        }
    }
}
