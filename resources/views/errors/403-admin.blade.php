@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center py-8">
                        <!-- Error Icon -->
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center w-24 h-24 bg-danger bg-opacity-10 rounded-full">
                                <ion-icon name="lock-closed-outline" class="text-6xl text-danger"></ion-icon>
                            </div>
                        </div>

                        <!-- Error Code -->
                        <h1 class="display-1 fw-bold text-danger mb-3">403</h1>

                        <!-- Error Title -->
                        <h2 class="h2 mb-3">Akses Ditolak</h2>

                        <!-- Error Description -->
                        <p class="text-muted mb-6 fs-5">
                            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. 
                            Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
                        </p>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <!-- Back Button -->
                            <button onclick="history.back()" class="btn btn-label-secondary">
                                <ion-icon name="arrow-back-outline" class="me-2"></ion-icon>
                                Kembali
                            </button>

                            <!-- Dashboard Button -->
                            <a href="{{ route('dashboard.index') }}" class="btn btn-primary">
                                <ion-icon name="home-outline" class="me-2"></ion-icon>
                                Dashboard
                            </a>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-6">
                            <small class="text-muted">
                                Error Code: 403 - Forbidden | 
                                Timestamp: {{ now()->format('d M Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .display-1 {
        font-size: 8rem !important;
        font-weight: 700 !important;
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }
    
    .bg-opacity-10 {
        animation: float 3s ease-in-out infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add interactive effects
        const buttons = document.querySelectorAll('button, .btn');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
    });
</script>
@endpush
@endsection
