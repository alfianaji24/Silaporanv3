<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check if the session has expired
        if (!Auth::check()) {
            // Clear any existing session data
            $request->session()->flush();
            $request->session()->regenerate();

            // Set flash message for expired session
            session()->flash('message', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // For karyawan, redirect to loginuser route
        // For admin, redirect to login route
        // Since we can't check role here (user not authenticated), 
        // we'll use the URL to determine the appropriate route
        $currentUrl = $request->fullUrl();
        
        // If accessing karyawan-specific URLs or from mobile, use loginuser
        if (strpos($currentUrl, '/login') !== false || 
            $request->userAgent() && strpos($request->userAgent(), 'Mobile') !== false) {
            return route('loginuser');
        }
        
        // Default to admin login
        return route('login');
    }
}
