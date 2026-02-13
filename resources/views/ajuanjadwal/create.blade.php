@extends('layouts.mobile.app')
@section('content')
    <style>
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

        /* Custom Floating Label CSS */
        .form-label-group {
            position: relative;
            margin-bottom: 5px; /* Compact spacing */
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 15px;
            top: 15px; /* Align with top padding of input */
            font-size: 22px;
            color: #32745e;
            z-index: 9;
            transition: all 0.3s ease;
            pointer-events: none; /* Ensure clicks pass through to input */
        }

        .form-label-group input,
        .form-label-group select,
        .form-label-group textarea {
            border-radius: 9px;
            height: 50px; /* Consistent height */
            padding: 20px 15px 5px 50px; /* Left padding increased for icon */
            font-size: 15px;
            line-height: 1.5;
            background-color: transparent !important;
            border: 1px solid #32745e;
            box-shadow: none;
            width: 100%;
            display: block;
            transition: all .1s;
        }
        
        .form-label-group textarea {
            height: 100px;
            padding-top: 25px; /* More padding top for textarea */
            resize: none;
        }

        .form-label-group label {
            position: absolute;
            top: 15px;
            left: 50px; /* Aligned with text start (after icon) */
            font-size: 15px;
            color: #32745e;
            pointer-events: none;
            transition: all .2s ease-in-out;
            margin-bottom: 0;
            background: transparent;
        }

        /* Active State (Focus or Has Value) */
        .form-label-group input:focus,
        .form-label-group select:focus,
        .form-label-group textarea:focus,
        .form-label-group input:not(:placeholder-shown),
        .form-label-group select:valid,
        .form-label-group textarea:not(:placeholder-shown) {
            border-color: #32745e; /* Theme color */
        }

        .form-label-group input:focus ~ label,
        .form-label-group select:focus ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group select:valid ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 5px;
            font-size: 11px;
            color: #32745e; /* Theme color */
            font-weight: 500;
        }
        
        /* Select specific fix since it always has value (even empty string matches :valid usually) */
        .form-label-group select {
             -webkit-appearance: none;
             -moz-appearance: none;
             appearance: none;
        }

    </style>
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Ajuan Perubahan Jadwal</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <div class="row" style="margin-top: 10px">
            <div class="col pl-3 pr-3">
                <form action="{{ route('ajuanjadwal.store') }}" method="POST" id="formAjuan" autocomplete="off">
                    @csrf
                    
                    @if(isset($karyawan) && count($karyawan) > 0)
                    <div class="form-label-group">
                        <ion-icon name="people-outline" class="input-icon"></ion-icon>
                        <select name="nik" id="nik" class="form-control" required>
                            <option value="" disabled selected></option>
                            @foreach ($karyawan as $d)
                                <option value="{{ $d->nik }}">{{ $d->nama_karyawan }} ({{ $d->nik }})</option>
                            @endforeach
                        </select>
                        <label for="nik">Pilih Karyawan</label>
                    </div>
                    @endif

                    <div class="form-label-group">
                        <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                        <input type="text" name="tanggal" id="tanggal" class="form-control" placeholder=" " required>
                        <label for="tanggal">Tanggal</label>
                    </div>
                    
                    <div class="form-label-group">
                        <ion-icon name="time-outline" class="input-icon"></ion-icon>
                        <select name="kode_jam_kerja_tujuan" id="kode_jam_kerja_tujuan" class="form-control" required>
                            <option value="" disabled selected></option>
                            @foreach ($jamkerja as $d)
                                <option value="{{ $d->kode_jam_kerja }}">{{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})</option>
                            @endforeach
                        </select>
                        <label for="kode_jam_kerja_tujuan">Shift Tujuan</label>
                    </div>

                    <div class="form-label-group">
                        <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                        <textarea name="keterangan" id="keterangan" class="form-control" placeholder=" " required></textarea>
                        <label for="keterangan">Alasan / Keterangan</label>
                    </div>

                    <div class="form-group mt-3">
                        <button class="btn btn-primary w-100" id="btnSimpan" style="height: 50px; border-radius: 9px;">
                            <i class="ti ti-send me-1"></i> Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        // Custom locale for Air Datepicker
        const localeIndo = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari ini',
            clear: 'Hapus',
            dateFormat: 'yyyy-MM-dd',
            timeFormat: 'HH:mm',
            firstDay: 1
        };

        new AirDatepicker('#tanggal', {
            locale: localeIndo,
            autoClose: true,
            isMobile: true, // Key feature for mobile
            buttons: ['today', 'clear'],
            position: 'auto center',
            onSelect: ({date, formattedDate, datepicker}) => {
                // Optional: trigger validation style or updates
            }
        });

        $("#formAjuan").submit(function(e) {
            let nik = $('#nik').val(); // Can be undefined if element doesn't exist
            let tanggal = $('#tanggal').val();
            let kode_jam_kerja = $('#kode_jam_kerja_tujuan').val();
            let keterangan = $('#keterangan').val();

            // Validate NIK only if the select element exists (Admin mode)
            if ($('#nik').length > 0 && (nik == "" || nik == null)) {
                 Swal.fire({title: "Oops!", text: 'Karyawan Harus Dipilih !', icon: "warning"});
                 return false;
            }

            if (tanggal == "") {
                 Swal.fire({title: "Oops!", text: 'Tanggal Harus Diisi !', icon: "warning"});
                 return false;
            } else if (kode_jam_kerja == "") {
                 Swal.fire({title: "Oops!", text: 'Shift Tujuan Harus Dipilih !', icon: "warning"});
                 return false;
            } else if (keterangan == "") {
                 Swal.fire({title: "Oops!", text: 'Keterangan Harus Diisi !', icon: "warning"});
                 return false;
            }
        });
    </script>
@endpush
