<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $process;

    public function backup_database()
    {
        $host = env('DB_HOST');
        $port = env('DB_PORT', 5432);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        $backupPath = storage_path('app/backup');
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        // $filename = $database.'_'.date('Ymd_His').'.backup';
        $filename = $database.'_'.date('Ym').'.backup';
        $file = $backupPath.DIRECTORY_SEPARATOR.$filename;

        // DETECT OS
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pgDump = 'C:\Program Files\PostgreSQL\16\bin\pg_dump.exe';
        } else {
            $pgDump = '/usr/bin/pg_dump';
        }

        $command = "\"$pgDump\" -h $host -p $port -U $username -F c -b -v -f \"$file\" $database";

        putenv("PGPASSWORD=$password");

        exec($command." 2>&1", $output, $returnVar);

        if ($returnVar === 0 && file_exists($file) && filesize($file) > 0) {
            return response()->json(['success' => true,'file' => $filename,'size' => filesize($file),'path' => $file]);
        }

        return response()->json(['success' => false,'output' => $output,'return' => $returnVar]);
    }

    public function restore_database($backupFile)
    {
        $host = env('DB_HOST');
        $port = env('DB_PORT', 5432);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        if (!file_exists($backupFile) || filesize($backupFile) == 0) {
            return response()->json(['success' => false, 'error' => 'File backup tidak ditemukan atau kosong']);
        }

        $ext = strtolower(pathinfo($backupFile, PATHINFO_EXTENSION));

        // DETECT OS
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $psql      = 'C:\Program Files\PostgreSQL\16\bin\psql.exe';
            $pgRestore = 'C:\Program Files\PostgreSQL\16\bin\pg_restore.exe';
        } else {
            $psql      = '/usr/bin/psql';
            $pgRestore = '/usr/bin/pg_restore';
        }

        putenv("PGPASSWORD=$password");

        if ($ext === 'sql') {
            // restore plain SQL
            $command = "\"$psql\" -h $host -p $port -U $username -d $database -f \"$backupFile\"";
        } else {
            // restore custom backup
            $command = "\"$pgRestore\" -h $host -p $port -U $username -d $database -c -v \"$backupFile\"";
        }

        exec($command." 2>&1", $output, $returnVar);

        if ($returnVar === 0) {
            return response()->json(['success' => true,'message' => 'Database berhasil di-restore','file' => basename($backupFile)]);
        }

        return response()->json(['success' => false,'output' => $output,'return' => $returnVar,'cmd' => $command]);
    }
    
    public function generateCode($length = 4, $type = 'letters') 
    {
        switch ($type) {
            case 'letters': // huruf saja A-Z
                $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);

            case 'numbers': // angka saja 0-9
                return str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);

            case 'mixed': // huruf + angka
                return strtoupper(Str::random($length));

            default:
                throw new InvalidArgumentException("Type harus 'letters', 'numbers', atau 'mixed'");
        }
    }
    // Contoh pemakaian
    // $kodeHuruf  = generateCode(4, 'letters'); // misal: "XZQP"
    // $kodeAngka  = generateCode(4, 'numbers'); // misal: "0385"
    // $kodeCampur = generateCode(6, 'mixed');   // misal: "A9C7XZ"

    // penyusunan lane
    public function centeredLaneOrder(int $laneCount): array
    {
        $center = (int) ceil($laneCount / 2);

        $order = [$center];

        for ($i = 1; count($order) < $laneCount; $i++) {
            if ($center - $i >= 1) {
                $order[] = $center - $i;
            }
            if ($center + $i <= $laneCount) {
                $order[] = $center + $i;
            }
        }

        return $order;
    }    

    // export data excel
    public function downloadExport($file)
    {
        $path = storage_path('app/public/exports/' . $file);
        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
