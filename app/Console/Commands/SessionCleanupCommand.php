<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SessionCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:cleanup {--diagnostic : Show diagnostic information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired sessions and diagnose 419 errors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('diagnostic')) {
            $this->showDiagnostic();
            return;
        }

        $this->info('Starting session cleanup...');
        
        // Check if sessions table exists
        if (!Schema::hasTable('sessions')) {
            $this->error('Sessions table does not exist!');
            return;
        }

        // Count total sessions
        $totalSessions = DB::table('sessions')->count();
        $this->info("Total sessions in database: {$totalSessions}");

        // Clean up expired sessions
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->delete();

        $this->info("Cleaned up {$deleted} expired sessions");

        // Show remaining sessions
        $remainingSessions = DB::table('sessions')->count();
        $this->info("Remaining active sessions: {$remainingSessions}");

        // Optimize table
        try {
            DB::statement('OPTIMIZE TABLE sessions');
            $this->info('Sessions table optimized');
        } catch (\Exception $e) {
            $this->warn('Could not optimize sessions table: ' . $e->getMessage());
        }

        $this->info('Session cleanup completed!');
    }

    private function showDiagnostic()
    {
        $this->info('=== SESSION DIAGNOSTIC ===');
        
        // Session configuration
        $this->info('Session Configuration:');
        $this->info('  - Driver: ' . config('session.driver'));
        $this->info('  - Lifetime: ' . config('session.lifetime') . ' minutes');
        $this->info('  - Expire on close: ' . (config('session.expire_on_close') ? 'Yes' : 'No'));
        $this->info('  - Secure: ' . (config('session.secure') ? 'Yes' : 'No'));
        $this->info('  - HTTP Only: ' . (config('session.http_only') ? 'Yes' : 'No'));
        $this->info('  - Same Site: ' . config('session.same_site'));

        // Database sessions info
        if (Schema::hasTable('sessions')) {
            $totalSessions = DB::table('sessions')->count();
            $activeSessions = DB::table('sessions')
                ->where('last_activity', '>', now()->subMinutes(config('session.lifetime'))->timestamp)
                ->count();
            $expiredSessions = $totalSessions - $activeSessions;

            $this->info("\nDatabase Sessions:");
            $this->info("  - Total sessions: {$totalSessions}");
            $this->info("  - Active sessions: {$activeSessions}");
            $this->info("  - Expired sessions: {$expiredSessions}");

            if ($expiredSessions > 0) {
                $this->warn("  ⚠️  Found {$expiredSessions} expired sessions that should be cleaned up!");
                $this->info('  💡 Run: php artisan session:cleanup');
            }
        } else {
            $this->warn("\n⚠️  Sessions table does not exist!");
        }

        // Recent activity
        if (Schema::hasTable('sessions')) {
            $recentActivity = DB::table('sessions')
                ->where('last_activity', '>', now()->subMinutes(30)->timestamp)
                ->count();
            
            $this->info("\nRecent Activity (last 30 minutes):");
            $this->info("  - Active sessions: {$recentActivity}");
        }

        $this->info("\n=== RECOMMENDATIONS ===");
        
        if (config('session.lifetime') < 120) {
            $this->warn("⚠️  Session lifetime is less than 2 hours. Consider increasing it.");
        }

        if (config('session.driver') === 'file') {
            $this->warn("⚠️  Using file session driver. Consider using database driver for better reliability.");
        }

        $this->info("💡 To prevent 419 errors:");
        $this->info("   1. Run 'php artisan session:cleanup' regularly");
        $this->info("   2. Consider increasing session lifetime");
        $this->info("   3. Clear browser cache if issues persist");
        $this->info("   4. Check for multiple login sessions");
    }
}
