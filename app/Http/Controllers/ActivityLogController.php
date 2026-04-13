<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'subject'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action') && $request->action !== '') {
            $query->forAction($request->action);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id !== '') {
            $query->forUser($request->user_id);
        }

        // Filter by module
        if ($request->has('module') && $request->module !== '') {
            $query->forModule($request->module);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = $request->has('date_to') && $request->date_to !== '' 
                ? Carbon::parse($request->date_to)->endOfDay() 
                : now();
            
            $query->dateRange($dateFrom, $dateTo);
        }

        // Search by description
        if ($request->has('search') && $request->search !== '') {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $activityLogs = $query->paginate(50);
        $users = User::orderBy('name')->get();

        // Get available modules
        $modules = ActivityLog::distinct('module')
            ->whereNotNull('module')
            ->pluck('module')
            ->sort()
            ->values();

        // Get available actions
        $actions = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'viewed' => 'Viewed',
            'exported' => 'Exported',
            'imported' => 'Imported',
            'login' => 'Login',
            'logout' => 'Logout',
            'accessed' => 'Accessed',
        ];

        return view('admin.activity_logs.index', compact(
            'activityLogs',
            'users',
            'modules',
            'actions'
        ));
    }

    /**
     * Display the specified activity log
     */
    public function show($id)
    {
        $activityLog = ActivityLog::with(['user', 'subject'])
            ->findOrFail($id);

        return view('admin.activity_logs.show', compact('activityLog'));
    }

    /**
     * Export activity logs to Excel
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with(['user', 'subject'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->has('action') && $request->action !== '') {
            $query->forAction($request->action);
        }

        if ($request->has('user_id') && $request->user_id !== '') {
            $query->forUser($request->user_id);
        }

        if ($request->has('module') && $request->module !== '') {
            $query->forModule($request->module);
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = $request->has('date_to') && $request->date_to !== '' 
                ? Carbon::parse($request->date_to)->endOfDay() 
                : now();
            
            $query->dateRange($dateFrom, $dateTo);
        }

        $logs = $query->get();

        // Create CSV export
        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Date Time',
                'User',
                'Action',
                'Module',
                'Subject',
                'Description',
                'IP Address',
                'User Agent',
                'URL',
                'Method'
            ]);

            // CSV Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'System',
                    $log->action_name,
                    $log->module,
                    $log->subject_name,
                    $log->description,
                    $log->ip_address,
                    $log->user_agent,
                    $log->url,
                    $log->method,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear old activity logs
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->back()->with('success', 
            "Successfully deleted {$deletedCount} activity logs older than {$request->days} days."
        );
    }

    /**
     * Get activity statistics
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week_logs' => ActivityLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_logs' => ActivityLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Top users by activity
        $topUsers = ActivityLog::selectRaw('user_id, COUNT(*) as count')
            ->with('user')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Top modules by activity
        $topModules = ActivityLog::selectRaw('module, COUNT(*) as count')
            ->whereNotNull('module')
            ->groupBy('module')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Activity by action type
        $actionsByType = ActivityLog::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'stats' => $stats,
            'topUsers' => $topUsers,
            'topModules' => $topModules,
            'actionsByType' => $actionsByType,
        ]);
    }
}
