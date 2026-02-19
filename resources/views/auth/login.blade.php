<!DOCTYPE html>
<html lang="id" class="light-style" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title>SILAPORAN | Puskesmas Balaraja</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Silaporan V3">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Silaporan V3">
    <meta name="description" content="Aplikasi Presensi GPS untuk Karyawan">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="{{ optional($general_setting)->theme_color_1 ?? '#696cff' }}">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/assets/img/icons/pwa/icon-512x512.png">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />

    <style>
        :root {
            --primary: {{ optional($general_setting)->theme_color_1 ?? '#696cff' }};
            --primary-hover: {{ optional($general_setting)->theme_color_2 ?? '#5f61e6' }};
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Public Sans', sans-serif; background: #fff; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 9rem 1.5rem; }
        .login-container { width: 100%; max-width: 400px; }

        .logo-section { text-align: center; margin-bottom: 2rem; }
        .logo-section img { height: 56px; width: auto; margin-bottom: 0.5rem; }
        .logo-section .brand { font-size: 1.5rem; font-weight: 700; color: #2d3748; }
        .logo-section .brand span { color: var(--primary); }

        .page-title { font-size: 1.75rem; font-weight: 700; color: #1a202c; text-align: center; margin-bottom: 1.75rem; }

        .form-group { margin-bottom: 1.25rem; }
        .input-wrap { position: relative; display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 0.5rem; background: #fff; transition: border-color 0.2s; }
        .input-wrap:focus-within { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.2); }
        .input-wrap .icon { padding: 0 1rem; color: #a0aec0; font-size: 1.25rem; }
        .input-wrap input { flex: 1; border: none; outline: none; padding: 0.875rem 1rem; font-size: 1rem; background: transparent; }
        .input-wrap input::placeholder { color: #a0aec0; }
        .input-wrap .toggle-pwd { padding: 0 1rem; color: #a0aec0; cursor: pointer; font-size: 1.25rem; background: none; border: none; }
        .input-wrap .toggle-pwd:hover { color: var(--primary); }

        .options-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem; }
        .remember { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #4a5568; cursor: pointer; }
        .remember input { width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer; }
        .forgot-link { font-size: 0.875rem; color: var(--primary); text-decoration: none; font-weight: 500; }
        .forgot-link:hover { text-decoration: underline; }

        .terms-text { font-size: 0.8125rem; color: #718096; margin-bottom: 0.5rem; line-height: 1.4; }
        .terms-text a { color: var(--primary); text-decoration: none; }
        .terms-text a:hover { text-decoration: underline; }
        .terms-links { display: flex; flex-direction: column; gap: 0.25rem; margin-bottom: 1.5rem; }
        .terms-links a { font-size: 0.875rem; color: var(--primary); text-decoration: none; }
        .terms-links a:hover { text-decoration: underline; }

        .btn-masuk { width: 100%; padding: 0.875rem 1.5rem; background: var(--primary); color: #fff; border: none; border-radius: 0.5rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-masuk:hover { background: var(--primary-hover); }

        .alt-options { margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: #718096; }
        .alt-options a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .alt-options a:hover { text-decoration: underline; }
        .alt-options > div { margin-bottom: 0.5rem; }

        .alert { padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .alert-danger { background: #fed7d7; color: #c53030; border: 1px solid #feb2b2; }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-section">
            @if (!empty(optional($general_setting)->logo) && Storage::disk('public')->exists('logo/' . optional($general_setting)->logo))
                <img src="{{ asset('storage/logo/' . optional($general_setting)->logo) }}" alt="Logo" />
            @else
                <img src="{{ asset('assets/login/images/logoweb-1.png') }}" alt="Logo" />
            @endif
            <div class="brand">{{ config('app.name') }}</div>
        </div>

        <h1 class="page-title">Masuk</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="login_type" value="karyawan">
            <div class="form-group">
                <div class="input-wrap">
                    <span class="icon"><i class="ti ti-mail"></i></span>
                    <input type="text" name="id_user" value="{{ old('id_user') }}" placeholder="Email atau Username *" autocomplete="username" required autofocus />
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrap">
                    <span class="icon"><i class="ti ti-lock"></i></span>
                    <input type="password" name="password" id="password" placeholder="Kata Sandi *" autocomplete="current-password" required />
                    <button type="button" class="toggle-pwd" onclick="togglePassword()" aria-label="Tampilkan sandi">
                        <i class="ti ti-eye-off" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="options-row">
                <label class="remember">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                    Ingat saya
                </label>
                @if (Route::has('register'))
                <div>Lupa Kata Sandi? <a href="#" id="linkDaftar">Lupa Kata Sandi?</a>
                @endif
            </div>

            <!-- <div style="text-align: center;">
                <div class="terms-text" style="margin-bottom: 6px;">
                    * Dengan masuk Saya Setuju dengan semua
                </div>
                <div class="terms-links">
                    <a href="#" style="margin-right:10px;">Syarat dan Ketentuan</a>
                    <a href="#">Kebijakan Privasi</a>
                </div>
            </div> -->

            <button type="submit" class="btn-masuk">Masuk</button>
        </form>

        <div class="alt-options">
            @if (Route::has('register'))
            <div>Tidak punya akun? <a href="#" id="linkDaftar">Daftar</a></div>
            @endif
        </div>
    </div>

    <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            }
        }

        document.getElementById('linkDaftar')?.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Daftar Akun?",
                text: "Silahkan Hubungi IT Support!!!",
                icon: "info"
            });
        });

        @if (session('error'))
        Swal.fire({
            title: "Akses Ditolak",
            text: "{{ session('error') }}",
            icon: "error"
        });
        @endif
    </script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>

    <!-- PWA Install Prompt -->
    @include('components.pwa-install-prompt')
</body>

</html>
