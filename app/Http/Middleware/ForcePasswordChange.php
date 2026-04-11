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
        if (Auth::check() && Auth::user()->password_change_required) {
            // Allow access to password change routes and logout
            $allowedRoutes = [
                'password.change',
                'password.update',
                'logout',
                'force.password.change',
                'force.password.update'
            ];

            // If user is employee and needs to change password, allow access to dashboard
            // because the password change popup will be displayed there.
            if (Auth::user()->hasRole('karyawan')) {
                $allowedRoutes[] = 'dashboard.index';
            }
            
            if (!$request->routeIs($allowedRoutes)) {
                // Only redirect employees to dashboard (will show popup)
                // Admin/staff can continue without forced password change
                if (Auth::user()->hasRole('karyawan')) {
                    return redirect()->route('dashboard.index');
                }
                // For admin/staff, just continue without forcing password change
            }
        }
        
        return $next($request);
    }
}
