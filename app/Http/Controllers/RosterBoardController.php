<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\MinPersonelShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RosterBoardController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) ($request->bulan ?: date('n'));
        $tahun = (int) ($request->tahun ?: date('Y'));
        $kode_dept = $request->kode_dept;
        $kode_cabang = $request->kode_cabang;
        $search = $request->search;

        $departemen = Departemen::orderBy('nama_dept')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $allJamKerja = Jamkerja::orderBy('nama_jam_kerja')->get();

        $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        $startDate = Carbon::create($tahun, $bulan, 1)->format('Y-m-d');
        $endDate = Carbon::create($tahun, $bulan, $daysInMonth)->format('Y-m-d');

        // Query Karyawan
        $karyawanQuery = Karyawan::with(['departemen', 'jabatan', 'cabang'])
            ->where('status_aktif_karyawan', '1');

        if (!empty($kode_dept)) {
            $karyawanQuery->where('kode_dept', $kode_dept);
        }
        if (!empty($kode_cabang)) {
            $karyawanQuery->where('kode_cabang', $kode_cabang);
        }
        if (!empty($search)) {
            $karyawanQuery->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $karyawans = $karyawanQuery->orderBy('nama_karyawan', 'asc')->get();
        $niks = $karyawans->pluck('nik')->toArray();

        // Map all Jam Kerja by kode_jam_kerja in memory
        $jamKerjaMap = $allJamKerja->keyBy('kode_jam_kerja');

        // BATCH PRE-FETCHING (Menghilangkan N x 31 query database)
        // 1. Ajuan Jadwal (Status approved 'a')
        $ajuanJadwalMap = [];
        if (!empty($niks)) {
            $ajuanList = \App\Models\AjuanJadwal::whereIn('nik', $niks)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where('status', 'a')
                ->get();
            foreach ($ajuanList as $aj) {
                $ajuanJadwalMap[$aj->nik][$aj->tanggal] = $aj->kode_jam_kerja_tujuan;
            }
        }

        // 2. Presensi Jam Kerja By Date (Khusus karyawan per tanggal)
        $byDateMap = [];
        if (!empty($niks)) {
            $byDateList = \App\Models\Setjamkerjabydate::whereIn('nik', $niks)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();
            foreach ($byDateList as $bd) {
                $byDateMap[$bd->nik][$bd->tanggal] = $bd->kode_jam_kerja;
            }
        }

        // 3. Grup Detail & Grup Jam Kerja By Date
        $grupByDateMap = []; // [kode_grup][tanggal] => kode_jam_kerja
        $karyawanGrupMap = []; // [nik] => kode_grup
        if (!empty($niks)) {
            $karyawanGrupList = \App\Models\GrupDetail::whereIn('nik', $niks)->get();
            $grupCodes = [];
            foreach ($karyawanGrupList as $kg) {
                $karyawanGrupMap[$kg->nik] = $kg->kode_grup;
                $grupCodes[] = $kg->kode_grup;
            }
            if (!empty($grupCodes)) {
                $grupCodes = array_unique($grupCodes);
                $grupJkList = \App\Models\GrupJamkerjaBydate::whereIn('kode_grup', $grupCodes)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->get();
                foreach ($grupJkList as $gjk) {
                    $tglStr = Carbon::parse($gjk->tanggal)->format('Y-m-d');
                    $grupByDateMap[$gjk->kode_grup][$tglStr] = $gjk->kode_jam_kerja;
                }
            }
        }

        // 4. Presensi Jam Kerja By Day (per karyawan per hari)
        $byDayMap = []; // [nik][hari] => kode_jam_kerja
        if (!empty($niks)) {
            $byDayList = \App\Models\Setjamkerjabyday::whereIn('nik', $niks)->get();
            foreach ($byDayList as $bd) {
                $byDayMap[$bd->nik][$bd->hari] = $bd->kode_jam_kerja;
            }
        }

        // 5. Presensi Jam Kerja By Dept (per kode_dept, kode_cabang, hari)
        $deptCabangCombos = [];
        foreach ($karyawans as $k) {
            $comboKey = ($k->kode_dept ?? '') . '|' . ($k->kode_cabang ?? '');
            $deptCabangCombos[$comboKey] = ['kode_dept' => $k->kode_dept, 'kode_cabang' => $k->kode_cabang];
        }

        $byDeptMap = []; // [kode_dept][kode_cabang][hari] => kode_jam_kerja
        if (!empty($deptCabangCombos)) {
            $deptList = \App\Models\Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                ->select(
                    'presensi_jamkerja_bydept.kode_dept',
                    'presensi_jamkerja_bydept.kode_cabang',
                    'presensi_jamkerja_bydept_detail.hari',
                    'presensi_jamkerja_bydept_detail.kode_jam_kerja'
                )
                ->get();

            foreach ($deptList as $dRow) {
                $byDeptMap[$dRow->kode_dept][$dRow->kode_cabang][$dRow->hari] = $dRow->kode_jam_kerja;
            }
        }

        // 6. Global Jam Kerja Setting
        $globalSetting = \App\Models\Pengaturanumum::find(1);
        $globalJkMap = [];
        if ($globalSetting && $globalSetting->global_jamkerja_aktif) {
            $globalJkList = \App\Models\GlobalJamkerja::all();
            foreach ($globalJkList as $gjk) {
                $globalJkMap[$gjk->hari] = $gjk->kode_jam_kerja;
            }
        }

        // Resolve Matrix Secara Instan dari Memory (O(1) lookup per sel)
        $matrix = [];
        $shiftCountsPerDate = []; // [tanggal][kode_jk] => count

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateCarbon = Carbon::create($tahun, $bulan, $d);
            $targetDate = $dateCarbon->format('Y-m-d');
            $namaHari = getnamaHari($dateCarbon->format('D'));

            foreach ($karyawans as $k) {
                if (!isset($matrix[$k->nik])) {
                    $matrix[$k->nik] = [
                        'karyawan' => $k,
                        'schedule' => [],
                        'stats' => ['total_kerja' => 0, 'total_off' => 0],
                    ];
                }

                // Resolusi hierarki 6 tingkat
                $resolvedKodeJk = null;

                // 1. Ajuan Jadwal Disetujui
                if (isset($ajuanJadwalMap[$k->nik][$targetDate])) {
                    $resolvedKodeJk = $ajuanJadwalMap[$k->nik][$targetDate];
                }
                // 2. Setjamkerjabydate (Khusus perorangan per tanggal)
                elseif (isset($byDateMap[$k->nik][$targetDate])) {
                    $resolvedKodeJk = $byDateMap[$k->nik][$targetDate];
                }
                // 3. Grup Jam Kerja By Date
                elseif (isset($karyawanGrupMap[$k->nik]) && isset($grupByDateMap[$karyawanGrupMap[$k->nik]][$targetDate])) {
                    $resolvedKodeJk = $grupByDateMap[$karyawanGrupMap[$k->nik]][$targetDate];
                }
                // 4. Setjamkerjabyday (Harian perorangan)
                elseif (isset($byDayMap[$k->nik][$namaHari])) {
                    $resolvedKodeJk = $byDayMap[$k->nik][$namaHari];
                }
                // 5. Jam Kerja By Dept
                elseif (isset($byDeptMap[$k->kode_dept][$k->kode_cabang][$namaHari])) {
                    $resolvedKodeJk = $byDeptMap[$k->kode_dept][$k->kode_cabang][$namaHari];
                }
                // 6. Global Jam Kerja
                elseif (isset($globalJkMap[$namaHari])) {
                    $resolvedKodeJk = $globalJkMap[$namaHari];
                }

                $resolvedJk = $resolvedKodeJk ? ($jamKerjaMap[$resolvedKodeJk] ?? null) : null;

                $matrix[$k->nik]['schedule'][$d] = [
                    'tanggal' => $targetDate,
                    'kode_jam_kerja' => $resolvedJk ? $resolvedJk->kode_jam_kerja : null,
                    'nama_jam_kerja' => $resolvedJk ? $resolvedJk->nama_jam_kerja : 'OFF',
                    'jam_masuk' => $resolvedJk ? $resolvedJk->jam_masuk : null,
                    'jam_pulang' => $resolvedJk ? $resolvedJk->jam_pulang : null,
                    'color' => $resolvedJk && !empty($resolvedJk->color) ? $resolvedJk->color : ($resolvedJk ? '#007bff' : '#6c757d'),
                    'is_override' => isset($byDateMap[$k->nik][$targetDate]),
                ];

                if ($resolvedJk) {
                    $matrix[$k->nik]['stats']['total_kerja']++;
                    $shiftCountsPerDate[$targetDate][$resolvedJk->kode_jam_kerja] = ($shiftCountsPerDate[$targetDate][$resolvedJk->kode_jam_kerja] ?? 0) + 1;
                } else {
                    $matrix[$k->nik]['stats']['total_off']++;
                }
            }
        }

        // Ambil aturan minimum personel untuk filter dept jika ada
        $minRulesQuery = MinPersonelShift::with(['jamKerja', 'departemen']);
        if (!empty($kode_dept)) {
            $minRulesQuery->where('kode_dept', $kode_dept);
        }
        $minRules = $minRulesQuery->get();

        // Hitung alerts
        $understaffedAlerts = [];
        if ($minRules->isNotEmpty()) {
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $targetDate = Carbon::create($tahun, $bulan, $d)->format('Y-m-d');
                foreach ($minRules as $rule) {
                    $actual = $shiftCountsPerDate[$targetDate][$rule->kode_jam_kerja] ?? 0;
                    if ($actual < $rule->min_personel) {
                        $understaffedAlerts[] = [
                            'tanggal' => $targetDate,
                            'hari' => $d,
                            'dept' => $rule->departemen->nama_dept ?? $rule->kode_dept,
                            'shift' => $rule->jamKerja ? $rule->jamKerja->nama_jam_kerja : $rule->kode_jam_kerja,
                            'actual' => $actual,
                            'required' => $rule->min_personel,
                        ];
                    }
                }
            }
        }

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.rosterboard.index', compact(
                'bulan',
                'tahun',
                'kode_dept',
                'kode_cabang',
                'search',
                'departemen',
                'cabang',
                'allJamKerja',
                'daysInMonth',
                'matrix',
                'minRules',
                'understaffedAlerts'
            ));
        }

        return view('rosterboard.index', compact(
            'bulan',
            'tahun',
            'kode_dept',
            'kode_cabang',
            'search',
            'departemen',
            'cabang',
            'allJamKerja',
            'daysInMonth',
            'matrix',
            'minRules',
            'understaffedAlerts'
        ));
    }

    /**
     * Resolusi hierarki jam kerja karyawan pada tanggal tertentu
     */
    private function resolveEmployeeShift($nik, $tanggal)
    {
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return null;
        }

        $dateCarbon = Carbon::parse($tanggal);
        $namaHari = getnamaHari($dateCarbon->format('D'));
        $targetDate = $dateCarbon->format('Y-m-d');

        // 1. Ajuan Jadwal Disetujui
        $ajuan = \App\Models\AjuanJadwal::where('nik', $nik)
            ->where('tanggal', $targetDate)
            ->where('status', 'a')
            ->first();
        if ($ajuan && !empty($ajuan->kode_jam_kerja_tujuan)) {
            return $ajuan->kode_jam_kerja_tujuan;
        }

        // 2. Setjamkerjabydate (Khusus perorangan per tanggal)
        $byDate = \App\Models\Setjamkerjabydate::where('nik', $nik)
            ->where('tanggal', $targetDate)
            ->first();
        if ($byDate) {
            return $byDate->kode_jam_kerja;
        }

        // 3. Grup Jam Kerja By Date
        $grupDetail = \App\Models\GrupDetail::where('nik', $nik)->first();
        if ($grupDetail) {
            $grupByDate = \App\Models\GrupJamkerjaBydate::where('kode_grup', $grupDetail->kode_grup)
                ->where('tanggal', $targetDate)
                ->first();
            if ($grupByDate && !empty($grupByDate->kode_jam_kerja)) {
                return $grupByDate->kode_jam_kerja;
            }
        }

        // 4. Setjamkerjabyday (Harian perorangan)
        $byDay = \App\Models\Setjamkerjabyday::where('nik', $nik)
            ->where('hari', $namaHari)
            ->first();
        if ($byDay && !empty($byDay->kode_jam_kerja)) {
            return $byDay->kode_jam_kerja;
        }

        // 5. Jam Kerja By Dept
        $byDept = \App\Models\Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
            ->where('kode_dept', $karyawan->kode_dept)
            ->where('kode_cabang', $karyawan->kode_cabang)
            ->where('hari', $namaHari)
            ->first();
        if ($byDept && !empty($byDept->kode_jam_kerja)) {
            return $byDept->kode_jam_kerja;
        }

        // 6. Global Jam Kerja
        $globalSetting = \App\Models\Pengaturanumum::find(1);
        if ($globalSetting && $globalSetting->global_jamkerja_aktif) {
            $globalJk = \App\Models\GlobalJamkerja::where('hari', $namaHari)->first();
            if ($globalJk && !empty($globalJk->kode_jam_kerja)) {
                return $globalJk->kode_jam_kerja;
            }
        }

        return null;
    }

    /**
     * Swap shift / update shift real-time dari Roster Board
     */
    public function updateShift(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'kode_jam_kerja' => 'nullable|exists:presensi_jamkerja,kode_jam_kerja',
        ]);

        try {
            $nik = $request->nik;
            $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
            $kode_jam_kerja = $request->kode_jam_kerja;

            if (!empty($kode_jam_kerja)) {
                DB::table('presensi_jamkerja_bydate')->updateOrInsert(
                    ['nik' => $nik, 'tanggal' => $tanggal],
                    ['kode_jam_kerja' => $kode_jam_kerja, 'created_at' => now(), 'updated_at' => now()]
                );
            } else {
                // Menghapus dari bydate atau mengatur libur
                DB::table('presensi_jamkerja_bydate')
                    ->where('nik', $nik)
                    ->where('tanggal', $tanggal)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Shift kerja karyawan berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fitur Swap Shift langsung antara 2 karyawan
     */
    public function swapShift(Request $request)
    {
        $request->validate([
            'nik_1' => 'required|exists:karyawan,nik',
            'nik_2' => 'required|exists:karyawan,nik',
            'tanggal_1' => 'required|date',
            'tanggal_2' => 'required|date',
            'kode_jk_1' => 'nullable|string',
            'kode_jk_2' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $nik1 = $request->nik_1;
            $nik2 = $request->nik_2;
            $tgl1 = Carbon::parse($request->tanggal_1)->format('Y-m-d');
            $tgl2 = Carbon::parse($request->tanggal_2)->format('Y-m-d');

            // Ambil shift awal baik dari input frontend (jika ada) atau resolusi sistem
            $kodeJk1 = $request->filled('kode_jk_1') ? $request->kode_jk_1 : $this->resolveEmployeeShift($nik1, $tgl1);
            $kodeJk2 = $request->filled('kode_jk_2') ? $request->kode_jk_2 : $this->resolveEmployeeShift($nik2, $tgl2);

            // Validasi jika keduanya tidak memiliki perbedaan / sama persis
            if ($kodeJk1 === $kodeJk2 && $nik1 === $nik2 && $tgl1 === $tgl2) {
                return response()->json([
                    'error' => 'Pilih dua sel jadwal yang berbeda untuk ditukar.'
                ], 422);
            }

            // Tukar jadwal:
            // 1. Karyawan 1 pada tgl1 mendapat kodeJk2 (shift dari Karyawan 2)
            if (!empty($kodeJk2)) {
                DB::table('presensi_jamkerja_bydate')->updateOrInsert(
                    ['nik' => $nik1, 'tanggal' => $tgl1],
                    ['kode_jam_kerja' => $kodeJk2, 'updated_at' => now(), 'created_at' => now()]
                );
            } else {
                DB::table('presensi_jamkerja_bydate')->where('nik', $nik1)->where('tanggal', $tgl1)->delete();
            }

            // 2. Karyawan 2 pada tgl2 mendapat kodeJk1 (shift dari Karyawan 1)
            if (!empty($kodeJk1)) {
                DB::table('presensi_jamkerja_bydate')->updateOrInsert(
                    ['nik' => $nik2, 'tanggal' => $tgl2],
                    ['kode_jam_kerja' => $kodeJk1, 'updated_at' => now(), 'created_at' => now()]
                );
            } else {
                DB::table('presensi_jamkerja_bydate')->where('nik', $nik2)->where('tanggal', $tgl2)->delete();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pertukaran shift berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
