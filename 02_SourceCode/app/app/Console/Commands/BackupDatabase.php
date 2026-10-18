<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Sao lưu CSDL MySQL bằng mysqldump vào storage/app/backups, giữ lại N bản gần nhất.
 * Khôi phục: mysql -u... -p... disanso < storage/app/backups/<file>.sql
 */
class BackupDatabase extends Command
{
    protected $signature = 'app:backup-db {--keep=7 : Số bản sao lưu giữ lại}';
    protected $description = 'Dump the MySQL database to storage/app/backups';

    public function handle(): int
    {
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $file = $dir.'/disanso_'.now()->format('Ymd_His').'.sql';
        $cfg = config('database.connections.mysql');

        $process = new Process([
            'mysqldump', '--single-transaction', '--routines', '--default-character-set=utf8mb4',
            '-h', $cfg['host'], '-P', (string) $cfg['port'], '-u', $cfg['username'], $cfg['database'],
        ], null, ['MYSQL_PWD' => $cfg['password']]);
        $process->setTimeout(600)->run();

        if (! $process->isSuccessful()) {
            Log::error('Database backup failed', ['error' => $process->getErrorOutput()]);
            $this->error('Backup failed: '.$process->getErrorOutput());

            return self::FAILURE;
        }
        file_put_contents($file, $process->getOutput());

        // Xoay vòng: chỉ giữ N bản mới nhất
        $files = glob($dir.'/disanso_*.sql');
        rsort($files);
        foreach (array_slice($files, (int) $this->option('keep')) as $old) {
            unlink($old);
        }

        $size = round(filesize($file) / 1024, 1);
        Log::info('Database backup completed', ['file' => basename($file), 'size_kb' => $size]);
        $this->info(now()->toDateTimeString()." backup OK: ".basename($file)." ({$size} KB)");

        return self::SUCCESS;
    }
}
