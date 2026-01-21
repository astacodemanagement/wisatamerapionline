<?php

namespace App\Http\Controllers;

use App\Models\LogHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Helpers\LogHelper;
use Illuminate\Http\JsonResponse;

class LogHistoryController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        $this->middleware('permission:loghistori-list', ['only' => ['index', 'store', 'clear']]);
        $this->middleware('permission:loghistori-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:loghistori-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:loghistori-delete', ['only' => ['destroy']]);
    }




    public function index()
    {
        $title = "Halaman Log Histori";
        $subtitle = "Menu Log Histori";
        $data_log_histori = LogHistory::orderBy('id', 'desc')->get();
        return view('log_histori', compact('data_log_histori', 'title', 'subtitle'));
    }


 

    public function deleteAll(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $oldData = [
                    'total_records' => LogHistory::count(),
                    'action' => 'Delete All Log Histories',
                ];

                LogHistory::truncate();

                LogHelper::logAction(
                    'log_histories',
                    null,
                    'Delete',
                    $oldData,
                    null
                );

                return response()->json(['success' => 'Semua log histori berhasil dihapus.']);
            } catch (\Exception $e) {
                \Log::error('Error deleting logs: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return response()->json(['error' => 'Gagal menghapus data log.'], 500);
            }
        }

        return response()->json(['error' => 'Akses tidak valid.'], 403);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $title = "Halaman Logs";
        $subtitle = "Menu Logs";
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return view('logs', ['logContent' => 'Log file not found.']);
        }

        $logContent = File::get($logPath);
        $logContent = nl2br(e($logContent));

        return view('logs', compact('logContent', 'title', 'subtitle'));
    }

    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return redirect()->route('logs.show')->with('success', 'Log berhasil dihapus.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id) {}



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {}
}
