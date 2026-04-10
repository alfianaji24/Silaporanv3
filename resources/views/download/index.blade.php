<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Aplikasi Silaporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-6">
                <img src="{{ asset('assets/login/images/logo_silaporan.png') }}" alt="Silaporan Logo" class="mx-auto h-20 w-auto">
            </div>
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Silaporan</h1>
            <p class="text-xl text-gray-600 mb-2">Sistem Informasi Laporan Karyawan</p>
            <p class="text-lg text-blue-600 font-semibold">Download Aplikasi Resmi</p>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto">
            <!-- Flash Messages -->
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Security Notice -->
            <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-8 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-green-800">Link Download Resmi</h3>
                        <p class="mt-2 text-sm text-green-700">
                            Halaman ini merupakan halaman download resmi dari sistem Silaporan. 
                            Anda berada di website resmi perusahaan. Download aplikasi hanya melalui halaman ini untuk memastikan keamanan dan keaslian aplikasi.
                        </p>
                    </div>
                </div>
                
                <!-- IP Information -->
                <div class="bg-white rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-800">IP Address Anda:</p>
                                <p class="text-lg font-mono text-green-600" id="userIP">Memuat...</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Domain:</p>
                            <p class="text-sm font-mono text-gray-800" id="currentDomain">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Cards -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Android Download -->
                <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                    <div class="text-center mb-4">
                        <div class="bg-green-100 rounded-full p-4 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85-.29-.15-.65-.06-.83.22l-1.88 3.24c-2.86-1.21-6.08-1.21-8.94 0L5.65 5.67c-.19-.29-.54-.38-.83-.22-.3.16-.42.54-.26.85L6.4 9.48C3.3 11.25 1.28 14.44 1 18h22c-.28-3.56-2.3-6.75-5.4-8.52zM7 15.25c-.69 0-1.25-.56-1.25-1.25s.56-1.25 1.25-1.25 1.25.56 1.25 1.25-.56 1.25-1.25 1.25zm10 0c-.69 0-1.25-.56-1.25-1.25s.56-1.25 1.25-1.25 1.25.56 1.25 1.25-.56 1.25-1.25 1.25z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Android</h3>
                        <p class="text-gray-600 mb-4">Aplikasi mobile untuk perangkat Android</p>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('download.apk') }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center inline-block text-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download APK
                        </a>
                        <p class="text-xs text-gray-500 text-center">Versi 3.0.0 • Size: 45MB</p>
                    </div>
                </div>

                <!-- IP Phone Download -->
                <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                    <div class="text-center mb-4">
                        <div class="bg-blue-100 rounded-full p-4 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Apple (iOS)</h3>
                        <p class="text-gray-600 mb-4">Aplikasi mobile untuk Apple (iOS)</p>
                    </div>
                    <div class="space-y-3">
                        <button onclick="showIOSInstructions()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Petunjuk Instalasi iOS
                        </button>
                        <p class="text-xs text-gray-500 text-center">Versi 3.0.0 • PWA untuk iOS</p>
                    </div>
                </div>
            </div>

            <!-- Installation Instructions -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Petunjuk Instalasi</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Android:</h4>
                        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                            <li>Download file APK dari tombol di atas</li>
                            <li>Buka Settings → Security → Unknown Sources</li>
                            <li>Aktifkan "Allow installation from unknown sources"</li>
                            <li>Buka file APK yang telah diunduh</li>
                            <li>Ikuti proses instalasi hingga selesai</li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">iOS (PWA):</h4>
                        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                            <li>Buka website Silaporan di Safari iOS</li>
                            <li>Klik tombol "Share" (ikon kotak dengan panah)</li>
                            <li>Scroll dan pilih "Add to Home Screen"</li>
                            <li>Konfirmasi nama aplikasi "Silaporan"</li>
                            <li>Klik "Add" untuk menambahkan ke homescreen</li>
                            <li>Aplikasi akan muncul di homescreen seperti app native</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Butuh Bantuan?</h3>
                <p class="text-gray-600 mb-4">Hubungi IT Support untuk bantuan instalasi dan troubleshooting</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        support@puskesmasbalaraja.com
                    </div>
                    <!-- <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Ext: 1234
                    </div> -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-gray-600">
            <p class="text-sm">© {{ date('Y') }} Silaporan - Sistem Informasi Laporan Karyawan</p>
            <p class="text-xs mt-2">Halaman download resmi • Jangan download dari sumber lain untuk keamanan data Anda</p>
        </div>
    </div>

    <script>
        // Get user IP address
        async function getUserIP() {
            try {
                // Try to get IP from a public API
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                return data.ip;
            } catch (error) {
                // Fallback to a different API
                try {
                    const response = await fetch('https://api.ip.sb/ip');
                    return await response.text();
                } catch (error) {
                    // Final fallback - use a placeholder
                    return 'Tidak dapat mendeteksi';
                }
            }
        }

        // Display IP and Domain information
        async function displayConnectionInfo() {
            const ipElement = document.getElementById('userIP');
            const domainElement = document.getElementById('currentDomain');
            
            // Display domain
            const currentDomain = window.location.hostname;
            domainElement.textContent = currentDomain;
            
            // Display IP with loading state
            ipElement.textContent = 'Mendeteksi...';
            
            const userIP = await getUserIP();
            ipElement.textContent = userIP;
            
            // Add click to copy functionality
            ipElement.style.cursor = 'pointer';
            ipElement.title = 'Klik untuk menyalin IP';
            ipElement.addEventListener('click', function() {
                navigator.clipboard.writeText(userIP).then(() => {
                    const originalText = ipElement.textContent;
                    ipElement.textContent = 'Tersalin!';
                    ipElement.classList.add('text-green-800');
                    
                    setTimeout(() => {
                        ipElement.textContent = originalText;
                        ipElement.classList.remove('text-green-800');
                    }, 2000);
                });
            });
        }

        // Show iOS installation instructions
        function showIOSInstructions() {
            const instructions = `
Cara Install PWA di iOS:

1. Buka website Silaporan (https://silaporan.puskesmasbalaraja.com/login) di Safari
2. Klik tombol "Share" (ikon kotak dengan panah) di bawah
3. Scroll dan pilih "Add to Home Screen"
4. Konfirmasi nama "Silaporan"
5. Klik "Add" untuk menambahkan ke homescreen
6. Aplikasi Silaporan akan muncul di homescreen Anda

Keuntungan PWA:
• Tidak perlu download dari App Store
• Update otomatis
• Offline mode (fitur tertentu)
• Performa cepat seperti app native
• Aman karena langsung dari website resmi
            `;
            
            alert(instructions);
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('PWA ServiceWorker registration successful');
                    })
                    .catch(function(error) {
                        console.log('PWA ServiceWorker registration failed');
                    });
            });
        }

        // Security: Verify domain for production
        const currentDomain = window.location.hostname;
        const allowedDomains = ['localhost', '127.0.0.1', 'silaporan.puskesmasbalaraja.com'];
        
        if (!allowedDomains.includes(currentDomain)) {
            document.body.innerHTML = `
                <div class="flex items-center justify-center min-h-screen bg-red-50">
                    <div class="text-center p-8 max-w-md">
                        <div class="mb-4">
                            <svg class="w-16 h-16 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-red-600 mb-4">Peringatan Keamanan</h1>
                        <p class="text-gray-700 mb-4">
                            Halaman ini tidak berada di domain resmi. Domain saat ini: <strong>${currentDomain}</strong>
                        </p>
                        <p class="text-gray-600 mb-4">
                            Mohon download aplikasi melalui website resmi:<br>
                            <strong>silaporan.puskesmasbalaraja.com</strong>
                        </p>
                        <div class="bg-red-100 border border-red-300 rounded-lg p-4 text-sm text-red-800">
                            <strong>⚠️ Risiko Keamanan:</strong><br>
                            Download dari sumber tidak resmi dapat membahayakan data dan perangkat Anda.
                        </div>
                    </div>
                </div>
            `;
        } else {
            console.log('Security: Domain verified -', currentDomain);
        }

        // Initialize connection info when page loads
        document.addEventListener('DOMContentLoaded', function() {
            displayConnectionInfo();
        });
    </script>
</body>
</html>
