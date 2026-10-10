<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\JadwalCard;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Setjamkerjabydate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PenjadwalanCardController extends Controller
{
    /**
     * Halaman Utama: Tabel Setting Periode Jadwal Card
     */
    public function index(Request $request)
    {
        $query = JadwalCard::with(['cabang', 'departemen', 'creator']);

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        if (!empty($request->tahun)) {
            $query->whereYear('dari', $request->tahun);
        }
        if (!empty($request->bulan)) {
            $query->whereMonth('dari', $request->bulan);
        }

        $data['jadwalcards'] = $query->orderBy('dari', 'desc')->orderBy('id', 'desc')->paginate(15);
        $data['departemen'] = Departemen::orderBy('nama_dept')->get();
        $data['cabang'] = Cabang::orderBy('nama_cabang')->get();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.penjadwalancard.index', $data);
        }

        return view('penjadwalancard.index', $data);
    }

    /**
     * Form Modal / Halaman Tambah Setting Periode
     */
    public function create()
    {
        $data['departemen'] = Departemen::orderBy('nama_dept')->get();
        $data['cabang'] = Cabang::orderBy('nama_cabang')->get();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.penjadwalancard.create', $data);
        }

        return view('penjadwalancard.create', $data);
    }

    /**
     * Simpan Master Setting Periode Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jadwal' => 'required|string|max:100',
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ], [
            'nama_jadwal.required' => 'Nama jadwal wajib diisi',
            'dari.required' => 'Tanggal mulai periode wajib diisi',
            'sampai.required' => 'Tanggal akhir periode wajib diisi',
            'sampai.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
        ]);

        // Validasi Overlap Periode Jadwal Card
        $overlap = JadwalCard::where(function ($query) use ($request) {
                $query->where('dari', '<=', $request->sampai)
                    ->where('sampai', '>=', $request->dari);
            })
            ->where(function ($query) use ($request) {
                if (!empty($request->kode_dept)) {
                    $query->where('kode_dept', $request->kode_dept)->orWhereNull('kode_dept');
                }
            })
            ->where(function ($query) use ($request) {
                if (!empty($request->kode_cabang)) {
                    $query->where('kode_cabang', $request->kode_cabang)->orWhereNull('kode_cabang');
                }
            })
            ->first();

        if ($overlap) {
            $tglMulai = Carbon::parse($overlap->dari)->translatedFormat('d M Y');
            $tglSampai = Carbon::parse($overlap->sampai)->translatedFormat('d M Y');
            return Redirect::back()->withInput()->with(
                messageError("Tanggal bentrok dengan jadwal yang sudah ada: '{$overlap->nama_jadwal}' ({$tglMulai} s/d {$tglSampai}).")
            );
        }

        try {
            $jadwalCard = JadwalCard::create([
                'nama_jadwal' => trim($request->nama_jadwal),
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'kode_cabang' => $request->kode_cabang ?: null,
                'kode_dept' => $request->kode_dept ?: null,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
            ]);

            return Redirect::route('penjadwalancard.atur', Crypt::encrypt($jadwalCard->id))
                ->with(messageSuccess('Setting Periode Jadwal Card berhasil dibuat. Silakan atur pembagian shift karyawan.'));
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(messageError('Gagal menyimpan: ' . $e->getMessage()));
        }
    }

    /**
     * Edit Setting Periode
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $data['jadwalcard'] = JadwalCard::findOrFail($id);
        $data['departemen'] = Departemen::orderBy('nama_dept')->get();
        $data['cabang'] = Cabang::orderBy('nama_cabang')->get();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.penjadwalancard.edit', $data);
        }

        return view('penjadwalancard.edit', $data);
    }

    /**
     * Update Setting Periode
     */
    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $jadwalCard = JadwalCard::findOrFail($id);

        $request->validate([
            'nama_jadwal' => 'required|string|max:100',
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        // Validasi Overlap Periode Jadwal Card (abaikan ID sendiri)
        $overlap = JadwalCard::where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->where('dari', '<=', $request->sampai)
                    ->where('sampai', '>=', $request->dari);
            })
            ->where(function ($query) use ($request) {
                if (!empty($request->kode_dept)) {
                    $query->where('kode_dept', $request->kode_dept)->orWhereNull('kode_dept');
                }
            })
            ->where(function ($query) use ($request) {
                if (!empty($request->kode_cabang)) {
                    $query->where('kode_cabang', $request->kode_cabang)->orWhereNull('kode_cabang');
                }
            })
            ->first();

        if ($overlap) {
            $tglMulai = Carbon::parse($overlap->dari)->translatedFormat('d M Y');
            $tglSampai = Carbon::parse($overlap->sampai)->translatedFormat('d M Y');
            return Redirect::back()->withInput()->with(
                messageError("Tanggal bentrok dengan jadwal yang sudah ada: '{$overlap->nama_jadwal}' ({$tglMulai} s/d {$tglSampai}).")
            );
        }

        try {
            $jadwalCard->update([
                'nama_jadwal' => trim($request->nama_jadwal),
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'kode_cabang' => $request->kode_cabang ?: null,
                'kode_dept' => $request->kode_dept ?: null,
                'keterangan' => $request->keterangan,
            ]);

            return Redirect::route('penjadwalancard.index')
                ->with(messageSuccess('Setting Periode Jadwal Card berhasil diperbarui.'));
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(messageError('Gagal memperbarui: ' . $e->getMessage()));
        }
    }

    /**
     * Hapus Setting Periode
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        DB::beginTransaction();
        try {
            $jadwalCard = JadwalCard::findOrFail($id);

            // Bersihkan data jam kerja by date karyawan sesuai scope dan rentang tanggal periode ini
            $karyawanQuery = Karyawan::where('status_aktif_karyawan', '1');
            if (!empty($jadwalCard->kode_dept)) {
                $karyawanQuery->where('kode_dept', $jadwalCard->kode_dept);
            }
            if (!empty($jadwalCard->kode_cabang)) {
                $karyawanQuery->where('kode_cabang', $jadwalCard->kode_cabang);
            }
            $niks = $karyawanQuery->pluck('nik')->toArray();

            if (!empty($niks)) {
                Setjamkerjabydate::whereIn('nik', $niks)
                    ->whereBetween('tanggal', [$jadwalCard->dari, $jadwalCard->sampai])
                    ->delete();
            }

            $jadwalCard->delete();
            DB::commit();

            return Redirect::route('penjadwalancard.index')
                ->with(messageSuccess('Setting Periode Jadwal Card dan jam kerja by date terkait berhasil dihapus.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError('Gagal menghapus: ' . $e->getMessage()));
        }
    }

    /**
     * Halaman Atur / Setting Card Shift (Tampilan Kanban Drag & Drop + Ceklis)
     */
    public function atur($id)
    {
        $id = Crypt::decrypt($id);
        $jadwalcard = JadwalCard::with(['cabang', 'departemen'])->findOrFail($id);
        $jamkerja = Jamkerja::orderBy('nama_jam_kerja')->get();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.penjadwalancard.atur', compact('jadwalcard', 'jamkerja'));
        }

        return view('penjadwalancard.atur', compact('jadwalcard', 'jamkerja'));
    }

    /**
     * API Ambil Data Karyawan & Pembagian Shift per Periode
     */
    public function getData(Request $request)
    {
        $id = $request->id;
        $jadwalcard = JadwalCard::findOrFail($id);

        $dari = $jadwalcard->dari;
        $sampai = $jadwalcard->sampai;

        // Query Karyawan Target Sesuai Scope Periode
        $karyawanQuery = Karyawan::with(['departemen', 'cabang', 'jabatan'])
            ->where('status_aktif_karyawan', '1');

        if (!empty($jadwalcard->kode_dept)) {
            $karyawanQuery->where('kode_dept', $jadwalcard->kode_dept);
        }
        if (!empty($jadwalcard->kode_cabang)) {
            $karyawanQuery->where('kode_cabang', $jadwalcard->kode_cabang);
        }

        $karyawans = $karyawanQuery->orderBy('nama_karyawan')->get();
        $niks = $karyawans->pluck('nik')->toArray();

        // Ambil jadwal bydate di rentang tanggal
        $byDateRecords = [];
        if (!empty($niks)) {
            $byDateList = Setjamkerjabydate::whereIn('nik', $niks)
                ->whereBetween('tanggal', [$dari, $sampai])
                ->get();

            foreach ($byDateList as $row) {
                $byDateRecords[$row->nik][$row->tanggal] = $row->kode_jam_kerja;
            }
        }

        $period = CarbonPeriod::create($dari, $sampai);
        $totalDays = count($period);

        $allJamKerja = Jamkerja::orderBy('nama_jam_kerja')->get();
        $result = [
            'unassigned' => [],
            'shifts' => []
        ];

        foreach ($allJamKerja as $jk) {
            $result['shifts'][$jk->kode_jam_kerja] = [];
        }

        foreach ($karyawans as $k) {
            $assignedJkCounts = [];
            $karyawanDates = $byDateRecords[$k->nik] ?? [];

            foreach ($period as $dt) {
                $tglStr = $dt->format('Y-m-d');
                $assignedJk = $karyawanDates[$tglStr] ?? null;
                if ($assignedJk) {
                    $assignedJkCounts[$assignedJk] = ($assignedJkCounts[$assignedJk] ?? 0) + 1;
                }
            }

            $primaryJk = null;
            if (!empty($assignedJkCounts)) {
                arsort($assignedJkCounts);
                $primaryJk = array_key_first($assignedJkCounts);
            }

            $karyawanObj = [
                'nik' => $k->nik,
                'nama_karyawan' => $k->nama_karyawan,
                'nama_dept' => $k->departemen->nama_dept ?? '-',
                'nama_cabang' => $k->cabang->nama_cabang ?? '-',
                'foto' => $k->foto ? asset('storage/karyawan/' . $k->foto) : null,
                'assigned_count' => $primaryJk ? ($assignedJkCounts[$primaryJk] ?? 0) : 0,
                'total_days' => $totalDays,
            ];

            if ($primaryJk && isset($result['shifts'][$primaryJk])) {
                $result['shifts'][$primaryJk][] = $karyawanObj;
            } else {
                $result['unassigned'][] = $karyawanObj;
            }
        }

        return response()->json([
            'success' => true,
            'total_days' => $totalDays,
            'data' => $result
        ]);
    }

    /**
     * API Move / Pindah Shift (Drag & Drop / Ceklis)
     */
    public function move(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'nik' => 'required',
            'kode_jam_kerja' => 'required|string',
        ]);

        try {
            $jadwalcard = JadwalCard::findOrFail($request->id);
            $niks = is_array($request->nik) ? $request->nik : [$request->nik];
            $dari = $jadwalcard->dari;
            $sampai = $jadwalcard->sampai;
            $targetJk = $request->kode_jam_kerja;

            $period = CarbonPeriod::create($dari, $sampai);

            DB::beginTransaction();

            foreach ($niks as $nik) {
                if (empty($nik)) continue;

                if ($targetJk === 'unassigned') {
                    Setjamkerjabydate::where('nik', $nik)
                        ->whereBetween('tanggal', [$dari, $sampai])
                        ->delete();
                } else {
                    foreach ($period as $dt) {
                        $tglStr = $dt->format('Y-m-d');
                        Setjamkerjabydate::updateOrInsert(
                            ['nik' => $nik, 'tanggal' => $tglStr],
                            ['kode_jam_kerja' => $targetJk, 'updated_at' => now(), 'created_at' => now()]
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penjadwalan karyawan berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
