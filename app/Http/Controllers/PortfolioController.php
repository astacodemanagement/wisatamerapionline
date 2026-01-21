<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\SubService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:portfolio-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:portfolio-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:portfolio-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:portfolio-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Portfolio";
        $subtitle = "Menu Portfolio";
        $portfolios = Portfolio::with(['sub_service.service'])->get();
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('portfolios.index', compact('portfolios', 'sub_services', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Portfolio";
        $subtitle = "Form Tambah Portfolio";
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('portfolioss.create', compact('sub_services', 'title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:portfolios,name',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'description_1' => 'required|string',
            'description_2' => 'required|string',
            'description_3' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'sub_service_id' => 'required|exists:sub_services,id',
        ], [
            'name.required' => 'Nama portfolio wajib diisi.',
            'name.unique' => 'Nama portfolio sudah terdaftar.',
            'name.max' => 'Nama portfolio tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'icon.mimes' => 'Ikon harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'icon.max' => 'Ukuran ikon tidak boleh lebih dari 4MB.',
            'description_1.required' => 'Deskripsi 1 wajib diisi.',
            'description_2.required' => 'Deskripsi 2 wajib diisi.',
            'description_3.required' => 'Deskripsi 3 wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'sub_service_id.required' => 'Sub layanan wajib dipilih.',
            'sub_service_id.exists' => 'Sub layanan tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/portfolios')
                : null;
            $input['icon'] = $request->hasFile('icon')
                ? $this->imageService->handleImageUpload($request->file('icon'), 'upload/portfolios/icons')
                : null;

            $portfolio = Portfolio::create($input);
            LogHelper::logAction('portfolios', $portfolio->id, 'Create', null, $portfolio->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Portfolio berhasil ditambahkan.',
                    'data' => $portfolio
                ], 200);
            }

            return redirect()->route('portfolios.index')->with('success', 'Portfolio berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan portfolio: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan portfolio.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $portfolio = Portfolio::with(['sub_service.service'])->findOrFail($id);
            return response()->json($portfolio, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data portfolio: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data portfolio.',
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
            $portfolio = Portfolio::findOrFail($id);
            $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
            return response()->json([
                'portfolio' => $portfolio,
                'sub_services' => $sub_services
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data portfolio untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data portfolio.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:portfolios,name,' . $id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4048',
            'description_1' => 'required|string',
            'description_2' => 'required|string',
            'description_3' => 'required|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'sub_service_id' => 'required|exists:sub_services,id',
        ], [
            'name.required' => 'Nama portfolio wajib diisi.',
            'name.unique' => 'Nama portfolio sudah terdaftar.',
            'name.max' => 'Nama portfolio tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'icon.mimes' => 'Ikon harus berupa file dengan format JPG, JPEG, PNG, atau WEBP.',
            'icon.max' => 'Ukuran ikon tidak boleh lebih dari 4MB.',
            'description_1.required' => 'Deskripsi 1 wajib diisi.',
            'description_2.required' => 'Deskripsi 2 wajib diisi.',
            'description_3.required' => 'Deskripsi 3 wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "nonactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'sub_service_id.required' => 'Sub layanan wajib dipilih.',
            'sub_service_id.exists' => 'Sub layanan tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $portfolio = Portfolio::findOrFail($id);
            $oldData = $portfolio->toArray();
            $input = $validated;

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/portfolios',
                    $portfolio->image
                );
            } else {
                $input['image'] = $portfolio->image;
            }

            if ($request->hasFile('icon')) {
                $input['icon'] = $this->imageService->handleImageUpload(
                    $request->file('icon'),
                    'upload/portfolios/icons',
                    $portfolio->icon
                );
            } else {
                $input['icon'] = $portfolio->icon;
            }

            $portfolio->update($input);
            LogHelper::logAction('portfolios', $portfolio->id, 'Update', $oldData, $portfolio->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Portfolio berhasil diperbarui.',
                    'data' => $portfolio
                ], 200);
            }

            return redirect()->route('portfolios.index')->with('success', 'Portfolio berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui portfolio: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui portfolio: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui portfolio.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $portfolio = Portfolio::findOrFail($id);
            $oldData = $portfolio->toArray();

            if ($portfolio->image) {
                $filePath = public_path('upload/portfolios/' . $portfolio->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            if ($portfolio->icon) {
                $filePath = public_path('upload/portfolios/icons/' . $portfolio->icon);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $portfolio->delete();
            LogHelper::logAction('portfolios', $portfolio->id, 'Delete', $oldData, null);

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Portfolio berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('portfolios.index')->with('success', 'Portfolio berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus portfolio: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus portfolio.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus portfolio.');
        }
    }
}