<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:newsletter-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:newsletter-create', ['only' => ['store']]);
        $this->middleware('permission:newsletter-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:newsletter-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = 'Halaman Newsletter';
        $subtitle = 'Menu Newsletter';
        $newsletters = Newsletter::all();
        return view('newsletters.index', compact('newsletters', 'title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|max:255|unique:newsletters,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/newsletters');
            }

            $newsletter = Newsletter::create($input);

            LogHelper::logAction('newsletters', $newsletter->id, 'Create', null, $newsletter->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Newsletter berhasil ditambahkan.', 'data' => $newsletter], 200);
            }
            return redirect()->route('newsletters.index')->with('success', 'Newsletter berhasil ditambahkan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan newsletters: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal menambahkan newsletters.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $newsletter = Newsletter::findOrFail($id);
            return response()->json($newsletter, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data newsletters: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data newsletters.', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $newsletter = Newsletter::findOrFail($id);
            return response()->json($newsletter, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data newsletters untuk edit: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data newsletters.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'email' => 'required|string|max:255|unique:newsletters,email,' . $id,
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        try {
            DB::beginTransaction();

            $newsletter = Newsletter::findOrFail($id);
            $oldData = $newsletter->toArray();
            $input = $validated;
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/newsletters', $newsletter->image);
            } else {
                $input['image'] = $newsletter->image;
            }

            $newsletter->update($input);

            LogHelper::logAction('newsletters', $newsletter->id, 'Update', $oldData, $newsletter->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Newsletter berhasil diperbarui.', 'data' => $newsletter], 200);
            }
            return redirect()->route('newsletters.index')->with('success', 'Newsletter berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui newsletters: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui newsletters: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal memperbarui newsletters.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $newsletter = Newsletter::findOrFail($id);
            $oldData = $newsletter->toArray();
            if ($newsletter->image) {
                $filePath = public_path('upload/newsletters/' . $newsletter->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $newsletter->delete();

            LogHelper::logAction('newsletters', $newsletter->id, 'Delete', $oldData, null);

            DB::commit();

            if (request()->ajax()) {
                return response()->json(['message' => 'Newsletter berhasil dihapus.'], 200);
            }
            return redirect()->route('newsletters.index')->with('success', 'Newsletter berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus newsletters: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['message' => 'Gagal menghapus newsletters.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus newsletters.');
        }
    }
}
