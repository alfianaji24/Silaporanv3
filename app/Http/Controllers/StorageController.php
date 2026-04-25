<?php

namespace App\Http\Controllers;

use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StorageController extends Controller
{
    /**
     * Display storage dashboard
     */
    public function dashboard(): View
    {
        $stats = StorageService::getStorageStats('local');
        $presensiStats = StorageService::getPresensiStats();
        $laporanStats = StorageService::getLaporanStats();
        
        return view('storage.dashboard', compact('stats', 'presensiStats', 'laporanStats'));
    }

    /**
     * Upload file
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'path' => 'nullable|string',
            'disk' => 'nullable|string|in:local,public,s3'
        ]);

        try {
            $path = $request->input('path', 'uploads');
            $disk = $request->input('disk', 'public');
            
            $filePath = StorageService::upload(
                $request->file('file'),
                $path,
                $disk
            );

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_path' => $filePath,
                'url' => StorageService::getUrl($filePath, $disk)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload presensi file
     */
    public function uploadPresensi(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|max:5120', // 5MB max
            'nik' => 'required|string',
            'type' => 'required|in:photo,fingerprint'
        ]);

        try {
            $filePath = StorageService::uploadPresensi(
                $request->file('file'),
                $request->input('nik'),
                $request->input('type')
            );

            return response()->json([
                'success' => true,
                'message' => 'Presensi file uploaded successfully',
                'file_path' => $filePath,
                'url' => StorageService::getUrl($filePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload karyawan photo
     */
    public function uploadKaryawanPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|max:2048', // 2MB max
            'nik' => 'required|string'
        ]);

        try {
            $filePath = StorageService::uploadKaryawanPhoto(
                $request->file('file'),
                $request->input('nik')
            );

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'file_path' => $filePath,
                'url' => StorageService::getUrl($filePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload laporan file
     */
    public function uploadLaporan(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
            'kategori' => 'required|string',
            'tanggal' => 'nullable|string|date_format:Y-m-d'
        ]);

        try {
            $filePath = StorageService::uploadLaporan(
                $request->file('file'),
                $request->input('kategori'),
                $request->input('tanggal', date('Y-m-d'))
            );

            return response()->json([
                'success' => true,
                'message' => 'Laporan file uploaded successfully',
                'file_path' => $filePath,
                'url' => StorageService::getUrl($filePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'file_path' => 'required|string',
            'disk' => 'nullable|string'
        ]);

        try {
            $disk = $request->input('disk', 'public');
            $success = StorageService::delete($request->input('file_path'), $disk);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'File deleted successfully' : 'File not found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get storage statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $disk = $request->input('disk', 'local');
        
        try {
            $stats = StorageService::getStorageStats($disk);
            $presensiStats = StorageService::getPresensiStats();
            $laporanStats = StorageService::getLaporanStats();

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'presensi_stats' => $presensiStats,
                'laporan_stats' => $laporanStats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data to storage
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'data' => 'required|array',
            'filename' => 'nullable|string',
            'type' => 'required|in:laporan,presensi'
        ]);

        try {
            if ($request->input('type') === 'laporan') {
                $filename = StorageService::exportLaporan(
                    $request->input('data'),
                    $request->input('filename')
                );
            } else {
                // For presensi, use the same export method but different path
                $filename = $request->input('filename') ?: 'presensi_export_' . date('Y-m-d_H-i-s') . '.csv';
                $path = "exports/presensi";
                $csvContent = self::arrayToCsv($request->input('data'));
                \Storage::disk('local')->put("app/{$path}/{$filename}", $csvContent);
                $filename = "{$path}/{$filename}";
            }

            return response()->json([
                'success' => true,
                'message' => 'Data exported successfully',
                'file_path' => $filename,
                'download_url' => StorageService::getUrl($filename, 'local')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Backup to cloud
     */
    public function backup(Request $request): JsonResponse
    {
        $request->validate([
            'local_path' => 'required|string',
            'cloud_path' => 'nullable|string'
        ]);

        try {
            $success = StorageService::backupToCloud(
                $request->input('local_path'),
                $request->input('cloud_path')
            );

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Backup completed successfully' : 'Backup failed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup storage
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'nullable|string',
            'days' => 'nullable|integer|min:1',
            'disk' => 'nullable|string'
        ]);

        try {
            $path = $request->input('path', '');
            $days = $request->input('days', 30);
            $disk = $request->input('disk', 'local');

            $deletedCount = StorageService::cleanupOldFiles($path, $days, $disk);

            return response()->json([
                'success' => true,
                'message' => 'Cleanup completed successfully',
                'deleted_files' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert array to CSV (helper method)
     */
    private function arrayToCsv(array $data): string
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
