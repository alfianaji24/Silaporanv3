<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir - Karyawan</title>
    @include('layouts.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .bounce {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            60% {
                transform: translateY(-15px);
            }
        }

        .rotate-slow {
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body class="flex items-center justify-center p-4 bg-pattern">
    <!-- Animated background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-16 h-16 bg-white opacity-10 rounded-full rotate-slow"></div>
        <div class="absolute bottom-20 right-20 w-24 h-24 bg-white opacity-10 rounded-full rotate-slow" style="animation-delay: -5s;"></div>
        <div class="absolute top-1/3 right-1/4 w-12 h-12 bg-white opacity-10 rounded-full rotate-slow" style="animation-delay: -10s;"></div>
        <div class="absolute bottom-1/3 left-1/4 w-20 h-20 bg-white opacity-10 rounded-full rotate-slow" style="animation-delay: -15s;"></div>
    </div>

    <div class="w-full max-w-lg relative z-10">
        <div class="glass-effect rounded-3xl p-8 card-hover" data-aos="fade-up" data-aos-duration="1000">
            <div class="text-center">
                <!-- Custom illustration for karyawan -->
                <div class="mb-8 bounce" data-aos="zoom-in" data-aos-delay="200">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 mx-auto bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <h1 class="text-4xl font-bold gradient-text mb-4" data-aos="fade-up" data-aos-delay="400">
                    Oops! Sesi Berakhir
                </h1>

                <p class="text-gray-600 mb-6 text-lg" data-aos="fade-up" data-aos-delay="600">
                    Hai, Karyawan! 👋<br>
                    Sesi Anda telah berakhir karena alasan keamanan. Silakan login kembali untuk melanjutkan aktivitas Anda.
                </p>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-8" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-purple-700">
                            <strong>Info:</strong> Ini adalah langkah keamanan untuk melindungi data Anda.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center" data-aos="fade-up" data-aos-delay="1000">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-800 text-white font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-purple-900 transition duration-300 ease-in-out transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Login Karyawan
                    </a>
                    
                    <button onclick="window.history.back()" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition duration-300 ease-in-out transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </button>
                </div>

                <p class="text-xs text-gray-500 mt-6" data-aos="fade-up" data-aos-delay="1200">
                    Butuh bantuan? Hubungi HR Department
                </p>
            </div>
        </div>
    </div>

    <script>
        AOS.init({
            once: true,
            offset: 50
        });

        // Auto redirect after 10 seconds
        let countdown = 10;
        const countdownElement = document.createElement('div');
        countdownElement.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 text-sm';
        countdownElement.innerHTML = `Auto redirect in <span class="font-bold text-purple-600">${countdown}</span> detik...`;
        document.body.appendChild(countdownElement);

        const timer = setInterval(() => {
            countdown--;
            countdownElement.innerHTML = `Auto redirect in <span class="font-bold text-purple-600">${countdown}</span> detik...`;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '{{ route('login') }}';
            }
        }, 1000);

        // Cancel auto redirect on user interaction
        document.addEventListener('click', () => {
            clearInterval(timer);
            if (countdownElement.parentNode) {
                countdownElement.parentNode.removeChild(countdownElement);
            }
        });
    </script>
</body>

</html>
