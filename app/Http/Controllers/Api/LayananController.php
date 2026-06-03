<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\Cabang;
use App\Models\Slipgaji;
use App\Models\Kontrak;
use App\Models\Pelanggaran;
use App\Models\Jamkerja;
use App\Models\AjuanJadwal;
use App\Models\KpiEmployee;
use App\Models\KpiDetail;
use App\Models\KpiIndicatorDetail;
use App\Models\KpiPeriod;
use App\Models\Facerecognition;
use App\Models\AktivitasKaryawan;
use App\Models\Pengumuman;
use App\Models\Pengaturanumum;
use App\Models\Gajipokok;
use App\Models\Bpjskesehatan;
use App\Models\Bpjstenagakerja;
use App\Models\Denda;
use App\Models\Presensi;
use App\Models\Setjamkerjabydate;
use App\Models\GrupDetail;
use App\Models\GrupJamkerjaBydate;
use App\Models\Setjamkerjabyday;
use App\Models\Detailsetjamkerjabydept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class LayananController extends Controller
{
    /**
     * Get Virtual ID Card Details
     */
    public function idcard(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $karyawan = Karyawan::with(['jabatan', 'departemen', 'cabang'])->where('nik', $userKaryawan->nik)->first();
            if (!$karyawan) {
                return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nik' => $karyawan->nik,
                    'nik_show' => $karyawan->nik_show,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'nama_lengkap' => $karyawan->nama_lengkap,
                    'nama_jabatan' => $karyawan->jabatan?->nama_jabatan ?? 'Karyawan',
                    'nama_dept' => $karyawan->departemen?->nama_dept ?? '-',
                    'nama_cabang' => $karyawan->cabang?->nama_cabang ?? '-',
                    'rfid_uid' => $karyawan->rfid_uid ?? '-',
                    'foto' => $karyawan->foto,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get published salary periods or calculate dynamic payslip details
     */
    public function slipgaji(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }
            $nik = $userKaryawan->nik;

            // If no specific month/year is requested, return list of periods
            if (!$request->has('bulan') || !$request->has('tahun')) {
                $periods = Slipgaji::where('status', '1')
                    ->orderBy('tahun', 'desc')
                    ->orderBy('bulan', 'desc')
                    ->get();

                // If empty, return fallback periods to ensure smooth UX
                if ($periods->isEmpty()) {
                    $currentBulan = (int)date('m');
                    $currentTahun = date('Y');
                    $prevBulan = $currentBulan == 1 ? 12 : $currentBulan - 1;
                    $prevTahun = $currentBulan == 1 ? $currentTahun - 1 : $currentTahun;

                    $periods = collect([
                        [
                            'kode_slip_gaji' => 'GJ' . str_pad($currentBulan, 2, '0', STR_PAD_LEFT) . $currentTahun,
                            'bulan' => $currentBulan,
                            'tahun' => $currentTahun,
                            'status' => 1,
                        ],
                        [
                            'kode_slip_gaji' => 'GJ' . str_pad($prevBulan, 2, '0', STR_PAD_LEFT) . $prevTahun,
                            'bulan' => $prevBulan,
                            'tahun' => $prevTahun,
                            'status' => 1,
                        ]
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'data' => $periods
                ]);
            }

            $bulan = (int)$request->input('bulan');
            $tahun = (int)$request->input('tahun');

            $karyawan = Karyawan::with(['jabatan', 'departemen', 'cabang'])->where('nik', $nik)->first();
            if (!$karyawan) {
                return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan'], 404);
            }

            // 1. Date boundaries based on settings
            $generalsetting = Pengaturanumum::where('id', 1)->first();
            if (!$generalsetting) {
                $generalsetting = (object)[
                    'periode_laporan_dari' => 1,
                    'periode_laporan_sampai' => 31,
                    'periode_laporan_next_bulan' => 0,
                    'total_jam_bulan' => 173
                ];
            }

            $periode_laporan_dari = $generalsetting->periode_laporan_dari;
            $periode_laporan_sampai = $generalsetting->periode_laporan_sampai;
            $periode_laporan_lintas_bulan = $generalsetting->periode_laporan_next_bulan;

            if ($periode_laporan_lintas_bulan == 1) {
                if ($bulan == 1) {
                    $prev_bulan = 12;
                    $prev_tahun = $tahun - 1;
                } else {
                    $prev_bulan = $bulan - 1;
                    $prev_tahun = $tahun;
                }
            } else {
                $prev_bulan = $bulan;
                $prev_tahun = $tahun;
            }

            $prev_bulan = str_pad($prev_bulan, 2, '0', STR_PAD_LEFT);
            $periode_dari = $prev_tahun . '-' . $prev_bulan . '-' . $periode_laporan_dari;
            $periode_sampai = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . $periode_laporan_sampai;

            // 2. Fetch basic salary (Gajipokok)
            $gajiPokokRecord = Gajipokok::where('nik', $nik)
                ->where('tanggal_berlaku', '<=', $periode_sampai)
                ->orderBy('tanggal_berlaku', 'desc')
                ->first();
            $gaji_pokok = $gajiPokokRecord ? $gajiPokokRecord->jumlah : 0;
            $jenis_upah = $gajiPokokRecord ? $gajiPokokRecord->jenis_upah : 'Bulanan';

            // 3. Fetch BPJS
            $bpjsKesehatanRecord = Bpjskesehatan::where('nik', $nik)
                ->where('tanggal_berlaku', '<=', $periode_sampai)
                ->orderBy('tanggal_berlaku', 'desc')
                ->first();
            $bpjs_kesehatan = $bpjsKesehatanRecord ? $bpjsKesehatanRecord->jumlah : 0;

            $bpjsTenagakerjaRecord = Bpjstenagakerja::where('nik', $nik)
                ->where('tanggal_berlaku', '<=', $periode_sampai)
                ->orderBy('tanggal_berlaku', 'desc')
                ->first();
            $bpjs_tenagakerja = $bpjsTenagakerjaRecord ? $bpjsTenagakerjaRecord->jumlah : 0;

            // 4. Fetch Allowances
            $tunjanganHeader = DB::table('karyawan_tunjangan')
                ->where('nik', $nik)
                ->where('tanggal_berlaku', '<=', $periode_sampai)
                ->orderBy('tanggal_berlaku', 'desc')
                ->first();

            $allowances = [];
            if ($tunjanganHeader) {
                $tunjanganDetails = DB::table('karyawan_tunjangan_detail')
                    ->join('jenis_tunjangan', 'karyawan_tunjangan_detail.kode_jenis_tunjangan', '=', 'jenis_tunjangan.kode_jenis_tunjangan')
                    ->where('karyawan_tunjangan_detail.kode_tunjangan', $tunjanganHeader->kode_tunjangan)
                    ->select('jenis_tunjangan.nama_jenis_tunjangan', 'karyawan_tunjangan_detail.kode_jenis_tunjangan', 'karyawan_tunjangan_detail.jumlah')
                    ->get();
                foreach ($tunjanganDetails as $detail) {
                    $allowances[] = [
                        'name' => $detail->nama_jenis_tunjangan,
                        'code' => $detail->kode_jenis_tunjangan,
                        'amount' => (int)$detail->jumlah
                    ];
                }
            } else {
                $allowances = [
                    ['name' => 'Tunjangan Jabatan', 'code' => 'TJB', 'amount' => 500000],
                    ['name' => 'Tunjangan Transportasi', 'code' => 'TTR', 'amount' => 300000],
                    ['name' => 'Tunjangan Makan', 'code' => 'TMA', 'amount' => 200000],
                ];
            }

            // 5. Fetch Adjustments
            $penyesuaian = DB::table('karyawan_penyesuaian_gaji')
                ->join('karyawan_penyesuaian_gaji_detail', 'karyawan_penyesuaian_gaji.kode_penyesuaian_gaji', '=', 'karyawan_penyesuaian_gaji_detail.kode_penyesuaian_gaji')
                ->where('karyawan_penyesuaian_gaji_detail.nik', $nik)
                ->where('karyawan_penyesuaian_gaji.bulan', $bulan)
                ->where('karyawan_penyesuaian_gaji.tahun', $tahun)
                ->select('karyawan_penyesuaian_gaji_detail.penambah', 'karyawan_penyesuaian_gaji_detail.pengurang')
                ->first();

            $penambah = $penyesuaian ? (int)$penyesuaian->penambah : 0;
            $pengurang = $penyesuaian ? (int)$penyesuaian->pengurang : 0;

            // 6. Day-by-Day Presensi Deduction Calculation
            $upah_perjam = $generalsetting->total_jam_bulan > 0 ? ($gaji_pokok / $generalsetting->total_jam_bulan) : 0;

            $denda_list = Denda::all()->toArray();
            $datalibur = getdatalibur($periode_dari, $periode_sampai);
            $datalembur = getlembur($periode_dari, $periode_sampai);

            $jadwal_bydate = DB::table('presensi_jamkerja_bydate')
                ->join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('presensi_jamkerja_bydate.tanggal', 'presensi_jamkerja.total_jam')
                ->whereBetween('presensi_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
                ->where('presensi_jamkerja_bydate.nik', $nik)
                ->get()->pluck('total_jam', 'tanggal')->toArray();

            $jadwal_grup_bydate = DB::table('grup_detail')
                ->join('grup_jamkerja_bydate', 'grup_detail.kode_grup', '=', 'grup_jamkerja_bydate.kode_grup')
                ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('grup_jamkerja_bydate.tanggal', 'presensi_jamkerja.total_jam')
                ->whereBetween('grup_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
                ->where('grup_detail.nik', $nik)
                ->get()->pluck('total_jam', 'tanggal')->toArray();

            $jadwal_byday = DB::table('presensi_jamkerja_byday')
                ->join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('presensi_jamkerja_byday.hari', 'presensi_jamkerja.total_jam')
                ->where('presensi_jamkerja_byday.nik', $nik)
                ->get()->pluck('total_jam', 'hari')->toArray();

            $jadwal_bydept = DB::table('presensi_jamkerja_bydept_detail')
                ->join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('presensi_jamkerja_bydept_detail.hari', 'presensi_jamkerja.total_jam')
                ->where('presensi_jamkerja_bydept.kode_dept', $karyawan->kode_dept)
                ->where('presensi_jamkerja_bydept.kode_cabang', $karyawan->kode_cabang)
                ->get()->pluck('total_jam', 'hari')->toArray();

            $presensiLogs = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
                ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')
                ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
                ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')
                ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
                ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
                ->where('presensi.nik', $nik)
                ->whereBetween('presensi.tanggal', [$periode_dari, $periode_sampai])
                ->select(
                    'presensi.*',
                    'nama_jam_kerja',
                    'jam_masuk',
                    'jam_pulang',
                    'istirahat',
                    'jam_awal_istirahat',
                    'jam_akhir_istirahat',
                    'lintashari',
                    'total_jam',
                    'presensi_izinabsen.keterangan as keterangan_izin_absen',
                    'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                    'presensi_izincuti.keterangan as keterangan_izin_cuti'
                )
                ->get()->keyBy('tanggal');

            $tanggal_presensi = $periode_dari;
            $total_denda = 0;
            $total_potongan_jam = 0;
            $total_menit_terlambat = 0;

            while (strtotime($tanggal_presensi) <= strtotime($periode_sampai)) {
                $search = ['nik' => $nik, 'tanggal' => $tanggal_presensi];
                $ceklibur = ceklibur($datalibur, $search);
                
                $denda = 0;
                $potongan_jam = 0;

                if (isset($presensiLogs[$tanggal_presensi])) {
                    $pLog = $presensiLogs[$tanggal_presensi];
                    if ($pLog->status == 'h') {
                        $jam_masuk = $tanggal_presensi . ' ' . $pLog->jam_masuk;
                        $terlambat = hitungjamterlambat($pLog->jam_in, $jam_masuk);
                        
                        $denda_dari_db = $pLog->denda;
                        
                        if ($denda_dari_db !== null) {
                            $denda = $denda_dari_db;
                            if ($terlambat != null) {
                                if ($terlambat['desimal_terlambat'] >= 1) {
                                    $potongan_jam_terlambat = $terlambat['desimal_terlambat'] > $pLog->total_jam ? $pLog->total_jam : $terlambat['desimal_terlambat'];
                                } else {
                                    $potongan_jam_terlambat = 0;
                                    $total_menit_terlambat += $terlambat['menitterlambat'];
                                }
                            } else {
                                $potongan_jam_terlambat = 0;
                            }
                        } else {
                            if ($terlambat != null) {
                                if ($terlambat['desimal_terlambat'] < 1) {
                                    $potongan_jam_terlambat = 0;
                                    $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                                    $total_menit_terlambat += $terlambat['menitterlambat'];
                                } else {
                                    $potongan_jam_terlambat = $terlambat['desimal_terlambat'] > $pLog->total_jam ? $pLog->total_jam : $terlambat['desimal_terlambat'];
                                    $denda = 0;
                                    $total_menit_terlambat += round($terlambat['desimal_terlambat'] * 60);
                                }
                            } else {
                                $potongan_jam_terlambat = 0;
                                $denda = 0;
                            }
                        }

                        $pulangcepat = hitungpulangcepat(
                            $tanggal_presensi,
                            $pLog->jam_out,
                            $pLog->jam_pulang,
                            $pLog->istirahat,
                            $pLog->jam_awal_istirahat,
                            $pLog->jam_akhir_istirahat,
                            $pLog->lintashari
                        );
                        $pulangcepat = $pulangcepat > $pLog->total_jam ? $pLog->total_jam : $pulangcepat;
                        $potongan_tidak_absen_masuk_atau_pulang = (empty($pLog->jam_out) || empty($pLog->jam_in)) ? $pLog->total_jam : 0;
                        
                        $potongan_jam = $potongan_tidak_absen_masuk_atau_pulang == 0 ? ($pulangcepat + $potongan_jam_terlambat) : $potongan_tidak_absen_masuk_atau_pulang;

                    } else if ($pLog->status == 'i' || $pLog->status == 'a') {
                        $potongan_jam = $pLog->total_jam;
                        $denda = $pLog->denda !== null ? $pLog->denda : 0;
                    }
                } else {
                    if (empty($ceklibur)) {
                        $totalJamJadwal = $jadwal_bydate[$tanggal_presensi] ?? null;
                        if ($totalJamJadwal === null) {
                            $totalJamJadwal = $jadwal_grup_bydate[$tanggal_presensi] ?? null;
                        }
                        if ($totalJamJadwal === null) {
                            $nama_hari = getHari($tanggal_presensi);
                            $totalJamJadwal = $jadwal_byday[$nama_hari] ?? null;
                        }
                        if ($totalJamJadwal === null) {
                            $nama_hari = getHari($tanggal_presensi);
                            $totalJamJadwal = $jadwal_bydept[$nama_hari] ?? null;
                        }
                        
                        $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                        if ($totalJamJadwal !== null && !$is_future) {
                            $potongan_jam = $totalJamJadwal;
                        }
                    }
                }

                $total_denda += $denda;
                $total_potongan_jam += $potongan_jam;
                $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
            }

            $jumlah_potongan_jam = round($upah_perjam) * $total_potongan_jam;
            $total_potongan = round($jumlah_potongan_jam) + $total_denda + $bpjs_kesehatan + $bpjs_tenagakerja;

            $total_allowances = 0;
            foreach ($allowances as $allw) {
                $total_allowances += $allw['amount'];
            }

            $take_home_pay = $gaji_pokok + $total_allowances + $penambah - $pengurang - $total_potongan;

            $hours = floor($total_menit_terlambat / 60);
            $minutes = $total_menit_terlambat % 60;
            $rekap_keterlambatan = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'data' => [
                    'nik' => $karyawan->nik,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'nama_jabatan' => $karyawan->jabatan?->nama_jabatan ?? 'Karyawan',
                    'periode_dari' => $periode_dari,
                    'periode_sampai' => $periode_sampai,
                    'gaji_pokok' => $gaji_pokok,
                    'jenis_upah' => $jenis_upah,
                    'allowances' => $allowances,
                    'penambah' => $penambah,
                    'pengurang' => $pengurang,
                    'bpjs_kesehatan' => $bpjs_kesehatan,
                    'bpjs_tenagakerja' => $bpjs_tenagakerja,
                    'denda_keterlambatan' => $total_denda,
                    'potongan_jam_kerja' => round($jumlah_potongan_jam),
                    'total_potongan' => $total_potongan,
                    'take_home_pay' => $take_home_pay,
                    'total_menit_terlambat' => $total_menit_terlambat,
                    'rekap_keterlambatan' => $rekap_keterlambatan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get Contract History
     */
    public function kontrak(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $kontraks = Kontrak::where('nik', $userKaryawan->nik)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kontraks
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get warning letters (Surat Peringatan - SP)
     */
    public function sp(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $sps = Pelanggaran::where('nik', $userKaryawan->nik)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sps
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resolve employee schedule for a month
     */
    public function jadwal(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }
            $nik = $userKaryawan->nik;
            $karyawan = Karyawan::where('nik', $nik)->first();

            $bulan = (int)$request->query('bulan', date('m'));
            $tahun = (int)$request->query('tahun', date('Y'));

            $start_date = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));

            $tanggal = $start_date;
            $schedule = [];

            // Cache schedule settings
            $jadwal_bydate = DB::table('presensi_jamkerja_bydate')
                ->join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('presensi_jamkerja_bydate.tanggal', 'presensi_jamkerja.kode_jam_kerja', 'presensi_jamkerja.nama_jam_kerja', 'presensi_jamkerja.jam_masuk', 'presensi_jamkerja.jam_pulang')
                ->whereBetween('presensi_jamkerja_bydate.tanggal', [$start_date, $end_date])
                ->where('presensi_jamkerja_bydate.nik', $nik)
                ->get()->keyBy('tanggal')->toArray();

            $jadwal_grup_bydate = DB::table('grup_detail')
                ->join('grup_jamkerja_bydate', 'grup_detail.kode_grup', '=', 'grup_jamkerja_bydate.kode_grup')
                ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('grup_jamkerja_bydate.tanggal', 'presensi_jamkerja.kode_jam_kerja', 'presensi_jamkerja.nama_jam_kerja', 'presensi_jamkerja.jam_masuk', 'presensi_jamkerja.jam_pulang')
                ->whereBetween('grup_jamkerja_bydate.tanggal', [$start_date, $end_date])
                ->where('grup_detail.nik', $nik)
                ->get()->keyBy('tanggal')->toArray();

            $jadwal_byday = DB::table('presensi_jamkerja_byday')
                ->join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('presensi_jamkerja_byday.hari', 'presensi_jamkerja.kode_jam_kerja', 'presensi_jamkerja.nama_jam_kerja', 'presensi_jamkerja.jam_masuk', 'presensi_jamkerja.jam_pulang')
                ->where('presensi_jamkerja_byday.nik', $nik)
                ->get()->keyBy('hari')->toArray();

            $jadwal_bydept = DB::table('presensi_jamkerja_bydept_detail')
                ->join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('presensi_jamkerja_bydept_detail.hari', 'presensi_jamkerja.kode_jam_kerja', 'presensi_jamkerja.nama_jam_kerja', 'presensi_jamkerja.jam_masuk', 'presensi_jamkerja.jam_pulang')
                ->where('presensi_jamkerja_bydept.kode_dept', $karyawan->kode_dept)
                ->where('presensi_jamkerja_bydept.kode_cabang', $karyawan->kode_cabang)
                ->get()->keyBy('hari')->toArray();

            while (strtotime($tanggal) <= strtotime($end_date)) {
                $kode_jk = null;
                $nama_jk = 'Off / Libur';
                $jam_masuk = null;
                $jam_pulang = null;

                if (isset($jadwal_bydate[$tanggal])) {
                    $jk = $jadwal_bydate[$tanggal];
                    $kode_jk = $jk->kode_jam_kerja;
                    $nama_jk = $jk->nama_jam_kerja;
                    $jam_masuk = $jk->jam_masuk;
                    $jam_pulang = $jk->jam_pulang;
                } elseif (isset($jadwal_grup_bydate[$tanggal])) {
                    $jk = $jadwal_grup_bydate[$tanggal];
                    $kode_jk = $jk->kode_jam_kerja;
                    $nama_jk = $jk->nama_jam_kerja;
                    $jam_masuk = $jk->jam_masuk;
                    $jam_pulang = $jk->jam_pulang;
                } else {
                    $hari = getHari($tanggal);
                    if (isset($jadwal_byday[$hari])) {
                        $jk = $jadwal_byday[$hari];
                        $kode_jk = $jk->kode_jam_kerja;
                        $nama_jk = $jk->nama_jam_kerja;
                        $jam_masuk = $jk->jam_masuk;
                        $jam_pulang = $jk->jam_pulang;
                    } elseif (isset($jadwal_bydept[$hari])) {
                        $jk = $jadwal_bydept[$hari];
                        $kode_jk = $jk->kode_jam_kerja;
                        $nama_jk = $jk->nama_jam_kerja;
                        $jam_masuk = $jk->jam_masuk;
                        $jam_pulang = $jk->jam_pulang;
                    }
                }

                $schedule[] = [
                    'tanggal' => $tanggal,
                    'kode_jam_kerja' => $kode_jk,
                    'nama_jam_kerja' => $nama_jk,
                    'jam_masuk' => $jam_masuk,
                    'jam_pulang' => $jam_pulang,
                ];

                $tanggal = date('Y-m-d', strtotime('+1 day', strtotime($tanggal)));
            }

            return response()->json([
                'success' => true,
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get shift swap request options (permitted target shifts)
     */
    public function tukarShiftOptions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $karyawan = Karyawan::where('nik', $userKaryawan->nik)->first();
            if (!$karyawan) {
                return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan'], 404);
            }

            $options = Jamkerja::visibleUntukJabatanKaryawan($karyawan->kode_jabatan)
                ->orderBy('nama_jam_kerja')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $options
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get list of submitted shift trade requests
     */
    public function getTukarShift(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $trades = AjuanJadwal::with(['jamKerjaAwal', 'jamKerjaTujuan'])
                ->where('nik', $userKaryawan->nik)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trades
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Submit a shift trade request
     */
    public function submitTukarShift(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }
            $nik = $userKaryawan->nik;
            $karyawan = Karyawan::where('nik', $nik)->first();

            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date',
                'kode_jam_kerja_tujuan' => 'required|exists:presensi_jamkerja,kode_jam_kerja',
                'keterangan' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if target shift is allowed for employee's position
            $isAllowed = Jamkerja::where('kode_jam_kerja', $request->kode_jam_kerja_tujuan)
                ->visibleUntukJabatanKaryawan($karyawan->kode_jabatan)
                ->exists();

            if (!$isAllowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shift tujuan tidak diizinkan untuk jabatan Anda.'
                ], 400);
            }

            // Resolve original shift
            $tanggal = $request->tanggal;
            $namahari = getnamaHari(date('D', strtotime($tanggal)));
            $kode_jam_kerja_awal = null;

            // 1. By Date
            $jk_date = Setjamkerjabydate::where('nik', $nik)->where('tanggal', $tanggal)->first();
            if ($jk_date) {
                $kode_jam_kerja_awal = $jk_date->kode_jam_kerja;
            } else {
                // 2. By Group
                $cek_group = GrupDetail::where('nik', $nik)->first();
                if ($cek_group) {
                    $jk_group = GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)
                        ->where('tanggal', $tanggal)
                        ->first();
                    if ($jk_group) {
                        $kode_jam_kerja_awal = $jk_group->kode_jam_kerja;
                    }
                }
            }
            if ($kode_jam_kerja_awal == null) {
                // 3. By Day
                $jk_day = Setjamkerjabyday::where('nik', $nik)->where('hari', $namahari)->first();
                if ($jk_day) {
                    $kode_jam_kerja_awal = $jk_day->kode_jam_kerja;
                }
            }
            if ($kode_jam_kerja_awal == null) {
                // 4. By Dept
                $jk_dept = Detailsetjamkerjabydept::where('kode_dept', $karyawan->kode_dept)
                    ->where('kode_cabang', $karyawan->kode_cabang)
                    ->where('hari', $namahari)
                    ->first();
                if ($jk_dept) {
                    $kode_jam_kerja_awal = $jk_dept->kode_jam_kerja;
                }
            }

            $ajuan = AjuanJadwal::create([
                'nik' => $nik,
                'tanggal' => $request->tanggal,
                'kode_jam_kerja_awal' => $kode_jam_kerja_awal,
                'kode_jam_kerja_tujuan' => $request->kode_jam_kerja_tujuan,
                'keterangan' => $request->keterangan,
                'status' => 'p'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan tukar shift berhasil dikirim',
                'data' => $ajuan
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get KPI Evaluations & Scores
     */
    public function kpi(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $evaluations = KpiEmployee::with(['period', 'details.indicator'])
                ->where('nik', $userKaryawan->nik)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $evaluations
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get Face ID Enrollment Status
     */
    public function faceid(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $wajahCount = Facerecognition::where('nik', $userKaryawan->nik)->count();
            $generalsetting = Pengaturanumum::where('id', 1)->first();
            $faceRecognitionEnabled = $generalsetting ? (bool) $generalsetting->face_recognition : false;

            return response()->json([
                'success' => true,
                'registered' => $wajahCount > 0,
                'count' => $wajahCount,
                'face_recognition_enabled' => $faceRecognitionEnabled
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get Karyawan Activity logs
     */
    public function getAktivitas(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $activities = AktivitasKaryawan::where('nik', $userKaryawan->nik)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Submit Daily Activity log
     */
    public function submitAktivitas(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userKaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 404);
            }
            $nik = $userKaryawan->nik;

            $validator = Validator::make($request->all(), [
                'aktivitas' => 'required|string|max:1000',
                'foto' => 'nullable|string', // Base64 string
                'lokasi' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'nik' => $nik,
                'aktivitas' => $request->aktivitas,
                'lokasi' => $request->lokasi
            ];

            if ($request->filled('foto')) {
                $fotoData = $request->input('foto');
                if (strpos($fotoData, 'data:image') === 0) {
                    $image_parts = explode(";base64,", $fotoData);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1] ?? 'png';
                    $image_base64 = base64_decode($image_parts[1]);
                } else {
                    $image_type = 'png';
                    $image_base64 = base64_decode($fotoData);
                }

                $fotoName = time() . '_aktivitas.' . $image_type;
                $destinationPath = 'public/uploads/aktivitas/';

                if (!Storage::exists($destinationPath)) {
                    Storage::makeDirectory($destinationPath, 0775, true);
                }

                Storage::put($destinationPath . $fotoName, $image_base64);
                $data['foto'] = $fotoName;
            }

            $akt = AktivitasKaryawan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil ditambahkan',
                'data' => $akt
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get internal announcements
     */
    public function pengumuman(Request $request): JsonResponse
    {
        try {
            $announcements = Pengumuman::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $announcements
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
