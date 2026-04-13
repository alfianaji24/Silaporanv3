<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogUserLogin
{
    use InteractsWithQueue;

    protected $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            $user = $event->user;
            
            // Check for existing active sessions for karyawan
            if ($user->hasRole('karyawan')) {
                $existingSession = UserSession::where('user_id', $user->id)
                    ->active()
                    ->first();
                
                // Additional check: block if same IP has active session from different user
                $sameIPSession = UserSession::where('ip_address', $this->getClientIP($this->request))
                    ->active()
                    ->where('user_id', '!=', $user->id)
                    ->whereHas('user', function($query) {
                        $query->whereHas('roles', function($roleQuery) {
                            $roleQuery->where('name', 'karyawan');
                        });
                    })
                    ->first();
                
                if ($sameIPSession) {
                    // Block login if same IP is used by another karyawan
                    $blockedData = [
                        'device' => $sameIPSession->device_type,
                        'ip' => $sameIPSession->ip_address,
                        'login_time' => $sameIPSession->login_time->format('d M Y H:i'),
                        'browser' => $sameIPSession->browser,
                        'platform' => $sameIPSession->platform,
                        'other_user' => $sameIPSession->user->name
                    ];
                    
                    Auth::logout();
                    $this->request->session()->invalidate();
                    $this->request->session()->regenerateToken();
                    
                    // Save flash data after session regeneration
                    session()->flash('login_blocked', $blockedData);
                    
                    \Log::warning('Login blocked - IP already used by another karyawan', [
                        'user_id' => $user->id,
                        'blocked_ip' => $this->getClientIP($this->request),
                        'active_user_id' => $sameIPSession->user_id,
                        'active_user_name' => $sameIPSession->user->name
                    ]);
                    
                    return;
                }
                
                if ($existingSession) {
                    // Store session info for blocking alert
                    $blockedData = [
                        'device' => $existingSession->device_type,
                        'ip' => $existingSession->ip_address,
                        'login_time' => $existingSession->login_time->format('d M Y H:i'),
                        'browser' => $existingSession->browser,
                        'platform' => $existingSession->platform
                    ];
                    
                    // Force logout current login attempt
                    Auth::logout();
                    $this->request->session()->invalidate();
                    $this->request->session()->regenerateToken();
                    
                    // Save flash data after session regeneration
                    session()->flash('login_blocked', $blockedData);
                    
                    \Log::warning('Login blocked for karyawan - active session exists', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'blocked_ip' => $this->request->ip(),
                        'active_session_ip' => $existingSession->ip_address,
                        'active_session_device' => $existingSession->device_type
                    ]);
                    
                    return; // Stop login process
                }
            }
            
            // Create new session (only if not blocked)
            UserSession::createSession($user, $this->request);
            
            \Log::info('User login session created', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'session_id' => session()->getId(),
                'login_time' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create user login session', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP($request)
    {
        $ip = $request->ip();
        
        // Check for forwarded IP
        if ($request->header('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            $ip = trim($ips[0]);
        } elseif ($request->header('X-Real-IP')) {
            $ip = $request->header('X-Real-IP');
        } elseif ($request->header('HTTP_X_FORWARDED_FOR')) {
            $ips = explode(',', $request->header('HTTP_X_FORWARDED_FOR'));
            $ip = trim($ips[0]);
        }
        
        return $ip;
    }
}
