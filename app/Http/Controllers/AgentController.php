<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Agent;
use App\Services\ImageService;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:agent-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:agent-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:agent-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:agent-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Agent";
        $subtitle = "Menu Agent";
        $data_agent = Agent::all();
        return view('agents.index', compact('data_agent', 'title', 'subtitle'));
    }


    public function create(): View {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:agents,name',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama agent wajib diisi.',
            'name.unique' => 'Nama agent sudah terdaftar.',
            'name.max' => 'Nama agent tidak boleh lebih dari 255 karakter.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
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
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/agents')
                : null;
            

            $agent = Agent::create($input);

            // Log action using LogHelper
            LogHelper::logAction(
                'agents',
                $agent->id,
                'Create',
                null,
                $agent->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Agent berhasil ditambahkan.',
                    'data' => $agent
                ], 200);
            }

            return redirect()->route('agent.index')->with('success', 'Agent berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan agent: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan agent.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $agent = Agent::findOrFail($id);
            return response()->json($agent, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data agent: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data agent.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $agent = Agent::findOrFail($id);
            return response()->json($agent, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data agent untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data agent.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:agents,name,' . $id,
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama agent wajib diisi.',
            'name.unique' => 'Nama agent sudah terdaftar.',
            'name.max' => 'Nama agent tidak boleh lebih dari 255 karakter.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
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

            $agent = Agent::findOrFail($id);
            $oldData = $agent->toArray();
            $input = $validated;

            // Handle image upload
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/agents',
                    $agent->image
                );
            } else {
                $input['image'] = $agent->image;
            }

            

            $agent->update($input);

            // Log action
            LogHelper::logAction(
                'agents',
                $agent->id,
                'Update',
                $oldData,
                $agent->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Agent berhasil diperbarui.',
                    'data' => $agent
                ], 200);
            }

            return redirect()->route('agent.index')->with('success', 'Agent berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui agent: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui agent: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui agent.',
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

            $agent = Agent::findOrFail($id);
            $oldData = $agent->toArray();

             // Delete associated image if exists
            if ($agent->image) {
                $filePath = public_path('upload/agents/' . $agent->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $agent->delete();

            // Log action
            LogHelper::logAction(
                'agents',
                $agent->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Agent berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('agent.index')->with('success', 'Agent berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus agent: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus agent.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus agent.');
        }
    }
}
