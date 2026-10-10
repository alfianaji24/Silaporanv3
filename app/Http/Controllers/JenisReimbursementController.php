<?php

namespace App\Http\Controllers;

use App\Models\JenisReimbursement;
use App\Models\Karyawan;
use App\Models\ReimbursementKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class JenisReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisReimbursement::query();

        if (!empty($request->nama_jenis)) {
            $query->where(function($q) use ($request) {
                $q->where('nama_jenis', 'like', '%' . $request->nama_jenis . '%')
                  ->orWhere('kode_jenis_reimburse', 'like', '%' . $request->nama_jenis . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->nama_jenis . '%');
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->wajib_bukti !== null && $request->wajib_bukti !== '') {
            $query->where('wajib_bukti', $request->wajib_bukti);
        }

        $data['jenis_reimbursement'] = $query->orderBy('kode_jenis_reimburse')->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.reimbursement.jenis.index' : 'reimbursement.jenis.index';
        return view($view, $data);
    }

    public function create()
    {
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.reimbursement.jenis.create' : 'reimbursement.jenis.create';
        return view($view);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jenis_reimburse' => 'required|string|max:5|unique:jenis_reimbursement,kode_jenis_reimburse',
            'nama_jenis' => 'required|string|max:100',
        ], [
            'kode_jenis_reimburse.required' => 'Kode wajib diisi',
            'kode_jenis_reimburse.unique' => 'Kode sudah digunakan',
            'nama_jenis.required' => 'Nama jenis wajib diisi',
        ]);

        try {
            JenisReimbursement::create([
                'kode_jenis_reimburse' => strtoupper($request->kode_jenis_reimburse),
                'nama_jenis' => $request->nama_jenis,
                'deskripsi' => $request->deskripsi,
                'batas_nominal' => $request->batas_nominal ? str_replace(['.', ','], ['', '.'], $request->batas_nominal) : null,
                'batas_nominal_bulanan' => $request->batas_nominal_bulanan ? str_replace(['.', ','], ['', '.'], $request->batas_nominal_bulanan) : null,
                'batas_nominal_tahunan' => $request->batas_nominal_tahunan ? str_replace(['.', ','], ['', '.'], $request->batas_nominal_tahunan) : null,
                'wajib_bukti' => $request->wajib_bukti ?? 0,
                'status' => $request->status ?? 1,
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(messageError($e->getMessage()));
        }
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $data['jenis'] = JenisReimbursement::findOrFail($id);
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.reimbursement.jenis.edit' : 'reimbursement.jenis.edit';
        return view($view, $data);
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'nama_jenis' => 'required|string|max:100',
        ]);

        try {
            $jenis = JenisReimbursement::findOrFail($id);
            $jenis->update([
                'nama_jenis' => $request->nama_jenis,
                'deskripsi' => $request->deskripsi,
                'batas_nominal' => $request->batas_nominal ? str_replace(['.', ','], ['', '.'], $request->batas_nominal) : null,
                'batas_nominal_bulanan' => $request->batas_nominal_bulanan ? str_replace(['.', ','], ['', '.'], $request->batas_nominal_bulanan) : null,
                'batas_nominal_tahunan' => $request->batas_nominal_tahunan ? str_replace(['.', ','], ['', '.'], $request->batas_nominal_tahunan) : null,
                'wajib_bukti' => $request->wajib_bukti ?? 0,
                'status' => $request->status ?? 1,
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        try {
            JenisReimbursement::destroy($id);
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    // Enrollment Methods
    public function setkaryawan($id)
    {
        $id = Crypt::decrypt($id);
        $data['jenis'] = JenisReimbursement::findOrFail($id);
        
        $query = ReimbursementKaryawan::where('kode_jenis_reimburse', $data['jenis']->kode_jenis_reimburse)
            ->join('karyawan', 'reimbursement_karyawan.nik', '=', 'karyawan.nik')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select(
                'reimbursement_karyawan.*',
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.nik',
                'karyawan.foto',
                'karyawan.status_aktif_karyawan',
                'karyawan.kode_dept',
                'karyawan.kode_cabang',
                'departemen.nama_dept',
                'cabang.nama_cabang'
            );

        $user = auth()->user();
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();

        if (!$user->isSuperAdmin()) {
            $query->whereIn('karyawan.kode_cabang', $user->getCabangCodes())
                  ->whereIn('karyawan.kode_dept', $user->getDepartemenCodes());
        }

        $data['details'] = $query->orderBy('karyawan.nama_karyawan', 'asc')->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.reimbursement.jenis.setkaryawan' : 'reimbursement.jenis.setkaryawan';
        return view($view, $data);
    }

    public function addkaryawan($id)
    {
        $id = Crypt::decrypt($id);
        $data['jenis'] = JenisReimbursement::findOrFail($id);
        
        $user = auth()->user();
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();

        $query = Karyawan::where('status_aktif_karyawan', 1)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->whereNotExists(function ($q) use ($data) {
                $q->select(DB::raw(1))
                    ->from('reimbursement_karyawan')
                    ->whereRaw('reimbursement_karyawan.nik = karyawan.nik')
                    ->where('kode_jenis_reimburse', $data['jenis']->kode_jenis_reimburse);
            });

        if (!$user->isSuperAdmin()) {
            $query->whereIn('karyawan.kode_cabang', $user->getCabangCodes())
                  ->whereIn('karyawan.kode_dept', $user->getDepartemenCodes());
        }

        $data['karyawan'] = $query->select('karyawan.nik', 'karyawan.nik_show', 'karyawan.nama_karyawan', 'karyawan.foto', 'karyawan.status_aktif_karyawan', 'departemen.nama_dept', 'cabang.nama_cabang', 'karyawan.kode_dept', 'karyawan.kode_cabang')
            ->orderBy('nama_karyawan')
            ->get();

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.reimbursement.jenis.addkaryawan' : 'reimbursement.jenis.addkaryawan';
        return view($view, $data);
    }

    public function storekaryawan(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $jenis = JenisReimbursement::findOrFail($id);
        
        $request->validate([
            'nik' => 'required',
            'tanggal_mulai' => 'required|date',
        ]);

        $user = auth()->user();

        try {
            // Support multiple NIK if bulk selection is used in view
            $niks = is_array($request->nik) ? $request->nik : [$request->nik];
            
            foreach ($niks as $nik) {
                if (!$user->isSuperAdmin()) {
                    $karyawan = Karyawan::where('nik', $nik)->firstOrFail();
                    if (!in_array($karyawan->kode_cabang, $user->getCabangCodes()) || !in_array($karyawan->kode_dept, $user->getDepartemenCodes())) {
                        abort(403, 'Akses ditolak.');
                    }
                }

                ReimbursementKaryawan::updateOrCreate(
                    [
                        'nik' => $nik,
                        'kode_jenis_reimburse' => $jenis->kode_jenis_reimburse
                    ],
                    [
                        'batas_nominal_override' => $request->batas_nominal_override ? str_replace(['.', ','], ['', '.'], $request->batas_nominal_override) : null,
                        'tanggal_mulai' => $request->tanggal_mulai,
                        'tanggal_selesai' => $request->tanggal_selesai,
                        'status' => 1
                    ]
                );
            }

            return Redirect::back()->with(messageSuccess('Karyawan Berhasil Didaftarkan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroykaryawan($id)
    {
        $id = Crypt::decrypt($id);
        $reimbursementKaryawan = ReimbursementKaryawan::with('karyawan')->findOrFail($id);
        $user = auth()->user();

        if (!$user->isSuperAdmin()) {
            if (!in_array($reimbursementKaryawan->karyawan->kode_cabang, $user->getCabangCodes()) || !in_array($reimbursementKaryawan->karyawan->kode_dept, $user->getDepartemenCodes())) {
                abort(403, 'Akses ditolak.');
            }
        }

        try {
            $reimbursementKaryawan->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
