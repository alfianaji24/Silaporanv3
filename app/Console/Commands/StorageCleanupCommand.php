<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class StorageCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'storage:cleanup 
                            {--path= : Path to clean (default: all)}
                            {--days=30 : Delete files older than X days}
                            {--disk=local : Storage disk to clean}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old files from storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->option('path') ?: '';
        $days = $this->option('days');
        $disk = $this->option('disk');
        $dryRun = $this->option('dry-run');

        $this->info("Starting storage cleanup...");
        $this->info("Disk: {$disk}");
        $this->info("Path: " . ($path ?: 'all'));
        $this->info("Delete files older than: {$days} days");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No files will be deleted");
        }

        try {
            if ($dryRun) {
                $this->showFilesToDelete($path, $days, $disk);
            } else {
                $deletedCount = StorageService::cleanupOldFiles($path, $days, $disk);
                $this->info("Cleanup completed. Deleted {$deletedCount} files.");
                
                Log::info("Storage cleanup completed", [
                    'path' => $path,
                    'disk' => $disk,
                    'days' => $days,
                    'deleted_files' => $deletedCount
                ]);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Cleanup failed: " . $e->getMessage());
            Log::error("Storage cleanup failed", [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Show files that would be deleted (dry run mode)
     */
    private function showFilesToDelete(string $path, int $days, string $disk): void
    {
        $cutoffTime = now()->subDays($days)->timestamp;
        $files = \Storage::disk($disk)->files($path);
        $totalSize = 0;
        $fileCount = 0;

        $this->info("\nFiles that would be deleted:");
        $this->table(['File', 'Size', 'Modified'], []);

        foreach ($files as $file) {
            $lastModified = \Storage::disk($disk)->lastModified($file);
            
            if ($lastModified < $cutoffTime) {
                $size = \Storage::disk($disk)->size($file);
                $totalSize += $size;
                $fileCount++;

                $this->table([
                    $file,
                    $this->formatBytes($size),
                    date('Y-m-d H:i:s', $lastModified)
                ], []);
            }
        }

        $this->info("\nSummary:");
        $this->line("Total files to delete: {$fileCount}");
        $this->line("Total space to free: " . $this->formatBytes($totalSize));
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
