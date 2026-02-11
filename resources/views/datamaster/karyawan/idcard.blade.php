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

    <div id="content-section" style="margin-top: 70px; padding-bottom: 100px; display: flex; flex-direction: column; align-items: center;">
        
        <!-- ID Card Wrapper -->
        <div class="idcard-wrapper" id="idcard-area">
            
            <!-- Abstract Background Shapes -->
            <div class="card-bg-shape shape-1"></div>
            <div class="card-bg-shape shape-2"></div>

            <!-- Header Section -->
            <div class="idcard-header">
                <div class="company-info">
                    @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                        <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" class="company-logo" alt="Logo">
                    @else
                        <img src="https://placehold.co/100x100?text=Logo" class="company-logo" alt="Logo">
                    @endif
                    <span class="company-name">{{ $generalsetting->nama_perusahaan ?? 'Company Name' }}</span>
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
                <h2 class="employee-name">{{ textUpperCase($karyawan->nama_karyawan) }}</h2>
                <div class="employee-role-badge">{{ $karyawan->nama_jabatan }}</div>
            </div>

            <!-- Details Section -->
            <div class="idcard-details">
                <div class="detail-row">
                    <div class="detail-icon"><ion-icon name="id-card-outline"></ion-icon></div>
                    <div class="detail-content">
                        <span class="label">NIK Identity</span>
                        <span class="value">{{ $karyawan->nik }}</span>
                    </div>
                </div>
                <div class="detail-row">
                     <div class="detail-icon"><ion-icon name="business-outline"></ion-icon></div>
                    <div class="detail-content">
                        <span class="label">Department</span>
                        <span class="value">{{ $karyawan->nama_dept }}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon"><ion-icon name="calendar-outline"></ion-icon></div>
                    <div class="detail-content">
                        <span class="label">Joined Date</span>
                        <span class="value">{{ date('d F Y', strtotime($karyawan->tanggal_masuk)) }}</span>
                    </div>
                </div>
            </div>

            <!-- Barcode Section -->
            <div class="barcode-section">
                {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.8, 45, 'black') !!}
                <span class="barcode-text">{{ $karyawan->nik }}</span>
            </div>

            <!-- Footer Decor -->
            <div class="card-footer-decor"></div>
        </div>

        <!-- Download Button -->
        <div class="action-buttons mt-4">
            <button id="download-idcard" class="btn btn-primary btn-lg rounded-pill shadow-lg" style="background: linear-gradient(135deg, #32745e, #2a5d4b); border: none; padding-left: 25px; padding-right: 25px;">
                <ion-icon name="download-outline" style="margin-right: 8px;"></ion-icon>
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

        .idcard-wrapper {
            width: 340px;
            /* height: 580px; */
            background: #ffffff;
            border-radius: 25px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255,255,255,0.5);
            margin-bottom: 20px;
        }

        /* Abstract shapes */
        .card-bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            z-index: 0;
        }
        .shape-1 {
            width: 250px;
            height: 250px;
            background: rgba(50, 116, 94, 0.1);
            top: -50px;
            right: -50px;
        }
        .shape-2 {
            width: 200px;
            height: 200px;
            background: rgba(88, 144, 125, 0.15);
            bottom: -50px;
            left: -50px;
        }

        /* Header */
        .idcard-header {
            position: relative;
            z-index: 2;
            padding: 25px 25px 0 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .company-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .company-logo {
            width: 40px;
            height: auto;
        }
        .company-name {
            font-weight: 600;
            color: #32745e;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Profile */
        .idcard-profile-section {
            z-index: 2;
            text-align: center;
            margin-top: 30px;
        }
        .profile-frame {
            width: 130px;
            height: 130px;
            margin: 0 auto 15px;
            padding: 4px;
            background: white; /* linear-gradient(135deg, #32745e, #58907D); */
            border-radius: 50%;
            box-shadow: 0 10px 25px rgba(50, 116, 94, 0.2);
            position: relative;
        }
        /* Ring effect */
        .profile-frame::after {
            content: '';
            position: absolute;
            top: -5px; left: -5px; right: -5px; bottom: -5px;
            border-radius: 50%;
            background: linear-gradient(135deg, #32745e, #81c7af);
            z-index: -1;
        }

        .profile-pic {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
        }
        .employee-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            line-height: 1.2;
        }
        .employee-role-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 5px 15px;
            background: #e8f5f1;
            color: #32745e;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 20px;
        }

        /* Details */
        .idcard-details {
            z-index: 2;
            margin-top: 30px;
            padding: 0 35px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .detail-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .detail-icon {
            width: 36px;
            height: 36px;
            background: #f8fafb;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #32745e;
            font-size: 1.2rem;
        }
        .detail-content {
            display: flex;
            flex-direction: column;
        }
        .detail-content .label {
            font-size: 0.75rem;
            color: #8898aa;
            font-weight: 500;
            text-transform: uppercase;
        }
        .detail-content .value {
            font-size: 0.95rem;
            color: #333;
            font-weight: 600;
        }

        /* Barcode */
        .barcode-section {
            z-index: 2;
            margin-top: 35px;
            margin-bottom: 25px;
            text-align: center;
        }
        .barcode-section > div {
            margin: 0 auto !important; /* Center the generated barcode div */
            display: inline-block;
        }
        .barcode-text {
            display: block;
            margin-top: 5px;
            font-size: 0.85rem;
            letter-spacing: 2px;
            color: #555;
        }

        /* Footer Decor */
        .card-footer-decor {
            height: 8px;
            width: 100%;
            background: linear-gradient(90deg, #32745e, #58907D);
            position: absolute;
            bottom: 0;
            left: 0;
        }

        @media screen and (max-width: 360px) {
            .idcard-wrapper {
                width: 90%;
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
