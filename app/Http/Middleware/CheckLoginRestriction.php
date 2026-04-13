<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSession;

class CheckLoginRestriction
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check for login attempts
        if ($request->isMethod('POST') && $request->routeIs('login')) {
            \Log::info('CheckLoginRestriction middleware triggered', [
                'route_name' => $request->route()->getName(),
                'method' => $request->method(),
                'email' => $request->input('email')
            ]);
            
            $credentials = $request->only('email', 'password');
            
            // Try to authenticate user to check role
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                
                \Log::info('User authenticated in middleware', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'is_karyawan' => $user->hasRole('karyawan')
                ]);
                
                // Check if user is karyawan
                if ($user->hasRole('karyawan')) {
                    // Check for existing active sessions (single device rule)
                    $existingSession = UserSession::where('user_id', $user->id)
                        ->active()
                        ->first();
                    
                    \Log::info('Checking existing sessions for karyawan', [
                        'user_id' => $user->id,
                        'existing_session_found' => $existingSession ? true : false
                    ]);
                    
                    // Logout the authenticated user
                    Auth::logout();
                    
                    if ($existingSession) {
                        \Log::info('Login blocked for karyawan - existing session found', [
                            'user_id' => $user->id,
                            'existing_session_id' => $existingSession->session_id
                        ]);
                        
                        // Block login if user already has active session
                        return redirect()->route('loginuser')
                            ->withInput($request->except('password'))
                            ->with('login_blocked', [
                                'device' => $existingSession->device_type,
                                'ip' => $existingSession->ip_address,
                                'login_time' => $existingSession->login_time->format('d M Y H:i'),
                                'browser' => $existingSession->browser,
                                'platform' => $existingSession->platform
                            ]);
                    }
                }
                
                // Logout temporary authentication
                Auth::logout();
                
                \Log::info('Temporary authentication logged out, proceeding with login');
            } else {
                \Log::warning('Authentication failed in CheckLoginRestriction middleware', [
                    'email' => $request->input('email')
                ]);
            }
        } else {
            \Log::info('CheckLoginRestriction middleware bypassed', [
                'method' => $request->method(),
                'route_name' => $request->route() ? $request->route()->getName() : 'no route',
                'is_post' => $request->isMethod('POST'),
                'is_login_route' => $request->routeIs('login')
            ]);
        }
        
        return $next($request);
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
