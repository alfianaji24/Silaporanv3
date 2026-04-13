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
            // Allow access to password change routes and logout
            $allowedRoutes = [
                'password.change',
                'password.update',
                'logout',
                'login'
            ];

            // If user needs to change password, allow access to dashboard
            // because the password change popup will be displayed there.
            $allowedRoutes[] = 'dashboard.index';
            
            if (!$request->routeIs($allowedRoutes)) {
                // Redirect employees to dashboard (will show popup)
                return redirect()->route('dashboard.index')
                    ->with('warning', 'Anda wajib mengganti password default sebelum menggunakan akun ini.');
            }
        }
        
        return $next($request);
    }
}
