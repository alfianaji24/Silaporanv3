<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Presensi Karyawan</title>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000000">
        <!-- HEADER -->
        <tr>
            <td colspan="10" style="text-align: left; vertical-align: middle;">
                <h4 style="line-height: 20px; margin-bottom: 5px; font-weight: bold; font-size: 14px">
                    LAPORAN PRESENSI<br>
                    {{ $generalsetting->nama_perusahaan }}<br>
                    PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} - {{ date('d-m-Y', strtotime($periode_sampai)) }}
                </h4>
                <span style="font-style: italic; font-size: 12px">{{ $generalsetting->alamat }}</span><br>
                <span style="font-style: italic; font-size: 12px">{{ $generalsetting->telepon }}</span>
            </td>
        </tr>
        <tr><td colspan="10"></td></tr>
        
        <!-- DATA KARYAWAN -->
        <tr>
            <td colspan="2" rowspan="5" style="border: 1px solid #000000; vertical-align: top;"></td>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000000;">NIK</td>
            <td colspan="6" style="border: 1px solid #000000;">: {{ $karyawan->nik_show ?? $karyawan->nik }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000000;">Nama</td>
            <td colspan="6" style="border: 1px solid #000000;">: {{ $karyawan->nama_karyawan }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000000;">Jabatan</td>
            <td colspan="6" style="border: 1px solid #000000;">: {{ $karyawan->nama_jabatan }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000000;">Departemen</td>
            <td colspan="6" style="border: 1px solid #000000;">: {{ $karyawan->nama_dept }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000000;">Cabang</td>
            <td colspan="6" style="border: 1px solid #000000;">: {{ $karyawan->nama_cabang }}</td>
        </tr>
        <tr><td colspan="10"></td></tr>

        <!-- TABEL PRESENSI -->
        <tr>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">No</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Tanggal</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Hari</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Jadwal</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Masuk</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Pulang</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Status</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Terlambat</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Denda</th>
            <th style="border: 1px solid #000000; background-color: #0949b8; color: #fff; text-align: center; vertical-align: middle; font-weight: bold;">Pot. Jam</th>
        </tr>

        @php
            $total_hadir = 0;
            $total_izin = 0;
            $total_sakit = 0;
            $total_cuti = 0;
            $total_alfa = 0;
            $total_terlambat = 0;
            $total_denda = 0;
            $total_potongan_jam = 0;

            // Mapping presensi per tanggal agar mudah diakses
            $presensiByDate = [];
            foreach ($presensi as $row) {
                $presensiByDate[$row->tanggal] = $row;
            }

            // Mapping jadwal kerja untuk karyawan ini
            $mapJadwalByDate = $jadwal_bydate[$karyawan->nik] ?? [];
            $mapJadwalGrupByDate = $jadwal_grup_bydate[$karyawan->nik] ?? [];
            $mapJadwalByDay = $jadwal_byday[$karyawan->nik] ?? [];
            $keyDeptCabang = $karyawan->kode_dept . '|' . $karyawan->kode_cabang;
            $mapJadwalByDept = $jadwal_bydept[$keyDeptCabang] ?? [];

            $tanggal_presensi = $periode_dari;
            $no = 1;
        @endphp

        @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
            @php
                $d = $presensiByDate[$tanggal_presensi] ?? null;

                $jam_masuk = $d ? $d->tanggal . ' ' . $d->jam_masuk : null;
                $terlambat = $d ? hitungjamterlambat($d->jam_in, $jam_masuk) : null;
                $pulangcepat = $d
                    ? hitungpulangcepat(
                        $d->tanggal,
                        $d->jam_out,
                        $d->jam_pulang,
                        $d->istirahat,
                        $d->jam_awal_istirahat,
                        $d->jam_akhir_istirahat,
                        $d->lintashari,
                    )
                    : 0;
                if ($d) {
                    $pulangcepat = $pulangcepat > $d->total_jam ? $d->total_jam : $pulangcepat;
                }

                // Default status
                $status = $d ? $d->status : '-';
                $color_status_hex = '#FFFFFF'; // Default white
                $text_color_hex = '#000000'; // Default black

                if ($status == 'h') {
                    $color_status_hex = '#008000'; // Green
                    $text_color_hex = '#FFFFFF';
                } elseif ($status == 'i') {
                    $color_status_hex = '#FFFF00'; // Yellow
                    $text_color_hex = '#000000';
                } elseif ($status == 's') {
                    $color_status_hex = '#0000FF'; // Blue
                    $text_color_hex = '#FFFFFF';
                } elseif ($status == 'c') {
                    $color_status_hex = '#FFA500'; // Orange
                    $text_color_hex = '#FFFFFF';
                } elseif ($status == 'a') {
                    $color_status_hex = '#FF0000'; // Red
                    $text_color_hex = '#FFFFFF';
                } else {
                    $color_status_hex = '#808080'; // Gray
                    $text_color_hex = '#FFFFFF';
                }

                // Hitung Denda & Potongan Jam
                if ($terlambat != null) {
                    if ($terlambat['desimal_terlambat'] < 1) {
                        $potongan_jam_terlambat = 0;
                        $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                    } else {
                        $potongan_jam_terlambat =
                            $d && $terlambat['desimal_terlambat'] > $d->total_jam ? $d->total_jam : $terlambat['desimal_terlambat'];
                        $denda = 0;
                    }
                } else {
                    $potongan_jam_terlambat = 0;
                    $denda = 0;
                }

                 // Rekap & potongan jam (Logic duplicated from print view)
                 if ($d) {
                    if ($d->status == 'h') {
                        $total_hadir++;
                        $total_terlambat += $terlambat['desimal_terlambat'] ?? 0;
                    } elseif ($d->status == 'i') {
                        $total_izin++;
                    } elseif ($d->status == 's') {
                        $total_sakit++;
                    } elseif ($d->status == 'c') {
                        $total_cuti++;
                    } elseif ($d->status == 'a') {
                        $total_alfa++;
                    }

                    $total_denda += $denda;
                    $potongan_tidak_absen_masuk_atau_pulang = empty($d->jam_out) || empty($d->jam_in) ? $d->total_jam : 0;
                    $potongan_jam =
                        $potongan_tidak_absen_masuk_atau_pulang == 0
                            ? $pulangcepat + $potongan_jam_terlambat
                            : $potongan_tidak_absen_masuk_atau_pulang;
                } else {
                     // Tidak ada data presensi → cek apakah punya jadwal kerja
                     $potongan_jam = 0;
                     $denda = 0;
                     $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;
                     if ($totalJamJadwal === null) {
                         $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                     }
                     if ($totalJamJadwal === null) {
                         $nama_hari = getHari($tanggal_presensi);
                         $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                     }
                     if ($totalJamJadwal === null) {
                         $nama_hari = isset($nama_hari) ? $nama_hari : getHari($tanggal_presensi);
                         $totalJamJadwal = $mapJadwalByDept[$nama_hari] ?? null;
                     }

                     if ($totalJamJadwal !== null) {
                         $status = 'a';
                         $color_status_hex = '#FF0000'; // Red
                         $text_color_hex = '#FFFFFF';
                         $potongan_jam = $totalJamJadwal;
                         $total_alfa++;
                     } else {
                         $status = 'libur';
                         $color_status_hex = '#008000'; // Green
                         $text_color_hex = '#FFFFFF';
                     }
                }
                $total_potongan_jam += $potongan_jam;
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $no }}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ date('d-m-y', strtotime($tanggal_presensi)) }}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ getHari($tanggal_presensi) }}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                    @if ($d)
                        {{ $d->nama_jam_kerja }} - {{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }}
                    @else
                        -
                    @endif
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                    @if ($d && $d->jam_in != null)
                        {{ date('H:i', strtotime($d->jam_in)) }}
                    @elseif ($d)
                        <span style="color: #FF0000">Belum Absen</span>
                    @else
                        <span style="color: #FF0000">Belum Absen</span>
                    @endif
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                    @if ($d && $d->jam_out != null)
                        {{ date('H:i', strtotime($d->jam_out)) }}
                    @elseif ($d)
                        <span style="color: #FF0000">Belum Absen</span>
                    @else
                        <span style="color: #FF0000">Belum Absen</span>
                    @endif
                    @if ($pulangcepat > 0)
                        <br>
                        <span style="color: #FF0000">(-{{ $pulangcepat }})</span>
                    @endif
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $color_status_hex }}; color: {{ $text_color_hex }};">
                     @if ($status == 'libur')
                        LIBUR
                    @elseif ($status != '-')
                        {{ textUpperCase($status) }}
                    @else
                        -
                    @endif
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                     @if($terlambat != null)
                        <span style="color: {{ isset($terlambat['color']) && $terlambat['color'] == 'red' ? '#FF0000' : '#000000' }}">
                            {{ $terlambat['show_laporan'] ?? $terlambat['show'] }}
                        </span>
                     @endif
                </td>
                <td style="border: 1px solid #000000; text-align: right; vertical-align: middle; color: #FF0000;">
                    {{ $denda ? formatAngka($denda) : '' }}
                </td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; color: #FF0000;">
                    {{ $potongan_jam }}
                </td>
            </tr>
            @php
                $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                $no++;
            @endphp
        @endwhile

        <!-- REKAPITULASI -->
        <tr><td colspan="10"></td></tr>
        <tr>
            <th colspan="2" style="border: 1px solid #000000; background-color: #0949b8; color: #fff; font-weight: bold;">Rekapitulasi Presensi</th>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Hadir</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $total_hadir }}</td>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Izin</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $total_izin }}</td>
             <td colspan="8"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Sakit</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $total_sakit }}</td>
             <td colspan="8"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Cuti</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $total_cuti }}</td>
             <td colspan="8"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Alfa</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $total_alfa }}</td>
             <td colspan="8"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Terlambat</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $total_terlambat }} Jam</td>
             <td colspan="8"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000;">Denda</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ formatAngka($total_denda) }}</td>
             <td colspan="8"></td>
        </tr>
         <tr>
            <td style="border: 1px solid #000000;">Pot. Jam</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $total_potongan_jam }} Jam</td>
             <td colspan="8"></td>
        </tr>
    </table>
</body>
</html>
