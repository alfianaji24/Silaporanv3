<!DOCTYPE html>
<html lang="id" class="h-full" style="background-color: {{ $general_setting->theme_color_1 ?? '#0a5c41' }};">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Login | {{ $general_setting->nama_aplikasi ?? 'Silaporan Mobile Apps' }}</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="{{ $general_setting->nama_aplikasi ?? 'SILAPORAN' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $general_setting->nama_aplikasi ?? 'SILAPORAN' }}">
    <meta name="description" content="Aplikasi Mobile Presensi GPS & HRMS Karyawan">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="{{ $general_setting->theme_color_1 ?? '#0a5c41' }}">
    <meta name="msapplication-navbutton-color" content="{{ $general_setting->theme_color_1 ?? '#0a5c41' }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Favicon & Apple Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="/manifest.json?v={{ file_exists(public_path('manifest.json')) ? filemtime(public_path('manifest.json')) : time() }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tabler Icons (Local + CDN) -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        theme: {
                            1: '{{ $general_setting->theme_color_1 ?? "#0a5c41" }}',
                            2: '{{ $general_setting->theme_color_2 ?? "#117956" }}',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Native mobile touch & viewport optimizations */
        html, body {
            height: 100%;
            width: 100%;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior: none;
            background-color: {{ $general_setting->theme_color_1 ?? '#0a5c41' }};
        }

        /* Safe area insets for iOS notch & home bar */
        .safe-top {
            padding-top: max(env(safe-area-inset-top, 0px), 16px);
        }
        .safe-bottom {
            padding-bottom: max(env(safe-area-inset-bottom, 0px), 16px);
        }
    </style>
</head>

<body class="h-full font-sans antialiased text-slate-800 flex flex-col justify-between min-h-screen relative overflow-x-hidden selection:bg-emerald-500/20"
      style="background: linear-gradient(180deg, {{ $general_setting->theme_color_1 ?? '#0a5c41' }} 0%, {{ $general_setting->theme_color_2 ?? '#117956' }} 100%);">

    <!-- Subtle Background Overlay Image -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <img src="{{ asset('assets/login/images/bg-login-v2.jpg') }}"
             alt="Office"
             class="w-full h-full object-cover object-center transform scale-110"
             style="opacity: 0.08; -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 40%, rgba(0,0,0,1) 100%); mask-image: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 40%, rgba(0,0,0,1) 100%);">
        <div class="absolute inset-0 bg-gradient-to-b from-black/10 via-transparent to-black/20"></div>
    </div>

    <!-- MAIN APP SCREEN CONTAINER -->
    <div class="relative z-10 flex flex-col flex-1 min-h-screen">

        <!-- HEADER / BRAND HERO SECTION (Native App Header) -->
        <header class="safe-top px-6 pt-7 pb-6 flex flex-col justify-end text-white">

            <!-- App Brand Row -->
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                        <img src="{{ asset('storage/logo/' . $general_setting->logo) }}"
                             alt="Logo"
                             class="w-11 h-11 rounded-full object-cover shrink-0 shadow-md border-2 border-white/30">
                    @else
                        <div class="w-11 h-11 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center shrink-0 shadow-md">
                            <i class="ti ti-layers text-xl text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-lg font-black tracking-tight leading-tight">
                            {{ $general_setting->nama_aplikasi ?? 'SILAPORAN' }}
                        </h1>
                        <span class="text-[11px] font-semibold text-white/80 uppercase tracking-wider block">
                            {{ $general_setting->nama_perusahaan ?? 'Mobile Attendance' }}
                        </span>
                    </div>
                </div>

                <!-- Online Status Pill -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 border border-white/20 text-[11px] font-medium text-white/90 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Online</span>
                </div>
            </div>

            <!-- Welcome Greeting -->
            <div class="mt-2 space-y-1">
                <h2 class="text-2xl font-black tracking-tight text-white">
                    Masuk ke Akun
                </h2>
                <p class="text-xs text-white/80 font-normal leading-relaxed">
                    Silakan isi kredensial akun karyawan Anda untuk melanjutkan presensi dan layanan HR.
                </p>
            </div>
        </header>

        <!-- BOTTOM SHEET CARD (Crisp, Native Mobile App Feel) -->
        <main class="flex-1 bg-white rounded-t-[32px] px-6 pt-7 pb-6 shadow-[0_-8px_30px_rgba(0,0,0,0.12)] flex flex-col justify-between">

            <div class="space-y-5">

                <!-- Pull Indicator / Handle Bar -->
                <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto -mt-3 mb-4"></div>

                <!-- Error Messages -->
                @if (session('error'))
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-xs text-rose-700">
                        <i class="ti ti-alert-circle text-base text-rose-600 shrink-0 mt-0.5"></i>
                        <div class="font-semibold">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-xs text-rose-700">
                        <i class="ti ti-alert-circle text-base text-rose-600 shrink-0 mt-0.5"></i>
                        <div class="font-semibold space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- NIK / Email Input -->
                    <div class="space-y-1.5">
                        <label for="id_user" class="text-xs font-bold text-slate-700 block">
                            Username / NIK / Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-user text-lg"></i>
                            </div>
                            <input type="text"
                                   name="id_user"
                                   id="id_user"
                                   value="{{ old('id_user') }}"
                                   placeholder="Contoh: 123456 atau email"
                                   required
                                   autocomplete="username"
                                   class="w-full h-12 pl-10 pr-4 bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-2xl text-xs sm:text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none transition">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-xs font-bold text-slate-700">
                                Password
                            </label>
                            <a href="javascript:void(0)"
                               onclick="alert('Silakan hubungi tim HRD / Administrator kantor Anda untuk bantuan reset kata sandi.')"
                               class="text-[11px] font-semibold text-slate-500 hover:text-slate-900">
                                Lupa Password?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-lock text-lg"></i>
                            </div>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="••••••••••••"
                                   required
                                   autocomplete="current-password"
                                   class="w-full h-12 pl-10 pr-12 bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-2xl text-xs sm:text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none transition">
                            <button type="button"
                                    id="btnTogglePassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition cursor-pointer"
                                    tabindex="-1">
                                <i class="ti ti-eye text-lg" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-0 cursor-pointer">
                            <span class="text-xs text-slate-600 font-medium">Simpan sesi login saya</span>
                        </label>
                    </div>

                    <!-- Primary Action Button (Native App Button Feel) -->
                    <button type="submit"
                            class="w-full h-12 rounded-2xl text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md active:scale-[0.98] transition cursor-pointer mt-3"
                            style="background-color: {{ $general_setting->theme_color_1 ?? '#0a5c41' }};">
                        <span>Masuk ke Akun</span>
                        <i class="ti ti-arrow-right text-base"></i>
                    </button>
                </form>

            </div>

            <!-- Footer Meta Info -->
            <footer class="safe-bottom pt-6 text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }} {{ $general_setting->nama_perusahaan ?? 'Company' }} • All rights reserved.
            </footer>

        </main>
    </div>

    <!-- Interactive Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Show/Hide Toggle
            const btnToggle = document.getElementById('btnTogglePassword');
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (btnToggle && passInput && eyeIcon) {
                btnToggle.addEventListener('click', function() {
                    const isPassword = passInput.type === 'password';
                    passInput.type = isPassword ? 'text' : 'password';
                    eyeIcon.className = isPassword ? 'ti ti-eye-off text-lg' : 'ti ti-eye text-lg';
                });
            }
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        registration.update();
                    })
                    .catch(function(err) {
                        console.log('SW registration error:', err);
                    });
            });
        }
    </script>
    @include('components.pwa-install-prompt')
</body>

</html>
