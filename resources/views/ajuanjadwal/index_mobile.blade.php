@extends('layouts.mobile.app')
@section('content')
    <style>
        .avatar {
            position: relative;
            width: 2.5rem;
            height: 2.5rem;
            cursor: pointer;
        }

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
            <div class="pageTitle">Ajuan Jadwal</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section">
        <div class="row" style="margin-top: 10px">
            <div class="col">
                @foreach ($ajuanjadwal as $d)
                    @php
                        $namahari = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                        $day_eng = date('D', strtotime($d->tanggal));
                        $day_indo = isset($namahari[$day_eng]) ? $namahari[$day_eng] : $day_eng;
                        $day_short = strtoupper(substr($day_indo, 0, 3));
                        $tgl = date('d', strtotime($d->tanggal));
                        
                        $text_color = $d->status == 'p' ? '#ff9f40' : ($d->status == 'a' ? 'var(--color-nav)' : '#e74c3c');
                        $bg_color = $d->status == 'p' ? 'rgba(255, 159, 64, 0.1)' : ($d->status == 'a' ? 'rgba(var(--color-nav-rgb), 0.1)' : 'rgba(231, 76, 60, 0.1)');
                        
                        $badge_bg = $d->status == 'p' ? '#fff3cd' : ($d->status == 'a' ? '#d1e7dd' : '#f8d7da');
                        $badge_color = $d->status == 'p' ? '#856404' : ($d->status == 'a' ? '#0f5132' : '#721c24');
                        $badge_border = $d->status == 'p' ? '#ffeeba' : ($d->status == 'a' ? '#badbcc' : '#f5c6cb');
                        $status_text = $d->status == 'p' ? 'Pending' : ($d->status == 'a' ? 'Disetujui' : 'Ditolak');
                    @endphp
                    <form method="POST" name="deleteform" class="deleteform"
                        action="{{ route('ajuanjadwal.delete', Crypt::encrypt($d->id)) }}">
                        @csrf
                        @method('DELETE')
                        <div class="card mb-1 {{ $d->status == 'p' ? 'cancel-confirm' : '' }}" 
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
                                        <span style="font-size: 14px; font-weight: 600; color: #333;">{{ DateToIndo($d->tanggal) }}</span>
                                        <span class="badge" style="background-color: {{ $badge_bg }}; color: {{ $badge_color }}; font-size: 10px; border: 1px solid {{ $badge_border }}; white-space: nowrap;">
                                            {{ $status_text }}
                                        </span>
                                    </div>
                                    <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                        {{ $d->jamKerjaAwal ? $d->jamKerjaAwal->nama_jam_kerja : '-' }}
                                        <ion-icon name="arrow-forward-outline" style="font-size: 11px; margin: 0 3px; color: #aaa; vertical-align: middle;"></ion-icon>
                                        <span style="font-weight: 600;">{{ $d->jamKerjaTujuan->nama_jam_kerja }}</span>
                                    </div>
                                    <p class="text-muted mb-0 text-truncate" style="font-size: 11px; margin-top: 2px;">
                                        {{ $d->keterangan }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                @endforeach
                <!-- Pagination if needed -->
                {{ $ajuanjadwal->links() }} 
            </div>
        </div>

        <div class="fab-button animate bottom-left" style="margin-bottom:70px">
            <a href="{{ route('ajuanjadwal.create') }}" class="fab bg-primary">
                <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
            </a>
        </div>
    </div>
@endsection
