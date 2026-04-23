<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .error-container {
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="error-container max-w-2xl w-full">
            <!-- Mobile & Desktop Responsive Layout -->
            <div class="text-center">
                <!-- Error Icon -->
                <div class="floating mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 md:w-32 md:h-32 bg-white/20 backdrop-blur-sm rounded-full border-4 border-white/30">
                        <ion-icon name="lock-closed-outline" class="text-white text-4xl md:text-5xl"></ion-icon>
                    </div>
                </div>

                <!-- Error Code -->
                <h1 class="text-6xl md:text-8xl font-bold text-white mb-4">
                    403
                </h1>

                <!-- Error Title -->
                <h2 class="text-2xl md:text-3xl font-semibold text-white mb-4">
                    Akses Ditolak
                </h2>

                <!-- Error Description -->
                <p class="text-white/80 text-base md:text-lg mb-8 max-w-md mx-auto">
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <!-- Back Button -->
                    <button onclick="history.back()" class="w-full sm:w-auto px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-lg border border-white/30 hover:bg-white/30 transition-all duration-300 flex items-center justify-center gap-2">
                        <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                        <span>Kembali</span>
                    </button>

                    <!-- Dashboard Button -->
                    <a href="{{ route('dashboard.index') }}" class="w-full sm:w-auto px-6 py-3 bg-white text-purple-600 rounded-lg hover:bg-gray-100 transition-all duration-300 flex items-center justify-center gap-2 font-semibold">
                        <ion-icon name="home-outline" class="text-xl"></ion-icon>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Additional Info -->
                <div class="mt-12 text-white/60 text-sm">
                    <p class="mb-2">Error Code: 403 - Forbidden</p>
                    <p>Timestamp: {{ now()->format('d M Y H:i:s') }}</p>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="fixed top-10 left-10 w-20 h-20 bg-white/10 rounded-full pulse"></div>
            <div class="fixed bottom-10 right-10 w-32 h-32 bg-white/10 rounded-full pulse" style="animation-delay: 1s;"></div>
            <div class="fixed top-1/2 right-20 w-16 h-16 bg-white/10 rounded-full pulse" style="animation-delay: 2s;"></div>
        </div>
    </div>

    <!-- Optional: Add JavaScript for additional interactions -->
    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Parallax effect on mouse move (desktop only)
            if (window.innerWidth > 768) {
                document.addEventListener('mousemove', function(e) {
                    const x = e.clientX / window.innerWidth;
                    const y = e.clientY / window.innerHeight;
                    
                    const floatingElements = document.querySelectorAll('.floating');
                    floatingElements.forEach((el, index) => {
                        const speed = (index + 1) * 10;
                        el.style.transform = `translateY(${y * speed}px) translateX(${x * speed}px)`;
                    });
                });
            }

            // Add ripple effect on button clicks
            const buttons = document.querySelectorAll('button, a');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.classList.add('absolute', 'bg-white/30', 'rounded-full', 'animate-ping');
                    ripple.style.width = ripple.style.height = '20px';
                    ripple.style.left = e.offsetX - 10 + 'px';
                    ripple.style.top = e.offsetY - 10 + 'px';
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        });
    </script>
</body>
</html>
