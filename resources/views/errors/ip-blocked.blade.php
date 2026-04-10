<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Dibatasi - Silaporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-red-50 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <!-- Warning Icon -->
            <div class="text-center mb-6">
                <svg class="w-20 h-20 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-red-600 text-center mb-4">Akses Dibatasi</h1>
            
            <!-- Message -->
            <div class="bg-red-100 border border-red-300 rounded-lg p-4 mb-6 text-center">
                <p class="text-red-800 font-medium">
                    {{ $message ?? 'Akses Anda dibatasi dikarenakan Anda memiliki riwayat jaringan buruk' }}
                </p>
            </div>

            <!-- Domain Info -->
            <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4 mb-6 text-center">
                <p class="text-yellow-800 text-sm">
                    <strong>Halaman ini tidak berada di domain resmi.</strong><br>
                    Domain saat ini: <span id="currentDomain" class="font-mono bg-yellow-200 px-2 py-1 rounded"></span>
                </p>
            </div>

            <!-- IP Information -->
            <div class="bg-gray-100 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">📍 Informasi IP Anda</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">
                            <strong>IP Address:</strong> 
                            <span class="font-mono bg-white px-2 py-1 rounded border">{{ $ip ?? 'Unknown' }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">
                            <strong>Waktu:</strong> {{ now()->format('d M Y H:i:s') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Request Form -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-blue-800 mb-4">📝 Ajukan Permintaan Penghapusan Blokir</h3>
                <form id="requestForm" class="space-y-4">
                    <input type="hidden" id="ipAddress" value="{{ $ip ?? '' }}">
                    
                    <div>
                        <label for="requestReason" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan permintaan penghapusan blokir:
                        </label>
                        <textarea id="requestReason" name="reason" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: Saya adalah karyawan yang sah, atau ini kesalahan sistem, atau saya menggunakan VPN untuk keperluan kerja..."></textarea>
                    </div>

                    <div>
                        <label for="requestEmail" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Anda (untuk konfirmasi):
                        </label>
                        <input type="email" id="requestEmail" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <label for="requestName" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap:
                        </label>
                        <input type="text" id="requestName" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nama lengkap Anda">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        Kirim Permintaan Penghapusan Blokir
                    </button>
                </form>
            </div>

            <!-- Security Notice -->
            <div class="bg-red-100 border border-red-300 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-red-800 mb-2">⚠️ Notifikasi Keamanan</h3>
                <p class="text-sm text-red-700">
                    Sistem kami mendeteksi adanya aktivitas mencurigakan dari jaringan Anda. 
                    Untuk keamanan data dan sistem, akses ini sementara dibatasi.
                </p>
            </div>

            <!-- Contact Information -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">📞 Hubungi IT Support</h3>
                <p class="text-sm text-gray-700 mb-2">
                    Jika Anda merasa ini adalah kesalahan, silakan hubungi:
                </p>
                <p class="text-sm text-gray-700">
                    <strong>Email:</strong> support@puskesmasbalaraja.com<br>
                    <strong>Telepon:</strong> IT Support Puskesmas Balaraja
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button onclick="location.reload()" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Coba Lagi
                </button>
                <button onclick="window.location.href='/'" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                    Kembali ke Beranda
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>© {{ date('Y') }} Silaporan - Sistem Informasi Laporan Karyawan</p>
            <p class="mt-1">Protected by Advanced Security System</p>
        </div>
    </div>

    <script>
        // Display current domain
        document.getElementById('currentDomain').textContent = window.location.hostname;

        // Auto refresh after 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);

        // Handle form submission
        document.getElementById('requestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                ip: document.getElementById('ipAddress').value,
                reason: formData.get('reason'),
                email: formData.get('email'),
                name: formData.get('name'),
                timestamp: new Date().toISOString(),
                user_agent: navigator.userAgent,
                domain: window.location.hostname,
                url: window.location.href
            };

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Mengirim...';
            submitBtn.disabled = true;

            // Send request to server (simulated - in real implementation, this would be an API call)
            console.log('IP Unblock Request:', data);
            
            // Simulate API call
            setTimeout(() => {
                // Show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'bg-green-100 border border-green-300 rounded-lg p-4 mb-4';
                successDiv.innerHTML = `
                    <h3 class="font-semibold text-green-800 mb-2">✅ Permintaan Terkirim</h3>
                    <p class="text-sm text-green-700">
                        Permintaan penghapusan blokir untuk IP <strong>${data.ip}</strong> telah dikirim ke IT Support.
                        Kami akan memproses permintaan Anda dalam waktu 1x24 jam.
                    </p>
                    <p class="text-sm text-green-700 mt-2">
                        <strong>No. Tiket:</strong> #${Date.now()}<br>
                        <strong>Email konfirmasi:</strong> ${data.email}
                    </p>
                `;
                
                // Replace form with success message
                this.parentElement.replaceWith(successDiv);
                
                // Log the request for admin review
                const logData = {
                    type: 'ip_unblock_request',
                    ip: data.ip,
                    email: data.email,
                    name: data.name,
                    reason: data.reason,
                    timestamp: data.timestamp,
                    domain: data.domain
                };
                
                // In real implementation, this would be sent to server
                console.log('Request logged for admin review:', logData);
                
            }, 1500);
        });

        // Disable right click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Disable certain keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') || 
                (e.ctrlKey && e.shiftKey && e.key === 'C') ||
                (e.ctrlKey && e.key === 'u')) {
                e.preventDefault();
            }
        });

        // Add copy IP functionality
        function copyIP() {
            const ipElement = document.querySelector('#ipAddress');
            if (ipElement && ipElement.value) {
                navigator.clipboard.writeText(ipElement.value).then(() => {
                    // Show copied feedback
                    const ipSpan = document.querySelector('span.font-mono');
                    const originalText = ipSpan.textContent;
                    ipSpan.textContent = 'Tersalin!';
                    ipSpan.classList.add('bg-green-200');
                    
                    setTimeout(() => {
                        ipSpan.textContent = originalText;
                        ipSpan.classList.remove('bg-green-200');
                    }, 2000);
                });
            }
        }

        // Make IP clickable to copy
        document.addEventListener('DOMContentLoaded', function() {
            const ipSpan = document.querySelector('span.font-mono');
            if (ipSpan) {
                ipSpan.style.cursor = 'pointer';
                ipSpan.title = 'Klik untuk menyalin IP';
                ipSpan.addEventListener('click', copyIP);
            }
        });
    </script>
</body>
</html>
