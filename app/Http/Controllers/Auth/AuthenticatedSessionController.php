<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $agent = new \Jenssegers\Agent\Agent();
        $isV2 = function_exists('get_admin_theme') && get_admin_theme() === 'v2';

        if ($agent->isMobile()) {
            return $isV2 ? view('v2.auth.loginusermobile') : view('auth.loginusermobile');
        }

        if ($isV2) {
            return view('v2.auth.login');
        }
        return view('auth.login');
    }

    /**
     * Display the admin login view.
     */
    public function createAdmin(): View
    {
        session(['is_admin_route' => true]);
        if (function_exists('get_admin_theme') && get_admin_theme() === 'v2') {
            return view('v2.auth.login');
        }
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Check if block admin login is active
        $setting = \App\Models\Pengaturanumum::where('id', 1)->first();
        $user = auth()->user();

        if ($user) {
            $isAdminRoute = session()->get('is_admin_route');
            
            // Block karyawan on admin login page
            if ($isAdminRoute && $user->hasRole('karyawan')) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'id_user' => 'Karyawan tidak diperbolehkan login melalui halaman khusus Administrator.',
                ]);
            }
            
            // Block admin on main login page if setting is enabled
            if ($setting && $setting->block_admin_login && !$user->hasRole('karyawan') && !$isAdminRoute) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'id_user' => 'User tidak terdaftar.',
                ]);
            }

            // Block karyawan if web access is disabled
            if ($user->hasRole('karyawan')) {
                $userkaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
                if ($userkaryawan) {
                    $karyawan = \App\Models\Karyawan::where('nik', $userkaryawan->nik)->first();
                    if ($karyawan && $karyawan->status_akses_web === '0') {
                        auth()->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'id_user' => 'Akses login web untuk akun Anda dinonaktifkan. Silahkan gunakan aplikasi mobile Android.',
                        ]);
                    }
                }
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
        $user = Auth::user();
        $isAdmin = $user && !$user->hasRole('karyawan');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($isAdmin) {
            $setting = \App\Models\Pengaturanumum::where('id', 1)->first();
            if ($setting && $setting->block_admin_login) {
                $adminPath = $setting->admin_login_url ?: 'panel';
                return redirect($adminPath);
            }
        }

        return redirect('/');
    }
}
