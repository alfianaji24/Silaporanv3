<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StorageService
{
    /**
     * Upload file to specified disk and path
     */
    public static function upload(UploadedFile $file, string $path = '', string $disk = 'public'): string
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($path, $filename, $disk);
        
        return $filePath;
    }

    /**
     * Upload file to cloud storage
     */
    public static function uploadToCloud(UploadedFile $file, string $path = '', string $disk = 's3'): string
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($path, $filename, $disk);
        
        return $filePath;
    }

    /**
     * Upload presensi file (photo/fingerprint)
     */
    public static function uploadPresensi(UploadedFile $file, string $nik = '', string $type = 'photo'): string
    {
        $path = "presensi/{$type}/" . date('Y/m/d');
        if ($nik) {
            $path .= "/{$nik}";
        }
        
        $filename = $nik . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($path, $filename, 'public');
        
        return $filePath;
    }

    /**
     * Upload karyawan photo
     */
    public static function uploadKaryawanPhoto(UploadedFile $file, string $nik): string
    {
        $path = "photos/karyawan";
        $filename = $nik . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($path, $filename, 'public');
        
        return $filePath;
    }

    /**
     * Upload laporan file
     */
    public static function uploadLaporan(UploadedFile $file, string $kategori = '', string $tanggal = ''): string
    {
        $path = "laporan/{$kategori}";
        if ($tanggal) {
            $path .= "/{$tanggal}";
        }
        
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($path, $filename, 'public');
        
        return $filePath;
    }

    /**
     * Create backup to cloud storage
     */
    public static function backupToCloud(string $localPath, string $cloudPath = '', string $disk = 's3_backup'): bool
    {
        try {
            $cloudPath = $cloudPath ?: 'backups/' . basename($localPath);
            Storage::disk($disk)->put($cloudPath, Storage::disk('local')->get($localPath));
            return true;
        } catch (\Exception $e) {
            \Log::error('Backup to cloud failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Export laporan data to storage
     */
    public static function exportLaporan(array $data, string $filename = null): string
    {
        $filename = $filename ?: 'laporan_export_' . date('Y-m-d_H-i-s') . '.csv';
        $path = "exports/laporan";
        
        $csvContent = self::arrayToCsv($data);
        Storage::disk('local')->put("app/{$path}/{$filename}", $csvContent);
        
        return "{$path}/{$filename}";
    }

    /**
     * Get file URL from storage
     */
    public static function getUrl(string $path, string $disk = 'public'): ?string
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }
        
        return null;
    }

    /**
     * Delete file from storage
     */
    public static function delete(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Get storage usage statistics
     */
    public static function getStorageStats(string $disk = 'local'): array
    {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'directories' => 0,
            'presensi_files' => 0,
            'laporan_files' => 0,
            'photo_files' => 0,
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'percentage_used' => 0,
        ];

        try {
            $files = Storage::disk($disk)->allFiles();
            $directories = Storage::disk($disk)->allDirectories();

            $stats['total_files'] = count($files);
            $stats['directories'] = count($directories);

            foreach ($files as $file) {
                $size = Storage::disk($disk)->size($file);
                $stats['total_size'] += $size;

                if (str_contains($file, 'presensi')) {
                    $stats['presensi_files']++;
                }
                if (str_contains($file, 'laporan')) {
                    $stats['laporan_files']++;
                }
                if (str_contains($file, 'photos')) {
                    $stats['photo_files']++;
                }
            }

            $stats['total_size_human'] = self::formatBytes($stats['total_size']);

            // Get disk space information
            $storagePath = storage_path();
            if ($disk === 'public') {
                $storagePath = public_path();
            }

            if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
                $freeBytes = disk_free_space($storagePath);
                $totalBytes = disk_total_space($storagePath);
                
                if ($freeBytes !== false && $totalBytes !== false) {
                    $stats['free'] = $freeBytes;
                    $stats['total'] = $totalBytes;
                    $stats['used'] = $totalBytes - $freeBytes;
                    $stats['percentage_used'] = ($stats['used'] / $stats['total']) * 100;
                }
            }

            // Fallback to estimated values if disk functions fail
            if ($stats['total'] === 0) {
                // Estimate based on typical server storage (50GB)
                $estimatedTotal = 50 * 1024 * 1024 * 1024; // 50GB in bytes
                $stats['total'] = $estimatedTotal;
                $stats['used'] = $stats['total_size'];
                $stats['free'] = $estimatedTotal - $stats['total_size'];
                $stats['percentage_used'] = ($stats['used'] / $stats['total']) * 100;
            }

        } catch (\Exception $e) {
            \Log::error('Failed to get storage stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get presensi storage statistics
     */
    public static function getPresensiStats(): array
    {
        $stats = [
            'today_photos' => 0,
            'today_fingerprints' => 0,
            'month_photos' => 0,
            'month_fingerprints' => 0,
        ];

        try {
            $today = date('Y/m/d');
            $thisMonth = date('Y/m');

            // Count today's files
            $todayPhotos = Storage::disk('public')->files("presensi/photo/{$today}");
            $todayFingerprints = Storage::disk('public')->files("presensi/fingerprint/{$today}");
            
            $stats['today_photos'] = count($todayPhotos);
            $stats['today_fingerprints'] = count($todayFingerprints);

            // Count this month's files
            $monthPhotos = Storage::disk('public')->allFiles("presensi/photo/{$thisMonth}");
            $monthFingerprints = Storage::disk('public')->allFiles("presensi/fingerprint/{$thisMonth}");
            
            $stats['month_photos'] = count($monthPhotos);
            $stats['month_fingerprints'] = count($monthFingerprints);

        } catch (\Exception $e) {
            \Log::error('Failed to get presensi stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get laporan storage statistics
     */
    public static function getLaporanStats(): array
    {
        $stats = [
            'today_laporan' => 0,
            'month_laporan' => 0,
            'by_kategori' => [],
        ];

        try {
            $today = date('Y/m/d');
            $thisMonth = date('Y/m');

            // Count today's laporan
            $todayLaporan = Storage::disk('public')->allFiles("laporan");
            foreach ($todayLaporan as $file) {
                if (str_contains($file, $today)) {
                    $stats['today_laporan']++;
                }
            }

            // Count this month's laporan
            $monthLaporan = Storage::disk('public')->allFiles("laporan");
            foreach ($monthLaporan as $file) {
                if (str_contains($file, $thisMonth)) {
                    $stats['month_laporan']++;
                }
                
                // Count by category
                $parts = explode('/', $file);
                if (isset($parts[1])) {
                    $kategori = $parts[1];
                    if (!isset($stats['by_kategori'][$kategori])) {
                        $stats['by_kategori'][$kategori] = 0;
                    }
                    $stats['by_kategori'][$kategori]++;
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to get laporan stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Format bytes to human readable format
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Cleanup old files from storage
     */
    public static function cleanupOldFiles(string $path, int $days = 30, string $disk = 'local'): int
    {
        $deletedCount = 0;
        $cutoffTime = now()->subDays($days)->timestamp;

        try {
            $files = Storage::disk($disk)->files($path);

            foreach ($files as $file) {
                $lastModified = Storage::disk($disk)->lastModified($file);
                
                if ($lastModified < $cutoffTime) {
                    Storage::disk($disk)->delete($file);
                    $deletedCount++;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Cleanup failed: ' . $e->getMessage());
        }

        return $deletedCount;
    }

    /**
     * Convert array to CSV
     */
    private static function arrayToCsv(array $data): string
    {
        $csv = '';
        
        if (!empty($data)) {
            // Header
            $csv .= implode(',', array_keys($data[0])) . "\n";
            
            // Data rows
            foreach ($data as $row) {
                $csv .= implode(',', array_map(function($value) {
                    return '"' . str_replace('"', '""', $value) . '"';
                }, $row)) . "\n";
            }
        }
        
        return $csv;
    }
}
