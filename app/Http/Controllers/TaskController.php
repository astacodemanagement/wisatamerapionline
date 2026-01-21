<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:task-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:task-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:task-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:task-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = "Halaman Tugas";
        $subtitle = "Menu Tugas";
        $tasks = Task::with('division')->orderBy('order_display')->get();
        $divisions = Division::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('tasks.index', compact('tasks', 'divisions', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Tambah Tugas";
        $subtitle = "Form Tambah Tugas";
        $divisions = Division::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
        return view('tasks.create', compact('divisions', 'title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status_task' => 'required|in:belum,proses,selesai',
                'division_id' => 'required|exists:divisions,id',
                'pj' => 'nullable|string|max:255',
                'deadline' => 'nullable|date|after_or_equal:today',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'title.required' => 'Judul tugas wajib diisi.',
                'title.max' => 'Judul tugas tidak boleh lebih dari 255 karakter.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status_task.required' => 'Status tugas wajib dipilih.',
                'status_task.in' => 'Status tugas tidak valid.',
                'division_id.required' => 'Divisi wajib dipilih.',
                'division_id.exists' => 'Divisi tidak valid.',
                'pj.string' => 'Penanggung jawab harus berupa teks.',
                'pj.max' => 'Penanggung jawab tidak boleh lebih dari 255 karakter.',
                'deadline.date' => 'Tanggal tenggat harus berupa tanggal yang valid.',
                'deadline.after_or_equal' => 'Tanggal tenggat tidak boleh sebelum hari ini.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $task = Task::create($validated);
            LogHelper::logAction('tasks', $task->id, 'Create', null, $task->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Tugas berhasil ditambahkan.',
                'data' => $task
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat menambahkan tugas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menambahkan tugas.',
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
            $task = Task::with('division')->findOrFail($id);
            return response()->json($task, 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data tugas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data tugas.',
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
            $task = Task::findOrFail($id);
            $divisions = Division::where('status', 'active')->orderBy('order_display')->get(['id', 'name']);
            return response()->json([
                'task' => $task,
                'divisions' => $divisions
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data tugas untuk edit: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data tugas.',
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
            $task = Task::findOrFail($id);
            $oldData = $task->toArray();

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status_task' => 'required|in:belum,proses,selesai',
                'division_id' => 'required|exists:divisions,id',
                'pj' => 'nullable|string|max:255',
                'deadline' => 'nullable|date|after_or_equal:today',
                'status' => 'required|in:active,nonactive',
                'order_display' => 'required|integer|min:0',
            ], [
                'title.required' => 'Judul tugas wajib diisi.',
                'title.max' => 'Judul tugas tidak boleh lebih dari 255 karakter.',
                'description.string' => 'Deskripsi harus berupa teks.',
                'status_task.required' => 'Status tugas wajib dipilih.',
                'status_task.in' => 'Status tugas tidak valid.',
                'division_id.required' => 'Divisi wajib dipilih.',
                'division_id.exists' => 'Divisi tidak valid.',
                'pj.string' => 'Penanggung jawab harus berupa teks.',
                'pj.max' => 'Penanggung jawab tidak boleh lebih dari 255 karakter.',
                'deadline.date' => 'Tanggal tenggat harus berupa tanggal yang valid.',
                'deadline.after_or_equal' => 'Tanggal tenggat tidak boleh sebelum hari ini.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
                'order_display.required' => 'Urutan tampilan wajib diisi.',
                'order_display.integer' => 'Urutan tampilan harus berupa angka.',
                'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            ]);

            $task->update($validated);
            LogHelper::logAction('tasks', $task->id, 'Update', $oldData, $task->toArray());
            DB::commit();

            return response()->json([
                'message' => 'Tugas berhasil diperbarui.',
                'data' => $task
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat memperbarui tugas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui tugas.',
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
            $task = Task::findOrFail($id);
            $oldData = $task->toArray();

            $task->delete();
            LogHelper::logAction('tasks', $task->id, 'Delete', $oldData, null);
            DB::commit();

            return response()->json([
                'message' => 'Tugas berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus tugas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus tugas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}