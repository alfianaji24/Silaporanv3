@extends('layouts.app')

@section('title', 'Sedang Dalam Perbaikan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center py-8">
                        <!-- Maintenance Icon -->
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center relative">
                                <div class="w-32 h-32 bg-warning bg-opacity-10 rounded-full flex items-center justify-center gear-spin">
                                    <ion-icon name="build-outline" class="text-6xl text-warning"></ion-icon>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-16 h-16 bg-warning bg-opacity-20 rounded-full flex items-center justify-center gear-spin-reverse">
                                        <ion-icon name="settings-outline" class="text-3xl text-warning"></ion-icon>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Title -->
                        <h1 class="h1 mb-3">Sedang Dalam Perbaikan</h1>

                        <!-- Maintenance Description -->
                        <p class="text-muted mb-6 fs-5">
                            Kami sedang melakukan pemeliharaan sistem untuk memberikan layanan yang lebih baik. 
                            Sistem akan kembali normal dalam waktu singkat.
                        </p>

                        <!-- Countdown Timer -->
                        <div class="mb-6">
                            <h4 class="mb-3">Perkiraan Waktu Tersisa</h4>
                            <div class="d-flex justify-content-center gap-3">
                                <div class="bg-light rounded p-3 min-w-[80px]">
                                    <div id="hours-admin" class="display-6 fw-bold text-warning">02</div>
                                    <div class="text-muted small">Jam</div>
                                </div>
                                <div class="bg-light rounded p-3 min-w-[80px]">
                                    <div id="minutes-admin" class="display-6 fw-bold text-warning">30</div>
                                    <div class="text-muted small">Menit</div>
                                </div>
                                <div class="bg-light rounded p-3 min-w-[80px]">
                                    <div id="seconds-admin" class="display-6 fw-bold text-warning">00</div>
                                    <div class="text-muted small">Detik</div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-6 max-w-md mx-auto">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Progress Pemeliharaan</span>
                                <span id="progress-percent-admin" class="text-warning fw-semibold">75%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning progress-fill" role="progressbar" style="width: 75%"></div>
                            </div>
                        </div>

                        <!-- Status Updates -->
                        <div class="mb-6 max-w-2xl mx-auto text-start">
                            <h4 class="mb-3">Status Pemeliharaan</h4>
                            <div class="list-group">
                                <div class="list-group-item d-flex align-items-center gap-3">
                                    <ion-icon name="checkmark-circle-outline" class="text-success fs-4"></ion-icon>
                                    <span>Backup data selesai</span>
                                </div>
                                <div class="list-group-item d-flex align-items-center gap-3">
                                    <ion-icon name="checkmark-circle-outline" class="text-success fs-4"></ion-icon>
                                    <span>Update sistem sedang berjalan</span>
                                </div>
                                <div class="list-group-item d-flex align-items-center gap-3">
                                    <div class="spinner-border spinner-border-sm text-warning" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span>Optimasi database dalam proses...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="bg-light rounded-xl p-6 max-w-md mx-auto mb-6">
                            <h4 class="mb-3">Butuh Bantuan?</h4>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <ion-icon name="mail-outline"></ion-icon>
                                        <span>support@silaporan.com</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <ion-icon name="call-outline"></ion-icon>
                                        <span>(021) 1234-5678</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Refresh Button -->
                        <button onclick="location.reload()" class="btn btn-warning btn-lg">
                            <ion-icon name="refresh-outline" class="me-2"></ion-icon>
                            Refresh Halaman
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
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

@push('scripts')
<script>
    // Set target time (2 hours 30 minutes from now)
    const targetTime = new Date().getTime() + (2 * 60 * 60 * 1000) + (30 * 60 * 1000);
    
    // Update countdown every second
    const countdown = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetTime - now;
        
        if (distance < 0) {
            clearInterval(countdown);
            document.getElementById('hours-admin').innerHTML = '00';
            document.getElementById('minutes-admin').innerHTML = '00';
            document.getElementById('seconds-admin').innerHTML = '00';
            
            // Auto refresh when countdown reaches zero
            setTimeout(() => {
                location.reload();
            }, 2000);
            return;
        }
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('hours-admin').innerHTML = hours.toString().padStart(2, '0');
        document.getElementById('minutes-admin').innerHTML = minutes.toString().padStart(2, '0');
        document.getElementById('seconds-admin').innerHTML = seconds.toString().padStart(2, '0');
    }, 1000);

    // Simulate progress update
    let progress = 75;
    const progressInterval = setInterval(function() {
        if (progress >= 100) {
            clearInterval(progressInterval);
            document.getElementById('progress-percent-admin').innerHTML = '100%';
            return;
        }
        
        progress += Math.random() * 2;
        if (progress > 100) progress = 100;
        
        document.getElementById('progress-percent-admin').innerHTML = Math.floor(progress) + '%';
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
