<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'platform',
        'browser',
        'is_active',
        'last_activity',
        'login_time',
        'logout_time',
        'is_forced_logout',
        'forced_logout_reason',
        'forced_by_admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_forced_logout' => 'boolean',
        'last_activity' => 'datetime',
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forcedByAdmin()
    {
        return $this->belongsTo(User::class, 'forced_by_admin_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('last_activity', '>=', now()->subHours($hours));
    }

    // Static methods for session management
    public static function createSession($user, $request)
    {
        try {
            $agent = new \Jenssegers\Agent\Agent();
            
            return self::create([
                'user_id' => $user->id,
                'session_id' => session()->getId(),
                'ip_address' => self::getClientIP($request),
                'user_agent' => $request->userAgent(),
                'device_type' => self::getDeviceType($agent),
                'platform' => $agent->platform() ?: 'Unknown',
                'browser' => $agent->browser() ?: 'Unknown',
                'is_active' => true,
                'last_activity' => now(),
                'login_time' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create user session', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public static function deactivateUserSessions($userId, $excludeSessionId = null)
    {
        $query = self::where('user_id', $userId)->active();
        
        if ($excludeSessionId) {
            $query->where('session_id', '!=', $excludeSessionId);
        }
        
        return $query->update([
            'is_active' => false,
            'logout_time' => now(),
        ]);
    }

    public static function forceLogoutUser($userId, $reason, $adminId)
    {
        $sessions = self::where('user_id', $userId)->active()->get();
        
        foreach ($sessions as $session) {
            $session->update([
                'is_active' => false,
                'logout_time' => now(),
                'is_forced_logout' => true,
                'forced_logout_reason' => $reason,
                'forced_by_admin_id' => $adminId,
            ]);
        }
        
        return $sessions->count();
    }

    /**
     * Force logout a specific session
     */
    public static function forceLogoutSession($sessionId)
    {
        $session = self::where('session_id', $sessionId)->first();
        if ($session) {
            $session->update([
                'is_active' => false,
                'logout_time' => now(),
            ]);
            
            \Log::info('Session force logged out', [
                'session_id' => $sessionId,
                'user_id' => $session->user_id,
                'logout_time' => now()->toDateTimeString()
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Force logout all active sessions for a user
     */
    public static function forceLogoutAllUserSessions($userId)
    {
        $sessions = self::where('user_id', $userId)
            ->where('is_active', true)
            ->get();
            
        $updatedCount = 0;
        foreach ($sessions as $session) {
            $session->update([
                'is_active' => false,
                'logout_time' => now(),
            ]);
            $updatedCount++;
        }
        
        \Log::info('All user sessions force logged out', [
            'user_id' => $userId,
            'sessions_updated' => $updatedCount,
            'logout_time' => now()->toDateTimeString()
        ]);
        
        return $updatedCount;
    }

    private static function getClientIP($request)
    {
        $ipHeaders = [
            'CF-Connecting-IP',
            'X-Forwarded-For',
            'X-Real-IP',
            'X-Client-IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            $ip = $request->header($header);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                return $ip;
            }
        }

        return $request->ip();
    }

    private static function getDeviceType($agent)
    {
        $userAgent = $agent->getUserAgent();
        
        // Detect Flutter WebView
        if (strpos($userAgent, 'wv') !== false && strpos($userAgent, 'flutter') !== false) {
            return 'Flutter WebView';
        }
        
        // Detect other WebView
        if (strpos($userAgent, 'wv') !== false) {
            return 'Mobile WebView';
        }
        
        if ($agent->isMobile()) {
            return 'Mobile';
        } elseif ($agent->isTablet()) {
            return 'Tablet';
        } elseif ($agent->isDesktop()) {
            return 'Desktop';
        } else {
            return 'Unknown';
        }
    }
}
