<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->password_changed_at === null && Auth::user()->hasRole('karyawan')) {
            // Allow access to dashboard, settings, and basic routes
            $allowedRoutes = [
                'dashboard.index',
                'profile.index',  // Settings/Profile page
                'password.update', // Password change form
                'logout',
                'login'
            ];
            
            if (!$request->routeIs($allowedRoutes)) {
                // Redirect to dashboard with message to change password
                return redirect()->route('dashboard.index')
                    ->with('password_required', 'Mohon ubah password terlebih dahulu di menu Setting untuk mengakses fitur lainnya.');
            }
        }
        
        return $next($request);
    }
}
