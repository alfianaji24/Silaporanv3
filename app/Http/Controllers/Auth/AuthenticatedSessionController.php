<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        // Show login.blade.php for /login route (supports both admin & karyawan)
        if ($request->route()->getName() === 'login') {
            return view('auth.login');
        }
        
        // Default to login.blade.php (for admin)
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * /login = all users (admin & karyawan) | / = user only (non-karyawan)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $loginType = $request->input('login_type', 'user');
        $user = Auth::user();

        if ($loginType === 'karyawan') {
            if (!$user->hasRole('karyawan')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Mohon dapat menggunakan login administrator');
            }
        }
        // Remove the restriction for karyawan users trying to login via /login
        // Now both admin and karyawan can use the same login page

        $request->session()->regenerate();

        // Session creation is now handled by LogUserLogin event listener
        // This ensures real-time session tracking for all users

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $agent = new \Jenssegers\Agent\Agent();
        $isMobile = $agent->isMobile();
        $user = Auth::user();
        $isAdmin = $user && !$user->hasRole('karyawan');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Admin logout -> redirect to root URL (/)
        if ($isAdmin) {
            return redirect('/');
        }

        // Karyawan logout -> redirect to /login (loginuser route for karyawan)
        return redirect()->route('loginuser');
    }
}
