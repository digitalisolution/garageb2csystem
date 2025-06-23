<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearSession extends Command
{
    protected $signature = 'session:clear';
    protected $description = 'Clear all session data';

    public function handle()
    {
        $sessionDriver = config('session.driver');

        if ($sessionDriver === 'file') {
            $files = Storage::files('framework/sessions');
            Storage::delete($files);
            $this->info('File-based sessions cleared successfully.');
        } elseif ($sessionDriver === 'database') {
            \DB::table('sessions')->truncate();
            $this->info('Database sessions cleared successfully.');
        } elseif ($sessionDriver === 'redis' || $sessionDriver === 'memcached') {
            \Cache::flush();
            $this->info('Cache-based sessions cleared successfully.');
        } else {
            $this->warn('Session driver not supported.');
        }

        return 0;
    }
}
