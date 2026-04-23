@extends('layouts.mobile.modern')

@section('title', 'Sedang Dalam Perbaikan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-yellow-50 flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full">
        <!-- Maintenance Container -->
        <div class="text-center">
            <!-- Animated Maintenance Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center relative">
                    <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center gear-spin">
                        <ion-icon name="build-outline" class="text-4xl text-orange-600"></ion-icon>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center gear-spin-reverse">
                            <ion-icon name="settings-outline" class="text-xl text-orange-700"></ion-icon>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Title -->
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Sedang Dalam Perbaikan</h1>

            <!-- Maintenance Description -->
            <p class="text-gray-600 mb-6 leading-relaxed">
                Kami sedang melakukan pemeliharaan sistem. 
                Mohon tunggu beberapa saat, sistem akan kembali normal.
            </p>

            <!-- Countdown Timer -->
            <div class="mb-6">
                <div class="flex justify-center gap-2">
                    <div class="bg-white rounded-lg p-3 shadow-sm min-w-[60px]">
                        <div id="hours-mobile" class="text-2xl font-bold text-orange-600">02</div>
                        <div class="text-xs text-gray-500">Jam</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm min-w-[60px]">
                        <div id="minutes-mobile" class="text-2xl font-bold text-orange-600">30</div>
                        <div class="text-xs text-gray-500">Menit</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm min-w-[60px]">
                        <div id="seconds-mobile" class="text-2xl font-bold text-orange-600">00</div>
                        <div class="text-xs text-gray-500">Detik</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between text-sm mb-2 text-gray-600">
                    <span>Progress</span>
                    <span id="progress-percent-mobile">75%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-orange-500 h-full rounded-full progress-fill" style="width: 75%"></div>
                </div>
            </div>

            <!-- Status Updates -->
            <div class="mb-6 text-left">
                <div class="space-y-2">
                    <div class="bg-white rounded-lg p-3 flex items-center gap-3 shadow-sm">
                        <ion-icon name="checkmark-circle" class="text-green-500 text-lg"></ion-icon>
                        <span class="text-sm">Sedang diperbaiki...</span>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white rounded-xl p-4 mb-6 shadow-sm">
                <h3 class="font-semibold mb-2 text-gray-800">Butuh Bantuan?</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <div class="flex items-center justify-center gap-2">
                        <ion-icon name="mail-outline"></ion-icon>
                        <span>support@silaporan.com</span>
                    </div>
                </div>
            </div>

            <!-- Refresh Button -->
            <button onclick="location.reload()" class="w-full px-6 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition-all duration-300 flex items-center justify-center gap-2">
                <ion-icon name="refresh-outline" class="text-xl"></ion-icon>
                <span>Refresh</span>
            </button>
        </div>
    </div>
</div>

@push('mystyle')
<style>
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
    
    .progress-fill {
        animation: progressFill 3s ease-out forwards;
    }
    
    @keyframes progressFill {
        from { width: 0%; }
        to { width: 75%; }
    }
</style>
@endpush

@push('myscript')
<script>
    // Set target time (2 hours 30 minutes from now)
    const targetTime = new Date().getTime() + (2 * 60 * 60 * 1000) + (30 * 60 * 1000);
    
    // Update countdown every second
    const countdown = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetTime - now;
        
        if (distance < 0) {
            clearInterval(countdown);
            document.getElementById('hours-mobile').innerHTML = '00';
            document.getElementById('minutes-mobile').innerHTML = '00';
            document.getElementById('seconds-mobile').innerHTML = '00';
            
            // Auto refresh when countdown reaches zero
            setTimeout(() => {
                location.reload();
            }, 2000);
            return;
        }
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('hours-mobile').innerHTML = hours.toString().padStart(2, '0');
        document.getElementById('minutes-mobile').innerHTML = minutes.toString().padStart(2, '0');
        document.getElementById('seconds-mobile').innerHTML = seconds.toString().padStart(2, '0');
    }, 1000);

    // Simulate progress update
    let progress = 75;
    const progressInterval = setInterval(function() {
        if (progress >= 100) {
            clearInterval(progressInterval);
            document.getElementById('progress-percent-mobile').innerHTML = '100%';
            return;
        }
        
        progress += Math.random() * 2;
        if (progress > 100) progress = 100;
        
        document.getElementById('progress-percent-mobile').innerHTML = Math.floor(progress) + '%';
        document.querySelectorAll('.progress-fill').forEach(el => {
            el.style.width = progress + '%';
        });
    }, 5000);

    // Auto refresh every 5 minutes
    setInterval(() => {
        location.reload();
    }, 300000);
</script>
@endpush
@endsection
