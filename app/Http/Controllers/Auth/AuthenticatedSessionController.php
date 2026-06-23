<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserLoginLog;
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
        // Show loginuser.blade.php for /login route
        if ($request->route()->getName() === 'login') {
            return view('auth.loginuser');
        }
        
        // Default to loginuser.blade.php
        return view('auth.loginuser');
    }

    /**
     * Handle an incoming authentication request.
     * /login = all users (admin & karyawan) | / = user only (non-karyawan)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $loginType = $request->input('login_type', 'user');
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($loginType === 'karyawan') {
            $isKaryawan = $user && $user->roles->contains('name', 'karyawan');
            if (!$isKaryawan) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Mohon dapat menggunakan login administrator');
            }
        }
        // Remove the restriction for karyawan users trying to login via /login
        // Now both admin and karyawan can use the same login page

        $request->session()->regenerate();

        // Record successful login into user login log
        UserLoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
        ]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $agent = new \Jenssegers\Agent\Agent();
        $isMobile = $agent->isMobile();
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isAdmin = $user && !$user->roles->contains('name', 'karyawan');

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
