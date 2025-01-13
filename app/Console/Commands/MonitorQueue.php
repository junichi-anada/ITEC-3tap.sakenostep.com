<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class MonitorQueue extends Command
{
    protected $signature = 'queue:monitor';
    protected $description = 'Monitor and restart queue worker if needed';

    public function handle()
    {
        try {
            // queue:workプロセスの確認
            $result = Process::run('ps aux | grep "[q]ueue:work"');
            
            if ($result->output() === '') {
                // プロセスが見つからない場合は再起動
                Log::warning('Queue worker not found. Restarting...');
                
                Process::run('php artisan queue:restart');
                Process::start('php artisan queue:work --sleep=3 --tries=3 --max-time=3600 > /dev/null 2>&1 &');
                
                Log::info('Queue worker restarted successfully');
            }
        } catch (\Exception $e) {
            Log::error('Failed to monitor queue: ' . $e->getMessage());
        }
    }
} 