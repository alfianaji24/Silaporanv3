@php
    $scheme = $general_setting->mobile_theme_scheme ?? config('themes.default', 'green');
    
    // Get theme colors from config/themes.php
    $themeSchemes = config('themes.schemes', []);
    
    // Extract only primary, primary_light, and bg_body for backward compatibility
    $themeData = $themeSchemes[$scheme] ?? $themeSchemes[config('themes.default', 'green')] ?? [];
    $t = [
        'primary' => $themeData['primary'] ?? '#32745e',
        'primary_light' => $themeData['primary_light'] ?? '#58907D',
        'bg_body' => $themeData['bg_body'] ?? '#f0fdf9',
    ];

    $isDark = false;

    // Share variables globally
    view()->share('isDark', $isDark);
    view()->share('t', $t);
    view()->share('scheme', $scheme);
@endphp
