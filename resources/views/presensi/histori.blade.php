@extends('layouts.mobile.app')
@section('content')
    <style>
        .avatar {
            position: relative;
            width: 2.5rem;
            height: 2.5rem;
            cursor: pointer;
        }
        
        .iconpresence {
            color: var(--color-nav);
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
            position: relative;
            z-index: 1;
        }

        /* Custom Flatpickr Styling */
        .flatpickr-date {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            padding-right: 45px !important;
        }

        .flatpickr-date:focus {
            border-color: var(--color-nav) !important;
            box-shadow: 0 0 0 3px rgba(var(--color-nav-rgb), 0.1) !important;
        }

        .flatpickr-date::after {
            content: '';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2332745e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
            z-index: 1;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .flatpickr-date:hover::after,
        .flatpickr-date:focus::after {
            opacity: 1;
            /* Filter to colorize the SVG if possible, or just accept default for now. 
               Ideally use mask-image for full dynamic color. */
            /* transform: translateY(-50%) scale(1.1); */
        }

        /* Flatpickr Calendar Container */
        .flatpickr-calendar {
            border-radius: 16px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
            border: none !important;
            overflow: hidden;
            animation: slideDown 0.3s ease-out;
            max-width: 100%;
            box-sizing: border-box !important;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Flatpickr Header */
        .flatpickr-months {
            background: linear-gradient(135deg, var(--color-nav) 0%, var(--color-nav-active) 100%) !important;
            padding: 15px 0 !important;
            border-radius: 16px 16px 0 0;
        }

        .flatpickr-month {
            color: white !important;
        }

        .flatpickr-current-month {
            color: white !important;
            font-weight: 600 !important;
            font-size: 16px !important;
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: white !important;
            fill: white !important;
            padding: 8px !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }

        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.1);
        }

        /* Flatpickr Weekdays */
        .flatpickr-weekdays {
            background: rgba(var(--color-nav-rgb), 0.1) !important;
            padding: 10px 0 !important;
        }

        .flatpickr-weekday {
            color: var(--color-nav) !important;
            font-weight: 600 !important;
            font-size: 13px !important;
        }

        /* Flatpickr Days */
        .flatpickr-days {
            padding: 10px !important;
        }

        .flatpickr-day {
            border-radius: 10px !important;
            border: 2px solid transparent !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
        }

        .flatpickr-day:hover {
            background: rgba(var(--color-nav-rgb), 0.1) !important;
            border-color: var(--color-nav) !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(var(--color-nav-rgb), 0.2) !important;
        }

        .flatpickr-day.selected {
            background: linear-gradient(135deg, var(--color-nav) 0%, var(--color-nav-active) 100%) !important;
            border-color: var(--color-nav) !important;
            color: white !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(var(--color-nav-rgb), 0.4) !important;
        }

        .flatpickr-day.today {
            border-color: var(--color-nav) !important;
            background: rgba(var(--color-nav-rgb), 0.1) !important;
            color: var(--color-nav) !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.today.selected {
            background: linear-gradient(135deg, var(--color-nav) 0%, var(--color-nav-active) 100%) !important;
            color: white !important;
        }

        .flatpickr-day.flatpickr-disabled {
            color: #ccc !important;
            opacity: 0.5 !important;
        }

        /* Flatpickr Time Input (if enabled) */
        .flatpickr-time {
            border-top: 1px solid #e0e0e0 !important;
            padding: 15px !important;
        }

        .flatpickr-time input {
            border-radius: 8px !important;
            border: 2px solid #e0e0e0 !important;
            transition: all 0.2s ease !important;
        }

        .flatpickr-time input:hover {
            border-color: var(--color-nav) !important;
        }

        /* Mobile Responsive - Enhanced */
        @media (max-width: 576px) {
            .flatpickr-calendar {
                width: calc(100vw - 32px) !important;
                max-width: calc(100vw - 32px) !important;
                min-width: calc(100vw - 32px) !important;
                left: 16px !important;
                right: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .flatpickr-calendar .flatpickr-innerContainer {
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .flatpickr-calendar .flatpickr-days {
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .flatpickr-day {
                height: 38px !important;
                line-height: 38px !important;
                font-size: 14px !important;
            }

            .flatpickr-weekday {
                font-size: 12px !important;
                padding: 8px 0 !important;
            }

            .flatpickr-months {
                padding: 12px 0 !important;
            }

            .flatpickr-current-month {
                font-size: 14px !important;
            }

            .flatpickr-prev-month,
            .flatpickr-next-month {
                padding: 6px !important;
            }

            .flatpickr-days {
                padding: 8px !important;
            }
        }

        /* Extra Small Mobile */
        @media (max-width: 375px) {
            .flatpickr-calendar {
                width: calc(100vw - 24px) !important;
                max-width: calc(100vw - 24px) !important;
                min-width: calc(100vw - 24px) !important;
                left: 12px !important;
                right: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .flatpickr-day {
                height: 35px !important;
                line-height: 35px !important;
                font-size: 13px !important;
            }

            .flatpickr-weekday {
                font-size: 11px !important;
            }
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

        /* SKELETON LOADING STYLES */
        .content-hide {
            display: none !important;
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-input {
            height: 45px;
            width: 100%;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .skeleton-button {
            height: 45px;
            width: 100%;
            border-radius: 10px;
        }

        .skeleton-card {
            height: 80px;
            border-radius: 10px;
            margin-bottom: 5px;
            padding: 10px;
            display: flex;
            align-items: center;
        }

        .skeleton-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            margin-right: 10px;
        }

        .skeleton-content {
            flex: 1;
        }

        .skeleton-line {
            height: 12px;
            margin-bottom: 6px;
            border-radius: 4px;
        }

        .skeleton-badge {
            height: 16px;
            width: 60px;
            border-radius: 4px;
        }
    </style>
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="#" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Histori Presensi</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <!-- SKELETON LOADER -->
        <div id="skeleton-loader" style="margin-top: 30px">
            <div class="row mb-4">
                <div class="col">
                    <div class="row">
                        <div class="col-6" style="padding-right: 5px;">
                            <div class="skeleton skeleton-input"></div>
                        </div>
                        <div class="col-6" style="padding-left: 5px;">
                            <div class="skeleton skeleton-input"></div>
                        </div>
                    </div>
                    <div class="skeleton skeleton-button"></div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="card mb-1 skeleton-card" style="border: 1px solid #f0f0f0; box-shadow: none;">
                            <div class="skeleton skeleton-icon"></div>
                            <div class="skeleton-content">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="skeleton skeleton-line" style="width: 100px;"></div>
                                    <div class="skeleton skeleton-badge"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="skeleton skeleton-line" style="width: 150px;"></div>
                                    <div class="skeleton skeleton-badge" style="width: 40px;"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div id="real-content" class="content-hide">
            <div class="row mb-4" style="margin-top: 30px">
            <div class="col">
                <form action="{{ route('presensi.histori') }}" method="GET">
                    <input type="text" class="feedback-input dari flatpickr-date" name="dari" placeholder="Dari" id="datePicker" value="{{ Request('dari') }}" />
                    <input type="text" class="feedback-input sampai flatpickr-date" name="sampai" placeholder="Sampai" id="datePicker2"
                        value="{{ Request('sampai') }}" />
                    <button class="btn btn-primary w-100" id="btnSimpan"><ion-icon name="search-circle-outline"></ion-icon>Cari</button>
                </form>
            </div>
        </div>
        <div class="row overflow-scroll" style="height: 100vh;">
            <div class="col">
                @if ($datapresensi->isEmpty())
                    <div class="alert alert-warning d-flex align-items-center">
                        <ion-icon name="information-circle-outline" style="font-size: 24px;" class="mr-2"></ion-icon>
                        <p style="font-size: 14px">Data Tidak Ditemukan</p>
                    </div>
                @endif
                @foreach ($datapresensi as $d)
                    <div class="card mb-1 card-hover" style="border: 1px solid var(--color-nav); border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <div class="card-body p-2 d-flex align-items-center">
                            <div class="icon-container mr-1 d-flex align-items-center justify-content-center" 
                                style="width: 45px; height: 45px; border-radius: 12px; flex-shrink: 0; 
                                background-color: {{ $d->status == 'h' ? 'rgba(var(--color-nav-rgb), 0.1)' : ($d->status == 'i' ? 'rgba(30, 144, 255, 0.1)' : ($d->status == 's' ? 'rgba(255, 99, 132, 0.1)' : ($d->status == 'c' ? 'rgba(255, 159, 64, 0.1)' : 'rgba(231, 76, 60, 0.1)'))) }};">
                                @if ($d->status == 'h')
                                    <ion-icon name="finger-print-outline" style="font-size: 20px; color: var(--color-nav);"></ion-icon>
                                @elseif ($d->status == 'i')
                                    <ion-icon name="document-text-outline" style="font-size: 20px; color: #1e90ff;"></ion-icon>
                                @elseif ($d->status == 's')
                                    <ion-icon name="medkit-outline" style="font-size: 20px; color: #ff6384;"></ion-icon>
                                @elseif ($d->status == 'c')
                                    <ion-icon name="calendar-outline" style="font-size: 20px; color: #ff9f40;"></ion-icon>
                                @elseif ($d->status == 'a')
                                    <ion-icon name="alert-circle-outline" style="font-size: 20px; color: #e74c3c;"></ion-icon>
                                @endif
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h5 class="mb-0 text-truncate" style="font-size: 14px; font-weight: 600; color: #333;">{{ DateToIndo($d->tanggal) }}</h5>
                                            <span class="badge" style="background-color: #f8f9fa; color: #666; font-weight: normal; font-size: 10px; border: 1px solid #eee;">
                                                {{ $d->nama_jam_kerja }} ({{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }})
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            @if ($d->status == 'h')
                                                @php
                                                    $jam_in_ts = strtotime($d->jam_in);
                                                    $jam_masuk_ts = strtotime($d->tanggal . ' ' . $d->jam_masuk);
                                                    $is_late = $jam_in_ts > $jam_masuk_ts;
                                                    $jam_telat = 0;
                                                    $menit_telat = 0;
                                                    
                                                    if ($is_late) {
                                                        $terlambat_selisih = $jam_in_ts - $jam_masuk_ts;
                                                        $jam_telat = floor($terlambat_selisih / 3600);
                                                        $sisa = $terlambat_selisih % 3600;
                                                        $menit_telat = floor($sisa / 60);
                                                    }
                                                    
                                                    // Calculation Logic
                                                    $denda_display = 0;
                                                    $potongan_jam = 0;
                                                    $potongan_jam_terlambat = 0;
                                                    $pulangcepat = 0;
                                                    $potongan_tidak_scan = 0;

                                                    // Cek apakah denda sudah dikunci (ada di database)
                                                    $denda_dari_db = !empty($d->denda) ? $d->denda : null;

                                                    if ($denda_dari_db !== null) {
                                                        // Gunakan denda dari database
                                                        $denda_display = $denda_dari_db;
                                                        if ($is_late) {
                                                            $desimal_terlambat = $jam_telat + round($menit_telat / 60, 2);
                                                            if ($desimal_terlambat >= 1) {
                                                                $potongan_jam_terlambat = $desimal_terlambat > $d->total_jam ? $d->total_jam : $desimal_terlambat;
                                                            }
                                                        }
                                                    } else {
                                                        // Hitung manual
                                                        if ($is_late){
                                                            $desimal_terlambat = $jam_telat + round($menit_telat / 60, 2);
                                                            if ($desimal_terlambat < 1) {
                                                                $denda_display = hitungdenda($denda_list, $menit_telat);
                                                                $potongan_jam_terlambat = 0;
                                                            } else {
                                                                $denda_display = 0;
                                                                $potongan_jam_terlambat = $desimal_terlambat > $d->total_jam ? $d->total_jam : $desimal_terlambat;
                                                            }
                                                        }
                                                    }

                                                    $pulangcepat = hitungpulangcepat(
                                                        $d->tanggal,
                                                        $d->jam_out,
                                                        $d->jam_pulang,
                                                        $d->istirahat,
                                                        $d->jam_awal_istirahat,
                                                        $d->jam_akhir_istirahat,
                                                        $d->lintashari
                                                    );
                                                    $pulangcepat = $pulangcepat > $d->total_jam ? $d->total_jam : $pulangcepat;

                                                    // Khusus Histori: Jangan hitung tidak scan jika hari ini (sama dengan dashboard)
                                                    if ($d->tanggal != date('Y-m-d')) {
                                                        if (empty($d->jam_out) || empty($d->jam_in)) {
                                                            $potongan_tidak_scan = $d->total_jam;
                                                        }
                                                    }

                                                    if ($potongan_tidak_scan > 0) {
                                                        $potongan_jam = $potongan_tidak_scan;
                                                    } else {
                                                        $potongan_jam = $pulangcepat + $potongan_jam_terlambat;
                                                    }

                                                    // Cek Status Potongan (Toggle)
                                                    $status_potongan_row = isset($d->status_potongan) ? $d->status_potongan : $namasettings->status_potongan_jam;
                                                    
                                                    if ($status_potongan_row == 0) {
                                                        $potongan_jam = 0;
                                                        $denda_display = 0;
                                                    }
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span style="color: #555; font-size: 12px; font-weight: 500;">
                                                        {{ $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '__:__' }}
                                                        <span style="color: #ccc; margin: 0 5px;">-</span>
                                                        {{ $d->jam_out != null ? date('H:i', strtotime($d->jam_out)) : '__:__' }}
                                                    </span>
                                                    @if ($is_late)
                                                        <span class="badge bg-danger" style="font-size: 10px;">
                                                            Telat {{ $jam_telat > 0 ? $jam_telat . 'j ' : '' }}{{ $menit_telat }}m
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success" style="font-size: 10px;">Tepat Waktu</span>
                                                    @endif
                                                </div>
                                            @elseif ($d->status == 'i')
                                                <span style="color: #1e90ff; font-size: 12px;">Izin: {{ $d->keterangan_izin }}</span>
                                            @elseif ($d->status == 's')
                                                <span style="color: #ff6384; font-size: 12px;">Sakit: {{ $d->keterangan_izin_sakit }}</span>
                                            @elseif ($d->status == 'c')
                                                <span style="color: #ff9f40; font-size: 12px;">Cuti: {{ $d->keterangan_izin_cuti }}</span>
                                            @elseif ($d->status == 'a')
                                                @php
                                                    $potongan_jam = $d->total_jam;
                                                    $denda_display = !empty($d->denda) ? $d->denda : 0;
                                                    
                                                    $status_potongan_row = isset($d->status_potongan) ? $d->status_potongan : $namasettings->status_potongan_jam;
                                                    if ($status_potongan_row == 0) {
                                                        $potongan_jam = 0;
                                                    }
                                                @endphp
                                                <span style="color: #e74c3c; font-size: 12px;">Alpha: Tanpa Keterangan</span>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex flex-wrap gap-1">
                                            @if ($d->status == 'h' && $d->jam_in != null)
                                                @if ($denda_display > 0)
                                                    <span class="badge bg-danger" style="font-size: 10px;">
                                                        Denda Rp. {{ number_format($denda_display) }}
                                                    </span>
                                                @endif
                                                
                                                @if ($pulangcepat > 0)
                                                    <span class="badge bg-danger" style="font-size: 10px;">
                                                        Pulang Cepat
                                                    </span>
                                                @endif

                                                @if ($potongan_jam > 0 && ($d->jam_out != null || $d->tanggal != date('Y-m-d')))
                                                    @if ($namasettings->status_potongan_jam == 1 || (isset($d->status_potongan) && $d->status_potongan == 1))
                                                        <span class="badge bg-danger" style="font-size: 10px;">
                                                            PJ: {{ number_format($potongan_jam, 2) }} Jam
                                                        </span>
                                                    @endif
                                                @endif
                                            @elseif ($d->status == 'a')
                                                @if ($denda_display > 0)
                                                    <span class="badge bg-danger" style="font-size: 10px;">
                                                        Denda Rp. {{ number_format($denda_display) }}
                                                    </span>
                                                @endif
                                                @if ($potongan_jam > 0 && ($d->jam_out != null || $d->tanggal != date('Y-m-d')))
                                                    @if ($namasettings->status_potongan_jam == 1 || (isset($d->status_potongan) && $d->status_potongan == 1))
                                                        <span class="badge bg-danger" style="font-size: 10px;">
                                                            PJ: {{ number_format($potongan_jam, 2) }} Jam
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Define Indonesian locale for flatpickr
        const indonesianLocale = {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        };

        // Initialize flatpickr for date inputs with enhanced styling and mobile optimization
        const datePicker1 = flatpickr('#datePicker', {
            dateFormat: 'Y-m-d',
            allowInput: false,
            monthSelectorType: 'static',
            animate: true,
            locale: indonesianLocale,
            clickOpens: true,
            disableMobile: false,
            defaultDate: "{{ Request('dari') }}" || null,
            onOpen: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.style.animation = 'slideDown 0.3s ease-out';
                if (window.innerWidth <= 576) {
                    const padding = window.innerWidth <= 375 ? 12 : 16;
                    const calendarWidth = window.innerWidth - (padding * 2);
                    instance.calendarContainer.style.position = 'fixed';
                    instance.calendarContainer.style.left = padding + 'px';
                    instance.calendarContainer.style.right = 'auto';
                    instance.calendarContainer.style.width = calendarWidth + 'px';
                    instance.calendarContainer.style.maxWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.minWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.margin = '0';
                    instance.calendarContainer.style.padding = '0';
                    instance.calendarContainer.style.boxSizing = 'border-box';
                }
            },
            onReady: function(selectedDates, dateStr, instance) {
                if (window.innerWidth <= 576) {
                    const padding = window.innerWidth <= 375 ? 12 : 16;
                    const calendarWidth = window.innerWidth - (padding * 2);
                    instance.calendarContainer.style.width = calendarWidth + 'px';
                    instance.calendarContainer.style.maxWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.minWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.boxSizing = 'border-box';
                }
            }
        });

        const datePicker2 = flatpickr('#datePicker2', {
            dateFormat: 'Y-m-d',
            allowInput: false,
            monthSelectorType: 'static',
            animate: true,
            locale: indonesianLocale,
            clickOpens: true,
            disableMobile: false,
            defaultDate: "{{ Request('sampai') }}" || null,
            onOpen: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.style.animation = 'slideDown 0.3s ease-out';
                if (window.innerWidth <= 576) {
                    const padding = window.innerWidth <= 375 ? 12 : 16;
                    const calendarWidth = window.innerWidth - (padding * 2);
                    instance.calendarContainer.style.position = 'fixed';
                    instance.calendarContainer.style.left = padding + 'px';
                    instance.calendarContainer.style.right = 'auto';
                    instance.calendarContainer.style.width = calendarWidth + 'px';
                    instance.calendarContainer.style.maxWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.minWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.margin = '0';
                    instance.calendarContainer.style.padding = '0';
                    instance.calendarContainer.style.boxSizing = 'border-box';
                }
            },
            onReady: function(selectedDates, dateStr, instance) {
                if (window.innerWidth <= 576) {
                    const padding = window.innerWidth <= 375 ? 12 : 16;
                    const calendarWidth = window.innerWidth - (padding * 2);
                    instance.calendarContainer.style.width = calendarWidth + 'px';
                    instance.calendarContainer.style.maxWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.minWidth = calendarWidth + 'px';
                    instance.calendarContainer.style.boxSizing = 'border-box';
                }
            }
        });

        // Handle window resize for responsive calendar
        $(window).on('resize', function() {
            if (window.innerWidth <= 576) {
                const padding = window.innerWidth <= 375 ? 12 : 16;
                const calendarWidth = window.innerWidth - (padding * 2);
                $('.flatpickr-calendar').css({
                    'width': calendarWidth + 'px',
                    'max-width': calendarWidth + 'px',
                    'min-width': calendarWidth + 'px',
                    'left': padding + 'px',
                    'right': 'auto',
                    'margin': '0',
                    'padding': '0',
                    'box-sizing': 'border-box'
                });
            }
        });


        // SKELETON LOADING LOGIC
        $(window).on('load', function() {
            setTimeout(function() {
                $("#skeleton-loader").fadeOut(300, function() {
                    $(this).remove();
                    $("#real-content").removeClass("content-hide").hide().fadeIn(300);
                });
            }, 500); // 0.5s delay
        });
    </script>
@endpush
