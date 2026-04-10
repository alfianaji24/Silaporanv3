<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IPBlacklistController extends Controller
{
    /**
     * Display a listing of blacklisted IPs.
     */
    public function index(Request $request)
    {
        $query = DB::table('ip_blacklists')
            ->orderBy('blocked_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by threat level
        if ($request->has('threat_level') && $request->threat_level !== '') {
            $query->where('threat_level', '>=', $request->threat_level);
        }

        // Search by IP address
        if ($request->has('search') && $request->search !== '') {
            $query->where('ip_address', 'like', '%' . $request->search . '%');
        }

        $blacklistedIPs = $query->paginate(20);

        // Get static IPs from middleware
        $staticIPs = [
            '192.168.1.100',
            '10.0.0.50'
        ];

        // Get recent access logs
        $recentLogs = $this->getRecentAccessLogs();

        return view('admin.ip-blacklist.index', compact('blacklistedIPs', 'staticIPs', 'recentLogs'));
    }

    /**
     * Store a newly blacklisted IP.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip|unique:ip_blacklists,ip_address',
            'reason' => 'nullable|string|max:255',
            'threat_level' => 'nullable|integer|min:1|max:10',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::table('ip_blacklists')->insert([
            'ip_address' => $request->ip_address,
            'reason' => $request->reason ?? 'Manual block by admin',
            'source' => 'manual',
            'threat_level' => $request->threat_level ?? 5,
            'blocked_at' => now(),
            'expires_at' => $request->expires_at,
            'is_active' => true,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info('IP manually blacklisted', [
            'ip' => $request->ip_address,
            'reason' => $request->reason,
            'admin' => auth()->user()->name ?? 'Unknown'
        ]);

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP ' . $request->ip_address . ' berhasil ditambahkan ke blacklist');
    }

    /**
     * Update the specified IP blacklist.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
            'threat_level' => 'nullable|integer|min:1|max:10',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::table('ip_blacklists')
            ->where('id', $id)
            ->update([
                'reason' => $request->reason,
                'threat_level' => $request->threat_level,
                'expires_at' => $request->expires_at,
                'notes' => $request->notes,
                'updated_at' => now()
            ]);

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP blacklist berhasil diperbarui');
    }

    /**
     * Toggle active status of IP blacklist.
     */
    public function toggle($id)
    {
        $ipBlacklist = DB::table('ip_blacklists')->where('id', $id)->first();
        
        if (!$ipBlacklist) {
            return redirect()->route('ip-blacklist.index')
                ->with('error', 'IP blacklist tidak ditemukan');
        }

        $newStatus = !$ipBlacklist->is_active;
        
        DB::table('ip_blacklists')
            ->where('id', $id)
            ->update([
                'is_active' => $newStatus,
                'updated_at' => now()
            ]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        
        Log::info('IP blacklist status toggled', [
            'ip' => $ipBlacklist->ip_address,
            'new_status' => $newStatus,
            'admin' => auth()->user()->name ?? 'Unknown'
        ]);

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP ' . $ipBlacklist->ip_address . ' berhasil ' . $statusText);
    }

    /**
     * Remove the specified IP from blacklist.
     */
    public function destroy($id)
    {
        $ipBlacklist = DB::table('ip_blacklists')->where('id', $id)->first();
        
        if (!$ipBlacklist) {
            return redirect()->route('ip-blacklist.index')
                ->with('error', 'IP blacklist tidak ditemukan');
        }

        DB::table('ip_blacklists')->where('id', $id)->delete();

        Log::info('IP removed from blacklist', [
            'ip' => $ipBlacklist->ip_address,
            'admin' => auth()->user()->name ?? 'Unknown'
        ]);

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP ' . $ipBlacklist->ip_address . ' berhasil dihapus dari blacklist');
    }

    /**
     * Get recent access logs for monitoring.
     */
    private function getRecentAccessLogs()
    {
        // This would read from log file or database
        // For now, return empty array
        return [];
    }

    /**
     * Show statistics dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_blocked' => DB::table('ip_blacklists')->count(),
            'active_blocked' => DB::table('ip_blacklists')->where('is_active', true)->count(),
            'expired_blocked' => DB::table('ip_blacklists')
                ->where('expires_at', '<', now())
                ->where('expires_at', '!=', null)
                ->count(),
            'high_threat' => DB::table('ip_blacklists')
                ->where('is_active', true)
                ->where('threat_level', '>=', 7)
                ->count(),
            'blocked_today' => DB::table('ip_blacklists')
                ->whereDate('blocked_at', today())
                ->count(),
            'blocked_this_week' => DB::table('ip_blacklists')
                ->whereBetween('blocked_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count()
        ];

        $recentBlocks = DB::table('ip_blacklists')
            ->orderBy('blocked_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.ip-blacklist.dashboard', compact('stats', 'recentBlocks'));
    }
}
