@extends('layouts.mobile.app')
@section('content')
    <style>
        .avatar {
            position: relative;
            width: 2.5rem;
            height: 2.5rem;
            cursor: pointer;
        }

        /* Tambahkan style untuk header dan content */
        #header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        #content-section {
            margin-top: 70px;
            padding-top: 5px;
            padding-bottom: 80px;
            position: relative;
            z-index: 1;
        }

        .avatar-sm {
            width: 2rem;
            height: 2rem;
        }

        .avatar-sm .avatar-initial {
            font-size: .8125rem;
        }

        .avatar .avatar-initial {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background-color: #eeedf0;
            font-size: .9375rem;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }

        /* Skeleton Loader Styles */
        .skeleton {
            background-color: #e0e0e0;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }

        .skeleton::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            transform: translateX(-100%);
            background-image: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0,
                rgba(255, 255, 255, 0.2) 20%,
                rgba(255, 255, 255, 0.5) 60%,
                rgba(255, 255, 255, 0)
            );
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .skeleton-circle {
            border-radius: 50%;
        }

        .skeleton-text {
            height: 10px;
            margin-bottom: 6px;
        }

        .skeleton-badge {
            height: 20px;
            border-radius: 10px;
        }

        .content-hide {
            display: none;
        }
    </style>
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Pengajuan Izin</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <!-- Skeleton Loader -->
        <div id="skeleton-loader">
            <div class="row" style="margin-top:10px">
                <div class="col">
                    <div class="transactions">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="item mb-2" style="padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                <div class="detail">
                                    <div class="skeleton skeleton-circle me-4" style="width: 2.5rem; height: 2.5rem;"></div>
                                    <div style="flex: 1;">
                                        <div class="skeleton skeleton-text" style="width: 40%; height: 14px; margin-bottom: 8px;"></div>
                                        <div class="skeleton skeleton-text" style="width: 60%;"></div>
                                        <div class="skeleton skeleton-text" style="width: 80%;"></div>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="skeleton skeleton-badge" style="width: 60px;"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="real-content" class="content-hide">
            <div class="row" style="margin-top: 10px">
                <div class="col">
                    @foreach ($pengajuan_izin as $d)
                        @php
                            if ($d->ket == 'i') {
                                $route = 'izinabsen.delete';
                                $ket_text = 'Izin Absen';
                            } elseif ($d->ket == 's') {
                                $route = 'izinsakit.delete';
                                $ket_text = 'Izin Sakit';
                            } elseif ($d->ket == 'c') {
                                $route = 'izincuti.delete';
                                $ket_text = 'Izin Cuti';
                            } elseif ($d->ket == 'd') {
                                $route = 'izindinas.delete';
                                $ket_text = 'Izin Dinas';
                            }

                            $namahari = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                            $day_eng = date('D', strtotime($d->dari));
                            $day_indo = isset($namahari[$day_eng]) ? $namahari[$day_eng] : $day_eng;
                            $day_short = strtoupper(substr($day_indo, 0, 3));
                            $tgl = date('d', strtotime($d->dari));
                            $jml_hari = date_diff(date_create($d->dari), date_create($d->sampai))->format('%a') + 1;

                            $text_color = $d->status_izin == 0 ? '#ff9f40' : ($d->status_izin == 1 ? 'var(--color-nav)' : '#e74c3c');
                            $bg_color = $d->status_izin == 0 ? 'rgba(255, 159, 64, 0.1)' : ($d->status_izin == 1 ? 'rgba(var(--color-nav-rgb), 0.1)' : 'rgba(231, 76, 60, 0.1)');

                            $badge_bg = $d->status_izin == 0 ? '#fff3cd' : ($d->status_izin == 1 ? '#d1e7dd' : '#f8d7da');
                            $badge_color = $d->status_izin == 0 ? '#856404' : ($d->status_izin == 1 ? '#0f5132' : '#721c24');
                            $badge_border = $d->status_izin == 0 ? '#ffeeba' : ($d->status_izin == 1 ? '#badbcc' : '#f5c6cb');
                            $status_text = $d->status_izin == 0 ? 'Pending' : ($d->status_izin == 1 ? 'Disetujui' : 'Ditolak');
                        @endphp
                        <form method="POST" name="deleteform" class="deleteform"
                            action="{{ route($route, Crypt::encrypt($d->kode)) }}">
                            @csrf
                            @method('DELETE')
                            <div class="card mb-1 {{ $d->status_izin == 0 ? 'cancel-confirm' : '' }}"
                                style="border: 1px solid var(--color-nav); border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                                <div class="card-body p-2 d-flex align-items-center" style="gap: 10px;">
                                    {{-- Date Badge --}}
                                    <div class="d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px; min-width: 45px; border-radius: 12px; background-color: {{ $bg_color }};">
                                        <div style="text-align: center; line-height: 1;">
                                            <span style="font-size: 10px; font-weight: 700; display: block; color: {{ $text_color }};">{{ $day_short }}</span>
                                            <span style="font-size: 16px; font-weight: 800; display: block; margin-top: 1px; color: {{ $text_color }};">{{ $tgl }}</span>
                                        </div>
                                    </div>
                                    {{-- Content --}}
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="font-size: 14px; font-weight: 600; color: #333;">{{ $ket_text }}</span>
                                            <span class="badge" style="background-color: {{ $badge_bg }}; color: {{ $badge_color }}; font-size: 10px; border: 1px solid {{ $badge_border }}; white-space: nowrap;">
                                                {{ $status_text }}
                                            </span>
                                        </div>
                                        <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                            {{ DateToIndo($d->dari) }} - {{ DateToIndo($d->sampai) }}
                                            <span class="badge bg-secondary" style="font-size: 9px; vertical-align: middle; margin-left: 4px;">{{ $jml_hari }} Hari</span>
                                        </div>
                                        <p class="text-muted mb-0 text-truncate" style="font-size: 11px; margin-top: 2px;">
                                            {{ $d->keterangan }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="fab-button animate bottom-left dropdown" style="margin-bottom:70px">
            <a href="#" class="fab bg-primary" data-toggle="dropdown">
                <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item bg-primary" href="{{ route('izinabsen.create') }}">
                    <ion-icon name="document-outline" role="img" class="md hydrated"
                        aria-label="image outline"></ion-icon>
                    <p>Izin Absen</p>
                </a>

                <a class="dropdown-item bg-primary" href="{{ route('izinsakit.create') }}">
                    <ion-icon name="bag-add-outline"></ion-icon>
                    <p>Izin Sakit</p>
                </a>
                <a class="dropdown-item bg-primary" href="{{ route('izincuti.create') }}">
                    <ion-icon name="document-outline" role="img" class="md hydrated"
                        aria-label="videocam outline"></ion-icon>
                    <p>Izin Cuti</p>
                </a>
                <a class="dropdown-item bg-primary" href="{{ route('izindinas.create') }}">
                    <ion-icon name="airplane-outline"></ion-icon>
                    <p>Izin Dinas</p>
                </a>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('skeleton-loader').style.display = 'none';
                document.getElementById('real-content').classList.remove('content-hide');
            }, 500); // 0.5s delay for smooth effect
        });
    </script>
@endpush
