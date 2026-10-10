<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izindinas;
use App\Models\Izinsakit;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{
    /**
     * Get list of permission/leave requests for mobile.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil data karyawan tidak ditemukan'
            ], 404);
        }
        $nik = $userkaryawan->nik;

        $izinabsen = Izinabsen::where('nik', $nik)
            ->select('kode_izin as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'i\' as ket'), 'status', 'approval_step', DB::raw('NULL as doc_sid'));

        $izinsakit = Izinsakit::where('nik', $nik)
            ->select('kode_izin_sakit as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'s\' as ket'), 'status', 'approval_step', 'doc_sid');

        $izincuti = Izincuti::where('nik', $nik)
            ->select('kode_izin_cuti as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'c\' as ket'), 'status', 'approval_step', DB::raw('NULL as doc_sid'));

        $izin_dinas = Izindinas::where('nik', $nik)
            ->select('kode_izin_dinas as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'d\' as ket'), 'status', 'approval_step', DB::raw('NULL as doc_sid'));

        // Koreksi
        $koreksi = \App\Models\Koreksi::where('nik', $nik)
            ->select('kode_koreksi as kode', 'tanggal', 'keterangan', 'tanggal as dari', 'tanggal as sampai', DB::raw('\'k\' as ket'), 'status', 'approval_step', DB::raw('NULL as doc_sid'));

        $pengajuan_izin = $izinabsen->union($izinsakit)->union($izincuti)->union($izin_dinas)->union($koreksi)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) use ($nik) {
                // Add absolute URL for attachment if it exists
                if ($item->doc_sid) {
                    $item->doc_sid_url = asset('storage/uploads/sid/' . $item->doc_sid);
                } else {
                    $item->doc_sid_url = null;
                }

                // Hitung total hari kerja efektif
                if ($item->ket == 'k') {
                    $item->total_hari = 1;
                } else {
                    $item->total_hari = hitungHariKerja($nik, $item->dari, $item->sampai)['total_hari'];
                }

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $pengajuan_izin
        ]);
    }

    /**
     * Hitung hari kerja efektif via API Mobile
     */
    public function getHitungHari(Request $request)
    {
        $user = $request->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil data karyawan tidak ditemukan'
            ], 404);
        }
        $nik = $userkaryawan->nik;
        $dari = $request->dari;
        $sampai = $request->sampai ?? $dari;

        if (empty($dari)) {
            return response()->json([
                'success' => false,
                'total_hari' => 0,
                'message' => 'Tanggal belum dipilih'
            ], 400);
        }

        $hitung = hitungHariKerja($nik, $dari, $sampai);
        return response()->json([
            'success' => true,
            'total_hari' => $hitung['total_hari'],
            'hari_libur_count' => count($hitung['hari_libur']),
            'hari_off_count' => count($hitung['hari_off']),
            'detail_libur' => $hitung['hari_libur'],
            'detail_off' => $hitung['hari_off'],
        ]);
    }

    /**
     * Submit a permission/leave request.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil data karyawan tidak ditemukan'
            ], 404);
        }
        $nik = $userkaryawan->nik;

        $validator = Validator::make($request->all(), [
            'jenis_izin' => 'required|in:i,s,c,d', // i=absen, s=sakit, c=cuti, d=dinas
            'dari' => 'required|date_format:Y-m-d',
            'sampai' => 'required|date_format:Y-m-d|after_or_equal:dari',
            'keterangan' => 'required|string',
            'sid' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // for sickness
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $jenis = $request->input('jenis_izin');
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');
        $keterangan = $request->input('keterangan');

        // Check active working days
        $hitungHari = hitungHariKerja($nik, $dari, $sampai);
        $jmlhari = $hitungHari['total_hari'];

        if ($jmlhari <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Rentang tanggal yang dipilih tidak memiliki hari kerja aktif (seluruhnya hari libur / off)!'
            ], 400);
        }

        // Check if there's overlap in dates across any permission table
        $cek_absen = Izinabsen::where('nik', $nik)
            ->where(function($q) use ($dari, $sampai) {
                $q->whereBetween('dari', [$dari, $sampai])
                  ->orWhereBetween('sampai', [$dari, $sampai]);
            })->first();

        $cek_sakit = Izinsakit::where('nik', $nik)
            ->where(function($q) use ($dari, $sampai) {
                $q->whereBetween('dari', [$dari, $sampai])
                  ->orWhereBetween('sampai', [$dari, $sampai]);
            })->first();

        $cek_cuti = Izincuti::where('nik', $nik)
            ->where(function($q) use ($dari, $sampai) {
                $q->whereBetween('dari', [$dari, $sampai])
                  ->orWhereBetween('sampai', [$dari, $sampai]);
            })->first();

        $cek_dinas = Izindinas::where('nik', $nik)
            ->where(function($q) use ($dari, $sampai) {
                $q->whereBetween('dari', [$dari, $sampai])
                  ->orWhereBetween('sampai', [$dari, $sampai]);
            })->first();

        if ($cek_absen || $cek_sakit || $cek_cuti || $cek_dinas) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki pengajuan izin/sakit/cuti/dinas pada rentang tanggal tersebut!'
            ], 400);
        }

        DB::beginTransaction();
        try {
            if ($jenis == 'i') {
                // Izin Absen
                $general_setting = DB::table('pengaturan_umum')->first();
                if ($general_setting && $general_setting->batasi_hari_izin == 1) {
                    if ($jmlhari > $general_setting->jml_hari_izin_max) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Maksimal izin absen adalah ' . $general_setting->jml_hari_izin_max . ' hari kerja!'
                        ], 400);
                    }
                }

                $lastizin = Izinabsen::select('kode_izin')
                    ->whereRaw('YEAR(dari)="' . date('Y', strtotime($dari)) . '"')
                    ->whereRaw('MONTH(dari)="' . date('m', strtotime($dari)) . '"')
                    ->orderBy("kode_izin", "desc")
                    ->first();
                $last_kode = $lastizin ? $lastizin->kode_izin : '';
                $kode = buatkode($last_kode, "IA" . date('ym', strtotime($dari)), 4);

                $izin = new Izinabsen();
                $izin->kode_izin = $kode;
                $izin->nik = $nik;
                $izin->tanggal = $dari;
                $izin->dari = $dari;
                $izin->sampai = $sampai;
                $izin->keterangan = $keterangan;
                $izin->status = 0;
                $izin->approval_step = 1;
                $izin->save();

            } elseif ($jenis == 's') {
                // Izin Sakit
                $general_setting = DB::table('pengaturan_umum')->first();
                if ($general_setting && !empty($general_setting->batasi_hari_izin_sakit) && $general_setting->batasi_hari_izin_sakit == 1 && !empty($general_setting->jml_hari_izin_sakit_max)) {
                    if ($jmlhari > $general_setting->jml_hari_izin_sakit_max) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Maksimal izin sakit adalah ' . $general_setting->jml_hari_izin_sakit_max . ' hari kerja!'
                        ], 400);
                    }
                }

                $lastizinsakit = Izinsakit::select('kode_izin_sakit')
                    ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($dari)) . '"')
                    ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($dari)) . '"')
                    ->orderBy("kode_izin_sakit", "desc")
                    ->first();
                $last_kode = $lastizinsakit ? $lastizinsakit->kode_izin_sakit : '';
                $kode = buatkode($last_kode, "IS" . date('ym', strtotime($dari)), 4);

                $sid_name = null;
                if ($request->hasfile('sid')) {
                    $sid_name = $kode . ".jpg";
                }

                $sakit = new Izinsakit();
                $sakit->kode_izin_sakit = $kode;
                $sakit->nik = $nik;
                $sakit->tanggal = $dari;
                $sakit->dari = $dari;
                $sakit->sampai = $sampai;
                $sakit->keterangan = $keterangan;
                $sakit->status = 0;
                $sakit->approval_step = 1;
                $sakit->id_user = $user->id;
                if ($sid_name) {
                    $sakit->doc_sid = $sid_name;
                }
                $sakit->save();

                if ($request->hasfile('sid') && $sid_name) {
                    $destination_sid_path = "/public/uploads/sid";
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($request->file('sid'));
                    $encodedImage = (string) $image->toJpeg(75);
                    Storage::put($destination_sid_path . "/" . $sid_name, $encodedImage);
                }

            } elseif ($jenis == 'c') {
                // Izin Cuti
                $kode_cuti = $request->input('kode_cuti', 'C01');
                $cutiModel = \App\Models\Cuti::where('kode_cuti', $kode_cuti)->first();
                $jml_hari_max = $cutiModel ? $cutiModel->jumlah_hari : 12;

                if ($kode_cuti == "C01") {
                    $tahun_cuti = date('Y', strtotime($dari));
                    $cek_cuti_dipakai = \App\Models\Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                        ->where('presensi.nik', $nik)
                        ->whereRaw("YEAR(presensi.tanggal) = $tahun_cuti")
                        ->count();
                    $sisa_cuti = $jml_hari_max - $cek_cuti_dipakai;

                    if ($jmlhari > $sisa_cuti) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Sisa cuti tahunan Anda adalah ' . $sisa_cuti . ' hari. Pengajuan ' . $jmlhari . ' hari kerja melebihi batas!'
                        ], 400);
                    }
                }

                $lastizincuti = Izincuti::select('kode_izin_cuti')
                    ->whereRaw('YEAR(dari)="' . date('Y', strtotime($dari)) . '"')
                    ->whereRaw('MONTH(dari)="' . date('m', strtotime($dari)) . '"')
                    ->orderBy("kode_izin_cuti", "desc")
                    ->first();
                $last_kode = $lastizincuti ? $lastizincuti->kode_izin_cuti : '';
                $kode = buatkode($last_kode, "IC" . date('ym', strtotime($dari)), 4);

                $cuti = new Izincuti();
                $cuti->kode_izin_cuti = $kode;
                $cuti->nik = $nik;
                $cuti->tanggal = $dari;
                $cuti->dari = $dari;
                $cuti->sampai = $sampai;
                $cuti->kode_cuti = $kode_cuti;
                $cuti->keterangan = $keterangan;
                $cuti->status = 0;
                $cuti->approval_step = 1;
                $cuti->save();

            } elseif ($jenis == 'd') {
                // Izin Dinas
                $lastizindinas = Izindinas::select('kode_izin_dinas')
                    ->whereRaw('YEAR(dari)="' . date('Y', strtotime($dari)) . '"')
                    ->whereRaw('MONTH(dari)="' . date('m', strtotime($dari)) . '"')
                    ->orderBy("kode_izin_dinas", "desc")
                    ->first();
                $last_kode = $lastizindinas ? $lastizindinas->kode_izin_dinas : '';
                $kode = buatkode($last_kode, "ID" . date('ym', strtotime($dari)), 4);

                $dinas = new Izindinas();
                $dinas->kode_izin_dinas = $kode;
                $dinas->nik = $nik;
                $dinas->tanggal = $dari;
                $dinas->dari = $dari;
                $dinas->sampai = $sampai;
                $dinas->keterangan = $keterangan;
                $dinas->status = 0;
                $dinas->approval_step = 1;
                $dinas->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan izin berhasil disimpan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pengajuan izin: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel/delete a pending permission/leave request.
     */
    public function destroy(Request $request, $kode)
    {
        $user = $request->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil data karyawan tidak ditemukan'
            ], 404);
        }
        $nik = $userkaryawan->nik;

        // Determine type based on code prefix
        $prefix = substr($kode, 0, 2);

        $record = null;
        if ($prefix === 'IA') {
            $record = Izinabsen::where('kode_izin', $kode)->where('nik', $nik)->first();
        } elseif ($prefix === 'IS') {
            $record = Izinsakit::where('kode_izin_sakit', $kode)->where('nik', $nik)->first();
        } elseif ($prefix === 'IC') {
            $record = Izincuti::where('kode_izin_cuti', $kode)->where('nik', $nik)->first();
        } elseif ($prefix === 'ID') {
            $record = Izindinas::where('kode_izin_dinas', $kode)->where('nik', $nik)->first();
        }

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ], 404);
        }

        if ($record->status != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan yang sudah diproses tidak dapat dibatalkan'
            ], 400);
        }

        // If it's sickness and has SID document, delete the file
        if ($prefix === 'IS' && $record->doc_sid) {
            $sid_path = "/public/uploads/sid/" . $record->doc_sid;
            if (Storage::exists($sid_path)) {
                Storage::delete($sid_path);
            }
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil dibatalkan.'
        ]);
    }
}
