<div class="title" style="text-align: center; margin-bottom: 10px;">
    <h1 style="font-size: 12pt; font-weight: bold; text-decoration: underline; margin-bottom: 2px; text-transform: uppercase;">SURAT PERINTAH KERJA LEMBUR (SPKL)</h1>
    <p style="font-size: 10pt; margin: 0; color: #333333; font-weight: bold;">Nomor : @{{no_spl}}</p>
</div>

<div class="content">
    <p style="text-align: justify; margin-bottom: 8px; font-size: 9.5pt; line-height: 1.25;">
        Berdasarkan kebutuhan operasional dan penyelesaian target pekerjaan perusahaan, dengan ini pimpinan/atasan memberikan perintah dan penugasan kerja lembur kepada karyawan berikut:
    </p>

    <!-- Bagian I: Data Karyawan -->
    <p style="font-weight: bold; margin-bottom: 3px; text-transform: uppercase; font-size: 9.5pt; border-bottom: 1px solid #333; padding-bottom: 2px;">I. Data Karyawan Yang Ditugaskan</p>
    <table style="width: 100%; margin-bottom: 8px; border-collapse: collapse; font-size: 9.5pt;">
        <tr>
            <td style="width: 150px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>
            <td style="width: 10px; padding: 2px 0; vertical-align: top;">:</td>
            <td style="padding: 2px 0; vertical-align: top;"><strong>@{{nama_karyawan}}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0; vertical-align: top;">NIK / NIK Perusahaan</td>
            <td style="padding: 2px 0; vertical-align: top;">:</td>
            <td style="padding: 2px 0; vertical-align: top;">@{{nik}}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0; vertical-align: top;">Jabatan</td>
            <td style="padding: 2px 0; vertical-align: top;">:</td>
            <td style="padding: 2px 0; vertical-align: top;">@{{jabatan}}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0; vertical-align: top;">Departemen / Divisi</td>
            <td style="padding: 2px 0; vertical-align: top;">:</td>
            <td style="padding: 2px 0; vertical-align: top;">@{{departemen}}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0; vertical-align: top;">Kantor Cabang / Unit</td>
            <td style="padding: 2px 0; vertical-align: top;">:</td>
            <td style="padding: 2px 0; vertical-align: top;">@{{cabang}}</td>
        </tr>
    </table>

    <!-- Bagian II: Rincian Tugas Lembur -->
    <p style="font-weight: bold; margin-bottom: 3px; text-transform: uppercase; font-size: 9.5pt; border-bottom: 1px solid #333; padding-bottom: 2px;">II. Rincian Penugasan Kerja Lembur</p>
    <table style="width: 100%; margin-bottom: 8px; border-collapse: collapse; border: 1px solid #333; font-size: 9.5pt;">
        <tr>
            <td style="width: 28%; padding: 4px 8px; border: 1px solid #333; font-weight: bold; background-color: #f2f2f2;">Hari / Tanggal</td>
            <td style="width: 72%; padding: 4px 8px; border: 1px solid #333;"><strong>@{{hari}}, @{{tanggal_lembur}}</strong></td>
        </tr>
        <tr>
            <td style="padding: 4px 8px; border: 1px solid #333; font-weight: bold; background-color: #f2f2f2;">Waktu Penugasan</td>
            <td style="padding: 4px 8px; border: 1px solid #333;">@{{jam_mulai}} s/d @{{jam_selesai}} WIB (@{{estimasi_durasi}})</td>
        </tr>
        <tr>
            <td style="padding: 4px 8px; border: 1px solid #333; font-weight: bold; background-color: #f2f2f2;">Uraian Tugas Lembur</td>
            <td style="padding: 4px 8px; border: 1px solid #333; line-height: 1.25;">@{{keterangan_lembur}}</td>
        </tr>
        <tr>
            <td style="padding: 4px 8px; border: 1px solid #333; font-weight: bold; background-color: #f2f2f2;">Status Persetujuan</td>
            <td style="padding: 4px 8px; border: 1px solid #333;"><strong>@{{status_approval}}</strong></td>
        </tr>
    </table>

    <!-- Bagian III: Ketentuan -->
    <p style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 9.5pt; border-bottom: 1px solid #333; padding-bottom: 2px;">III. Ketentuan Pelaksanaan Lembur</p>
    <ol style="margin-top: 3px; margin-bottom: 12px; padding-left: 18px; line-height: 1.25; font-size: 9pt;">
        <li>Karyawan wajib melaksanakan tugas lembur dengan penuh rasa tanggung jawab sesuai dengan uraian tugas di atas.</li>
        <li>Karyawan wajib melakukan presensi lembur masuk dan pulang secara tepat waktu melalui sistem presensi perusahaan.</li>
        <li>Perhitungan upah/insentif lembur mengacu pada ketentuan dan peraturan ketenagakerjaan yang berlaku di perusahaan.</li>
    </ol>

    <!-- Bagian IV: Tanda Tangan -->
    <div style="margin-top: 15px; page-break-inside: avoid;">
        <p style="text-align: right; margin-bottom: 6px; font-size: 9.5pt;">
            @{{cabang}}, @{{tanggal_lembur}}
        </p>
        <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 9.5pt;">
            <tr>
                <td style="width: 33.33%; vertical-align: top;">
                    <p style="margin-bottom: 45px; font-weight: bold; margin-top: 0;">Karyawan Yang Ditugaskan,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">@{{nama_karyawan}}</p>
                    <p style="margin: 0; font-size: 8.5pt; color: #555;">Penerima Perintah</p>
                </td>
                <td style="width: 33.33%; vertical-align: top;">
                    <p style="margin-bottom: 45px; font-weight: bold; margin-top: 0;">Atasan Langsung / Pemohon,</p>
                    <p style="margin: 0; font-weight: bold;">( ............................................ )</p>
                    <p style="margin: 0; font-size: 8.5pt; color: #555;">Pemberi Perintah</p>
                </td>
                <td style="width: 33.33%; vertical-align: top;">
                    <p style="margin-bottom: 45px; font-weight: bold; margin-top: 0;">Mengetahui & Menyetujui,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">@{{nama_hrd}}</p>
                    <p style="margin: 0; font-size: 8.5pt; color: #555;">HRD / Pimpinan Perusahaan</p>
                </td>
            </tr>
        </table>
    </div>
</div>
