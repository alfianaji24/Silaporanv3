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
        // If accessed via /login route, show loginuser.blade.php (for karyawan)
        if ($request->route()->getName() === 'login') {
            return view('auth.loginuser');
        }
        
        // Default to login.blade.php (for admin)
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * /login = karyawan only | / = user only (non-karyawan)
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
        } else {
            if ($user->hasRole('karyawan')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('loginuser')->with('error', 'Anda Tidak Memiliki Akses Untuk Login, Silahkan Hubungi IT Support!!!');
            }
        }

        $request->session()->regenerate();

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

        // Karyawan logout -> redirect to /login
        return redirect()->route('login');
    }
}
