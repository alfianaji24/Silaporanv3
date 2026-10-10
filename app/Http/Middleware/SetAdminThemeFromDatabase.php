<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminThemeFromDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!app()->runningInConsole()) {
            try {
                $setting = \App\Models\Pengaturanumum::where('id', 1)->first();
                $theme = $setting->admin_theme ?? 'v2';
                session(['admin_theme' => $theme]);
            } catch (\Exception $e) {
                // Ignore if db error
            }
        }

        return $next($request);
    }
}
