<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AutoSessionCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:auto-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically clean up expired sessions (for scheduler)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Only run if sessions table exists
        if (!Schema::hasTable('sessions')) {
            return;
        }

        // Clean up expired sessions silently
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->delete();

        if ($deleted > 0) {
            \Log::info("Auto cleanup: Removed {$deleted} expired sessions");
        }
    }
}
