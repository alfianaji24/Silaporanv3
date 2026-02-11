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
            border-color: #32745e !important;
            box-shadow: 0 0 0 3px rgba(50, 116, 94, 0.1) !important;
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
            transform: translateY(-50%) scale(1.1);
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
            background: linear-gradient(135deg, #32745e 0%, #58907D 100%) !important;
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
            background: rgba(50, 116, 94, 0.1) !important;
            padding: 10px 0 !important;
        }

        .flatpickr-weekday {
            color: #32745e !important;
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
            background: rgba(50, 116, 94, 0.1) !important;
            border-color: #32745e !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(50, 116, 94, 0.2) !important;
        }

        .flatpickr-day.selected {
            background: linear-gradient(135deg, #32745e 0%, #58907D 100%) !important;
            border-color: #32745e !important;
            color: white !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(50, 116, 94, 0.4) !important;
        }

        .flatpickr-day.today {
            border-color: #32745e !important;
            background: rgba(50, 116, 94, 0.1) !important;
            color: #32745e !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.today.selected {
            background: linear-gradient(135deg, #32745e 0%, #58907D 100%) !important;
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
            border-color: #32745e !important;
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
    </style>
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Lembur</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <div class="row" style="margin-top: 40px">
            <div class="col">
                <form action="{{ route('lembur.index') }}" method="GET">
                    <input type="text" class="feedback-input dari flatpickr-date" name="dari" placeholder="Dari" id="datePicker"
                        value="{{ Request('dari') }}" />
                    <input type="text" class="feedback-input sampai flatpickr-date" name="sampai" placeholder="Sampai" id="datePicker2"
                        value="{{ Request('sampai') }}" />
                    <button class="btn btn-primary w-100" id="btnSimpan"><ion-icon
                            name="search-circle-outline"></ion-icon>Cari</button>
                </form>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <div class="transactions">
                    @foreach ($lembur as $d)
                        <form method="POST" name="deleteform" class="deleteform me-1 mb-1"
                            action="{{ route('lembur.delete', Crypt::encrypt($d->id)) }}">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="item {{ $d->status == 0 ? 'cancel-confirm' : '' }}">
                                <div class="detail">
                                    <div class="avatar avatar-sm me-4">
                                        <span class="avatar-initial rounded-circle bg-success">
                                            <ion-icon name="time-outline"></ion-icon>
                                        </span>
                                    </div>
                                    <div>
                                        <strong>
                                            {{ DateToIndo($d->tanggal) }}
                                        </strong>
                                        <p>{{ date('d-m-Y H:i', strtotime($d->lembur_mulai)) }} -
                                            {{ date('d-m-Y H:i', strtotime($d->lembur_selesai)) }}</p>
                                        <p>{{ $d->keterangan }}</p>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="price">
                                        @if ($d->status == 0)
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif ($d->status == 1)
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif ($d->status == 2)
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </div>
                                    <div class="status">

                                    </div>
                                </div>
                            </a>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="fab-button  bottom-right dropdown" style="margin-bottom:70px">
            <a href="{{ route('lembur.create') }}" class="fab bg-primary">
                <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
            </a>
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
    </script>
@endpush
