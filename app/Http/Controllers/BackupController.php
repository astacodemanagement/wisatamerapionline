<?php

namespace App\Http\Controllers;

use App\Models\LogHistori;


use Illuminate\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:backupdatabase-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:backupdatabase-create', ['only' => ['create', 'store', 'manualBackup']]);
        $this->middleware('permission:backupdatabase-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:backupdatabase-delete', ['only' => ['destroy']]);
    }


    private function simpanLogHistori($aksi, $tabelAsal, $idEntitas, $pengguna, $dataLama, $dataBaru)
    {
        $log = new LogHistori();
        $log->tabel_asal = $tabelAsal;
        $log->id_entitas = $idEntitas;
        $log->aksi = $aksi;
        $log->waktu = now();
        $log->pengguna = $pengguna;
        $log->data_lama = $dataLama;
        $log->data_baru = $dataBaru;
        $log->save();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Back Up Database";
        $subtitle = "Menu Back Up Database";
        $backups = File::files(base_path('public/backup'));
        return view('backupdatabase.index', compact('title', 'backups', 'subtitle'));
    }

    public function manualBackup()
    {
        try {
            // Path tempat menyimpan backup
            $fileName = date('Y-m-d_H-i-s') . '_backup.sql';
            $backupPath = public_path('backup/' . $fileName);

            // Gunakan full path ke mysqldump
            // $mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump.exe";
            $mysqldumpPath = "C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe";

            $host = Config::get('database.connections.mysql.host');
            $database = Config::get('database.connections.mysql.database');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');

            // Pastikan folder backup ada
            if (!file_exists(public_path('backup'))) {
                mkdir(public_path('backup'), 0777, true);
            }

            // Buat command dengan password jika ada
            if (!empty($password)) {
                $command = "\"{$mysqldumpPath}\" -h {$host} -u {$username} -p{$password} {$database} > \"{$backupPath}\"";
            } else {
                $command = "\"{$mysqldumpPath}\" -h {$host} -u {$username} {$database} > \"{$backupPath}\"";
            }

            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new Exception('Backup gagal!');
            }

            // Menyiapkan data untuk log
            $loggedInUserId = Auth::id();
            $backupInfo = [
                'file_name' => $fileName,
                'file_size' => File::size($backupPath),
                'created_at' => now()->format('Y-m-d H:i:s')
            ];

            // Simpan log histori dengan aksi yang sederhana
            $this->simpanLogHistori(
                'Create',           // Aksi yang disederhanakan
                'Backup Database',                  // Tabel asal
                0,                          // ID entitas
                $loggedInUserId,            // User yang melakukan backup
                null,                       // Data lama
                json_encode($backupInfo)    // Data baru
            );

            return response()->download($backupPath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            // Log error dengan aksi yang sederhana
            $errorInfo = [
                'error_message' => $e->getMessage(),
                'attempted_at' => now()->format('Y-m-d H:i:s')
            ];

            $this->simpanLogHistori(
                'Create',           // Aksi yang disederhanakan
                'Backup Database',                  // Tabel asal
                0,                          // ID entitas
                Auth::id(),                 // User yang melakukan backup
                null,                       // Data lama
                json_encode($errorInfo)     // Data baru
            );

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View {}




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
     * @param  \App\Adjustments  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}

    public function print($id) {}



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Adjustments  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View {}





    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Adjustments  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {}







    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Adjustments  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {}
}
