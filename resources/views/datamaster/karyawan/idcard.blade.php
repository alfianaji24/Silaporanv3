@extends('layouts.mobile.app')
@section('content')
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">ID Card</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section" class="idcard-page-content">

        <!-- Wrapper untuk pembesaran tampilan di layar (ukuran cetak tetap 53.98 x 85.6mm) -->
        <div class="idcard-display-scale">
        <!-- ID Card (frame fix: lebar 53.98mm x tinggi 85.6mm) -->
        <div class="idcard-wrapper" id="idcard-area">

            <!-- Abstract Background Shapes -->
            <div class="card-bg-shape shape-1"></div>
            <div class="card-bg-shape shape-2"></div>

            <!-- Header Section -->
            <div class="idcard-header">
                <div class="company-info">
                    <img src="{{ asset('assets/img/logo_kabtgr.png') }}" class="company-logo" alt="Logo">
                    <span class="company-name">PEMERINTAH</span>
                    <span class="company-name">KABUPATEN TANGERANG</span>
                    <span class="company-name">DINAS KESEHATAN</span>
                    <span class="company-name">UPTD PUSKESMAS BALARAJA</span>
                </div>
            </div>

            <!-- Profile & Main Info -->
            <div class="idcard-profile-section">
                <div class="profile-frame">
                    @if (!empty($karyawan->foto))
                        @if (Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="profile-pic" alt="Profile">
                        @else
                            <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}" class="profile-pic" alt="Profile">
                        @endif
                    @else
                        <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}" class="profile-pic" alt="Profile">
                    @endif
                </div>
                <div class="profile-info">
                    <h2 class="employee-name">{{ textUpperCase($karyawan->nama_karyawan) }}</h2>
                    <div class="employee-role-badge">{{ $karyawan->nama_jabatan }}</div>
                </div>
            </div>

            <!-- Barcode Section -->
            <!-- <div class="barcode-section">
               {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.8, 45, 'black') !!}
               <span class="barcode-text">{{ $karyawan->nik }}</span>
            </div> -->

            <!-- Footer Decor -->
            <div class="card-footer-decor"></div>
        </div>
        </div>

        <!-- Tombol di luar frame ID Card -->
        <div class="idcard-actions">
            <button type="button" id="download-idcard" class="btn-simpan-galeri">
                <ion-icon name="download-outline"></ion-icon>
                Simpan ke Galeri
            </button>
        </div>

    </div>

    <style>
        /* Modern ID Card CSS */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        body {
            background: #f0f3f8;
            font-family: 'Outfit', sans-serif;
        }

        /* Area halaman: card + tombol, tanpa frame tambahan */
        .idcard-page-content {
            margin-top: 70px;
            padding-bottom: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }

        /* Pembesaran tampilan di layar: ~1.65x agar terlihat seperti versi sebelumnya (340px) */
        .idcard-display-scale {
            transform: scale(1.65);
            transform-origin: center top;
            margin-bottom: 56mm; /* ruang untuk overflow akibat scale */
        }

        /* Tombol di luar frame ID Card */
        .idcard-actions {
            margin-top: 24px;
            width: 100%;
            max-width: 58.98mm;
            display: flex;
            justify-content: center;
        }
        .btn-simpan-galeri {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #32745e, #2a5d4b);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(50, 116, 94, 0.35);
        }
        .btn-simpan-galeri:active {
            opacity: 0.9;
        }
        .btn-simpan-galeri ion-icon {
            font-size: 1.25rem;
        }

        /* Ukuran fix: Lebar 53.98mm x Tinggi 85.6mm, tidak berubah meski konten bertambah */
        .idcard-wrapper {
            width: 53.98mm;
            height: 85.6mm;
            min-width: 53.98mm;
            max-width: 53.98mm;
            min-height: 85.6mm;
            max-height: 85.6mm;
            background: #ffffff;
            border-radius: 3mm;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.08);
            box-sizing: border-box;
        }

        /* Abstract shapes - proporsional dengan frame */
        .card-bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(8px);
            z-index: 0;
        }
        .shape-1 {
            width: 25mm;
            height: 25mm;
            background: rgba(50, 116, 94, 0.1);
            top: -5mm;
            right: -5mm;
        }
        .shape-2 {
            width: 18mm;
            height: 18mm;
            background: rgba(88, 144, 125, 0.12);
            bottom: -4mm;
            left: -4mm;
        }

        /* Header - ringkas untuk frame 53.98 x 85.6mm */
        .idcard-header {
            position: relative;
            z-index: 2;
            padding: 1.5mm 2mm 0 2mm;
            flex-shrink: 0;
        }
        .company-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5mm;
        }
        .company-logo {
            width: 15mm;
            margin-bottom: 2mm;
            height: auto;
            max-height: 15mm;
            object-fit: contain;
        }
        .company-name {
            display: block;
            text-align: center;
            font-weight: 600;
            color: #32745e;
            font-size: 10px;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.1px;
        }

        /* Profile - foto di tengah, nama & jabatan di bawah */
        .idcard-profile-section {
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-top: 3mm;
            padding: 0 2mm;
            flex-shrink: 0;
        }
        .profile-frame {
            width: 22mm;
            height: 22mm;
            flex-shrink: 0;
            margin: 0 auto;
            padding: 1px;
            background: white;
            border-radius: 0%;
            box-shadow: 0 1px 4px rgba(50, 116, 94, 0.25);
            position: relative;
        }
        .profile-frame::after {
            content: '';
            position: absolute;
            top: -1.5px; left: -1.5px; right: -1.5px; bottom: -1.5px;
            border-radius: 0%;
            background: linear-gradient(135deg, #32745e, #81c7af);
            z-index: -1;
        }
        .profile-pic {
            width: 100%;
            height: 100%;
            border-radius: 0%;
            object-fit: cover;
            border: 1.5px solid #fff;
        }
        .profile-info {
            margin-top: 3mm;
            width: 100%;
        }
        .employee-name {
            margin-top: 2mm;
            font-size: 11px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .employee-role-badge {
            display: inline-block;
            margin-top: 0.5mm;
            padding: 0.5mm 1.5mm;
            background: #e8f5f1;
            color: #32745e;
            font-size: 8px;
            font-weight: 600;
            border-radius: 2px;
        }

        /* Details - padat */
        .idcard-details {
            z-index: 2;
            margin-top: 1.5mm;
            padding: 0 2mm;
            display: flex;
            flex-direction: column;
            gap: 1mm;
            flex-shrink: 0;
        }
        .detail-row {
            display: flex;
            align-items: center;
            gap: 1.5mm;
        }
        .detail-icon {
            width: 5mm;
            height: 5mm;
            min-width: 5mm;
            background: #f8fafb;
            border-radius: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #32745e;
            font-size: 6px;
        }
        .detail-content {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .detail-content .label {
            font-size: 3.5px;
            color: #8898aa;
            font-weight: 500;
            text-transform: uppercase;
        }
        .detail-content .value {
            font-size: 5px;
            color: #333;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Barcode */
        .barcode-section {
            z-index: 2;
            margin-top: 1.5mm;
            margin-bottom: 0;
            text-align: center;
            flex-shrink: 0;
        }
        .barcode-section > div {
            margin: 0 auto !important;
            display: inline-block;
            transform: scale(0.5);
            transform-origin: center top;
        }
        .barcode-text {
            display: block;
            margin-top: 0.5mm;
            font-size: 4px;
            letter-spacing: 0.5px;
            color: #555;
        }

        /* Footer Decor */
        .card-footer-decor {
            height: 1.5mm;
            width: 100%;
            background: linear-gradient(90deg, #32745e, #58907D);
            position: absolute;
            bottom: 0;
            left: 0;
            flex-shrink: 0;
        }

        /* Cetak: ukuran tetap 53.98mm x 85.6mm */
        @media print {
            .idcard-wrapper {
                width: 53.98mm !important;
                height: 85.6mm !important;
                min-width: 53.98mm !important;
                max-width: 53.98mm !important;
                min-height: 85.6mm !important;
                max-height: 85.6mm !important;
                box-shadow: none;
                page-break-inside: avoid;
            }
        }

        @media screen and (max-width: 360px) {
            .idcard-actions {
                max-width: 100%;
                padding: 0 16px;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('download-idcard');
            if (btn) {
                btn.addEventListener('click', function() {
                    var area = document.getElementById('idcard-area');
                    if (!area) {
                        alert('ID Card tidak ditemukan!');
                        return;
                    }
                    if (typeof html2canvas === 'undefined') {
                        alert('Gagal memuat html2canvas. Pastikan koneksi internet Anda stabil.');
                        return;
                    }

                    // Show loading state
                    var originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
                    btn.disabled = true;

                    html2canvas(area, {
                        backgroundColor: null,
                        scale: 3, // Higher scale for better quality
                        useCORS: true, // Important for loading images
                        logging: false
                    }).then(function(canvas) {
                        var link = document.createElement('a');
                        link.download = 'IDCard-{{ $karyawan->nama_karyawan }}.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();

                        // Reset button
                        btn.innerHTML = originalText;
                        btn.disabled = false;

                        // Success feedback (optional, using available toastr if present)
                        if(typeof toastr !== 'undefined') {
                            toastr.success('ID Card berhasil disimpan!');
                        }
                    }).catch(function(e) {
                        alert('Gagal membuat gambar: ' + e);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
                });
            }
        });
    </script>
@endsection
