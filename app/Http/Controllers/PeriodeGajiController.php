<?php

namespace App\Http\Controllers;

use App\Models\PeriodeGaji;
use App\Models\PeriodeGajiKaryawan;
use App\Models\MutasiPeriodeGaji;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PeriodeGajiController extends Controller
{
    public function index()
    {
        $data['periode_gaji'] = PeriodeGaji::withCount(['karyawanAktif'])->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.periodegaji.index' : 'payroll.periode_gaji.index';
        return view($view, $data);
    }

    public function create()
    {
        // Auto-generate kode periode gaji: PG01, PG02, dst.
        $lastPeriode = PeriodeGaji::orderBy('kode_periode_gaji', 'desc')->first();
        if ($lastPeriode) {
            $lastNumber = (int) substr($lastPeriode->kode_periode_gaji, 2);
            $nextNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            $kode_periode_gaji = 'PG' . $nextNumber;
        } else {
            $kode_periode_gaji = 'PG01';
        }

        $data['kode_periode_gaji'] = $kode_periode_gaji;
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.periodegaji.create' : 'payroll.periode_gaji.create';
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_periode_gaji' => 'required|max:10|unique:periode_gaji,kode_periode_gaji',
            'nama_periode' => 'required|string|max:100',
            'tanggal_dari' => 'required|numeric|min:1|max:31',
            'tanggal_sampai' => 'required|numeric|min:1|max:31',
            'metode_lintas_bulan' => 'required|numeric|in:0,1,2',
        ]);

        try {
            PeriodeGaji::create([
                'kode_periode_gaji' => $request->kode_periode_gaji,
                'nama_periode' => $request->nama_periode,
                'tanggal_dari' => $request->tanggal_dari,
                'tanggal_sampai' => $request->tanggal_sampai,
                'metode_lintas_bulan' => $request->metode_lintas_bulan,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return Redirect::back()->with(messageSuccess('Data Periode Gaji Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function edit($kode_periode_gaji)
    {
        $data['periode_gaji'] = PeriodeGaji::findOrFail($kode_periode_gaji);
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.periodegaji.edit' : 'payroll.periode_gaji.edit';
        return view($view, $data);
    }

    public function update(Request $request, $kode_periode_gaji)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:100',
            'tanggal_dari' => 'required|numeric|min:1|max:31',
            'tanggal_sampai' => 'required|numeric|min:1|max:31',
            'metode_lintas_bulan' => 'required|numeric|in:0,1,2',
        ]);

        try {
            $periode = PeriodeGaji::findOrFail($kode_periode_gaji);
            $periode->update([
                'nama_periode' => $request->nama_periode,
                'tanggal_dari' => $request->tanggal_dari,
                'tanggal_sampai' => $request->tanggal_sampai,
                'metode_lintas_bulan' => $request->metode_lintas_bulan,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return Redirect::back()->with(messageSuccess('Data Periode Gaji Berhasil Diupdate'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($kode_periode_gaji)
    {
        try {
            $periode = PeriodeGaji::findOrFail($kode_periode_gaji);
            $periode->delete();
            return Redirect::back()->with(messageSuccess('Data Periode Gaji Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    // ==========================================
    // SETTING & ASSIGN KARYAWAN PER PERIODE
    // ==========================================
    public function setting(Request $request, $kode_periode_gaji)
    {
        $data['periode_gaji'] = PeriodeGaji::findOrFail($kode_periode_gaji);
        $user = User::where('id', auth()->user()->id)->first();
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();

        // Karyawan aktif di periode ini
        $query = PeriodeGajiKaryawan::with(['karyawan', 'karyawan.cabang', 'karyawan.departemen', 'karyawan.jabatan'])
            ->where('kode_periode_gaji', $kode_periode_gaji)
            ->where('is_active', true);

        if (!empty($request->kode_cabang)) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('kode_cabang', $request->kode_cabang);
            });
        }
        if (!empty($request->kode_dept)) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('kode_dept', $request->kode_dept);
            });
        }
        if (!empty($request->nama_karyawan)) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%')
                  ->orWhere('nik', 'like', '%' . $request->nama_karyawan . '%');
            });
        }

        $data['karyawan_periode'] = $query->get();

        // Daftar semua periode untuk dropdown mutasi
        $data['all_periode'] = PeriodeGaji::where('is_active', true)->get();

        // Karyawan yang belum di-assign ke periode ini (untuk pilihan tambah)
        $assignedNiks = PeriodeGajiKaryawan::where('is_active', true)->pluck('nik')->toArray();
        $data['available_karyawan'] = Karyawan::where('status_aktif_karyawan', '1')
            ->whereNotIn('nik', $assignedNiks)
            ->orderBy('nama_karyawan')
            ->get();

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.periodegaji.setting' : 'payroll.periode_gaji.setting';
        return view($view, $data);
    }

    public function getAvailableKaryawan(Request $request, $kode_periode_gaji)
    {
        $assignedNiks = PeriodeGajiKaryawan::where('is_active', true)->pluck('nik')->toArray();

        $query = Karyawan::with(['cabang', 'departemen', 'jabatan'])
            ->where('status_aktif_karyawan', '1')
            ->whereNotIn('nik', $assignedNiks);

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        if (!empty($request->q)) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_karyawan', 'like', '%' . $request->q . '%')
                  ->orWhere('nik', 'like', '%' . $request->q . '%');
            });
        }

        $karyawan = $query->orderBy('nama_karyawan', 'asc')->get()->map(function($k) {
            $k->foto_url = (!empty($k->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists('/karyawan/' . $k->foto)) 
                ? getfotoKaryawan($k->foto) 
                : asset('assets/img/avatars/No_Image_Available.jpg');
            $k->status_karyawan_text = $k->status_karyawan == 'K' ? 'Kontrak' : ($k->status_karyawan == 'T' ? 'Tetap' : ($k->status_karyawan ?? ''));
            return $k;
        });

        return response()->json([
            'status' => 'success',
            'data' => $karyawan,
        ]);
    }

    public function assignKaryawan(Request $request, $kode_periode_gaji)
    {
        $request->validate([
            'nik' => 'required|array|min:1',
            'bulan' => 'required|numeric|between:1,12',
            'tahun' => 'required|numeric|digits:4',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->nik as $nik) {
                // Simpan penempatan karyawan ke periode gaji dengan bulan & tahun berlaku
                PeriodeGajiKaryawan::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'kode_periode_gaji' => $kode_periode_gaji,
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                        'is_active' => true,
                    ]
                );
            }

            DB::commit();
            return Redirect::back()->with(messageSuccess('Karyawan Berhasil Ditambahkan ke Periode Gaji'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function mutasiKaryawan(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'kode_periode_baru' => 'required|exists:periode_gaji,kode_periode_gaji',
            'bulan' => 'required|numeric|between:1,12',
            'tahun' => 'required|numeric|digits:4',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $assignmentLama = PeriodeGajiKaryawan::where('nik', $request->nik)->first();
            $kode_periode_lama = $assignmentLama ? $assignmentLama->kode_periode_gaji : null;

            if ($kode_periode_lama === $request->kode_periode_baru) {
                return Redirect::back()->with(messageError('Karyawan sudah berada di periode tersebut.'));
            }

            // Update assignment ke periode baru dengan bulan & tahun berlaku baru
            PeriodeGajiKaryawan::updateOrCreate(
                ['nik' => $request->nik],
                [
                    'kode_periode_gaji' => $request->kode_periode_baru,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'is_active' => true,
                ]
            );

            // Log mutasi
            $tanggalEfektif = sprintf('%04d-%02d-01', $request->tahun, $request->bulan);
            MutasiPeriodeGaji::create([
                'nik' => $request->nik,
                'kode_periode_lama' => $kode_periode_lama,
                'kode_periode_baru' => $request->kode_periode_baru,
                'tanggal_efektif' => $tanggalEfektif,
                'keterangan' => $request->keterangan ?? ('Mutasi mulai periode ' . getNamabulan($request->bulan) . ' ' . $request->tahun),
                'user_id' => auth()->id(),
            ]);

            DB::commit();
            return Redirect::back()->with(messageSuccess('Mutasi Periode Gaji Karyawan Berhasil Diproses'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function removeKaryawan($id)
    {
        try {
            $assignment = PeriodeGajiKaryawan::findOrFail($id);
            $assignment->delete();

            return Redirect::back()->with(messageSuccess('Karyawan Berhasil Dikeluarkan dari Periode Gaji Ini'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function riwayatMutasi(Request $request)
    {
        $query = MutasiPeriodeGaji::with(['karyawan', 'karyawan.cabang', 'karyawan.departemen', 'periodeLama', 'periodeBaru', 'user'])
            ->orderBy('tanggal_efektif', 'desc')
            ->orderBy('id', 'desc');

        if (!empty($request->nik)) {
            $query->where('nik', $request->nik);
        }
        if (!empty($request->kode_periode)) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_periode_baru', $request->kode_periode)
                  ->orWhere('kode_periode_lama', $request->kode_periode);
            });
        }

        $data['mutasi'] = $query->paginate(20);
        $data['all_periode'] = PeriodeGaji::all();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.periodegaji.riwayat_mutasi' : 'payroll.periode_gaji.riwayat_mutasi';
        return view($view, $data);
    }
}
