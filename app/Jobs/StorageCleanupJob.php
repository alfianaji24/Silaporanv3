<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class StorageCleanupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $path,
        private int $days = 30,
        private string $disk = 'local'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $deletedCount = StorageService::cleanupOldFiles($this->path, $this->days, $this->disk);
            
            Log::info("Storage cleanup completed", [
                'path' => $this->path,
                'disk' => $this->disk,
                'days' => $this->days,
                'deleted_files' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error("Storage cleanup failed", [
                'path' => $this->path,
                'disk' => $this->disk,
                'error' => $e->getMessage()
            ]);
            
            $this->fail($e);
        }
    }
}
