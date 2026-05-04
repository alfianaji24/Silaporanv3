<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;

class LaravelLogController extends Controller
{
    /**
     * Display a listing of Laravel logs
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return view('admin.laravel_logs.index', [
                'logs' => [],
                'levels' => [],
                'currentLevel' => $request->get('level', ''),
                'search' => $request->get('search', ''),
                'logFile' => 'laravel.log'
            ]);
        }

        $logContent = File::get($logPath);
        $lines = explode("\n", $logContent);
        
        // Parse log lines
        $parsedLogs = [];
        $currentLog = null;
        
        foreach ($lines as $line) {
            // Match log pattern: [2024-01-01 12:00:00] local.ERROR: message
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+):(.+)/', $line, $matches)) {
                if ($currentLog) {
                    $parsedLogs[] = $currentLog;
                }
                
                $currentLog = [
                    'datetime' => $matches[1],
                    'environment' => $matches[2],
                    'level' => $matches[3],
                    'message' => trim($matches[4]),
                    'stack' => []
                ];
            } elseif ($currentLog && !empty(trim($line))) {
                // Add to stack trace
                $currentLog['stack'][] = trim($line);
            }
        }
        
        if ($currentLog) {
            $parsedLogs[] = $currentLog;
        }
        
        // Reverse to show newest first
        $parsedLogs = array_reverse($parsedLogs);
        
        // Filter by level
        $currentLevel = $request->get('level', '');
        if ($currentLevel) {
            $parsedLogs = array_filter($parsedLogs, function($log) use ($currentLevel) {
                return strtolower($log['level']) === strtolower($currentLevel);
            });
        }
        
        // Filter by search
        $search = $request->get('search', '');
        if ($search) {
            $parsedLogs = array_filter($parsedLogs, function($log) use ($search) {
                $message = strtolower($log['message']);
                $stack = strtolower(implode(' ', $log['stack']));
                return strpos($message, strtolower($search)) !== false || strpos($stack, strtolower($search)) !== false;
            });
        }
        
        // Get unique log levels
        $levels = array_unique(array_map(function($log) {
            return $log['level'];
        }, $parsedLogs));
        sort($levels);

        // Pagination using LengthAwarePaginator
        $perPage = 50;
        $currentPage = $request->get('page', 1);
        $totalLogs = count($parsedLogs);

        $logs = new LengthAwarePaginator(
            array_slice($parsedLogs, ($currentPage - 1) * $perPage, $perPage),
            $totalLogs,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.laravel_logs.index', [
            'logs' => $logs,
            'levels' => $levels,
            'currentLevel' => $currentLevel,
            'search' => $search,
            'logFile' => 'laravel.log',
            'totalLogs' => $totalLogs
        ]);
    }

    
    /**
     * Download the log file
     */
    public function download()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return redirect()->back()->with('error', 'Log file not found.');
        }
        
        return response()->download($logPath, 'laravel_' . now()->format('Y-m-d_H-i-s') . '.log');
    }

    /**
     * Get log statistics
     */
    public function statistics()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return response()->json([
                'total_logs' => 0,
                'by_level' => [],
                'file_size' => 0
            ]);
        }

        $logContent = File::get($logPath);
        $lines = explode("\n", $logContent);
        
        $stats = [
            'total_logs' => 0,
            'by_level' => [],
            'file_size' => File::size($logPath)
        ];
        
        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \w+)\.(\w+):/', $line, $matches)) {
                $stats['total_logs']++;
                $level = $matches[2];
                if (!isset($stats['by_level'][$level])) {
                    $stats['by_level'][$level] = 0;
                }
                $stats['by_level'][$level]++;
            }
        }
        
        return response()->json($stats);
    }
}
