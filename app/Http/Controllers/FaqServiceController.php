<?php

namespace App\Http\Controllers;

use App\Models\FaqService;
use App\Models\SubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;
use App\Services\ImageService;

class FaqServiceController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:faq-service-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:faq-service-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:faq-service-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:faq-service-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman FAQ Service";
        $subtitle = "Menu FAQ Service";
        $faq_services = FaqService::with(['sub_service.service'])->get();
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('faq_services.index', compact('faq_services', 'sub_services', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah FAQ Service";
        $subtitle = "Form Tambah FAQ Service";
        $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('faq_services.create', compact('sub_services', 'title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'question' => 'required|string|max:255|unique:faq_services,question',
                'answer' => 'required|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'question.required' => 'Pertanyaan wajib diisi.',
                'question.unique' => 'Pertanyaan sudah terdaftar.',
                'question.max' => 'Pertanyaan tidak boleh lebih dari 255 karakter.',
                'answer.required' => 'Jawaban wajib diisi.',
                'answer.string' => 'Jawaban harus berupa teks.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/faq_services')
                : null;

            $faq_service = FaqService::create($input);
            LogHelper::logAction('faq_services', $faq_service->id, 'Create', null, $faq_service->toArray());
            DB::commit();

            return response()->json([
                'message' => 'FAQ Service berhasil ditambahkan.',
                'data' => $faq_service
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan FAQ Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan FAQ Service.',
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
            $faq_service = FaqService::with(['sub_service.service'])->findOrFail($id);
            return response()->json($faq_service, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data FAQ Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data FAQ Service.',
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
            $faq_service = FaqService::findOrFail($id);
            $sub_services = SubService::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
            return response()->json([
                'faq_service' => $faq_service,
                'sub_services' => $sub_services
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data FAQ Service untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data FAQ Service.',
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
            $faq_service = FaqService::findOrFail($id);
            $oldData = $faq_service->toArray();

            $validated = $request->validate([
                'question' => 'required|string|max:255|unique:faq_services,question,' . $id,
                'answer' => 'required|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'sub_service_id' => 'required|exists:sub_services,id',
            ], [
                'question.required' => 'Pertanyaan wajib diisi.',
                'question.unique' => 'Pertanyaan sudah terdaftar.',
                'question.max' => 'Pertanyaan tidak boleh lebih dari 255 karakter.',
                'answer.required' => 'Jawaban wajib diisi.',
                'answer.string' => 'Jawaban harus berupa teks.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
                'sub_service_id.required' => 'Sub layanan wajib dipilih.',
                'sub_service_id.exists' => 'Sub layanan tidak valid.',
            ]);

            $input = $validated;
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/faq_services', $faq_service->image)
                : $faq_service->image;

            $faq_service->update($input);
            LogHelper::logAction('faq_services', $faq_service->id, 'Update', $oldData, $faq_service->toArray());
            DB::commit();

            return response()->json([
                'message' => 'FAQ Service berhasil diperbarui.',
                'data' => $faq_service
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui FAQ Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui FAQ Service.',
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
            $faq_service = FaqService::findOrFail($id);
            $oldData = $faq_service->toArray();

            if ($faq_service->image) {
                $filePath = public_path('upload/faq_services/' . $faq_service->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $faq_service->delete();
            LogHelper::logAction('faq_services', $faq_service->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'FAQ Service berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus FAQ Service: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus FAQ Service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}