<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class Prevent419Errors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Redirect to login with specific message
            if (auth()->check() && auth()->user()->hasRole('karyawan')) {
                return redirect()->route('loginuser')
                    ->with('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
            } else {
                return redirect()->route('login')
                    ->with('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
        }
    }
}
