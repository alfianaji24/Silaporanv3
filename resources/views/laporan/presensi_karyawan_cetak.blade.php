<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Presensi Karyawan </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        @page {
            size: A4
        }

        .sheet {
            overflow: auto !important;
        }

        .tablereport {
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
        }

        .tablereport td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }

        .tablereport th {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            background-color: #0949b8;
            color: #fff;

            font-size: 13px
        }
    </style>
</head>

<body class="A4">
    <section class="sheet padding-10mm">
        <div class="header" style="margin-bottom: 10px">
            <table>
                <tr>
                    <td>
                        @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                            <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo Perusahaan" style="max-width: 100px;">
                        @else
                            <img src="https://placehold.co/100x100?text=Logo" alt="Logo Default" style="max-width: 100px;">
                        @endif
                    </td>
                    <td>
                        <h4 style="line-height: 20px; margin-bottom: 5px">
                            LAPORAN PRESENSI
                            <br>
                            {{ $generalsetting->nama_perusahaan }}
                            <br>
                            PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} - {{ date('d-m-Y', strtotime($periode_sampai)) }}
                        </h4>
                        <span style="font-style: italic;">{{ $generalsetting->alamat }}</span><br>
                        <span style="font-style: italic;">{{ $generalsetting->telepon }}</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="datakaryawan" style="display: flex; gap: 20px; margin-top: 40px">
            <div id="fotokaryawan">
                @if (!empty($karyawan->foto))
                    @if (Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                        <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="user image" class="d-block  ms-0 ms-sm-4 rounded " height="150"
                            width="140" style="object-fit: cover">
                    @else
                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="user image"
                            class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="150">
                    @endif
                @else
                    <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="user image"
                        class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="150">
                @endif

            </div>
            <div id="detailkaryawan">
                <table class="tablereport">
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $karyawan->nik_show ?? $karyawan->nik }}</td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>{{ $karyawan->nama_karyawan }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{{ $karyawan->nama_jabatan }}</td>
                    </tr>
                    <tr>
                        <td>Departemen</td>
                        <td>:</td>
                        <td>{{ $karyawan->nama_dept }}</td>
                    </tr>
                    <tr>
                        <td>Cabang</td>
                        <td>:</td>
                        <td>{{ $karyawan->nama_cabang }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="presensi" style="margin-top: 40px">
            <table class="tablereport">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jadwal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Status</th>
                    <th>Terlambat</th>
                    <th>Denda</th>
                    <th>Pot. Jam</th>
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

                    // Mapping jadwal kerja untuk karyawan ini (mengikuti prioritas laporan lain)
                    $mapJadwalByDate = $jadwal_bydate[$karyawan->nik] ?? [];
                    $mapJadwalGrupByDate = $jadwal_grup_bydate[$karyawan->nik] ?? [];
                    $mapJadwalByDay = $jadwal_byday[$karyawan->nik] ?? [];
                    $keyDeptCabang = $karyawan->kode_dept . '|' . $karyawan->kode_cabang;
                    $mapJadwalByDept = $jadwal_bydept[$keyDeptCabang] ?? [];

                    // Mulai dari periode_dari sampai periode_sampai
                    $tanggal_presensi = $periode_dari;
                    $no = 1;
                @endphp

                @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                    @php
                        /** @var \App\Models\Presensi|null $d */
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
                        if ($status == 'h') {
                            $color_status = 'green';
                        } elseif ($status == 'i') {
                            $color_status = 'yellow';
                        } elseif ($status == 's') {
                            $color_status = 'blue';
                        } elseif ($status == 'c') {
                            $color_status = 'orange';
                        } elseif ($status == 'a') {
                            $color_status = 'red';
                        } else {
                            $color_status = 'gray'; // default untuk tanggal tanpa presensi
                        }
                    @endphp

                    @if ($terlambat != null)
                        @if ($terlambat['desimal_terlambat'] < 1)
                            @php
                                $potongan_jam_terlambat = 0;
                                $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                            @endphp
                        @else
                            @php
                                $potongan_jam_terlambat =
                                    $d && $terlambat['desimal_terlambat'] > $d->total_jam ? $d->total_jam : $terlambat['desimal_terlambat'];
                                $denda = 0;
                            @endphp
                        @endif
                    @else
                        @php
                            $potongan_jam_terlambat = 0;
                            $denda = 0;
                        @endphp
                    @endif

                    @php
                        // Rekap & potongan jam
                        if ($d) {
                            // Ada data presensi
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

                            // Cek jadwal berurutan (by-date, grup, by-day, by-dept)
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
                                // Ada jadwal tapi tidak presensi → Alpa & potong full jam
                                $status = 'a';
                                $color_status = 'red';
                                $potongan_jam = $totalJamJadwal;
                                $total_alfa++;
                            } else {
                                // Tidak ada jadwal kerja → Libur (tanpa potongan)
                                $status = 'libur';
                                $color_status = 'green';
                            }
                        }

                        $total_potongan_jam += $potongan_jam;
                    @endphp

                    <tr>
                        <td>{{ $no }}</td>
                        <td>{{ date('d-m-y', strtotime($tanggal_presensi)) }}</td>
                        <td>{{ getHari($tanggal_presensi) }}</td>
                        <td>
                            @if ($d)
                                {{ $d->nama_jam_kerja }} - {{ date('H:i', strtotime($d->jam_masuk)) }} -
                                {{ date('H:i', strtotime($d->jam_pulang)) }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center">
                            @if ($d && $d->jam_in != null)
                                {{ date('H:i', strtotime($d->jam_in)) }}
                            @elseif ($d)
                                <span style="color: red">Belum Absen</span>
                            @else
                                <span style="color: red">Belum Absen</span>
                            @endif
                        </td>
                        <td style="text-align: center">
                            @if ($d && $d->jam_out != null)
                                {{ date('H:i', strtotime($d->jam_out)) }}
                            @elseif ($d)
                                <span style="color: red">Belum Absen</span>
                            @else
                                <span style="color: red">Belum Absen</span>
                            @endif
                            @if ($pulangcepat > 0)
                                <span style="color: red">
                                    (-{{ $pulangcepat }})
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center; background-color: {{ $color_status }}; color: #fff">
                            @if ($status == 'libur')
                                LIBUR
                            @elseif ($status != '-')
                                {{ textUpperCase($status) }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center">
                            {!! $terlambat != null ? $terlambat['show'] : '' !!}
                        </td>
                        <td style="text-align: right; color: red">
                            {{ $denda ? formatAngka($denda) : '' }}
                        </td>
                        <td style="text-align: center; color: red">
                            {{ $potongan_jam }}
                        </td>
                    </tr>

                    @php
                        $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                        $no++;
                    @endphp
                @endwhile
            </table>
        </div>
        <div class="rekap" style="margin-top: 40px">
            <table class="tablereport">
                <tr>
                    <th colspan="2">Rekapitulasi Presensi</th>
                </tr>
                <tr>
                    <th>Hadir</th>
                    <td style="text-align: center">{{ $total_hadir }}</td>
                </tr>
                <tr>
                    <th>Izin</th>
                    <td style="text-align: center">{{ $total_izin }}</td>
                </tr>
                <tr>
                    <th>Sakit</th>
                    <td style="text-align: center">{{ $total_sakit }}</td>
                </tr>
                <tr>
                    <th>Cuti</th>
                    <td style="text-align: center">{{ $total_cuti }}</td>
                </tr>
                <tr>
                    <th>Alfa</th>
                    <td style="text-align: center">{{ $total_alfa }}</td>
                </tr>
                <tr>
                    <th>Terlambat</th>
                    <td style="text-align: right">{{ $total_terlambat }} Jam</td>
                </tr>
                <tr>
                    <th>Denda</th>
                    <td style="text-align: right;">{{ formatAngka($total_denda) }}</td>
                </tr>
                <tr>
                    <th>Pot. Jam</th>
                    <td style="text-align: right;">{{ $total_potongan_jam }} Jam</td>
                </tr>
            </table>
        </div>
    </section>
</body>

</html>
