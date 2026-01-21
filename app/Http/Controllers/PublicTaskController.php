<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class PublicTaskController extends Controller
{
    /**
     * Display the public page for tasks and divisions.
     */
    public function index(): View
    {
        $divisions = Division::where('status', 'active')->orderBy('order_display')->get();
        $tasks = Task::with('division')->orderBy('order_display')->get();
        return view('public_tasks', compact('divisions', 'tasks'));
    }

    /**
     * Store a new division (public access with validation).
     */
    public function storeDivision(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:divisions,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,nonactive',
            'order_display' => 'required|integer|min:0',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
        ], [
            'name.required' => 'Nama divisi wajib diisi.',
            'name.unique' => 'Nama divisi sudah terdaftar.',
            'name.max' => 'Nama divisi tidak boleh lebih dari 255 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'order_display.required' => 'Urutan tampilan wajib diisi.',
            'order_display.integer' => 'Urutan tampilan harus berupa angka.',
            'order_display.min' => 'Urutan tampilan tidak boleh kurang dari 0.',
            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $input = $validator->validated();
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('upload/divisions'), $imageName);
                $input['image'] = $imageName;
            }

            $division = Division::create($input);
            return response()->json([
                'message' => 'Divisi berhasil ditambahkan.',
                'data' => $division
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan divisi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new task (public access with validation).
     */
    public function storeTask(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $task = Task::create($validator->validated());
            return response()->json([
                'message' => 'Tugas berhasil ditambahkan.',
                'data' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan tugas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a task's details.
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'message' => 'Tugas berhasil diambil.',
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'division' => $task->division->name ?? 'N/A',
                'description' => $task->description ?? 'N/A',
                'status_task' => ucfirst($task->status_task),
                'pj' => $task->pj ?? 'N/A',
                'deadline' => $task->deadline ? date('d-m-Y', strtotime($task->deadline)) : 'N/A',
                'status' => ucfirst($task->status),
                'start' => $task->deadline
            ]
        ], 200);
    }

    /**
     * Show the form for editing a task.
     */
    public function edit(Task $task): JsonResponse
    {
        return response()->json([
            'message' => 'Tugas berhasil diambil untuk diedit.',
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'division_id' => $task->division_id,
                'description' => $task->description,
                'status_task' => $task->status_task,
                'pj' => $task->pj,
                'deadline' => $task->deadline,
                'status' => $task->status,
                'order_display' => $task->order_display
            ]
        ], 200);
    }

    /**
     * Update a task (public access with validation).
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $task->update($validator->validated());
            return response()->json([
                'message' => 'Tugas berhasil diperbarui.',
                'data' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui tugas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}