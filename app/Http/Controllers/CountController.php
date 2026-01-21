<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Count;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class CountController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:count-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:count-create', ['only' => ['store']]);
        $this->middleware('permission:count-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:count-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $title = 'Halaman Count';
        $subtitle = 'Menu Count';
        $counts = Count::all();
        return view('counts.index', compact('counts', 'title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|string|max:255',
            'description' => 'required|string',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Name wajib diisi.',
            'name.max' => 'Name tidak boleh lebih dari 255 karakter.',
            'amount.required' => 'Amount wajib diisi.',
            'amount.max' => 'Amount tidak boleh lebih dari 255 karakter.',
            'description.required' => 'Description wajib diisi.',
            'order_display.required' => 'OrderDisplay wajib diisi.',
            'order_display.integer' => 'OrderDisplay harus berupa angka.',
            'order_display.min' => 'OrderDisplay tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $input = $validated;
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/counts');
            }

            $count = Count::create($input);

            LogHelper::logAction('counts', $count->id, 'Create', null, $count->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Count berhasil ditambahkan.', 'data' => $count], 200);
            }
            return redirect()->route('counts.index')->with('success', 'Count berhasil ditambahkan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan counts: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal menambahkan counts.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $count = Count::findOrFail($id);
            return response()->json($count, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data counts: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data counts.', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $count = Count::findOrFail($id);
            return response()->json($count, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data counts untuk edit: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data counts.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|string|max:255',
            'description' => 'required|string',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Name wajib diisi.',
            'name.max' => 'Name tidak boleh lebih dari 255 karakter.',
            'amount.required' => 'Amount wajib diisi.',
            'amount.max' => 'Amount tidak boleh lebih dari 255 karakter.',
            'description.required' => 'Description wajib diisi.',
            'order_display.required' => 'OrderDisplay wajib diisi.',
            'order_display.integer' => 'OrderDisplay harus berupa angka.',
            'order_display.min' => 'OrderDisplay tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $count = Count::findOrFail($id);
            $oldData = $count->toArray();
            $input = $validated;
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload($request->file('image'), 'upload/counts', $count->image);
            } else {
                $input['image'] = $count->image;
            }

            $count->update($input);

            LogHelper::logAction('counts', $count->id, 'Update', $oldData, $count->toArray());

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Count berhasil diperbarui.', 'data' => $count], 200);
            }
            return redirect()->route('counts.index')->with('success', 'Count berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui counts: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui counts: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Gagal memperbarui counts.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $count = Count::findOrFail($id);
            $oldData = $count->toArray();
            if ($count->image) {
                $filePath = public_path('upload/counts/' . $count->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $count->delete();

            LogHelper::logAction('counts', $count->id, 'Delete', $oldData, null);

            DB::commit();

            if (request()->ajax()) {
                return response()->json(['message' => 'Count berhasil dihapus.'], 200);
            }
            return redirect()->route('counts.index')->with('success', 'Count berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus counts: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['message' => 'Gagal menghapus counts.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus counts.');
        }
    }
}
