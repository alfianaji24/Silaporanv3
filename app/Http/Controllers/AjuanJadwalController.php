<?php

namespace App\Http\Controllers;

use App\Models\AjuanJadwal;
use App\Models\Jamkerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Models\Userkaryawan;
use App\Models\Karyawan;
use App\Models\Setjamkerjabydate;
use App\Models\GrupDetail;
use App\Models\GrupJamkerjaBydate;
use App\Models\Setjamkerjabyday;
use App\Models\Detailsetjamkerjabydept;

class AjuanJadwalController extends Controller
{
    public function index(Request $request)
    {
        $auth_user = auth()->user();
        $role = $auth_user->getRoleNames()->first();
        
        $query = AjuanJadwal::query();
        $query->with(['karyawan.jabatan', 'karyawan.departemen', 'karyawan.cabang', 'jamKerjaAwal', 'jamKerjaTujuan']);
        
        // If employee, only show their own requests
        if ($role == 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
            $nik = $userkaryawan ? $userkaryawan->nik : null;
            $query->where('nik', $nik);
        }

        // Filter by date
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        // Role-based Access Control (RBAC) & Filters
        if ($role != 'karyawan') {
            // Apply Branch/Dept restrictions for non-super admins
            if (!$auth_user->isSuperAdmin()) {
                $userCabangs = $auth_user->getCabangCodes();
                $userDepartemens = $auth_user->getDepartemenCodes();

                if (!empty($userCabangs)) {
                    $query->whereHas('karyawan', function ($q) use ($userCabangs) {
                        $q->whereIn('kode_cabang', $userCabangs);
                    });
                }
                
                if (!empty($userDepartemens)) {
                    $query->whereHas('karyawan', function ($q) use ($userDepartemens) {
                        $q->whereIn('kode_dept', $userDepartemens);
                    });
                }
            }

            // Filter by Name
            if (!empty($request->nama_karyawan)) {
                $query->whereHas('karyawan', function ($q) use ($request) {
                    $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
                });
            }

            // Filter by Cabang
            if (!empty($request->kode_cabang)) {
                $query->whereHas('karyawan', function ($q) use ($request) {
                    $q->where('kode_cabang', $request->kode_cabang);
                });
            }

            // Filter by Dept
            if (!empty($request->kode_dept)) {
                $query->whereHas('karyawan', function ($q) use ($request) {
                    $q->where('kode_dept', $request->kode_dept);
                });
            }
            
            // Filter by Status
            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }
        } else {
             // Karyawan filters (usually just date and maybe status)
             if (!empty($request->status)) {
                $query->where('status', $request->status);
            }
        }
        
        // Order by latest
        $query->orderBy('created_at', 'desc');

        $ajuanjadwal = $query->paginate(10);
        $ajuanjadwal->appends($request->all());

        $cabang = auth()->user()->getCabang();
        $departemen = auth()->user()->getDepartemen();

