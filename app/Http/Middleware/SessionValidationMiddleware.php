<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate for authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Skip session validation for admin users (they can have multiple sessions)
        if (!$user->hasRole('karyawan')) {
            return $next($request);
        }

        // Skip session validation for login/logout routes and Flutter WebView to prevent CSRF issues
        $userAgent = $request->userAgent();
        $isFlutterWebView = strpos($userAgent, 'wv') !== false && strpos($userAgent, 'Chrome') !== false;
        
        if ($request->is('login') || $request->is('logout') || 
            $request->isMethod('POST') || $isFlutterWebView) {
            return $next($request);
        }

        $sessionId = session()->getId();

        // Check if session is still active in database (only for karyawan)
        try {
            $userSession = \App\Models\UserSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->active()
                ->first();

            if (!$userSession) {
                // Session not found or inactive, force logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('loginuser')
                    ->with('error', '⏰ Session Anda telah berakhir karena tidak aktif terlalu lama. Silakan login kembali.');
            }

            // Update last activity
            $userSession->update(['last_activity' => now()]);
        } catch (\Exception $e) {
            // Log error but don't block user
            \Log::error('Session validation error', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
        }

        return $next($request);
    }
}
