<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Grup;
use App\Models\JadwalPeriode;
use App\Models\JadwalPeriodeItem;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\MinPersonelShift;
use App\Models\ShiftPattern;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class JadwalPeriodeController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPeriode::with(['cabang', 'departemen', 'grup', 'creator', 'publisher'])
            ->withCount('items');

        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }
        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        }
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }
        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        if (!empty($request->kode_grup)) {
            $query->where('kode_grup', $request->kode_grup);
        }

        $data['periodes'] = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $data['departemen'] = Departemen::orderBy('nama_dept')->get();
        $data['cabang'] = Cabang::orderBy('nama_cabang')->get();
        $data['grups'] = Grup::orderBy('nama_grup')->get();

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.jadwalperiode.index' : 'jadwalperiode.index';
        return view($view, $data);
    }

    public function create()
    {
        $data['departemen'] = Departemen::orderBy('nama_dept')->get();
        $data['cabang'] = Cabang::orderBy('nama_cabang')->get();
        $data['patterns'] = ShiftPattern::with('details')->where('is_active', true)->orderBy('nama_pola')->get();
        $data['grups'] = Grup::orderBy('nama_grup')->get();

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.jadwalperiode.create' : 'jadwalperiode.create';
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:100',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2050',
            'kode_dept' => 'nullable|exists:departemen,kode_dept',
            'kode_cabang' => 'nullable|exists:cabang,kode_cabang',
            'kode_grup' => 'nullable|exists:grup,kode_grup',
            'sumber' => 'required|in:pattern,copy,existing,empty',
            'kode_pola' => 'required_if:sumber,pattern|nullable|exists:shift_patterns,kode_pola',
            'tanggal_mulai_pola' => 'required_if:sumber,pattern|nullable|date',
        ], [
            'nama_periode.required' => 'Nama periode wajib diisi',
            'bulan.required' => 'Bulan wajib dipilih',
            'tahun.required' => 'Tahun wajib dipilih',
            'sumber.required' => 'Sumber jadwal wajib ditentukan',
            'kode_pola.required_if' => 'Template Pola Shift wajib dipilih jika memilih metode Pola Shift',
            'tanggal_mulai_pola.required_if' => 'Tanggal mulai siklus pola shift wajib diisi',
        ]);

        DB::beginTransaction();
        try {
            $bulan = (int) $request->bulan;
            $tahun = (int) $request->tahun;

            // 1. Buat Header Periode Jadwal
            $periode = JadwalPeriode::create([
                'nama_periode' => trim($request->nama_periode),
                'bulan' => $bulan,
                'tahun' => $tahun,
                'kode_cabang' => $request->kode_cabang ?: null,
                'kode_dept' => $request->kode_dept ?: null,
                'kode_grup' => $request->kode_grup ?: null,
                'status' => 'draft',
                'created_by' => Auth::id(),
                'catatan' => $request->catatan,
            ]);

            // 2. Ambil list Karyawan target
            $karyawanQuery = Karyawan::where('status_aktif_karyawan', '1');
            if (!empty($request->kode_dept)) {
                $karyawanQuery->where('kode_dept', $request->kode_dept);
            }
            if (!empty($request->kode_cabang)) {
                $karyawanQuery->where('kode_cabang', $request->kode_cabang);
            }
            if (!empty($request->kode_grup)) {
                $karyawanQuery->whereIn('nik', function ($q) use ($request) {
                    $q->select('nik')->from('grup_detail')->where('kode_grup', $request->kode_grup);
                });
            }
            $karyawans = $karyawanQuery->get();

            $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
            $itemsToInsert = [];

            // 3. Generate Draft Item berdasarkan metode sumber
            if ($request->sumber === 'pattern' && !empty($request->kode_pola) && !empty($request->tanggal_mulai_pola)) {
                $pattern = ShiftPattern::with('details')->where('kode_pola', $request->kode_pola)->first();
                $detailsByUrutan = $pattern ? $pattern->details->keyBy('urutan_hari') : collect();
                $siklus = $pattern ? $pattern->jumlah_hari_siklus : 7;
                $startDate = Carbon::parse($request->tanggal_mulai_pola)->startOfDay();

                foreach ($karyawans as $karyawan) {
                    for ($d = 1; $d <= $daysInMonth; $d++) {
                        $targetDate = Carbon::create($tahun, $bulan, $d)->startOfDay();
                        $diffDays = $startDate->diffInDays($targetDate, false);
                        $dayIndex = (($diffDays % $siklus) + $siklus) % $siklus + 1;

                        $detail = $detailsByUrutan->get($dayIndex);
                        $kodeJk = $detail ? $detail->kode_jam_kerja : null;

                        $itemsToInsert[] = [
                            'jadwal_periode_id' => $periode->id,
                            'nik' => $karyawan->nik,
                            'tanggal' => $targetDate->format('Y-m-d'),
                            'kode_jam_kerja' => $kodeJk,
                            'sumber' => 'pattern',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            } elseif ($request->sumber === 'copy') {
                // Copy dari bulan sebelumnya
                $prevMonthDate = Carbon::create($tahun, $bulan, 1)->subMonth();
                $prevBulan = $prevMonthDate->month;
                $prevTahun = $prevMonthDate->year;

                // Cek apakah ada jadwal periode published/draft bulan lalu
                $prevPeriode = JadwalPeriode::where('bulan', $prevBulan)
                    ->where('tahun', $prevTahun)
                    ->when($request->kode_dept, function ($q) use ($request) {
                        return $q->where('kode_dept', $request->kode_dept);
                    })
                    ->orderBy('id', 'desc')
                    ->first();

                if ($prevPeriode) {
                    $prevItems = JadwalPeriodeItem::where('jadwal_periode_id', $prevPeriode->id)->get();
                    $prevMap = [];
                    foreach ($prevItems as $pi) {
                        $dayNum = (int) Carbon::parse($pi->tanggal)->format('d');
                        $prevMap[$pi->nik][$dayNum] = $pi->kode_jam_kerja;
                    }

                    foreach ($karyawans as $karyawan) {
                        for ($d = 1; $d <= $daysInMonth; $d++) {
                            $targetDate = Carbon::create($tahun, $bulan, $d)->format('Y-m-d');
                            $kodeJk = $prevMap[$karyawan->nik][$d] ?? null;

                            $itemsToInsert[] = [
                                'jadwal_periode_id' => $periode->id,
                                'nik' => $karyawan->nik,
                                'tanggal' => $targetDate,
                                'kode_jam_kerja' => $kodeJk,
                                'sumber' => 'copy',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            } elseif ($request->sumber === 'existing') {
                // Ambil jadwal existing (misal dari presensi_jamkerja_byday / dept)
                $dayMapping = [
                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
                    5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
                ];

                foreach ($karyawans as $karyawan) {
                    // Cek byday personal
                    $byDayPersonal = DB::table('presensi_jamkerja_byday')
                        ->where('nik', $karyawan->nik)
                        ->pluck('kode_jam_kerja', 'hari')
                        ->toArray();

                    // Cek bydept detail
                    $byDept = DB::table('presensi_jamkerja_bydept_detail')
                        ->join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                        ->where('presensi_jamkerja_bydept.kode_dept', $karyawan->kode_dept)
                        ->pluck('presensi_jamkerja_bydept_detail.kode_jam_kerja', 'presensi_jamkerja_bydept_detail.hari')
                        ->toArray();

                    for ($d = 1; $d <= $daysInMonth; $d++) {
                        $targetDate = Carbon::create($tahun, $bulan, $d);
                        $hariName = $dayMapping[$targetDate->dayOfWeekIso] ?? '';

                        $kodeJk = $byDayPersonal[$hariName] ?? ($byDept[$hariName] ?? null);

                        $itemsToInsert[] = [
                            'jadwal_periode_id' => $periode->id,
                            'nik' => $karyawan->nik,
                            'tanggal' => $targetDate->format('Y-m-d'),
                            'kode_jam_kerja' => $kodeJk,
                            'sumber' => 'existing',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            } else {
                // Empty draft
                foreach ($karyawans as $karyawan) {
                    for ($d = 1; $d <= $daysInMonth; $d++) {
                        $targetDate = Carbon::create($tahun, $bulan, $d)->format('Y-m-d');
                        $itemsToInsert[] = [
                            'jadwal_periode_id' => $periode->id,
                            'nik' => $karyawan->nik,
                            'tanggal' => $targetDate,
                            'kode_jam_kerja' => null,
                            'sumber' => 'manual',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Batch insert chunks
            if (!empty($itemsToInsert)) {
                foreach (array_chunk($itemsToInsert, 500) as $chunk) {
                    JadwalPeriodeItem::insert($chunk);
                }
            }

            DB::commit();
            return Redirect::route('jadwalperiode.show', Crypt::encrypt($periode->id))
                ->with(messageSuccess("Draft jadwal {$periode->nama_periode} berhasil dibuat. Silakan review & edit sebelum publish."));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->with(messageError('Gagal membuat draft jadwal: ' . $e->getMessage()));
        }
    }

    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $periode = JadwalPeriode::with(['cabang', 'departemen', 'creator', 'publisher'])->findOrFail($id);

        $daysInMonth = Carbon::create($periode->tahun, $periode->bulan, 1)->daysInMonth;
        $allJamKerja = Jamkerja::orderBy('nama_jam_kerja')->get();

        // Ambil data items dan kelompokkan per karyawan
        $items = JadwalPeriodeItem::with('karyawan.departemen', 'karyawan.jabatan', 'jamKerja')
            ->where('jadwal_periode_id', $periode->id)
            ->get();

        // Grouping
        $matrix = [];
        foreach ($items as $item) {
            $nik = $item->nik;
            $dayNum = (int) Carbon::parse($item->tanggal)->format('d');
            if (!isset($matrix[$nik])) {
                $matrix[$nik] = [
                    'karyawan' => $item->karyawan,
                    'schedule' => [],
                    'stats' => ['total_kerja' => 0, 'total_off' => 0],
                ];
            }
            $matrix[$nik]['schedule'][$dayNum] = [
                'id' => $item->id,
                'kode_jam_kerja' => $item->kode_jam_kerja,
                'jamKerja' => $item->jamKerja,
                'tanggal' => $item->tanggal,
            ];

            if (!empty($item->kode_jam_kerja)) {
                $matrix[$nik]['stats']['total_kerja']++;
            } else {
                $matrix[$nik]['stats']['total_off']++;
            }
        }

        // Ambil aturan minimum personel untuk dept ini
        $minRules = MinPersonelShift::with('jamKerja')
            ->where('kode_dept', $periode->kode_dept)
            ->get();

        // Hitung alert personel per tanggal & shift
        $understaffedDays = [];
        if ($minRules->isNotEmpty()) {
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $targetDate = Carbon::create($periode->tahun, $periode->bulan, $d)->format('Y-m-d');
                $shiftCounts = [];

                foreach ($matrix as $nik => $data) {
                    $jkCode = $data['schedule'][$d]['kode_jam_kerja'] ?? null;
                    if ($jkCode) {
                        $shiftCounts[$jkCode] = ($shiftCounts[$jkCode] ?? 0) + 1;
                    }
                }

                foreach ($minRules as $rule) {
                    $actual = $shiftCounts[$rule->kode_jam_kerja] ?? 0;
                    if ($actual < $rule->min_personel) {
                        $understaffedDays[] = [
                            'tanggal' => $targetDate,
                            'hari' => $d,
                            'shift' => $rule->jamKerja ? $rule->jamKerja->nama_jam_kerja : $rule->kode_jam_kerja,
                            'actual' => $actual,
                            'required' => $rule->min_personel,
                        ];
                    }
                }
            }
        }

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.jadwalperiode.show' : 'jadwalperiode.show';
        return view($view, compact('periode', 'daysInMonth', 'allJamKerja', 'matrix', 'minRules', 'understaffedDays'));
    }

    public function updateCell(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $periode = JadwalPeriode::findOrFail($id);

        if ($periode->status === 'archived') {
            return response()->json(['error' => 'Jadwal yang sudah diarsip tidak dapat diubah'], 400);
        }

        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'kode_jam_kerja' => 'nullable|exists:presensi_jamkerja,kode_jam_kerja',
        ]);

        try {
            $item = JadwalPeriodeItem::updateOrCreate(
                [
                    'jadwal_periode_id' => $periode->id,
                    'nik' => $request->nik,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'kode_jam_kerja' => $request->kode_jam_kerja ?: null,
                    'sumber' => 'manual',
                ]
            );

            // Jika status sudah published, langsung sinkronisasi update ke presensi_jamkerja_bydate
            if ($periode->status === 'published') {
                if (!empty($request->kode_jam_kerja)) {
                    DB::table('presensi_jamkerja_bydate')->updateOrInsert(
                        ['nik' => $request->nik, 'tanggal' => $request->tanggal],
                        ['kode_jam_kerja' => $request->kode_jam_kerja, 'created_at' => now(), 'updated_at' => now()]
                    );
                } else {
                    DB::table('presensi_jamkerja_bydate')
                        ->where('nik', $request->nik)
                        ->where('tanggal', $request->tanggal)
                        ->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cell jadwal berhasil diupdate',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Publish draft jadwal ke sistem aktif (presensi_jamkerja_bydate)
     */
    public function publish(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $periode = JadwalPeriode::with('items')->findOrFail($id);

        if ($periode->status === 'archived') {
            return Redirect::back()->with(messageError('Jadwal sudah diarsip dan tidak dapat dipublish ulang'));
        }

        DB::beginTransaction();
        try {
            // Ambil semua items dari periode ini
            $items = $periode->items;
            $countPublished = 0;

            foreach ($items as $item) {
                if (!empty($item->kode_jam_kerja)) {
                    // Update or insert ke presensi_jamkerja_bydate (prioritas tertinggi)
                    DB::table('presensi_jamkerja_bydate')->updateOrInsert(
                        ['nik' => $item->nik, 'tanggal' => $item->tanggal],
                        [
                            'kode_jam_kerja' => $item->kode_jam_kerja,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                    $countPublished++;
                } else {
                    // Jika libur/OFF, hapus override agar default libur
                    DB::table('presensi_jamkerja_bydate')
                        ->where('nik', $item->nik)
                        ->where('tanggal', $item->tanggal)
                        ->delete();
                }
            }

            // Update status periode
            $periode->update([
                'status' => 'published',
                'published_by' => Auth::id(),
                'published_at' => now(),
            ]);

            DB::commit();
            return Redirect::back()->with(messageSuccess("Jadwal {$periode->nama_periode} berhasil di-PUBLISH! ({$countPublished} entri aktif tersimpan ke jadwal kerja harian karyawan)."));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError('Gagal mempublish jadwal: ' . $e->getMessage()));
        }
    }

    public function archive($id)
    {
        $id = Crypt::decrypt($id);
        try {
            $periode = JadwalPeriode::findOrFail($id);
            $periode->update(['status' => 'archived']);
            return Redirect::back()->with(messageSuccess('Jadwal berhasil diarsipkan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Gagal mengarsipkan: ' . $e->getMessage()));
        }
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        DB::beginTransaction();
        try {
            $periode = JadwalPeriode::with('items')->findOrFail($id);

            // Jika jadwal sudah published, bersihkan juga sinkronisasi di presensi_jamkerja_bydate
            if ($periode->status === 'published') {
                foreach ($periode->items as $item) {
                    DB::table('presensi_jamkerja_bydate')
                        ->where('nik', $item->nik)
                        ->where('tanggal', $item->tanggal)
                        ->delete();
                }
            }

            $periode->delete(); // cascade hapus jadwal_periode_items
            DB::commit();

            return Redirect::route('jadwalperiode.index')->with(messageSuccess('Periode jadwal dan jam kerja by date terkait berhasil dihapus'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError('Gagal menghapus: ' . $e->getMessage()));
        }
    }
}
