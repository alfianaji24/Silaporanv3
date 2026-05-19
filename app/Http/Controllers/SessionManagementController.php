<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSession;
use App\Models\User;

class SessionManagementController extends Controller
{
    /**
     * Display sessions for users (active and inactive)
     */
    public function index(Request $request)
    {
        UserSession::expireStaleSessions();

        $query = UserSession::with(['user', 'forcedByAdmin'])
            ->orderBy('created_at', 'desc');

        // Filter by status (active/inactive)
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        } else {
            // Default to show all sessions (empty status = all sessions)
            // No additional filter needed
        }

        // Filter by user type
        if ($request->has('user_type') && $request->user_type !== '') {
            if ($request->user_type === 'karyawan') {
                $query->whereHas('user', function($q) {
                    $q->role('karyawan');
                });
            } else {
                $query->whereHas('user', function($q) {
                    $q->whereDoesntHave('roles', function($roleQuery) {
                        $roleQuery->where('name', 'karyawan');
                    });
                });
            }
        }

        // Search by user name
        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $sessions = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_sessions' => UserSession::count(),
            'active_sessions' => UserSession::active()->count(),
            'inactive_sessions' => UserSession::where('is_active', false)->count(),
            'karyawan_sessions' => UserSession::whereHas('user', function($q) {
                $q->role('karyawan');
            })->count(),
            'admin_sessions' => UserSession::whereHas('user', function($q) {
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->where('name', 'karyawan');
                });
            })->count(),
        ];

        return view('admin.sessions.index', compact('sessions', 'stats'));
    }

    /**
     * Force logout a user session
     */
    public function forceLogout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255'
        ]);

        $userId = $request->user_id;
        $reason = $request->reason;
        $adminId = auth()->id();

        // Force logout all active sessions for the user
        $sessionsCount = UserSession::forceLogoutUser($userId, $reason, $adminId);

        // Get user info for notification
        $user = User::find($userId);

        return redirect()->back()->with('success', 
            "Berhasil force logout {$sessionsCount} session untuk user {$user->name}. Alasan: {$reason}"
        );
    }

    /**
     * Force logout specific session
     */
    public function forceLogoutSession(Request $request, $sessionId)
    {
        $session = UserSession::findOrFail($sessionId);
        
        $session->update([
            'is_active' => false,
            'logout_time' => now(),
            'is_forced_logout' => true,
            'forced_logout_reason' => $request->reason ?? 'Force logout by admin',
            'forced_by_admin_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 
            "Session untuk user {$session->user->name} berhasil di-logout."
        );
    }

    /**
     * Get active sessions for a specific user (AJAX)
     */
    public function getUserSessions($userId)
    {
        UserSession::expireStaleSessions($userId);

        $sessions = UserSession::with(['forcedByAdmin'])
            ->where('user_id', $userId)
            ->active()
            ->orderBy('last_activity', 'desc')
            ->get();

        return response()->json([
            'sessions' => $sessions->map(function($session) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'device_type' => $session->device_type,
                    'platform' => $session->platform,
                    'browser' => $session->browser,
                    'login_time' => $session->login_time->format('Y-m-d H:i:s'),
                    'last_activity' => $session->last_activity->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Clean up old inactive sessions
     */
    public function cleanupSessions()
    {
        // Delete sessions older than 30 days that are inactive
        $deleted = UserSession::where('is_active', false)
            ->where('logout_time', '<', now()->subDays(30))
            ->delete();

        return redirect()->back()->with('success', 
            "Berhasil menghapus {$deleted} session lama yang tidak aktif."
        );
    }

    /**
     * API endpoint for Flutter WebView session check
     */
    public function checkSession(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not authenticated',
                'redirect' => route('login')
            ], 401);
        }

        $user = Auth::user();
        $sessionId = session()->getId();

        // Check if session is still active
        $userSession = UserSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->active()
            ->first();

        if (!$userSession) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'error',
                'message' => 'Session expired or login from another device',
                'redirect' => route('login'),
                'reason' => $user->hasRole('karyawan') ? 'single_device_violation' : 'session_expired'
            ], 401);
        }

        // Update last activity
        $userSession->update(['last_activity' => now()]);

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first(),
            ],
            'session' => [
                'device_type' => $userSession->device_type,
                'ip_address' => $userSession->ip_address,
                'login_time' => $userSession->login_time->format('Y-m-d H:i:s'),
                'last_activity' => $userSession->last_activity->diffForHumans(),
            ]
        ]);
    }

    /**
     * API endpoint for Flutter WebView logout
     */
    public function logoutFlutter(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not authenticated'
            ], 401);
        }

        $user = Auth::user();
        $sessionId = session()->getId();

        // Update session status
        UserSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->active()
            ->update([
                'is_active' => false,
                'logout_time' => now(),
            ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
            'redirect' => route('login')
        ]);
    }
}
