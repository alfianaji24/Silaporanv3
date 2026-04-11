<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IPBlacklistMiddleware
{
    /**
     * List of blacklisted IPs (can be from database, config, or external API)
     */
    private $blacklistedIPs = [
        // Static blacklist - add critical IPs here if needed
        // Example: '192.168.1.100'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientIP = $this->getClientIP($request);
        
        // Check if IP is blacklisted
        if ($this->isIPBlacklisted($clientIP)) {
            return $this->blockedResponse($clientIP);
        }

        // Log IP access for monitoring
        $this->logIPAccess($clientIP, $request);

        return $next($request);
    }

    /**
     * Get client real IP address
     */
    private function getClientIP(Request $request): string
    {
        // Check for Cloudflare, proxies, etc.
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
                // X-Forwarded-For can contain multiple IPs, get the first one
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                return $ip;
            }
        }

        return $request->ip();
    }

    /**
     * Check if IP is blacklisted
     */
    private function isIPBlacklisted(string $ip): bool
    {
        // Check static blacklist
        if (in_array($ip, $this->blacklistedIPs)) {
            return true;
        }

        // Check against database table
        if ($this->checkDatabaseBlacklist($ip)) {
            return true;
        }

        return false;
    }

    /**
     * Return blocked response
     */
    private function blockedResponse(string $ip): Response
    {
        return response()->view('errors.ip-blocked', [
            'ip' => $ip,
            'message' => 'Akses Anda dibatasi dikarenakan Anda memiliki riwayat jaringan buruk'
        ], 403);
    }

    /**
     * Log IP access for monitoring
     */
    private function logIPAccess(string $ip, Request $request): void
    {
        $logData = [
            'ip' => $ip,
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
            'method' => $request->method(),
            'headers' => [
                'cf-connecting-ip' => $request->header('CF-Connecting-IP'),
                'x-forwarded-for' => $request->header('X-Forwarded-For'),
                'x-real-ip' => $request->header('X-Real-IP'),
                'cf-ray' => $request->header('CF-Ray'),
                'cf-country' => $request->header('CF-IPCountry'),
            ]
        ];

        \Log::info('IP Access: ' . json_encode($logData));
    }

    /**
     * Check IP against database blacklist
     */
    private function checkDatabaseBlacklist(string $ip): bool
    {
        try {
            \Log::info('Checking IP Blacklist', ['ip' => $ip]);
            
            // Check if IP exists in ip_blacklists table and is not expired
            $blacklisted = \App\Models\IPBlacklist::where('ip_address', $ip)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                })
                ->first();
            
            $isBlocked = $blacklisted ? true : false;
            
            \Log::info('IP Blacklist Check Result', [
                'ip' => $ip, 
                'blacklisted' => $isBlocked,
                'expires_at' => $blacklisted ? $blacklisted->expires_at : null,
                'is_expired' => $blacklisted && $blacklisted->expires_at && $blacklisted->expires_at <= now(),
                'total_blacklisted' => \App\Models\IPBlacklist::count()
            ]);
            
            if ($isBlocked) {
                \Log::warning('IP Blocked from Database', ['ip' => $ip]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('Error checking IP blacklist database', [
                'ip' => $ip,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Example method to check against AbuseIPDB API
     */
    private function checkAbuseIPDB(string $ip): bool
    {
        // This is just an example - you'd need to implement actual API call
        // $apiKey = config('services.abuseipdb.key');
        // $response = Http::get("https://api.abuseipdb.com/api/v2/check", [
        //     'ipAddress' => $ip,
        //     'maxAgeInDays' => 90,
        //     'verbose' => ''
        // ], [
        //     'Key' => $apiKey,
        //     'Accept' => 'application/json'
        // ]);
        
        // return $response->json('data.abuseConfidenceScore', 0) > 50;
        
        return false; // Placeholder
    }
}
