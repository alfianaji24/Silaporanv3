<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function index()
    {
        return view('download.index');
    }

    public function downloadApk()
    {
        // Coba beberapa lokasi yang mungkin
        $possiblePaths = [
            public_path('downloads/apk/Silaporan.apk'),
            public_path('downloads/apk/silaporan.apk'),
            public_path('downloads/Silaporan.apk'),
            public_path('downloads/silaporan.apk'),
        ];
        
        $filePath = null;
        $foundPath = null;
        
        // Cari file di lokasi-lokasi yang mungkin
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $filePath = $path;
                $foundPath = $path;
                break;
            }
        }
        
        if (!$filePath) {
            // Debug: tampilkan semua file yang ada di folder downloads
            $downloadsFolder = public_path('downloads');
            $allFiles = [];
            
            if (is_dir($downloadsFolder)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($downloadsFolder));
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'apk') {
                        $allFiles[] = str_replace(public_path(), '', $file->getPathname());
                    }
                }
            }
            
            return redirect()->route('download.index')->with('error', 
                'File APK tidak ditemukan di lokasi yang diharapkan. File APK yang ditemukan: ' . 
                (empty($allFiles) ? 'Tidak ada file APK' : implode(', ', $allFiles)) . 
                '. Pastikan file APK ada di folder downloads/apk/ atau downloads/'
            );
        }
        
        // Tentukan nama file untuk download
        $fileName = basename($filePath);
        
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }
}
