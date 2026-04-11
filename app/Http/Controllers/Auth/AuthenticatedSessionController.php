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

        // Log login information
        $this->logUserLogin($request, $user);

        // Session management based on user role
        try {
            if ($user->hasRole('karyawan')) {
                // Check if user already has active session (but allow Flutter WebView reconnection)
                $userAgent = $request->userAgent();
                $isFlutterWebView = strpos($userAgent, 'wv') !== false && strpos($userAgent, 'Chrome') !== false;
                
                $existingSession = \App\Models\UserSession::where('user_id', $user->id)
                    ->active()
                    ->first();

                if ($existingSession && !$isFlutterWebView) {
                    // User already logged in on another device, block login (except Flutter WebView)
                    $deviceInfo = $existingSession->device_type ?? 'Unknown';
                    if ($existingSession->browser) {
                        $deviceInfo .= ' - ' . $existingSession->browser;
                    }
                    $loginTime = $existingSession->login_time ? $existingSession->login_time->format('d M Y H:i') : 'Unknown';
                    $ipAddress = $existingSession->ip_address ?? 'Unknown';

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('error', "🚫 AKUN SEDANG AKTIF DI PERANGKAT LAIN!\n\nDevice: {$deviceInfo}\nIP: {$ipAddress}\nLogin: {$loginTime}\n\nSilakan logout dari perangkat lain terlebih dahulu atau hubungi admin support untuk force logout.");
                }

                // For Flutter WebView, update existing session or create new one
                if ($isFlutterWebView && $existingSession) {
                    // Update existing session for Flutter WebView reconnection
                    $existingSession->update([
                        'session_id' => session()->getId(),
                        'ip_address' => \App\Models\UserSession::getClientIP($request),
                        'user_agent' => $request->userAgent(),
                        'last_activity' => now(),
                    ]);
                } else {
                    // Create new session for karyawan
                    \App\Models\UserSession::createSession($user, $request);
                }
            } else {
                // Admin session management (multiple devices allowed)
                \App\Models\UserSession::createSession($user, $request);
            }
        } catch (\Exception $e) {
            // Log error but continue with login
            \Log::error('Session management error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
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
        $sessionId = session()->getId();

        Auth::guard('web')->logout();

        // Update session status in database
        if ($user) {
            try {
                \App\Models\UserSession::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->active()
                    ->update([
                        'is_active' => false,
                        'logout_time' => now(),
                    ]);
            } catch (\Exception $e) {
                \Log::error('Failed to update session on logout', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Admin logout -> redirect to root URL (/)
        if ($isAdmin) {
            return redirect('/');
        }

        // Karyawan logout -> redirect to /login
        return redirect()->route('login');
    }

    /**
     * Log user login information including IP and device details
     */
    private function logUserLogin(Request $request, $user): void
    {
        $agent = new Agent();
        
        // Get client IP address (similar to IPBlacklistMiddleware)
        $clientIP = $this->getClientIP($request);
        
        // Get device information
        $deviceInfo = [
            'ip_address' => $clientIP,
            'user_agent' => $request->userAgent(),
            'device_type' => $this->getDeviceType($agent),
            'platform' => $agent->platform() ?: 'Unknown',
            'browser' => $agent->browser() ?: 'Unknown',
            'browser_version' => $agent->version($agent->browser()) ?: 'Unknown',
            'is_mobile' => $agent->isMobile(),
            'is_tablet' => $agent->isTablet(),
            'is_desktop' => $agent->isDesktop(),
            'languages' => $request->getLanguages(),
            'login_time' => now()->toISOString(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->hasRole('karyawan') ? 'karyawan' : 'admin/staff',
        ];

        // Log to Laravel log
        \Log::info('User Login', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $deviceInfo['user_role'],
            'ip_address' => $clientIP,
            'device_type' => $deviceInfo['device_type'],
            'platform' => $deviceInfo['platform'],
            'browser' => $deviceInfo['browser'] . ' ' . $deviceInfo['browser_version'],
            'login_time' => $deviceInfo['login_time'],
        ]);

        // Save to database
        try {
            \App\Models\UserLoginLog::create([
                'user_id' => $user->id,
                'ip_address' => $clientIP,
                'user_agent' => $request->userAgent(),
                'device_type' => $deviceInfo['device_type'],
                'platform' => $deviceInfo['platform'],
                'browser' => $deviceInfo['browser'],
                'browser_version' => $deviceInfo['browser_version'],
                'is_mobile' => $deviceInfo['is_mobile'],
                'is_tablet' => $deviceInfo['is_tablet'],
                'is_desktop' => $deviceInfo['is_desktop'],
                'languages' => $deviceInfo['languages'],
                'login_time' => now(),
                'session_id' => session()->getId(),
                'is_successful' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to save login log to database', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get client real IP address (similar to IPBlacklistMiddleware)
     */
    private function getClientIP(Request $request): string
    {
        $ipHeaders = [
            'CF-Connecting-IP',    // Cloudflare
            'X-Forwarded-For',     // General proxy
            'X-Real-IP',           // Nginx
            'X-Client-IP',         // Some proxies
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            $ip = $request->header($header);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                return $ip;
            }
        }

        return $request->ip();
    }

    /**
     * Get device type description
     */
    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isMobile()) {
            return 'Mobile';
        } elseif ($agent->isTablet()) {
            return 'Tablet';
        } elseif ($agent->isDesktop()) {
            return 'Desktop';
        } else {
            return 'Unknown';
        }
    }
}
