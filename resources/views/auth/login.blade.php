<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name') }}{{ !empty($general_setting->nama_perusahaan) ? ' | ' . $general_setting->nama_perusahaan : '' }}</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="description" content="Aplikasi Presensi GPS untuk Karyawan">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#696cff">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/assets/img/icons/pwa/icon-512x512.png">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    @include('layouts.favicon')

    <link rel="stylesheet" href="{{ asset('assets/login/css/style.css') }}" />
    <style>
        :root {
            /* Dynamic Theme Colors */
            --theme-color-1: {{ $general_setting->theme_color_1 ?? '#053b22' }};
            --theme-color-2: {{ $general_setting->theme_color_2 ?? '#0b6a3a' }};
        }

        .sign-btn {
            background-color: var(--theme-color-1) !important;
        }

        .sign-btn:hover {
            background-color: var(--theme-color-2) !important;
        }

        .sign-btn:disabled {
            opacity: 0.8;
            cursor: not-allowed;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .bullets span.active {
            background-color: var(--theme-color-1) !important;
        }

        .carousel {
            background: var(--theme-color-1) !important;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            animation: slideIn 0.5s ease-out;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .text-group h2 {
            color: #ffffff !important;
        }

        .logo h4 {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.25;
            margin-top: 0.35rem;
        }

        .heading h2 {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.25;
            margin-top: 0.35rem;
            text-align: center;
        }
    </style>

</head>

<body>
    <main>
        <div class="box">
            <div class="inner-box">
                <div class="forms-wrap">
                    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="login_type" value="user">
                        <div class="logo">
                            @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                                <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Company Logo" style="height: auto; width: 80px; margin-bottom: 20px;" />
                            @else
                                <img src="{{ asset('assets/login/images/logo_silaporan.png') }}" alt="easyclass" />
                            @endif
                            <h4>{{ config('app.name') }}</h4>
                        </div>
                        <div class="heading">
                            <h2>Masuk</h2>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        @endif

                        <div class="actual-form">
                            <div class="input-wrap">
                                <input type="text" minlength="4" class="input-field @error('id_user') is-invalid @enderror" name="id_user"
                                    value="{{ old('id_user') }}" autocomplete="off" placeholder="Username / Email" required />
                                {{-- @error('id_user')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror --}}
                            </div>

                            <div class="input-wrap">
                                <input type="password" minlength="4" name="password" class="input-field @error('password') is-invalid @enderror"
                                    autocomplete="off" placeholder="Password" required />
                                {{-- @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror --}}
                            </div>

                            <div class="checkbox-wrap">
                                <input type="checkbox" id="remember" name="remember" style="margin-right: 8px; width: 16px; height: 16px;">
                                <label for="remember" style="color: #666; font-size: 14px; cursor: pointer; margin-left: 20px;">Remember Me</label>
                            </div>

                            <input type="submit" value="Sign In" class="sign-btn" id="btn-signin-submit" />

                            <!-- <p class="text">
                                Forgotten your password or you login datails?
                                <a href="#">Get help</a> signing in
                            </p> -->

                        </div>
                    </form>
                </div>
                <div class="carousel">
                    <div class="images-wrapper">
                        <img src="./img/image1.png" class="image img-1 show" alt="" />
                        <img src="./img/image2.png" class="image img-2" alt="" />
                        <img src="./img/image3.png" class="image img-3" alt="" />
                        {{-- Tambah gambar: <img src="./img/image4.png" class="image img-4" alt="" /> --}}
                    </div>
                    <div class="text-slider">
                        <div class="text-wrap">
                            <div class="text-group">
                                <h2>Absen Sat-Set, Kerja Jadi Nyaman!</h2>
                                <h2>Disiplin Tanpa Beban, Prestasi Tanpa Batas.</h2>
                                <h2>Kelola Kehadiranmu, Tingkatkan Kualitas Kerjamu.</h2>
                                {{-- Tambah kalimat: <h2>Teks kalimat ke-4 disini.</h2> --}}
                            </div>
                        </div>
                        <div class="bullets">
                            <span data-value="1"></span>
                            <span data-value="2"></span>
                            <span data-value="3"></span>
                            {{-- Tambah slide: <span data-value="4"></span> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- SweetAlert -->
    <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
    <!-- Javascript file -->
    <script src="{{ asset('assets/login/scripts/app.js') }}"></script>

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

    <!-- SweetAlert untuk error login -->
    @if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "Akses Ditolak",
                text: "{{ session('error') }}",
                icon: "error"
            });
        });
    </script>
    @endif

    <!-- Loading animation for login form -->
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = document.getElementById('btn-signin-submit');
            btn.disabled = true;
            btn.value = 'Tunggu...';
            // Add spinner to button
            btn.style.position = 'relative';
            btn.innerHTML = '<div class="spinner" style="margin-right: 8px;"></div>Tunggu...';
        });
    </script>

    <!-- PWA Install Prompt - Only on Login Page -->
    @include('components.pwa-install-prompt')
</body>

</html>
