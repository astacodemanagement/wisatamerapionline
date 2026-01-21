<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Faq;
use App\Services\ImageService;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:faq-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:faq-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:faq-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:faq-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Faq";
        $subtitle = "Menu Faq";
        $data_faq = Faq::all();
        return view('faqs.index', compact('data_faq', 'title', 'subtitle'));
    }


    public function create(): View {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255|unique:faqs,question',
            'answer' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.string' => 'Pertanyaan harus berupa teks.',
            'question.max' => 'Pertanyaan tidak boleh lebih dari 255 karakter.',
            'question.unique' => 'Pertanyaan sudah terdaftar.',
            'answer.required' => 'Jawaban wajib diisi.',
            'answer.string' => 'Jawaban harus berupa teks.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4 MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "Aktif" atau "Nonaktif".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;



            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/faqs')
                : null;


            $faq = Faq::create($input);

            // Log action using LogHelper
            LogHelper::logAction(
                'faqs',
                $faq->id,
                'Create',
                null,
                $faq->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Faq berhasil ditambahkan.',
                    'data' => $faq
                ], 200);
            }

            return redirect()->route('faq.index')->with('success', 'Faq berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan faq: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan faq.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            return response()->json($faq, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data faq: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data faq.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            return response()->json($faq, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data faq untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data faq.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
                'question' => 'required|string|max:255|unique:faqs,question,' . $id,
                'answer' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'question.required' => 'Pertanyaan wajib diisi.',
                'question.string' => 'Pertanyaan harus berupa teks.',
                'question.max' => 'Pertanyaan tidak boleh lebih dari 255 karakter.',
                'question.unique' => 'Pertanyaan sudah terdaftar.',
                'answer.required' => 'Jawaban wajib diisi.',
                'answer.string' => 'Jawaban harus berupa teks.',
                'image.image' => 'File harus berupa gambar.',
                'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4 MB.',
                'status.required' => 'Status wajib diisi.',
                'status.in' => 'Status harus "Aktif" atau "Nonaktif".',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

        try {
            DB::beginTransaction();

            $faq = Faq::findOrFail($id);
            $oldData = $faq->toArray();
            $input = $validated;

            // Handle image upload
            if ($request->hasFile('image')) {
                $input['image'] = $this->handleImageUpload(
                    $request->file('image'),
                    'upload/faqs',
                    $faq->image
                );
            } else {
                $input['image'] = $faq->image;
            }



            $faq->update($input);

            // Log action
            LogHelper::logAction(
                'faqs',
                $faq->id,
                'Update',
                $oldData,
                $faq->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Faq berhasil diperbarui.',
                    'data' => $faq
                ], 200);
            }

            return redirect()->route('faq.index')->with('success', 'Faq berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui faq: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui faq: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui faq.',
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

            $faq = Faq::findOrFail($id);
            $oldData = $faq->toArray();

            // Delete associated image if exists
            if ($faq->image) {
                $filePath = public_path('upload/faqs/' . $faq->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $faq->delete();

            // Log action
            LogHelper::logAction(
                'faqs',
                $faq->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Faq berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('faq.index')->with('success', 'Faq berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus faq: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus faq.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus faq.');
        }
    }
}