        if ($role == 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
            $nik = $userkaryawan ? $userkaryawan->nik : null;

            if ($nik) {
                $karyawan = Karyawan::where('nik', $nik)->with(['jabatan', 'departemen', 'cabang'])->first();
                return view('ajuanjadwal.index_mobile', compact('ajuanjadwal', 'karyawan'));
            }
             // Fallback if no NIK found (shouldn't happen for valid employee user)
             return redirect()->back()->with('warning', 'Data Karyawan tidak ditemukan.');
        }
        return view('ajuanjadwal.index', compact('ajuanjadwal', 'cabang', 'departemen'));
    }

    public function create()
    {
        $user = auth()->user();
        $karyawan = [];
        $jamkerja = collect();

        if (!$user->hasRole('karyawan')) {
            $karyawan = Karyawan::orderBy('nama_karyawan')->get();
        } else {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if ($userkaryawan) {
                $k = Karyawan::where('nik', $userkaryawan->nik)->first();
                if ($k) {
                    $jamkerja = Jamkerja::query()
                        ->visibleUntukJabatanKaryawan($k->kode_jabatan)
                        ->orderBy('nama_jam_kerja')
                        ->get();
                }
            }
        }

        // Use modal view if AJAX request, otherwise use regular view
        if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('ajuanjadwal.create-modal', compact('jamkerja', 'karyawan'));
        }

        return view('ajuanjadwal.create', compact('jamkerja', 'karyawan'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        if ($role == 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $nik = $userkaryawan ? $userkaryawan->nik : null;
            
            $request->validate([
                'tanggal' => 'required|date',
                'kode_jam_kerja_tujuan' => 'required',
                'keterangan' => 'required'
            ]);
        } else {
            $nik = $request->nik;
            $request->validate([
                'nik' => 'required',
                'tanggal' => 'required|date',
                'kode_jam_kerja_tujuan' => 'required',
                'keterangan' => 'required'
            ]);
        }

        if (empty($nik)) {
             return Redirect::back()->with(['warning' => 'NIK tidak ditemukan. Hubungi IT.']);
        }

        // Get Employee Data
    $karyawan = Karyawan::where('nik', $nik)->first();
    if (!$karyawan) {
        return Redirect::back()->with(['warning' => 'Data karyawan tidak ditemukan.']);
    }

    $izinTujuan = Jamkerja::query()
        ->where('kode_jam_kerja', $request->kode_jam_kerja_tujuan)
        ->visibleUntukJabatanKaryawan($karyawan->kode_jabatan)
        ->exists();
    if (!$izinTujuan) {
        return Redirect::back()->with(['warning' => 'Shift tujuan tidak diizinkan untuk jabatan karyawan ini.']);
    }

    $kode_cabang = $karyawan->kode_cabang;
    $kode_dept = $karyawan->kode_dept;
    $tanggal = $request->tanggal;
    $namahari = getnamaHari(date('D', strtotime($tanggal)));

    // Calculate Original Schedule (kode_jam_kerja_awal)
    $kode_jam_kerja_awal = null;

    // 1. Cek Jam Kerja By Date
    $jamkerja_by_date = Setjamkerjabydate::where('nik', $nik)->where('tanggal', $tanggal)->first();
    if ($jamkerja_by_date) {
        $kode_jam_kerja_awal = $jamkerja_by_date->kode_jam_kerja;
    }

    // 2. Jika tidak ada, Cek Jam Kerja Group
    if ($kode_jam_kerja_awal == null) {
        $cek_group = GrupDetail::where('nik', $nik)->first();
        if ($cek_group) {
            $jamkerja_group = GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)
                ->where('tanggal', $tanggal)
                ->first();
            if ($jamkerja_group) {
                $kode_jam_kerja_awal = $jamkerja_group->kode_jam_kerja;
            }
        }
    }

    // 3. Jika tidak ada, Cek Jam Kerja Harian (Per Orang)
    if ($kode_jam_kerja_awal == null) {
        $jamkerja_harian = Setjamkerjabyday::where('nik', $nik)->where('hari', $namahari)->first();
        if ($jamkerja_harian) {
             $kode_jam_kerja_awal = $jamkerja_harian->kode_jam_kerja;
        }
    }

    // 4. Jika tidak ada, Cek Jam Kerja Departemen (Default)
    if ($kode_jam_kerja_awal == null) {
        $jamkerja_dept = Detailsetjamkerjabydept::where('kode_dept', $kode_dept)
            ->where('kode_cabang', $kode_cabang)
            ->where('hari', $namahari)
            ->first();
        if ($jamkerja_dept) {
            $kode_jam_kerja_awal = $jamkerja_dept->kode_jam_kerja;
        }
    }
    
    try {
        AjuanJadwal::create([
            'nik' => $nik,
            'tanggal' => $request->tanggal,
            'kode_jam_kerja_awal' => $kode_jam_kerja_awal,
            'kode_jam_kerja_tujuan' => $request->kode_jam_kerja_tujuan,
            'keterangan' => $request->keterangan,
            'status' => 'p'
        ]);

        // Handle AJAX response for modal
        if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan Berhasil Disimpan',
                'redirect' => route('ajuanjadwal.index')
            ]);
        }

        return Redirect::route('ajuanjadwal.index')->with(['success' => 'Pengajuan Berhasil Disimpan']);
        } catch (\Exception $e) {
            // Handle AJAX response for modal
            if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function approve($id)
    {
        try {
            $ajuan = AjuanJadwal::findOrFail($id);
            $ajuan->update(['status' => 'a']); // Approved
            return Redirect::back()->with(['success' => 'Pengajuan Disetujui']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function cancelapprove($id)
    {
        try {
            $ajuan = AjuanJadwal::findOrFail($id);
            $ajuan->update(['status' => 'p']); // Revert to Pending
            return Redirect::back()->with(['success' => 'Approval Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function reject($id)
    {
        try {
            $ajuan = AjuanJadwal::findOrFail($id);
            $ajuan->update(['status' => 'r']); // Rejected
            return Redirect::back()->with(['success' => 'Pengajuan Ditolak']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $id = \Illuminate\Support\Facades\Crypt::decrypt($id);
            $ajuan = AjuanJadwal::findOrFail($id);

            if ($ajuan->status != 'p') {
                return Redirect::back()->with(['warning' => 'Hanya pengajuan dengan status Pending yang dapat dihapus.']);
            }

            $ajuan->delete();
            return Redirect::back()->with(['success' => 'Pengajuan berhasil dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    /**
     * Get jadwal awal karyawan untuk tanggal tertentu
     */
    public function getJadwalAwal(Request $request)
    {
        $nik = $request->nik;
        $tanggal = $request->tanggal;

        if (!$nik || !$tanggal) {
            return response()->json([
                'success' => false,
                'message' => 'NIK dan tanggal harus diisi'
            ], 400);
        }

        try {
            // Get employee data
            $karyawan = Karyawan::where('nik', $nik)->first();
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $kode_cabang = $karyawan->kode_cabang;
            $kode_dept = $karyawan->kode_dept;
            $namahari = getnamaHari(date('D', strtotime($tanggal)));

            // Calculate Original Schedule (kode_jam_kerja_awal)
            $kode_jam_kerja_awal = null;
            $jadwal_info = null;

            // 1. Cek Jam Kerja By Date
            $jamkerja_by_date = Setjamkerjabydate::where('nik', $nik)->where('tanggal', $tanggal)->first();
            if ($jamkerja_by_date) {
                $kode_jam_kerja_awal = $jamkerja_by_date->kode_jam_kerja;
                $jadwal_info = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja_awal)->first();
            }

            // 2. Jika tidak ada, Cek Jam Kerja Group
            if ($kode_jam_kerja_awal == null) {
                $cek_group = GrupDetail::where('nik', $nik)->first();
                if ($cek_group) {
                    $jamkerja_group = GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)
                        ->where('tanggal', $tanggal)
                        ->first();
                    if ($jamkerja_group) {
                        $kode_jam_kerja_awal = $jamkerja_group->kode_jam_kerja;
                        $jadwal_info = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja_awal)->first();
                    }
                }
            }

            // 3. Jika tidak ada, Cek Jam Kerja Harian (Per Orang)
            if ($kode_jam_kerja_awal == null) {
                $jamkerja_harian = Setjamkerjabyday::where('nik', $nik)->where('hari', $namahari)->first();
                if ($jamkerja_harian) {
                    $kode_jam_kerja_awal = $jamkerja_harian->kode_jam_kerja;
                    $jadwal_info = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja_awal)->first();
                }
            }

            // 4. Jika tidak ada, Cek Jam Kerja Departemen (Default)
            if ($kode_jam_kerja_awal == null) {
                $jamkerja_dept = Detailsetjamkerjabydept::where('kode_dept', $kode_dept)
                    ->where('kode_cabang', $kode_cabang)
                    ->where('hari', $namahari)
                    ->first();
                if ($jamkerja_dept) {
                    $kode_jam_kerja_awal = $jamkerja_dept->kode_jam_kerja;
                    $jadwal_info = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja_awal)->first();
                }
            }

            if ($jadwal_info) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'kode_jam_kerja' => $jadwal_info->kode_jam_kerja,
                        'nama_jam_kerja' => $jadwal_info->nama_jam_kerja,
                        'jam_masuk' => $jadwal_info->jam_masuk,
                        'jam_pulang' => $jadwal_info->jam_pulang,
                        'sumber' => $this->getSumberJadwal($nik, $tanggal, $kode_jam_kerja_awal)
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan untuk tanggal tersebut'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper untuk mendapatkan sumber jadwal
     */
    private function getSumberJadwal($nik, $tanggal, $kode_jam_kerja)
    {
        $namahari = getnamaHari(date('D', strtotime($tanggal)));

        // Cek By Date
        if (Setjamkerjabydate::where('nik', $nik)->where('tanggal', $tanggal)->where('kode_jam_kerja', $kode_jam_kerja)->exists()) {
            return 'Jadwal Per Tanggal';
        }

        // Cek Group
        $cek_group = GrupDetail::where('nik', $nik)->first();
        if ($cek_group) {
            if (GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)->where('tanggal', $tanggal)->where('kode_jam_kerja', $kode_jam_kerja)->exists()) {
                return 'Jadwal Group';
            }
        }

        // Cek Harian
        if (Setjamkerjabyday::where('nik', $nik)->where('hari', $namahari)->where('kode_jam_kerja', $kode_jam_kerja)->exists()) {
            return 'Jadwal Harian';
        }

        // Default: Departemen
        return 'Jadwal Departemen (Default)';
    }
}
