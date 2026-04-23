@extends('layouts.mobile.modern')

@section('title', 'Akses Ditolak')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full">
        <!-- Error Container -->
        <div class="text-center">
            <!-- Lock Icon with Animation -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4 animate-bounce">
                    <ion-icon name="lock-closed-outline" class="text-4xl text-red-600"></ion-icon>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="text-5xl font-bold text-red-600 mb-2">403</h1>

            <!-- Error Title -->
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Akses Ditolak</h2>

            <!-- Error Description -->
            <p class="text-gray-600 mb-8 leading-relaxed">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. 
                Fitur ini mungkin tidak tersedia untuk role Anda.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <!-- Back Button -->
                <button onclick="history.back()" class="w-full px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-300 flex items-center justify-center gap-2">
                    <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                    <span>Kembali</span>
                </button>

                <!-- Dashboard Button -->
                <a href="{{ route('dashboard.index') }}" class="w-full px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-all duration-300 flex items-center justify-center gap-2">
                    <ion-icon name="home-outline" class="text-xl"></ion-icon>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 text-gray-500 text-sm">
                <p class="mb-1">Error Code: 403 - Forbidden</p>
                <p>{{ now()->format('d M Y H:i') }}</p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="fixed top-10 left-10 w-16 h-16 bg-red-100 rounded-full opacity-50"></div>
        <div class="fixed bottom-10 right-10 w-24 h-24 bg-orange-100 rounded-full opacity-50"></div>
    </div>
</div>

@push('mystyle')
<style>
    .animate-bounce {
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
</style>
@endpush

@push('myscript')
<script>
    // Add interactive effects
    document.addEventListener('DOMContentLoaded', function() {
        // Add click feedback to buttons
        const buttons = document.querySelectorAll('button, a');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Add scale effect
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });
    });
</script>
@endpush
@endsection
