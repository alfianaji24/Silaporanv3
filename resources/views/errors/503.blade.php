<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Perbaikan</title>
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
            overflow-x: hidden;
        }
        .maintenance-container {
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
        .gear-spin {
            animation: spin 8s linear infinite;
        }
        .gear-spin-reverse {
            animation: spin-reverse 6s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes spin-reverse {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }
        .pulse-dot {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }
        .progress-fill {
            animation: progressFill 3s ease-out forwards;
        }
        @keyframes progressFill {
            from { width: 0%; }
            to { width: 75%; }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="maintenance-container max-w-4xl w-full">
            <!-- Animated Background Elements -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full gear-spin"></div>
                <div class="absolute bottom-20 right-20 w-24 h-24 bg-white/10 rounded-full gear-spin-reverse"></div>
                <div class="absolute top-1/3 right-1/4 w-16 h-16 bg-white/10 rounded-full pulse-dot"></div>
            </div>

            <!-- Main Content -->
            <div class="text-center text-white">
                <!-- Maintenance Icon -->
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center relative">
                        <!-- Outer Gear -->
                        <div class="w-32 h-32 md:w-40 md:h-40 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center gear-spin">
                            <ion-icon name="build-outline" class="text-5xl md:text-6xl"></ion-icon>
                        </div>
                        <!-- Inner Icon -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center gear-spin-reverse">
                                <ion-icon name="settings-outline" class="text-2xl md:text-3xl"></ion-icon>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Title -->
                <h1 class="text-3xl md:text-5xl font-bold mb-4">
                    Sedang Dalam Perbaikan
                </h1>

                <!-- Maintenance Description -->
                <p class="text-white/80 text-base md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                    Kami sedang melakukan pemeliharaan sistem untuk memberikan layanan yang lebih baik. 
                    Sistem akan kembali normal dalam waktu singkat. Terima kasih atas kesabaran Anda.
                </p>

                <!-- Countdown Timer -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Perkiraan Waktu Tersisa</h3>
                    <div class="flex justify-center gap-2 md:gap-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 md:p-4 min-w-[60px] md:min-w-[80px]">
                            <div id="hours" class="text-2xl md:text-3xl font-bold">02</div>
                            <div class="text-xs md:text-sm opacity-80">Jam</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 md:p-4 min-w-[60px] md:min-w-[80px]">
                            <div id="minutes" class="text-2xl md:text-3xl font-bold">30</div>
                            <div class="text-xs md:text-sm opacity-80">Menit</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 md:p-4 min-w-[60px] md:min-w-[80px]">
                            <div id="seconds" class="text-2xl md:text-3xl font-bold">00</div>
                            <div class="text-xs md:text-sm opacity-80">Detik</div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-8 max-w-md mx-auto">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Progress Pemeliharaan</span>
                        <span id="progress-percent">75%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden">
                        <div class="bg-white h-full rounded-full progress-fill" style="width: 75%"></div>
                    </div>
                </div>

                <!-- Status Updates -->
                <div class="mb-8 max-w-2xl mx-auto">
                    <h3 class="text-lg font-semibold mb-4">Status Pemeliharaan</h3>
                    <div class="space-y-2 text-left">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 flex items-center gap-3">
                            <ion-icon name="checkmark-circle-outline" class="text-green-300 text-xl"></ion-icon>
                            <span>Backup data selesai</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 flex items-center gap-3">
                            <ion-icon name="checkmark-circle-outline" class="text-green-300 text-xl"></ion-icon>
                            <span>Update sistem sedang berjalan</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 flex items-center gap-3">
                            <div class="w-4 h-4 bg-yellow-300 rounded-full pulse-dot"></div>
                            <span>Optimasi database dalam proses...</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 max-w-md mx-auto">
                    <h3 class="text-lg font-semibold mb-3">Butuh Bantuan?</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-center gap-2">
                            <ion-icon name="mail-outline"></ion-icon>
                            <span>support@puskesmasbalaraja.com</span>
                        </div>
                        <div class="flex items-center justify-center gap-2">
                            <ion-icon name="call-outline"></ion-icon>
                            <span>(021) 1234-5678</span>
                        </div>
                    </div>
                </div>

                <!-- Refresh Button -->
                <div class="mt-8">
                    <button onclick="location.reload()" class="px-6 py-3 bg-white text-purple-600 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 flex items-center justify-center gap-2 mx-auto">
                        <ion-icon name="refresh-outline" class="text-xl"></ion-icon>
                        <span>Refresh Halaman</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Countdown Timer -->
    <script>
        // Set target time (2 hours 30 minutes from now)
        const targetTime = new Date().getTime() + (2 * 60 * 60 * 1000) + (30 * 60 * 1000);
        
        // Update countdown every second
        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = targetTime - now;
            
            if (distance < 0) {
                clearInterval(countdown);
                document.getElementById('hours').innerHTML = '00';
                document.getElementById('minutes').innerHTML = '00';
                document.getElementById('seconds').innerHTML = '00';
                
                // Auto refresh when countdown reaches zero
                setTimeout(() => {
                    location.reload();
                }, 2000);
                return;
            }
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('hours').innerHTML = hours.toString().padStart(2, '0');
            document.getElementById('minutes').innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').innerHTML = seconds.toString().padStart(2, '0');
        }, 1000);

        // Simulate progress update
        let progress = 75;
        const progressInterval = setInterval(function() {
            if (progress >= 100) {
                clearInterval(progressInterval);
                document.getElementById('progress-percent').innerHTML = '100%';
                return;
            }
            
            progress += Math.random() * 2;
            if (progress > 100) progress = 100;
            
            document.getElementById('progress-percent').innerHTML = Math.floor(progress) + '%';
            document.querySelector('.progress-fill').style.width = progress + '%';
        }, 5000);

        // Add interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add parallax effect on mouse move (desktop only)
            if (window.innerWidth > 768) {
                document.addEventListener('mousemove', function(e) {
                    const x = (e.clientX / window.innerWidth - 0.5) * 20;
                    const y = (e.clientY / window.innerHeight - 0.5) * 20;
                    
                    const container = document.querySelector('.maintenance-container');
                    container.style.transform = `translateY(${y}px) translateX(${x}px)`;
                });
            }

            // Auto refresh every 5 minutes
            setInterval(() => {
                location.reload();
            }, 300000);
        });
    </script>
</body>
</html>
