<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Izinabsen;
use App\Models\Izinsakit;
use App\Models\Izincuti;
use App\Models\Izindinas;
use App\Models\Reimbursement;
use App\Models\ReimbursementDetail;
use App\Models\Userkaryawan;
use App\Models\User;
use App\Models\ApprovalLayer;
use App\Models\Approval;
use App\Models\Presensi;
use App\Services\ApprovalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    /**
     * Get pending approval requests delegated to the authenticated employee.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan || !$userkaryawan->approval_admin_id) {
            return response()->json([
                'success' => true,
                'has_access' => false,
                'message' => 'Akun Anda belum didelegasikan hak approval oleh Administrator.',
                'data' => null
            ], 200);
        }

        $admin = User::find($userkaryawan->approval_admin_id);
        if (!$admin) {
            return response()->json([
                'success' => true,
                'has_access' => false,
                'message' => 'Admin delegasi approval tidak ditemukan.',
                'data' => null
            ], 200);
        }

        $adminRole = $admin->getRoleNames()->first();
        $adminDeptCodes = $admin->getDepartemenCodes();
        $adminCabangCodes = $admin->getCabangCodes();

        $layers = ApprovalLayer::where('role_name', $adminRole)->get();
        $featureLevels = $layers->map(function ($l) {
            return [
                'feature' => $l->feature,
                'level' => $l->level,
                'kode_dept' => $l->kode_dept,
                'kode_jabatan' => $l->kode_jabatan
            ];
        });

        $pendingIzinAbsen = collect();
        $pendingIzinSakit = collect();
        $pendingIzinCuti = collect();
        $pendingIzinDinas = collect();
        $pendingReimbursement = collect();

        foreach ($featureLevels as $fl) {
            if ($fl['feature'] === 'IZIN') {
                // 1. Izin Absen
                $q1 = Izinabsen::where('presensi_izinabsen.status', 0)
                    ->where('presensi_izinabsen.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->select(
                        'presensi_izinabsen.*',
                        'karyawan.nama_karyawan',
                        'karyawan.kode_dept',
                        'karyawan.kode_cabang',
                        'karyawan.kode_jabatan',
                        'departemen.nama_dept',
                        'cabang.nama_cabang',
                        'jabatan.nama_jabatan'
                    );

                if ($fl['kode_dept']) $q1->where('karyawan.kode_dept', $fl['kode_dept']);
                if ($fl['kode_jabatan']) $q1->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                if (!empty($adminDeptCodes)) $q1->whereIn('karyawan.kode_dept', $adminDeptCodes);
                if (!empty($adminCabangCodes)) $q1->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                $pendingIzinAbsen = $pendingIzinAbsen->merge($q1->get());

                // 2. Izin Sakit
                $q2 = Izinsakit::where('presensi_izinsakit.status', 0)
                    ->where('presensi_izinsakit.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->select(
                        'presensi_izinsakit.*',
                        'karyawan.nama_karyawan',
                        'karyawan.kode_dept',
                        'karyawan.kode_cabang',
                        'karyawan.kode_jabatan',
                        'departemen.nama_dept',
                        'cabang.nama_cabang',
                        'jabatan.nama_jabatan'
                    );

                if ($fl['kode_dept']) $q2->where('karyawan.kode_dept', $fl['kode_dept']);
                if ($fl['kode_jabatan']) $q2->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                if (!empty($adminDeptCodes)) $q2->whereIn('karyawan.kode_dept', $adminDeptCodes);
                if (!empty($adminCabangCodes)) $q2->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                $pendingIzinSakit = $pendingIzinSakit->merge($q2->get());

                // 3. Izin Cuti
                $q3 = Izincuti::where('presensi_izincuti.status', 0)
                    ->where('presensi_izincuti.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->select(
                        'presensi_izincuti.*',
                        'karyawan.nama_karyawan',
                        'karyawan.kode_dept',
                        'karyawan.kode_cabang',
                        'karyawan.kode_jabatan',
                        'departemen.nama_dept',
                        'cabang.nama_cabang',
                        'jabatan.nama_jabatan'
                    );

                if ($fl['kode_dept']) $q3->where('karyawan.kode_dept', $fl['kode_dept']);
                if ($fl['kode_jabatan']) $q3->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                if (!empty($adminDeptCodes)) $q3->whereIn('karyawan.kode_dept', $adminDeptCodes);
                if (!empty($adminCabangCodes)) $q3->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                $pendingIzinCuti = $pendingIzinCuti->merge($q3->get());

                // 4. Izin Dinas
                $q4 = Izindinas::where('presensi_izindinas.status', 0)
                    ->where('presensi_izindinas.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->select(
                        'presensi_izindinas.*',
                        'karyawan.nama_karyawan',
                        'karyawan.kode_dept',
                        'karyawan.kode_cabang',
                        'karyawan.kode_jabatan',
                        'departemen.nama_dept',
                        'cabang.nama_cabang',
                        'jabatan.nama_jabatan'
                    );

                if ($fl['kode_dept']) $q4->where('karyawan.kode_dept', $fl['kode_dept']);
                if ($fl['kode_jabatan']) $q4->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                if (!empty($adminDeptCodes)) $q4->whereIn('karyawan.kode_dept', $adminDeptCodes);
                if (!empty($adminCabangCodes)) $q4->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                $pendingIzinDinas = $pendingIzinDinas->merge($q4->get());
            }

            if ($fl['feature'] === 'REIMBURSEMENT') {
                // 5. Reimbursement
                $q5 = Reimbursement::where('reimbursement.status', 'P')
                    ->where('reimbursement.approval_step', $fl['level'])
                    ->join('karyawan', 'reimbursement.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->select(
                        'reimbursement.*',
                        'karyawan.nama_karyawan',
                        'karyawan.nik_show',
                        'karyawan.kode_dept',
                        'karyawan.kode_cabang',
                        'karyawan.kode_jabatan',
                        'departemen.nama_dept',
                        'cabang.nama_cabang',
                        'jabatan.nama_jabatan'
                    );

                if ($fl['kode_dept']) $q5->where('karyawan.kode_dept', $fl['kode_dept']);
                if ($fl['kode_jabatan']) $q5->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                if (!empty($adminDeptCodes)) $q5->whereIn('karyawan.kode_dept', $adminDeptCodes);
                if (!empty($adminCabangCodes)) $q5->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                $pendingReimbursement = $pendingReimbursement->merge($q5->get());
            }
        }

        // Deduplicate
        $listAbsen = $pendingIzinAbsen->unique('kode_izin')->map(function ($item) {
            $hari = hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'];
            return [
                'kode' => $item->kode_izin,
                'type' => 'absen',
                'type_label' => 'Izin Absen',
                'nik' => $item->nik,
                'nama_karyawan' => $item->nama_karyawan,
                'nama_dept' => $item->nama_dept ?? '-',
                'nama_cabang' => $item->nama_cabang ?? '-',
                'nama_jabatan' => $item->nama_jabatan ?? '-',
                'tanggal_pengajuan' => $item->tanggal,
                'dari' => $item->dari,
                'sampai' => $item->sampai,
                'durasi_hari' => $hari,
                'keterangan' => $item->keterangan,
                'approval_step' => (int) $item->approval_step,
                'doc_sid_url' => null,
                'total_nominal' => null,
            ];
        });

        $listSakit = $pendingIzinSakit->unique('kode_izin_sakit')->map(function ($item) {
            $hari = hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'];
            return [
                'kode' => $item->kode_izin_sakit,
                'type' => 'sakit',
                'type_label' => 'Izin Sakit',
                'nik' => $item->nik,
                'nama_karyawan' => $item->nama_karyawan,
                'nama_dept' => $item->nama_dept ?? '-',
                'nama_cabang' => $item->nama_cabang ?? '-',
                'nama_jabatan' => $item->nama_jabatan ?? '-',
                'tanggal_pengajuan' => $item->tanggal,
                'dari' => $item->dari,
                'sampai' => $item->sampai,
                'durasi_hari' => $hari,
                'keterangan' => $item->keterangan,
                'approval_step' => (int) $item->approval_step,
                'doc_sid' => $item->doc_sid,
                'doc_sid_url' => $item->doc_sid ? asset('storage/uploads/sid/' . $item->doc_sid) : null,
                'total_nominal' => null,
            ];
        });

        $listCuti = $pendingIzinCuti->unique('kode_izin_cuti')->map(function ($item) {
            $hari = hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'];
            return [
                'kode' => $item->kode_izin_cuti,
                'type' => 'cuti',
                'type_label' => 'Izin Cuti',
                'nik' => $item->nik,
                'nama_karyawan' => $item->nama_karyawan,
                'nama_dept' => $item->nama_dept ?? '-',
                'nama_cabang' => $item->nama_cabang ?? '-',
                'nama_jabatan' => $item->nama_jabatan ?? '-',
                'tanggal_pengajuan' => $item->tanggal,
                'dari' => $item->dari,
                'sampai' => $item->sampai,
                'durasi_hari' => $hari,
                'keterangan' => $item->keterangan,
                'approval_step' => (int) $item->approval_step,
                'doc_sid_url' => null,
                'total_nominal' => null,
            ];
        });

        $listDinas = $pendingIzinDinas->unique('kode_izin_dinas')->map(function ($item) {
            $hari = hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'];
            return [
                'kode' => $item->kode_izin_dinas,
                'type' => 'dinas',
                'type_label' => 'Izin Dinas',
                'nik' => $item->nik,
                'nama_karyawan' => $item->nama_karyawan,
                'nama_dept' => $item->nama_dept ?? '-',
                'nama_cabang' => $item->nama_cabang ?? '-',
                'nama_jabatan' => $item->nama_jabatan ?? '-',
                'tanggal_pengajuan' => $item->tanggal,
                'dari' => $item->dari,
                'sampai' => $item->sampai,
                'durasi_hari' => $hari,
                'keterangan' => $item->keterangan,
                'approval_step' => (int) $item->approval_step,
                'doc_sid_url' => null,
                'total_nominal' => null,
            ];
        });

        $listReimburse = $pendingReimbursement->unique('no_reimbursement')->map(function ($item) {
            return [
                'kode' => $item->no_reimbursement,
                'type' => 'reimbursement',
                'type_label' => 'Reimbursement',
                'nik' => $item->nik,
                'nama_karyawan' => $item->nama_karyawan,
                'nama_dept' => $item->nama_dept ?? '-',
                'nama_cabang' => $item->nama_cabang ?? '-',
                'nama_jabatan' => $item->nama_jabatan ?? '-',
                'tanggal_pengajuan' => $item->tanggal_pengajuan,
                'dari' => $item->tanggal_pengajuan,
                'sampai' => $item->tanggal_pengajuan,
                'durasi_hari' => 1,
                'keterangan' => $item->catatan ?? '-',
                'approval_step' => (int) $item->approval_step,
                'doc_sid_url' => null,
                'total_nominal' => (double) $item->total_nominal,
            ];
        });

        $allList = collect()
            ->merge($listAbsen)
            ->merge($listSakit)
            ->merge($listCuti)
            ->merge($listDinas)
            ->merge($listReimburse)
            ->values();

        $totalPending = $allList->count();

        return response()->json([
            'success' => true,
            'has_access' => true,
            'data' => [
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'role' => $adminRole,
                ],
                'summary' => [
                    'total_pending' => $totalPending,
                    'count_absen' => $listAbsen->count(),
                    'count_sakit' => $listSakit->count(),
                    'count_cuti' => $listCuti->count(),
                    'count_dinas' => $listDinas->count(),
                    'count_reimbursement' => $listReimburse->count(),
                ],
                'items' => $allList
            ]
        ]);
    }

    /**
     * Get detail of a specific pending request.
     */
    public function detail(Request $request, $type, $kode)
    {
        $user = $request->user();
        $admin = $this->getDelegationAdmin($user);
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang delegasi approval.'
            ], 403);
        }

        $adminCabangs = $admin->getCabangCodes();
        $adminDepts = $admin->getDepartemenCodes();

        switch ($type) {
            case 'absen':
                $item = Izinabsen::where('kode_izin', $kode)
                    ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->select('presensi_izinabsen.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'karyawan.kode_cabang', 'karyawan.kode_dept', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang')
                    ->first();

                if (!$item) {
                    return response()->json(['success' => false, 'message' => 'Data izin absen tidak ditemukan.'], 404);
                }
                if (!in_array($item->kode_cabang, $adminCabangs) || !in_array($item->kode_dept, $adminDepts)) {
                    return response()->json(['success' => false, 'message' => 'Admin tidak memiliki akses ke data ini.'], 403);
                }

                $data = [
                    'kode' => $item->kode_izin,
                    'type' => 'absen',
                    'type_label' => 'Izin Absen',
                    'nik' => $item->nik,
                    'nama_karyawan' => $item->nama_karyawan,
                    'nama_dept' => $item->nama_dept ?? '-',
                    'nama_cabang' => $item->nama_cabang ?? '-',
                    'nama_jabatan' => $item->nama_jabatan ?? '-',
                    'tanggal_pengajuan' => $item->tanggal,
                    'dari' => $item->dari,
                    'sampai' => $item->sampai,
                    'durasi_hari' => hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'],
                    'keterangan' => $item->keterangan,
                    'approval_step' => (int) $item->approval_step,
                    'status' => $item->status,
                ];
                break;

            case 'sakit':
                $item = Izinsakit::where('kode_izin_sakit', $kode)
                    ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->select('presensi_izinsakit.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'karyawan.kode_cabang', 'karyawan.kode_dept', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang')
                    ->first();

                if (!$item) {
                    return response()->json(['success' => false, 'message' => 'Data izin sakit tidak ditemukan.'], 404);
                }
                if (!in_array($item->kode_cabang, $adminCabangs) || !in_array($item->kode_dept, $adminDepts)) {
                    return response()->json(['success' => false, 'message' => 'Admin tidak memiliki akses ke data ini.'], 403);
                }

                $data = [
                    'kode' => $item->kode_izin_sakit,
                    'type' => 'sakit',
                    'type_label' => 'Izin Sakit',
                    'nik' => $item->nik,
                    'nama_karyawan' => $item->nama_karyawan,
                    'nama_dept' => $item->nama_dept ?? '-',
                    'nama_cabang' => $item->nama_cabang ?? '-',
                    'nama_jabatan' => $item->nama_jabatan ?? '-',
                    'tanggal_pengajuan' => $item->tanggal,
                    'dari' => $item->dari,
                    'sampai' => $item->sampai,
                    'durasi_hari' => hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'],
                    'keterangan' => $item->keterangan,
                    'approval_step' => (int) $item->approval_step,
                    'status' => $item->status,
                    'doc_sid' => $item->doc_sid,
                    'doc_sid_url' => $item->doc_sid ? asset('storage/uploads/sid/' . $item->doc_sid) : null,
                ];
                break;

            case 'cuti':
                $item = Izincuti::where('kode_izin_cuti', $kode)
                    ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->select('presensi_izincuti.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'karyawan.kode_cabang', 'karyawan.kode_dept', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang')
                    ->first();

                if (!$item) {
                    return response()->json(['success' => false, 'message' => 'Data izin cuti tidak ditemukan.'], 404);
                }
                if (!in_array($item->kode_cabang, $adminCabangs) || !in_array($item->kode_dept, $adminDepts)) {
                    return response()->json(['success' => false, 'message' => 'Admin tidak memiliki akses ke data ini.'], 403);
                }

                $data = [
                    'kode' => $item->kode_izin_cuti,
                    'type' => 'cuti',
                    'type_label' => 'Izin Cuti',
                    'nik' => $item->nik,
                    'nama_karyawan' => $item->nama_karyawan,
                    'nama_dept' => $item->nama_dept ?? '-',
                    'nama_cabang' => $item->nama_cabang ?? '-',
                    'nama_jabatan' => $item->nama_jabatan ?? '-',
                    'tanggal_pengajuan' => $item->tanggal,
                    'dari' => $item->dari,
                    'sampai' => $item->sampai,
                    'durasi_hari' => hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'],
                    'keterangan' => $item->keterangan,
                    'approval_step' => (int) $item->approval_step,
                    'status' => $item->status,
                ];
                break;

            case 'dinas':
                $item = Izindinas::where('kode_izin_dinas', $kode)
                    ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->select('presensi_izindinas.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'karyawan.kode_cabang', 'karyawan.kode_dept', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang')
                    ->first();

                if (!$item) {
                    return response()->json(['success' => false, 'message' => 'Data izin dinas tidak ditemukan.'], 404);
                }
                if (!in_array($item->kode_cabang, $adminCabangs) || !in_array($item->kode_dept, $adminDepts)) {
                    return response()->json(['success' => false, 'message' => 'Admin tidak memiliki akses ke data ini.'], 403);
                }

                $data = [
                    'kode' => $item->kode_izin_dinas,
                    'type' => 'dinas',
                    'type_label' => 'Izin Dinas',
                    'nik' => $item->nik,
                    'nama_karyawan' => $item->nama_karyawan,
                    'nama_dept' => $item->nama_dept ?? '-',
                    'nama_cabang' => $item->nama_cabang ?? '-',
                    'nama_jabatan' => $item->nama_jabatan ?? '-',
                    'tanggal_pengajuan' => $item->tanggal,
                    'dari' => $item->dari,
                    'sampai' => $item->sampai,
                    'durasi_hari' => hitungHariKerja($item->nik, $item->dari, $item->sampai)['total_hari'],
                    'keterangan' => $item->keterangan,
                    'approval_step' => (int) $item->approval_step,
                    'status' => $item->status,
                ];
                break;

            case 'reimbursement':
                $item = Reimbursement::where('no_reimbursement', $kode)
                    ->join('karyawan', 'reimbursement.nik', '=', 'karyawan.nik')
                    ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                    ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->select('reimbursement.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'karyawan.kode_cabang', 'karyawan.kode_dept', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang')
                    ->first();

                if (!$item) {
                    return response()->json(['success' => false, 'message' => 'Data pengajuan reimbursement tidak ditemukan.'], 404);
                }
                if (!in_array($item->kode_cabang, $adminCabangs) || !in_array($item->kode_dept, $adminDepts)) {
                    return response()->json(['success' => false, 'message' => 'Admin tidak memiliki akses ke data ini.'], 403);
                }

                $details = ReimbursementDetail::where('reimbursement_id', $item->id)
                    ->join('jenis_reimbursement', 'reimbursement_detail.kode_jenis_reimburse', '=', 'jenis_reimbursement.kode_jenis_reimburse')
                    ->select('reimbursement_detail.*', 'jenis_reimbursement.nama_jenis')
                    ->get()
                    ->map(function ($d) {
                        return [
                            'id' => $d->id,
                            'nama_jenis' => $d->nama_jenis,
                            'tanggal_transaksi' => $d->tanggal_transaksi,
                            'nominal' => (double) $d->nominal,
                            'keterangan' => $d->keterangan,
                            'foto_nota' => $d->foto_nota ? asset('storage/uploads/reimbursement/' . $d->foto_nota) : null,
                        ];
                    });

                $data = [
                    'kode' => $item->no_reimbursement,
                    'type' => 'reimbursement',
                    'type_label' => 'Reimbursement',
                    'nik' => $item->nik,
                    'nama_karyawan' => $item->nama_karyawan,
                    'nama_dept' => $item->nama_dept ?? '-',
                    'nama_cabang' => $item->nama_cabang ?? '-',
                    'nama_jabatan' => $item->nama_jabatan ?? '-',
                    'tanggal_pengajuan' => $item->tanggal_pengajuan,
                    'total_nominal' => (double) $item->total_nominal,
                    'catatan' => $item->catatan ?? '-',
                    'approval_step' => (int) $item->approval_step,
                    'status' => $item->status,
                    'details' => $details,
                ];
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Tipe approval tidak valid.'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Process Approve or Reject for an item.
     */
    public function process(Request $request, $type, $kode)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,tolak',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $admin = $this->getDelegationAdmin($user);
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang delegasi approval.'
            ], 403);
        }

        $action = $request->input('action');
        $catatan = $request->input('catatan');
        $approvalService = app(ApprovalService::class);
        $userRole = $admin->getRoleNames()->first();
        $approvalUserId = $admin->id;

        DB::beginTransaction();
        try {
            switch ($type) {
                case 'absen':
                    $izin = Izinabsen::where('kode_izin', $kode)
                        ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
                        ->select('presensi_izinabsen.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
                        ->first();

                    if (!$izin) {
                        return response()->json(['success' => false, 'message' => 'Data izin absen tidak ditemukan.'], 404);
                    }

                    $currentStep = $izin->approval_step;
                    if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $izin->kode_dept, $izin->kode_jabatan, $user, $izin->kode_cabang)) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang approval tahap ke-' . $currentStep], 403);
                    }

                    if ($action === 'approve') {
                        Approval::create([
                            'approvable_type' => Izinabsen::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'approved',
                            'keterangan' => $catatan ?: ('Approved by ' . $admin->name),
                        ]);

                        $nextLevel = $currentStep + 1;
                        $nextRule = $approvalService->getLayer('IZIN', $nextLevel, $izin->kode_dept, $izin->kode_jabatan, $izin->kode_cabang);
                        if ($nextRule) {
                            Izinabsen::where('kode_izin', $kode)->update(['approval_step' => $nextLevel]);
                        } else {
                            // Final approval -> generate presensi record
                            $dari = $izin->dari;
                            $sampai = $izin->sampai;
                            $nik = $izin->nik;
                            $hitung = hitungHariKerja($nik, $dari, $sampai);
                            if ($hitung['total_hari'] > 0) {
                                foreach ($hitung['detail_hari'] as $item) {
                                    $tgl = $item['tanggal'];
                                    $jamkerja = $item['jamkerja'];
                                    $presensi = Presensi::where('nik', $nik)->where('tanggal', $tgl)->first();
                                    if ($presensi) {
                                        $id_presensi = $presensi->id;
                                        Presensi::where('id', $id_presensi)->update([
                                            'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                            'status' => 'i',
                                        ]);
                                    } else {
                                        $id_presensi = Presensi::insertGetId([
                                            'nik' => $nik,
                                            'tanggal' => $tgl,
                                            'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                            'status' => 'i',
                                        ]);
                                    }

                                    \App\Models\Approveizinabsen::firstOrCreate([
                                        'id_presensi' => $id_presensi,
                                        'kode_izin' => $kode,
                                    ]);
                                }
                            }
                            Izinabsen::where('kode_izin', $kode)->update(['status' => 1]);
                        }
                    } else {
                        // Tolak
                        Approval::create([
                            'approvable_type' => Izinabsen::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'rejected',
                            'keterangan' => $catatan ?: ('Rejected by ' . $admin->name),
                        ]);
                        Izinabsen::where('kode_izin', $kode)->update(['status' => 2]);
                    }
                    break;

                case 'sakit':
                    $izin = Izinsakit::where('kode_izin_sakit', $kode)
                        ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
                        ->select('presensi_izinsakit.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
                        ->first();

                    if (!$izin) {
                        return response()->json(['success' => false, 'message' => 'Data izin sakit tidak ditemukan.'], 404);
                    }

                    $currentStep = $izin->approval_step;
                    if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $izin->kode_dept, $izin->kode_jabatan, $user, $izin->kode_cabang)) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang approval tahap ke-' . $currentStep], 403);
                    }

                    if ($action === 'approve') {
                        Approval::create([
                            'approvable_type' => Izinsakit::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'approved',
                            'keterangan' => $catatan ?: ('Approved by ' . $admin->name),
                        ]);

                        $nextLevel = $currentStep + 1;
                        $nextRule = $approvalService->getLayer('IZIN', $nextLevel, $izin->kode_dept, $izin->kode_jabatan, $izin->kode_cabang);
                        if ($nextRule) {
                            Izinsakit::where('kode_izin_sakit', $kode)->update(['approval_step' => $nextLevel]);
                        } else {
                            // Final approval
                            $dari = $izin->dari;
                            $sampai = $izin->sampai;
                            $nik = $izin->nik;
                            $hitung = hitungHariKerja($nik, $dari, $sampai);
                            if ($hitung['total_hari'] > 0) {
                                $general_setting = DB::table('pengaturan_umum')->first();
                                $batasi_hari_izin_sakit = $general_setting->batasi_hari_izin_sakit ?? false;
                                $jml_hari_izin_sakit_max = $general_setting->jml_hari_izin_sakit_max ?? 0;

                                $detail_hari = $hitung['detail_hari'];
                                if ($batasi_hari_izin_sakit == 1 && $jml_hari_izin_sakit_max > 0) {
                                    $detail_hari = array_slice($detail_hari, 0, $jml_hari_izin_sakit_max);
                                }

                                foreach ($detail_hari as $item) {
                                    $tgl = $item['tanggal'];
                                    $jamkerja = $item['jamkerja'];
                                    $presensi = Presensi::where('nik', $nik)->where('tanggal', $tgl)->first();
                                    if ($presensi) {
                                        $id_presensi = $presensi->id;
                                        Presensi::where('id', $id_presensi)->update([
                                            'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                            'status' => 's',
                                        ]);
                                    } else {
                                        $id_presensi = Presensi::insertGetId([
                                            'nik' => $nik,
                                            'tanggal' => $tgl,
                                            'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                            'status' => 's',
                                        ]);
                                    }

                                    \App\Models\Approveizinsakit::firstOrCreate([
                                        'id_presensi' => $id_presensi,
                                        'kode_izin_sakit' => $kode,
                                    ]);
                                }
                            }
                            Izinsakit::where('kode_izin_sakit', $kode)->update(['status' => 1]);
                        }
                    } else {
                        Approval::create([
                            'approvable_type' => Izinsakit::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'rejected',
                            'keterangan' => $catatan ?: ('Rejected by ' . $admin->name),
                        ]);
                        Izinsakit::where('kode_izin_sakit', $kode)->update(['status' => 2]);
                    }
                    break;

                case 'cuti':
                    $izin = Izincuti::where('kode_izin_cuti', $kode)
                        ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
                        ->select('presensi_izincuti.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
                        ->first();

                    if (!$izin) {
                        return response()->json(['success' => false, 'message' => 'Data izin cuti tidak ditemukan.'], 404);
                    }

                    $currentStep = $izin->approval_step;
                    if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $izin->kode_dept, $izin->kode_jabatan, $user, $izin->kode_cabang)) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang approval tahap ke-' . $currentStep], 403);
                    }

                    if ($action === 'approve') {
                        Approval::create([
                            'approvable_type' => Izincuti::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'approved',
                            'keterangan' => $catatan ?: ('Approved by ' . $admin->name),
                        ]);

                        $nextLevel = $currentStep + 1;
                        $nextRule = $approvalService->getLayer('IZIN', $nextLevel, $izin->kode_dept, $izin->kode_jabatan, $izin->kode_cabang);
                        if ($nextRule) {
                            Izincuti::where('kode_izin_cuti', $kode)->update(['approval_step' => $nextLevel]);
                        } else {
                            $dari = $izin->dari;
                            $sampai = $izin->sampai;
                            $nik = $izin->nik;

                            $hitungCuti = hitungHariCuti($nik, $dari, $sampai);
                            if ($hitungCuti['total_hari'] > 0) {
                                foreach ($hitungCuti['detail_hari'] as $item) {
                                    $tgl_cuti = $item['tanggal'];
                                    $jamkerja = $item['jamkerja'];

                                    $presensi = Presensi::where('nik', $nik)->where('tanggal', $tgl_cuti)->first();
                                    if ($presensi) {
                                        $id_presensi = $presensi->id;
                                        Presensi::where('id', $id_presensi)->update([
                                            'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                            'status' => 'c',
                                        ]);
                                    } else {
                                        $id_presensi = Presensi::insertGetId([
                                            'nik' => $nik,
                                            'tanggal' => $tgl_cuti,
                                            'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                            'status' => 'c',
                                        ]);
                                    }

                                    \App\Models\Approveizincuti::firstOrCreate([
                                        'id_presensi' => $id_presensi,
                                        'kode_izin_cuti' => $kode,
                                    ]);
                                }
                            }
                            Izincuti::where('kode_izin_cuti', $kode)->update(['status' => 1]);
                        }
                    } else {
                        Approval::create([
                            'approvable_type' => Izincuti::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'rejected',
                            'keterangan' => $catatan ?: ('Rejected by ' . $admin->name),
                        ]);
                        Izincuti::where('kode_izin_cuti', $kode)->update(['status' => 2]);
                    }
                    break;

                case 'dinas':
                    $izin = Izindinas::where('kode_izin_dinas', $kode)
                        ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
                        ->select('presensi_izindinas.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
                        ->first();

                    if (!$izin) {
                        return response()->json(['success' => false, 'message' => 'Data izin dinas tidak ditemukan.'], 404);
                    }

                    $currentStep = $izin->approval_step;
                    if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $izin->kode_dept, $izin->kode_jabatan, $user, $izin->kode_cabang)) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang approval tahap ke-' . $currentStep], 403);
                    }

                    if ($action === 'approve') {
                        Approval::create([
                            'approvable_type' => Izindinas::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'approved',
                            'keterangan' => $catatan ?: ('Approved by ' . $admin->name),
                        ]);

                        $nextLevel = $currentStep + 1;
                        $nextRule = $approvalService->getLayer('IZIN', $nextLevel, $izin->kode_dept, $izin->kode_jabatan, $izin->kode_cabang);
                        if ($nextRule) {
                            Izindinas::where('kode_izin_dinas', $kode)->update(['approval_step' => $nextLevel]);
                        } else {
                            $dari = $izin->dari;
                            $sampai = $izin->sampai;
                            $nik = $izin->nik;
                            while (strtotime($dari) <= strtotime($sampai)) {
                                $presensi = Presensi::where('nik', $nik)->where('tanggal', $dari)->first();
                                if ($presensi) {
                                    $id_presensi = $presensi->id;
                                    Presensi::where('id', $id_presensi)->update(['status' => 'd']);
                                } else {
                                    $id_presensi = Presensi::insertGetId([
                                        'nik' => $nik,
                                        'tanggal' => $dari,
                                        'status' => 'd',
                                    ]);
                                }

                                \App\Models\Approveizindinas::create([
                                    'id_presensi' => $id_presensi,
                                    'kode_izin_dinas' => $kode,
                                ]);
                                $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
                            }
                            Izindinas::where('kode_izin_dinas', $kode)->update(['status' => 1]);
                        }
                    } else {
                        Approval::create([
                            'approvable_type' => Izindinas::class,
                            'approvable_id' => $kode,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'rejected',
                            'keterangan' => $catatan ?: ('Rejected by ' . $admin->name),
                        ]);
                        Izindinas::where('kode_izin_dinas', $kode)->update(['status' => 2]);
                    }
                    break;

                case 'reimbursement':
                    $reimburse = Reimbursement::where('no_reimbursement', $kode)
                        ->join('karyawan', 'reimbursement.nik', '=', 'karyawan.nik')
                        ->select('reimbursement.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
                        ->first();

                    if (!$reimburse) {
                        return response()->json(['success' => false, 'message' => 'Data reimbursement tidak ditemukan.'], 404);
                    }

                    $currentStep = $reimburse->approval_step;
                    if (!$approvalService->canApprove('REIMBURSEMENT', $currentStep, $userRole, $reimburse->kode_dept, $reimburse->kode_jabatan, $user, $reimburse->kode_cabang)) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang approval tahap ke-' . $currentStep], 403);
                    }

                    if ($action === 'approve') {
                        Approval::create([
                            'approvable_type' => Reimbursement::class,
                            'approvable_id' => $reimburse->id,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'approved',
                            'keterangan' => $catatan ?: ('Approved by ' . $admin->name),
                        ]);

                        $nextLevel = $currentStep + 1;
                        $nextRule = $approvalService->getLayer('REIMBURSEMENT', $nextLevel, $reimburse->kode_dept, $reimburse->kode_jabatan, $reimburse->kode_cabang);
                        if ($nextRule) {
                            Reimbursement::where('id', $reimburse->id)->update(['approval_step' => $nextLevel]);
                        } else {
                            Reimbursement::where('id', $reimburse->id)->update(['status' => 'A']);
                        }
                    } else {
                        Approval::create([
                            'approvable_type' => Reimbursement::class,
                            'approvable_id' => $reimburse->id,
                            'user_id' => $approvalUserId,
                            'level' => $currentStep,
                            'status' => 'rejected',
                            'keterangan' => $catatan ?: ('Rejected by ' . $admin->name),
                        ]);
                        Reimbursement::where('id', $reimburse->id)->update(['status' => 'D']);
                    }
                    break;

                default:
                    return response()->json(['success' => false, 'message' => 'Tipe pengajuan tidak valid.'], 400);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil di-' . ($action === 'approve' ? 'setujui' : 'tolak') . '.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to get delegated admin user.
     */
    private function getDelegationAdmin($user)
    {
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan || !$userkaryawan->approval_admin_id) {
            return null;
        }
        return User::find($userkaryawan->approval_admin_id);
    }
}
