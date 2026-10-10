<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Facerecognition;
use App\Models\Karyawan;
use App\Models\KonfigurasiDokumen;
use App\Models\Lembur;
use App\Models\Pengaturanumum;
use App\Models\User;
use App\Models\Userkaryawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $qlembur = Lembur::query();
        $qlembur->join('karyawan', 'lembur.nik', '=', 'karyawan.nik');
        $qlembur->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qlembur->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qlembur->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin dan bukan karyawan
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $qlembur->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $qlembur->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $qlembur->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $qlembur->whereRaw('1 = 0');
            }
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $qlembur->whereBetween('lembur.tanggal', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nama_karyawan)) {
            $qlembur->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        if (!empty($request->kode_cabang)) {
            $qlembur->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $qlembur->where('karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->status) || $request->status === '0') {
            $qlembur->where('lembur.status', $request->status);
        }

        if ($user->hasRole('karyawan')) {
            $qlembur->where('lembur.nik', $userkaryawan->nik);
        }

        $qlembur->orderBy('lembur.status');
        $qlembur->orderBy('lembur.tanggal', 'desc');
        $lembur = $qlembur->paginate(15);
        $lembur->appends($request->all());
        $data['lembur'] = $lembur;

        // Executive Metrics / Summary Statistics
        $qStats = Lembur::query()->join('karyawan', 'lembur.nik', '=', 'karyawan.nik');
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!empty($userCabangs)) {
                $qStats->whereIn('karyawan.kode_cabang', $userCabangs);
            }
            if (!empty($userDepartemens)) {
                $qStats->whereIn('karyawan.kode_dept', $userDepartemens);
            }
        }
        if ($user->hasRole('karyawan')) {
            $qStats->where('lembur.nik', $userkaryawan->nik);
        }
        $statsData = $qStats->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN lembur.status = 0 THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN lembur.status = 1 THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN lembur.status = 2 THEN 1 ELSE 0 END) as rejected
        ")->first();

        $data['stats'] = [
            'total' => $statsData->total ?? 0,
            'pending' => $statsData->pending ?? 0,
            'approved' => $statsData->approved ?? 0,
            'rejected' => $statsData->rejected ?? 0,
        ];

        if ($user->hasRole('karyawan')) {
            $data['cabang'] = collect();
            $data['departemen'] = collect();
        } else {
            $data['cabang'] = $user->getCabang();
            $data['departemen'] = $user->getDepartemen();
        }

        $viewPrefix = session('admin_theme', 'v2') === 'v2' ? 'v2.' : '';

        if ($user->hasRole('karyawan')) {
            return view($viewPrefix . 'lembur.index-karyawan', $data);
        } else {
            return view($viewPrefix . 'lembur.index', $data);
        }
    }


    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

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

        $viewPrefix = session('admin_theme', 'v2') === 'v2' ? 'v2.' : '';

        if ($user->hasRole('karyawan')) {
            return view($viewPrefix . 'lembur.create-karyawan', $data);
        } else {
            return view($viewPrefix . 'lembur.create', $data);
        }
    }


    public function store(Request $request)
    {
        $user = User::where('id', '=', auth()->user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$user->hasRole('karyawan')) {
            $nik = $request->nik;
            $request->validate([
                'nik' => 'required',
                'dari' => 'required',
                'sampai' => 'required',
                'keterangan' => 'required',
            ]);
        } else {
            $nik = $userkaryawan->nik;
            $request->validate([
                'dari' => 'required',
                'sampai' => 'required',
                'keterangan' => 'required',
            ]);
        }


        try {
            Lembur::create([
                'nik' => $nik,
                'tanggal' => date('Y-m-d', strtotime($request->dari)),
                'lembur_mulai' => $request->dari,
                'lembur_selesai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'status' => 0,
            ]);
            if ($user->hasRole('karyawan')) {
                return Redirect::route('lembur.index')->with('success', 'Data Lembur berhasil disimpan');
            } else {
                return Redirect::back()->with('success', 'Data Lembur berhasil disimpan');
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Data Lembur gagal disimpan' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('id', $id)
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->first();

        // Cek akses jika bukan super admin dan bukan karyawan
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $lembur->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data lembur ini.');
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
        $data['lembur'] = $lembur;
        $viewPrefix = session('admin_theme', 'v2') === 'v2' ? 'v2.' : '';
        return view($viewPrefix . 'lembur.edit', $data);
    }


    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
        ]);

        try {
            Lembur::where('id', $id)->update([
                'nik' => $request->nik,
                'tanggal' => date('Y-m-d', strtotime($request->dari)),
                'lembur_mulai' => $request->dari,
                'lembur_selesai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'lembur_in' => $request->lembur_in,
                'lembur_out' => $request->lembur_out,
                'status' => $request->lembur_in && $request->lembur_out ? 1 : 0,
            ]);
            return Redirect::back()->with('success', 'Data Lembur berhasil disimpan');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Data Lembur gagal disimpan' . $e->getMessage());
        }
    }


    public function approve($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('id', $id)
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        // Cek akses jika bukan super admin dan bukan karyawan
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($lembur->kode_cabang, $userCabangs) || !in_array($lembur->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data lembur ini.');
            }
        }

        $data['lembur'] = $lembur;
        $viewPrefix = session('admin_theme', 'v2') === 'v2' ? 'v2.' : '';
        return view($viewPrefix . 'lembur.approve', $data);
    }


    public function storeapprove(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('id', $id)
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->first();

        // Cek akses jika bukan super admin dan bukan karyawan
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($lembur->kode_cabang, $userCabangs) || !in_array($lembur->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data lembur ini.');
            }
        }

        try {
            if (isset($request->approve)) {
                Lembur::where('id', $id)->update([
                    'status' => 1,
                ]);
                return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
            } else {
                Lembur::where('id', $id)->update([
                    'status' => 2,
                ]);
                return Redirect::back()->with(messageSuccess('Data Ajuan Lembur Ditolak'));
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function cancelapprove($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('id', $id)
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->first();

        // Cek akses jika bukan super admin dan bukan karyawan
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($lembur->kode_cabang, $userCabangs) || !in_array($lembur->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data lembur ini.');
            }
        }

        try {
            Lembur::where('id', $id)->update([
                'status' => 0
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Dibatalkan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('id', $id)
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        // Cek akses jika bukan super admin dan bukan karyawan
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($lembur->kode_cabang, $userCabangs) || !in_array($lembur->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data lembur ini.');
            }
        }

        $data['lembur'] = $lembur;
        $viewPrefix = session('admin_theme', 'v2') === 'v2' ? 'v2.' : '';
        return view($viewPrefix . 'lembur.show', $data);
    }

    public function template(Request $request)
    {
        $template = KonfigurasiDokumen::where('kode_dokumen', 'SPL')->first();
        if (!$template) {
            $konten = view('lembur.default_template')->render();
            $template = KonfigurasiDokumen::create([
                'kode_dokumen' => 'SPL',
                'nama_dokumen' => 'Surat Perintah Kerja Lembur',
                'konten' => $konten
            ]);
        }
        $viewPrefix = session('admin_theme', 'v2') === 'v2' ? 'v2.' : '';
        return view($viewPrefix . 'lembur.template', compact('template'));
    }

    public function updateTemplate(Request $request)
    {
        // Handle Reset
        if ($request->has('reset') && $request->reset == 'true') {
            $konten = view('lembur.default_template')->render();
            KonfigurasiDokumen::updateOrCreate(
                ['kode_dokumen' => 'SPL'],
                [
                    'nama_dokumen' => 'Surat Perintah Kerja Lembur',
                    'konten' => $konten
                ]
            );
            return Redirect::back()->with(messageSuccess('Template Surat Perintah Lembur berhasil direset ke default.'));
        }

        $request->validate([
            'konten' => 'required'
        ]);

        KonfigurasiDokumen::updateOrCreate(
            ['kode_dokumen' => 'SPL'],
            [
                'nama_dokumen' => 'Surat Perintah Kerja Lembur',
                'konten' => $request->konten
            ]
        );

        return Redirect::back()->with(messageSuccess('Template Surat Perintah Lembur berhasil diperbarui.'));
    }

    protected function prepareOvertimeContent($lembur, $setting, $no_spl)
    {
        $template = KonfigurasiDokumen::where('kode_dokumen', 'SPL')->first();
        if (!$template) {
            $konten = view('lembur.default_template')->render();
        } else {
            $konten = $template->konten;
        }

        $daftar_hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $nama_hari = $daftar_hari[date('l', strtotime($lembur->tanggal))] ?? '';
        $hari_ini = $daftar_hari[date('l')] ?? '';

        // Durasi Rencana
        $start_est = strtotime($lembur->lembur_mulai);
        $end_est = strtotime($lembur->lembur_selesai);
        $diff_est = $end_est - $start_est;
        $est_hours = floor($diff_est / 3600);
        $est_minutes = floor(($diff_est % 3600) / 60);
        $durasi_rencana = $est_hours . " Jam" . ($est_minutes > 0 ? " " . $est_minutes . " Menit" : "");

        // Realisasi
        $durasi_aktual = '-';
        if (!empty($lembur->lembur_in) && !empty($lembur->lembur_out)) {
            $start_act = strtotime($lembur->lembur_in);
            $end_act = strtotime($lembur->lembur_out);
            $diff_act = $end_act - $start_act;
            $act_hours = floor($diff_act / 3600);
            $act_minutes = floor(($diff_act % 3600) / 60);
            $durasi_aktual = $act_hours . " Jam" . ($act_minutes > 0 ? " " . $act_minutes . " Menit" : "");
        }

        $status_text = 'Menunggu Persetujuan (Pending)';
        if ($lembur->status == 1) {
            $status_text = 'Disetujui (Approved)';
        } elseif ($lembur->status == 2) {
            $status_text = 'Ditolak (Rejected)';
        }

        $placeholders = [
            '{{no_spl}}' => $no_spl,
            '{{nama_karyawan}}' => $lembur->nama_karyawan,
            '{{nik}}' => $lembur->nik_show ?? $lembur->nik,
            '{{jabatan}}' => $lembur->nama_jabatan,
            '{{departemen}}' => $lembur->nama_dept,
            '{{cabang}}' => $lembur->nama_cabang,
            '{{hari}}' => $nama_hari,
            '{{tanggal_lembur}}' => DateToIndo($lembur->tanggal),
            '{{jam_mulai}}' => date('H:i', strtotime($lembur->lembur_mulai)),
            '{{jam_selesai}}' => date('H:i', strtotime($lembur->lembur_selesai)),
            '{{estimasi_durasi}}' => $durasi_rencana,
            '{{keterangan_lembur}}' => $lembur->keterangan,
            '{{realisasi_jam_mulai}}' => $lembur->lembur_in ? date('d-m-Y H:i', strtotime($lembur->lembur_in)) : '-',
            '{{realisasi_jam_selesai}}' => $lembur->lembur_out ? date('d-m-Y H:i', strtotime($lembur->lembur_out)) : '-',
            '{{realisasi_durasi}}' => $durasi_aktual,
            '{{status_approval}}' => $status_text,
            '{{nama_perusahaan}}' => $setting->nama_perusahaan ?? $setting->nama_aplikasi ?? 'Perusahaan',
            '{{alamat_perusahaan}}' => $lembur->alamat_cabang ?? $setting->alamat ?? '',
            '{{nama_hrd}}' => !empty($setting->nama_hrd) ? $setting->nama_hrd : '( ............................................ )',
            '{{tanggal_hari_ini}}' => DateToIndo(date('Y-m-d')),
            '{{hari_ini}}' => $hari_ini,
        ];

        foreach ($placeholders as $key => $value) {
            $konten = str_ireplace($key, $value, $konten);
        }

        return $konten;
    }

    public function print($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('lembur.id', $id)
            ->select(
                'lembur.*',
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.alamat',
                'karyawan.no_hp',
                'jabatan.nama_jabatan',
                'departemen.nama_dept',
                'cabang.nama_cabang',
                'cabang.alamat_cabang',
                'cabang.telepon_cabang'
            )
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        if (!$lembur) {
            abort(404, 'Data lembur tidak ditemukan.');
        }

        // Cek akses jika karyawan (hanya bisa cetak miliknya sendiri)
        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userkaryawan || $userkaryawan->nik !== $lembur->nik) {
                abort(403, 'Anda tidak memiliki akses untuk mencetak data lembur ini.');
            }
        } elseif (!$user->isSuperAdmin()) {
            // Cek akses cabang & departemen jika admin cabang/dept
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            $karyawanData = Karyawan::where('nik', $lembur->nik)->first();
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data lembur ini.');
            }
        }

        $setting = Pengaturanumum::first();

        // Nomor Surat Perintah Lembur: SPL/YYYY/MM/0000X
        $tahun = date('Y', strtotime($lembur->tanggal));
        $bulan = date('m', strtotime($lembur->tanggal));
        $no_spl = 'SPL/' . $tahun . '/' . $bulan . '/' . str_pad($lembur->id, 5, '0', STR_PAD_LEFT);

        $konten = $this->prepareOvertimeContent($lembur, $setting, $no_spl);

        $pdf = Pdf::loadView('lembur.print_dynamic', [
            'konten' => $konten,
            'lembur' => $lembur,
            'setting' => $setting
        ])->setPaper('A4', 'portrait');

        $filename = 'SPL_' . ($lembur->nik_show ?? $lembur->nik) . '_' . date('Ymd', strtotime($lembur->tanggal)) . '.pdf';

        return $pdf->stream($filename);
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('id', $id)
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->first();

        // Cek akses jika bukan super admin dan bukan karyawan
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($lembur->kode_cabang, $userCabangs) || !in_array($lembur->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data lembur ini.');
            }
        }

        try {
            Lembur::where('id', $id)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function createpresensi($id)
    {
        $id = Crypt::decrypt($id);
        $lembur = Lembur::where('id', $id)
            ->select(
                'lembur.*',
                'karyawan.nama_karyawan',
                'cabang.nama_cabang',
                'cabang.radius_cabang',
                'cabang.lokasi_cabang'
            )
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        $data['wajah'] = Facerecognition::where('nik', $lembur->nik)->count();
        $data['lembur'] = $lembur;
        return view('lembur.creatSILAPORAN', $data);
    }

    public function storepresensi(Request $request)
    {
        $id_lembur = $request->id_lembur;
        $lembur = Lembur::where('id', $id_lembur)
            ->select(
                'lembur.*',
                'karyawan.nama_karyawan',
                'karyawan.lock_location',
                'cabang.nama_cabang',
                'cabang.radius_cabang',
                'cabang.lokasi_cabang'
            )
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        $status_lock_location = $lembur->lock_location;
        $status = $request->status;
        $lokasi = $request->lokasi;

        $tanggal_sekarang = date("Y-m-d");
        $jam_sekarang = date("H:i");

        //Get Lokasi User
        $koordinat_user = explode(",", $lokasi);
        $latitude_user = $koordinat_user[0];
        $longitude_user = $koordinat_user[1];

        //Get Lokasi Kantor
        $lokasi_kantor = $lembur->lokasi_cabang;

        $koordinat_kantor = explode(",", $lokasi_kantor);
        $latitude_kantor = $koordinat_kantor[0];
        $longitude_kantor = $koordinat_kantor[1];

        $jarak = hitungjarak($latitude_kantor, $longitude_kantor, $latitude_user, $longitude_user);
        $radius = round($jarak["meters"]);


        $in_out = $status == 1 ? "in" : "out";
        $image = $request->image;
        $folderPath = "public/uploads/lembur/";
        $formatName = $lembur->nik . "-" . $tanggal_sekarang . "-" . $in_out;
        $formatName = $lembur->nik . "-" . $tanggal_sekarang . "-" . $in_out;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = $formatName . ".png";
            $file = $folderPath . $fileName;
            Storage::put($file, file_get_contents($image));
        } else {
            $image_parts = explode(";base64", $image);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $formatName . ".png";
            $file = $folderPath . $fileName;
            Storage::put($file, $image_base64);
        }

        $tanggal_presensi = $tanggal_sekarang;
        $jam_presensi = $tanggal_presensi . " " . $jam_sekarang;
        $batas_jam_absen = 30;


        $mulai_lembur = $lembur->lembur_mulai;
        //Jam Mulai Absen adalah 60 Menit Sebelum Jam Masuk
        $jam_mulai_masuk = date('Y-m-d H:i', strtotime('-' . $batas_jam_absen . ' minutes', strtotime($mulai_lembur)));

        //Jamulai Absen Pulang adalah 1 Jam dari Jam Masuk
        $jam_mulai_pulang =  date('Y-m-d H:i', strtotime('+' . $batas_jam_absen . ' minutes', strtotime($mulai_lembur)));


        //return $jam_mulai_pulang;
        $jam_pulang = $lembur->lembur_selesai;

        if ($status_lock_location == 1 && $radius > $lembur->radius_cabang) {
            return response()->json(['status' => false, 'message' => 'Anda Berada Di Luar Radius Kantor, Jarak Anda ' . formatAngka($radius) . ' Meters Dari Kantor', 'notifikasi' => 'notifikasi_radius'], 400);
        } else {
            if ($status == 1) {
                if ($lembur->lembur_in != null) {
                    return response()->json(['status' => false, 'message' => 'Anda Sudah Memulai Absen Lembur', 'notifikasi' => 'notifikasi_sudahabsen'], 400);
                } else if ($jam_presensi < $jam_mulai_masuk) {
                    return response()->json(['status' => false, 'message' => 'Maaf Belum Waktunya Absen Masuk, Waktu Absen Dimulai Pukul ' . formatIndo3($jam_mulai_masuk), 'notifikasi' => 'notifikasi_mulaiabsen'], 400);
                } else if ($jam_presensi > $jam_pulang) {
                    return response()->json(['status' => false, 'message' => 'Maaf Waktu Lembur Sudah Berakhir, Tidak Bisa Absen Masuk lagi ', 'notifikasi' => 'notifikasi_akhirabsen'], 400);
                } else {
                    try {
                        Lembur::where('id', $id_lembur)->update([
                            'lembur_in' => $jam_presensi,
                            'lokasi_lembur_in' => $lokasi,
                            'foto_lembur_in' => $fileName
                        ]);
                        // Storage::put($file, $image_base64);
                        return response()->json(['status' => true, 'message' => 'Berhasil Memulai Lembur', 'notifikasi' => 'notifikasi_absenmasuk'], 200);
                    } catch (\Exception $e) {
                        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
                    }
                }
            } else {
                if ($lembur->lembur_out != null) {
                    return response()->json(['status' => false, 'message' => 'Anda Sudah Absen Pulang', 'notifikasi' => 'notifikasi_sudahabsen'], 400);
                } else if ($jam_presensi < $jam_mulai_pulang) {
                    return response()->json(['status' => false, 'message' => 'Maaf Belum Waktunya Absen Pulang, Minimal 30 Menit Setelah Jam Mulai Lembur ', 'notifikasi' => 'notifikasi_mulaiabsen'], 400);
                } else {
                    try {
                        Lembur::where('id', $id_lembur)->update([
                            'lembur_out' => $jam_presensi,
                            'lokasi_lembur_out' => $lokasi,
                            'foto_lembur_out' => $fileName
                        ]);
                        // Storage::put($file, $image_base64);
                        return response()->json(['status' => true, 'message' => 'Berhasil Absen Pulang', 'notifikasi' => 'notifikasi_absenpulang'], 200);
                    } catch (\Exception $e) {
                        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
                    }
                }
            }
        }
    }
}
