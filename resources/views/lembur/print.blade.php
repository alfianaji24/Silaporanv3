<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Surat Perintah Lembur - {{ $lembur->nama_karyawan }} - {{ $no_spl }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 15mm 20mm;
        }

        body {
            font-family: 'Inter', Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.5;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Container styling */
        .wrapper {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header Logo & Info */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-logo {
            width: 75px;
            padding-right: 15px;
            vertical-align: middle;
        }

        .header-logo img {
            max-width: 75px;
            height: auto;
            display: block;
        }

        .header-info {
            vertical-align: middle;
            text-align: left;
        }

        .company-name {
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 3px 0;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .company-branch {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin: 0 0 2px 0;
        }

        .company-address {
            font-size: 11px;
            color: #475569;
            margin: 0;
            line-height: 1.4;
        }

        /* Document Title */
        .doc-title-container {
            text-align: center;
            margin: 20px 0 25px 0;
        }

        .doc-title {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 4px 0;
            color: #0f172a;
            text-decoration: underline;
        }

        .doc-number {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin: 0;
        }

        .intro-text {
            font-size: 12px;
            color: #334155;
            margin-bottom: 15px;
            text-align: justify;
        }

        /* Section Title */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 18px;
            margin-bottom: 10px;
            color: #334155;
            letter-spacing: 0.5px;
        }

        /* Info tables */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 5px 4px;
            vertical-align: top;
            font-size: 12px;
        }

        .info-table td.label {
            width: 170px;
            font-weight: 600;
            color: #475569;
        }

        .info-table td.separator {
            width: 15px;
            text-align: center;
            color: #475569;
        }

        .info-table td.value {
            color: #0f172a;
        }

        /* Detail Box Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 15px;
        }

        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
            letter-spacing: 0.3px;
        }

        .items-table td {
            padding: 8px 10px;
            font-size: 12px;
            border: 1px solid #cbd5e1;
            color: #1e293b;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-approved {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-rejected {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Signature block */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-date {
            text-align: right;
            font-size: 12px;
            color: #334155;
            margin-bottom: 15px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-title {
            font-weight: 600;
            color: #475569;
            font-size: 11px;
            margin-bottom: 65px; /* Space for physical signature */
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .signature-role {
            font-size: 10px;
            color: #64748b;
        }

        .note-box {
            margin-top: 25px;
            padding: 8px 12px;
            background-color: #f8fafc;
            border-left: 3px solid #64748b;
            font-size: 10px;
            color: #64748b;
            font-style: italic;
        }

        /* Print Media queries */
        @media print {
            body {
                background: #ffffff;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .items-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Floating Toolbar for Screen Mode */
        .no-print-toolbar {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
            z-index: 9999;
        }

        .btn-action {
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: #0f172a;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #1e293b;
        }

        .btn-close {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
    </style>
</head>

<body>
    <!-- Floating Toolbar (Not visible on print) -->
    <div class="no-print-toolbar no-print">
        <button onclick="window.print()" class="btn-action btn-print">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Cetak Dokumen
        </button>
        <button onclick="window.close()" class="btn-action btn-close">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Tutup
        </button>
    </div>

    @php
        $daftar_hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $nama_hari = $daftar_hari[date('l', strtotime($lembur->tanggal))] ?? '';

        // Perhitungan Estimasi Durasi
        $start_est = strtotime($lembur->lembur_mulai);
        $end_est = strtotime($lembur->lembur_selesai);
        $diff_est = $end_est - $start_est;
        $est_hours = floor($diff_est / 3600);
        $est_minutes = floor(($diff_est % 3600) / 60);
        $durasi_rencana = $est_hours . " Jam" . ($est_minutes > 0 ? " " . $est_minutes . " Menit" : "");

        // Perhitungan Realisasi
        $durasi_aktual = null;
        if (!empty($lembur->lembur_in) && !empty($lembur->lembur_out)) {
            $start_act = strtotime($lembur->lembur_in);
            $end_act = strtotime($lembur->lembur_out);
            $diff_act = $end_act - $start_act;
            $act_hours = floor($diff_act / 3600);
            $act_minutes = floor(($diff_act % 3600) / 60);
            $durasi_aktual = $act_hours . " Jam" . ($act_minutes > 0 ? " " . $act_minutes . " Menit" : "");
        }
    @endphp

    <div class="wrapper">
        <!-- Header / Kop Surat -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if (!empty($generalsetting->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists('logo/' . $generalsetting->logo))
                        <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo">
                    @else
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                    @endif
                </td>
                <td class="header-info">
                    <div class="company-name">{{ $generalsetting->nama_aplikasi ?? 'SILAPORAN' }}</div>
                    <div class="company-branch">{{ $lembur->nama_cabang }}</div>
                    <div class="company-address">
                        {{ $lembur->alamat_cabang ?? 'Kantor Operasional' }}
                        @if(!empty($lembur->telepon_cabang)) | Telp: {{ $lembur->telepon_cabang }} @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Document Title -->
        <div class="doc-title-container">
            <div class="doc-title">SURAT PERINTAH KERJA LEMBUR (SPKL)</div>
            <div class="doc-number">Nomor: {{ $no_spl }}</div>
        </div>

        <div class="intro-text">
            Berdasarkan kebutuhan operasional perusahaan, dengan ini pimpinan/atasan memberikan penugasan dan perintah kerja lembur kepada karyawan di bawah ini:
        </div>

        <!-- Section 1: Data Karyawan -->
        <div class="section-title">I. Identitas Karyawan Penerima Perintah</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ textUpperCase($lembur->nama_karyawan) }}</strong></td>
            </tr>
            <tr>
                <td class="label">NIK / NIK Perusahaan</td>
                <td class="separator">:</td>
                <td class="value">{{ $lembur->nik_show ?? $lembur->nik }} (Sistem: {{ $lembur->nik }})</td>
            </tr>
            <tr>
                <td class="label">Jabatan / Posisi</td>
                <td class="separator">:</td>
                <td class="value">{{ $lembur->nama_jabatan }}</td>
            </tr>
            <tr>
                <td class="label">Departemen / Unit Kerja</td>
                <td class="separator">:</td>
                <td class="value">{{ $lembur->nama_dept }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi Kantor / Cabang</td>
                <td class="separator">:</td>
                <td class="value">{{ $lembur->nama_cabang }}</td>
            </tr>
        </table>

        <!-- Section 2: Rincian Penugasan Lembur -->
        <div class="section-title">II. Rincian Pelaksanaan Kerja Lembur</div>
        <table class="items-table">
            <tr>
                <th style="width: 25%;">Hari / Tanggal</th>
                <td style="width: 75%;">
                    <strong>{{ $nama_hari }}, {{ DateToIndo($lembur->tanggal) }}</strong>
                </td>
            </tr>
            <tr>
                <th>Waktu Rencana Lembur</th>
                <td>
                    {{ date('H:i', strtotime($lembur->lembur_mulai)) }} WIB s/d {{ date('H:i', strtotime($lembur->lembur_selesai)) }} WIB
                    <span style="color: #64748b; font-size: 11px;">(Estimasi Durasi: <strong>{{ $durasi_rencana }}</strong>)</span>
                </td>
            </tr>
            <tr>
                <th>Uraian Tugas / Alasan Lembur</th>
                <td style="white-space: pre-line; line-height: 1.6;">{{ $lembur->keterangan }}</td>
            </tr>
            <tr>
                <th>Status Persetujuan</th>
                <td>
                    @if ($lembur->status == 1)
                        <span class="badge-status badge-approved">Disetujui (Approved)</span>
                    @elseif ($lembur->status == 2)
                        <span class="badge-status badge-rejected">Ditolak (Rejected)</span>
                    @else
                        <span class="badge-status badge-pending">Menunggu Persetujuan (Pending)</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Section 3: Realisasi Pelaksanaan (Jika Sudah Dimulai / Selesai) -->
        @if(!empty($lembur->lembur_in))
        <div class="section-title">III. Realisasi Presensi Lembur</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 33.33%;">Waktu Mulai Lembur</th>
                    <th style="width: 33.33%;">Waktu Selesai Lembur</th>
                    <th style="width: 33.33%;">Total Durasi Aktual</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ date('d-m-Y H:i', strtotime($lembur->lembur_in)) }} WIB
                    </td>
                    <td>
                        @if(!empty($lembur->lembur_out))
                            {{ date('d-m-Y H:i', strtotime($lembur->lembur_out)) }} WIB
                        @else
                            <span style="color: #d97706; font-style: italic;">Sedang Berlangsung</span>
                        @endif
                    </td>
                    <td>
                        @if($durasi_aktual)
                            <strong>{{ $durasi_aktual }}</strong>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- Section 4: Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-date">
                {{ $lembur->nama_cabang }}, {{ DateToIndo($lembur->tanggal) }}
            </div>

            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-title">Karyawan yang Ditugaskan,</div>
                        <div class="signature-name">{{ textCamelCase($lembur->nama_karyawan) }}</div>
                        <div class="signature-role">Penerima Perintah</div>
                    </td>
                    <td>
                        <div class="signature-title">Atasan Langsung / Pemohon,</div>
                        <div class="signature-name">( ............................................ )</div>
                        <div class="signature-role">Pemberi Perintah</div>
                    </td>
                    <td>
                        <div class="signature-title">Mengetahui & Menyetujui,</div>
                        <div class="signature-name">{{ !empty($generalsetting->nama_hrd) ? $generalsetting->nama_hrd : '( ............................................ )' }}</div>
                        <div class="signature-role">HRD / Pimpinan Perusahaan</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="note-box">
            <strong>Catatan:</strong> Surat Perintah Kerja Lembur ini merupakan dokumen resmi perusahaan. Karyawan yang melaksanakan kerja lembur wajib melakukan presensi lembur masuk dan pulang sesuai dengan jam penugasan yang telah disetujui.
        </div>
    </div>
</body>

</html>
