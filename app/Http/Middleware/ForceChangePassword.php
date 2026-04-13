<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip jika user belum login
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Skip untuk non-karyawan
        if (!$user->hasRole('karyawan')) {
            return $next($request);
        }

        // Skip jika sudah mengganti password
        if ($user->password_changed_at !== null) {
            return $next($request);
        }

        // Skip jika route yang diakses adalah change password atau logout
        $excludedRoutes = [
            'password.change',
            'password.update',
            'logout',
            'login'
        ];

        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }

        // Redirect ke halaman change password
        return redirect()->route('password.change')
            ->with('warning', 'Anda wajib mengganti password default sebelum menggunakan akun ini.');
    }
}
