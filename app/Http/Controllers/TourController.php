<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use App\Models\Tour;
use App\Services\ImageService;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('permission:tour-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:tour-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tour-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tour-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Tour";
        $subtitle = "Menu Tour";
        $data_tour = Tour::all();
        return view('tours.index', compact('data_tour', 'title', 'subtitle'));
    }


    public function create(): View {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tours,name',
            'slug' => 'required|string|max:255|unique:tours,slug',
            'price' => 'required|numeric|min:0',
            'price_label' => 'nullable|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'max_participants' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,inactive',
            'order_display' => 'required|integer|min:0',
            'route_tours' => 'nullable|array',
            'route_tours.*.route_name' => 'required_with:route_tours|string|max:255',
        ], [
            'name.required' => 'Nama tour wajib diisi.',
            'name.unique' => 'Nama tour sudah terdaftar.',
            'name.max' => 'Nama tour tidak boleh lebih dari 255 karakter.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.unique' => 'Slug sudah terdaftar.',
            'slug.max' => 'Slug tidak boleh lebih dari 255 karakter.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',
            'price_label.max' => 'Label harga tidak boleh lebih dari 255 karakter.',
            'duration_minutes.required' => 'Durasi wajib diisi.',
            'duration_minutes.integer' => 'Durasi harus berupa angka.',
            'duration_minutes.min' => 'Durasi minimal 1 menit.',
            'max_participants.required' => 'Maksimal peserta wajib diisi.',
            'max_participants.integer' => 'Maksimal peserta harus berupa angka.',
            'max_participants.min' => 'Maksimal peserta minimal 1 orang.',
            'location.required' => 'Lokasi wajib diisi.',
            'location.max' => 'Lokasi tidak boleh lebih dari 255 karakter.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus "active" atau "inactive".',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'route_tours.array' => 'Format routes tidak valid.',
            'route_tours.*.route_name.required_with' => 'Nama route wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $input = collect($validated)->except('route_tours')->all();
            
            if (empty($input['price_label'])) {
                $input['price_label'] = '/ Per Ticket';
            }

            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/tours')
                : null;
            
            $tour = Tour::create($input);

            // Simpan routes jika ada
            $routes = collect($request->input('route_tours', []))
                ->filter(fn($r) => !empty($r['route_name']))
                ->map(fn($r) => ['route_name' => $r['route_name']])
                ->values()
                ->all();
            if (!empty($routes)) {
                $tour->routes()->createMany($routes);
            }

            LogHelper::logAction(
                'tours',
                $tour->id,
                'Create',
                null,
                array_merge($tour->toArray(), ['route_tours' => $routes])
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Tour berhasil ditambahkan.',
                    'data' => $tour->load('routes')
                ], 200);
            }

            return redirect()->route('tours.index')->with('success', 'Tour berhasil ditambahkan.');
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
            Log::error('Kesalahan saat menambahkan tour: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menambahkan tour.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $tour = Tour::with('routes')->findOrFail($id);
            $data = $tour->toArray();
            $data['route_tours'] = $tour->routes->map(fn($r) => [
                'id' => $r->id,
                'route_name' => $r->route_name,
            ])->values()->all();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data tour: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data tour.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $tour = Tour::with('routes')->findOrFail($id);
            $data = $tour->toArray();
            $data['route_tours'] = $tour->routes->map(fn($r) => [
                'id' => $r->id,
                'route_name' => $r->route_name,
            ])->values()->all();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data tour untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data tour.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tours,name,' . $id,
            'slug' => 'required|string|max:255|unique:tours,slug,' . $id,
            'price' => 'required|numeric|min:0',
            'price_label' => 'nullable|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'max_participants' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required|in:active,inactive',
            'order_display' => 'required|integer|min:0',
            'route_tours' => 'nullable|array',
            'route_tours.*.route_name' => 'required_with:route_tours|string|max:255',
            'route_tours.*.id' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            $tour = Tour::findOrFail($id);
            $oldData = $tour->toArray();
            $input = collect($validated)->except('route_tours')->all();
            
            if (empty($input['price_label'])) {
                $input['price_label'] = '/ Per Ticket';
            }

            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/tours',
                    $tour->image
                );
            } else {
                $input['image'] = $tour->image;
            }

            $tour->update($input);

            // Sinkronisasi routes
            $payloadRoutes = collect($request->input('route_tours', []))
                ->filter(fn($r) => !empty($r['route_name']));

            $idsSent = $payloadRoutes->pluck('id')->filter()->values()->all();

            // Hapus route yang tidak ada dalam payload
            if (!empty($idsSent)) {
                $tour->routes()->whereNotIn('id', $idsSent)->delete();
            } else {
                // Jika tidak ada id yang dikirim sama sekali, hapus semua existing
                $tour->routes()->delete();
            }

            // Update atau create
            foreach ($payloadRoutes as $r) {
                if (!empty($r['id'])) {
                    $tour->routes()->where('id', $r['id'])->update([
                        'route_name' => $r['route_name'],
                    ]);
                } else {
                    $tour->routes()->create([
                        'route_name' => $r['route_name'],
                    ]);
                }
            }

            LogHelper::logAction(
                'tours',
                $tour->id,
                'Update',
                $oldData,
                $tour->load('routes')->toArray()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Tour berhasil diperbarui.',
                    'data' => $tour->load('routes')
                ], 200);
            }

            return redirect()->route('tours.index')->with('success', 'Tour berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error saat memperbarui tour: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui tour: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui tour.',
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

            $tour = Tour::findOrFail($id);
            $oldData = $tour->toArray();

             // Delete associated image if exists
            if ($tour->image) {
                $filePath = public_path('upload/tours/' . $tour->image);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $tour->delete();

            // Log action
            LogHelper::logAction(
                'tours',
                $tour->id,
                'Delete',
                $oldData,
                null
            );

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Tour berhasil dihapus.'
                ], 200);
            }

            return redirect()->route('tours.index')->with('success', 'Tour berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus tour: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'message' => 'Gagal menghapus tour.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus tour.');
        }
    }
}
