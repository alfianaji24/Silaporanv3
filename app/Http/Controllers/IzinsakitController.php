<?php

namespace App\Http\Controllers;

use App\Models\Approveizinsakit;
use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Detailsetjamkerjabydept;
use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izinsakit;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\Setjamkerjabydate;
use App\Models\Setjamkerjabyday;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Services\ApprovalService;
use App\Models\Approval;
use App\Models\ApprovalLayer;

class IzinsakitController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $qizin = Izinsakit::query();
        $qizin->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
        $qizin->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qizin->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qizin->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            if ($user->hasRole('karyawan')) {
                $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
                $qizin->where('presensi_izinsakit.nik', $userkaryawan ? $userkaryawan->nik : '');
            } else {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!empty($userCabangs)) {
                    $qizin->whereIn('karyawan.kode_cabang', $userCabangs);
                } else {
                    $qizin->whereRaw('1 = 0');
                }
                
                if (!empty($userDepartemens)) {
                    $qizin->whereIn('karyawan.kode_dept', $userDepartemens);
                } else {
                    $qizin->whereRaw('1 = 0');
                }
            }
        }

        $qizin->select(
            'presensi_izinsakit.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'karyawan.foto',
            'jabatan.nama_jabatan',
            'departemen.nama_dept',
            'cabang.nama_cabang',
            'karyawan.kode_dept'
        );
        if (!empty($request->dari) && !empty($request->sampai)) {
            $qizin->whereBetween('presensi_izinsakit.tanggal', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nama_karyawan)) {
            $qizin->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_cabang)) {
            $qizin->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $qizin->where('karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->status) || $request->status === '0') {
            $qizin->where('presensi_izinsakit.status', $request->status);
        }

        $qizin->orderBy('presensi_izinsakit.status');
        $qizin->orderBy('presensi_izinsakit.tanggal', 'desc');
        $izinsakit = $qizin->paginate(15);
        $izinsakit->appends($request->all());

        // Hitung statistik status pengajuan secara efisien (1 query group)
        $statsBase = Izinsakit::query()
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
        if (!$user->isSuperAdmin()) {
            if ($user->hasRole('karyawan')) {
                $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
                $statsBase->where('presensi_izinsakit.nik', $userkaryawan ? $userkaryawan->nik : '');
            } else {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                if (!empty($userCabangs)) {
                    $statsBase->whereIn('karyawan.kode_cabang', $userCabangs);
                } else {
                    $statsBase->whereRaw('1 = 0');
                }
                if (!empty($userDepartemens)) {
                    $statsBase->whereIn('karyawan.kode_dept', $userDepartemens);
                } else {
                    $statsBase->whereRaw('1 = 0');
                }
            }
        }
        $statusCounts = $statsBase->select('presensi_izinsakit.status', DB::raw('count(*) as total'))
            ->groupBy('presensi_izinsakit.status')
            ->pluck('total', 'status')
            ->toArray();

        $stats = [
            'pending' => $statusCounts[0] ?? 0,
            'disetujui' => $statusCounts[1] ?? 0,
            'ditolak' => $statusCounts[2] ?? 0,
        ];

        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['izinsakit'] = $izinsakit;
        $data['stats'] = $stats;

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.izinsakit.index', $data);
        }

        return view('izinsakit.index', $data);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $qkaryawan = Karyawan::query();
        $qkaryawan->select('karyawan.nik', 'karyawan.nama_karyawan', 'karyawan.nik_show');
        
        // Filter karyawan berdasarkan akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qkaryawan->whereIn('kode_cabang', $userCabangs);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qkaryawan->whereIn('kode_dept', $userDepartemens);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
        }
        
        $karyawan = $qkaryawan->get();

        $data['karyawan'] = $karyawan;

        if ($user->hasRole('karyawan')) {
            return view('izinsakit.create-mobile', $data);
        }

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.izinsakit.create', $data);
        }

        return view('izinsakit.create', $data);
    }

    public function edit($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $izinsakit->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }
        
        $qkaryawan = Karyawan::query();
        $qkaryawan->select('karyawan.nik', 'karyawan.nama_karyawan');
        
        // Filter karyawan berdasarkan akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qkaryawan->whereIn('kode_cabang', $userCabangs);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qkaryawan->whereIn('kode_dept', $userDepartemens);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
        }
        
        $karyawan = $qkaryawan->get();
        $data['karyawan'] = $karyawan;
        $data['izinsakit'] = $izinsakit;

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.izinsakit.edit', $data);
        }

        return view('izinsakit.edit', $data);
    }

    public function store(Request $request)
    {
        $user = User::findorfail(auth()->user()->id);
        $role = $user->getRoleNames()->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $nik = $user->hasRole('karyawan') ? $userkaryawan->nik : $request->nik;
        if ($role == 'karyawan') {
            $request->validate([
                'dari' => 'required',
                'sampai' => 'required',
                'keterangan' => 'required',
                'sid' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ]);
        } else {
            $request->validate([
                'nik' => 'required',
                'dari' => 'required',
                'sampai' => 'required',
                'keterangan' => 'required',
                'sid' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ]);
        }

        DB::beginTransaction();
        try {
            $hitung = hitungHariKerja($nik, $request->dari, $request->sampai);
            $jmlhari = $hitung['total_hari'];
            if ($jmlhari <= 0) {
                return Redirect::back()->with(messageError('Rentang tanggal yang dipilih tidak memiliki hari kerja aktif (seluruhnya hari libur / off)!'));
            }

            $general_setting = Pengaturanumum::where('id', 1)->first();
            $batasi_hari_izin_sakit = $general_setting->batasi_hari_izin_sakit ?? false;
            $jml_hari_izin_sakit_max = $general_setting->jml_hari_izin_sakit_max ?? 0;

            if ($batasi_hari_izin_sakit == 1 && $jml_hari_izin_sakit_max > 0 && $jmlhari > $jml_hari_izin_sakit_max) {
                return Redirect::back()->with(messageError('Tidak Boleh Lebih dari ' . $jml_hari_izin_sakit_max . ' Hari Kerja!'));
            }

            $cek_izin_absen = Izinabsen::where('nik', $nik)
                ->where(function($query) use ($request) {
                    $query->whereBetween('dari', [$request->dari, $request->sampai])
                          ->orWhereBetween('sampai', [$request->dari, $request->sampai]);
                })->first();

            $cek_izin_sakit = Izinsakit::where('nik', $nik)
                ->where(function($query) use ($request) {
                    $query->whereBetween('dari', [$request->dari, $request->sampai])
                          ->orWhereBetween('sampai', [$request->dari, $request->sampai]);
                })->first();

            $cek_izin_cuti = Izincuti::where('nik', $nik)
                ->where(function($query) use ($request) {
                    $query->whereBetween('dari', [$request->dari, $request->sampai])
                          ->orWhereBetween('sampai', [$request->dari, $request->sampai]);
                })->first();

            if ($cek_izin_absen) {
                return Redirect::back()->with(messageError('Anda Sudah Mengajukan Izin Absen/Sakit/Cuti Pada Rentang Tanggal Tersebut!'));
            } else if ($cek_izin_sakit) {
                return Redirect::back()->with(messageError('Anda Sudah Mengajukan Izin Absen/Sakit/Cuti Absen Pada Rentang Tanggal Tersebut!'));
            } else if ($cek_izin_cuti) {
                return Redirect::back()->with(messageError('Anda Sudah Mengajukan Izin Absen/Sakit/Cuti Absen Pada Rentang Tanggal Tersebut!'));
            }
            $lastizinsakit = Izinsakit::select('kode_izin_sakit')
                ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($request->dari)) . '"')
                ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($request->dari)) . '"')
                ->orderBy("kode_izin_sakit", "desc")
                ->first();
            $last_kode_izin_sakit = $lastizinsakit != null ? $lastizinsakit->kode_izin_sakit : '';
            $kode_izin_sakit  = buatkode($last_kode_izin_sakit, "IS"  . date('ym', strtotime($request->dari)), 4);


            $data_sid = [];
            if ($request->hasfile('sid')) {
                $sid_name =  $kode_izin_sakit . ".jpg";
                $sid = $sid_name;
                $data_sid = [
                    'doc_sid' => $sid,
                ];
            }

            $dataizinsakit = [
                'kode_izin_sakit' => $kode_izin_sakit,
                'nik' => $nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'status' => 0,
                'approval_step' => 1,
                'id_user' => $user->id,
            ];

            $data = array_merge($dataizinsakit, $data_sid);
            $simpandatasakit = Izinsakit::create($data);
            if ($simpandatasakit) {
                if ($request->hasfile('sid')) {
                    $destination_sid_path = "/public/uploads/sid";
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($request->file('sid'));
                    $encodedImage = (string) $image->toJpeg(75);
                    \Illuminate\Support\Facades\Storage::put($destination_sid_path . "/" . $sid_name, $encodedImage);
                }
            }
            DB::commit();
            if ($role == 'karyawan') {
                return Redirect::route('pengajuanizin.index')->with(messageSuccess('Data Berhasil Disimpan'));
            } else {
                return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function approve($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinabsen = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        $data['izinsakit'] = $izinabsen;

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.izinsakit.approve', $data);
        }

        return view('izinsakit.approve', $data);
    }

    public function storeapprove(Request $request, $kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->select('presensi_izinsakit.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Untuk delegasi, gunakan cabang/dept admin
            $accessUser = $user->getApprovalAdmin() ?? $user;
            $userCabangs = $accessUser->getCabangCodes();
            $userDepartemens = $accessUser->getDepartemenCodes();
            
            if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        // Dynamic Approval Logic
        $approvalService = app(ApprovalService::class);
        $userRole = $user->getRoleNames()->first();
        $currentStep = $izinsakit->approval_step;
        $approvalUserId = $approvalService->getApprovalUserId($user);
        $approvalAdmin = $approvalUserId != $user->id ? User::find($approvalUserId) : $user;

        // Check Authorization using Service
        $kode_cabang = $izinsakit->kode_cabang;
        if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $izinsakit->kode_dept, $izinsakit->kode_jabatan, $user, $kode_cabang)) {
             if (!$user->isSuperAdmin()) {
                 return Redirect::back()->with(messageError('Anda tidak memiliki wewenang untuk approval tahap ke-' . $currentStep));
             }
        }
        
        $dari = $izinsakit->dari;
        $sampai = $izinsakit->sampai;
        $nik = $izinsakit->nik;
        $kode_dept = $izinsakit->kode_dept;
        $kode_jabatan = $izinsakit->kode_jabatan;
        $error = '';
        DB::beginTransaction();
        try {
            if (isset($request->approve)) {
                
                // 1. Record Approval (atas nama admin jika delegasi)
                Approval::create([
                    'approvable_type' => Izinsakit::class,
                    'approvable_id' => $kode_izin_sakit,
                    'user_id' => $approvalUserId,
                    'level' => $currentStep,
                    'status' => 'approved',
                    'keterangan' => 'Approved by ' . $approvalAdmin->name,
                ]);

                // 2. Check for Next Level rule
                $nextLevel = $currentStep + 1;
                $nextRule = $approvalService->getLayer('IZIN', $nextLevel, $kode_dept, $kode_jabatan, $kode_cabang);
                
                 if ($nextRule && !$user->hasRole('super admin')) {
                    // Move to next step
                     Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'approval_step' => $nextLevel
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Berhasil disetujui (Tahap ' . $currentStep . '). Menunggu approval tahap selanjutnya.'));
                }

                // If No Next Rule -> FINAL APPROVAL
                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                    'status' => 1
                ]);

                $hitung = hitungHariKerja($nik, $dari, $sampai);
                if ($hitung['total_hari'] <= 0) {
                    $error .= 'Rentang tanggal izin sakit tidak memiliki hari kerja aktif untuk diproses! <br>';
                } else {
                    $general_setting = Pengaturanumum::where('id', 1)->first();
                    $batasi_hari_izin_sakit = $general_setting->batasi_hari_izin_sakit ?? false;
                    $jml_hari_izin_sakit_max = $general_setting->jml_hari_izin_sakit_max ?? 0;

                    $detail_hari = $hitung['detail_hari'];
                    if ($batasi_hari_izin_sakit == 1 && $jml_hari_izin_sakit_max > 0) {
                        $detail_hari = array_slice($detail_hari, 0, $jml_hari_izin_sakit_max);
                    }

                    foreach ($detail_hari as $item) {
                        $tgl_sakit = $item['tanggal'];
                        $jamkerja = $item['jamkerja'];

                        $existingPresensi = Presensi::where('nik', $nik)->where('tanggal', $tgl_sakit)->first();
                        if ($existingPresensi) {
                            $existingPresensi->update([
                                'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                'status' => 's',
                            ]);
                            $id_presensi = $existingPresensi->id;
                        } else {
                            $presensi = Presensi::create([
                                'nik' => $nik,
                                'tanggal' => $tgl_sakit,
                                'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                                'status' => 's',
                            ]);
                            $id_presensi = $presensi->id;
                        }

                        Approveizinsakit::firstOrCreate([
                            'id_presensi' => $id_presensi,
                            'kode_izin_sakit' => $kode_izin_sakit,
                        ]);
                    }
                }
            } else {
                 // REJECTION
                 Approval::create([
                    'approvable_type' => Izinsakit::class,
                    'approvable_id' => $kode_izin_sakit,
                    'user_id' => $approvalUserId,
                    'level' => $currentStep,
                    'status' => 'rejected',
                    'keterangan' => 'Rejected by ' . $approvalAdmin->name,
                ]);

                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                    'status' => 2
                ]);
            }
            if (!empty($error)) {
                DB::rollBack();
                return Redirect::back()->with(messageError($error));
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function cancelapprove($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->select('presensi_izinsakit.*', 'karyawan.kode_cabang', 'karyawan.kode_dept')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }
        
        DB::beginTransaction();
        try {
            // Case 1: Status is Pending (0) but moved steps (Intermediate Cancellation)
            if ($izinsakit->status == 0) {
                 // Logic: Find the approval for the *previous* step (current_step - 1)
                 // NOTE: Since approval_step points to the *requirement* (waiting for X), 
                 // the *last done* approval was at level (approval_step - 1).
                 $lastStep = $izinsakit->approval_step - 1;
                 
                $lastApproval = Approval::where('approvable_type', Izinsakit::class)
                    ->where('approvable_id', $kode_izin_sakit)
                    ->where('level', $lastStep)
                    ->where('user_id', $user->id) // Must be the one who approved it
                    ->first();

                if ($lastApproval) {
                    $lastApproval->delete();
                    Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'approval_step' => $lastStep
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Approval dibatalkan. Kembali ke tahap sebelumnya.'));
                } else {
                     return Redirect::back()->with(messageError('Anda tidak dapat membatalkan approval ini (Bukan approver terakhir atau sudah diproses lanjut).'));
                }
            } 
            // Case 2: Status is Final Approved (1)
            else if ($izinsakit->status == 1) {
                // This is the "Final Cancellation" - undo the final approval
                // Current code logic deletes presensi and resets status to 0. 
                // We should also delete the Final Approval record.
                
                // Find final approval record (highest level)
                $lastApproval = Approval::where('approvable_type', Izinsakit::class)
                    ->where('approvable_id', $kode_izin_sakit)
                    ->where('user_id', $user->id)
                    ->orderBy('level', 'desc')
                    ->first();

                if($lastApproval){
                     // Revert step to this level (so it becomes pending at this level again)
                     $revertStep = $lastApproval->level;
                     $lastApproval->delete();
                     
                     // Delete Presensi Data
                     $presensi = Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->get();
                     Presensi::whereIn('id', $presensi->pluck('id_presensi'))->delete();
                     Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();

                     Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'status' => 0,
                        'approval_step' => $revertStep
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Approval final dibatalkan. Kembali ke tahap sebelumnya.'));

                } else {
                    // Fallback for Super Admin or legacy cleanup 
                    $presensi = Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->get();
                    Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'status' => 0
                        // approval_step stays same?? or reset? 
                        // Safer to not touch approval_step if we don't know, but ideally we should knows.
                        // Im assuming legacy manual reset sets status 0.
                    ]);
                    Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();
                    Presensi::whereIn('id', $presensi->pluck('id_presensi'))->delete();
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
                }
            }
            // Case 3: Status is Rejected (2)
            else if ($izinsakit->status == 2) {
                 // Find the rejection approval record
                 $lastApproval = Approval::where('approvable_type', Izinsakit::class)
                    ->where('approvable_id', $kode_izin_sakit)
                    ->where('user_id', $user->id)
                    ->orderBy('level', 'desc')
                    ->first();

                 if($lastApproval){
                      // Revert step to this level
                      $revertStep = $lastApproval->level;
                      $lastApproval->delete();
                      
                      Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                         'status' => 0,
                         'approval_step' => $revertStep
                     ]);
                     DB::commit();
                     return Redirect::back()->with(messageSuccess('Penolakan dibatalkan. Kembali ke tahap sebelumnya.'));
                 } else {
                     // Fallback
                     Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                         'status' => 0
                     ]);
                     DB::commit();
                     return Redirect::back()->with(messageSuccess('Penolakan Berhasil Dibatalkan'));
                 }
            }
            
            return Redirect::back()->with(messageError('Status tidak valid untuk pembatalan.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function update(Request $request, $kode_izin_sakit)
    {
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);

        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
            'sid' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);
        DB::beginTransaction();
        try {
            $hitung = hitungHariKerja($request->nik, $request->dari, $request->sampai);
            $jmlhari = $hitung['total_hari'];
            if ($jmlhari <= 0) {
                return Redirect::back()->with(messageError('Rentang tanggal yang dipilih tidak memiliki hari kerja aktif (seluruhnya hari libur / off)!'));
            }

            $general_setting = Pengaturanumum::where('id', 1)->first();
            $batasi_hari_izin_sakit = $general_setting->batasi_hari_izin_sakit ?? false;
            $jml_hari_izin_sakit_max = $general_setting->jml_hari_izin_sakit_max ?? 0;

            if ($batasi_hari_izin_sakit == 1 && $jml_hari_izin_sakit_max > 0 && $jmlhari > $jml_hari_izin_sakit_max) {
                return Redirect::back()->with(messageError('Tidak Boleh Lebih dari ' . $jml_hari_izin_sakit_max . ' Hari Kerja!'));
            }

            $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->first();
            $data_sid = [];
            if ($request->hasfile('sid')) {
                $sid_name =  $kode_izin_sakit . ".jpg";
                $sid = $sid_name;
                $data_sid = [
                    'doc_sid' => $sid,
                ];
            }

            $dataizinsakit = [
                'nik' => $request->nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,

            ];

            $data = array_merge($dataizinsakit, $data_sid);

            $simpandatasakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update($data);
            if ($simpandatasakit) {
                if ($request->hasfile('sid')) {
                    $destination_sid_path = "/public/uploads/sid";
                    Storage::delete($destination_sid_path . "/" . $izinsakit->doc_sid);
                    
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($request->file('sid'));
                    $encodedImage = (string) $image->toJpeg(75);
                    \Illuminate\Support\Facades\Storage::put($destination_sid_path . "/" . $sid_name, $encodedImage);
                }
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function destroy($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Cek apakah user adalah pemilik izin (untuk karyawan)
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $isOwner = $userkaryawan && $userkaryawan->nik == $izinsakit->nik;
            
            // Jika bukan pemilik, cek akses cabang/dept
            if (!$isOwner) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
                }
            }
        }
        
        try {
            $doc_sid = $izinsakit->doc_sid;
            Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();
            if ($doc_sid) {
                $destination_sid_path = "/public/uploads/sid";
                if (Storage::exists($destination_sid_path . "/" . $doc_sid)) {
                    Storage::delete($destination_sid_path . "/" . $doc_sid);
                }
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function show($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinabsen = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        $data['izinsakit'] = $izinabsen;

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.izinsakit.show', $data);
        }

        return view('izinsakit.show', $data);
    }
}
