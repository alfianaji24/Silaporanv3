<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWaMessage;
use App\Models\Detailsetjamkerjabydept;
use App\Models\GrupDetail;
use App\Models\GrupJamkerjaBydate;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\LogAbsen;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\Setjamkerjabydate;
use App\Models\Setjamkerjabyday;
use App\Models\Cabang;
use App\Models\Izindinas;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function store()
    {
        $original_data = file_get_contents('php://input');
        $decoded_data = json_decode($original_data, true);
        $encoded_data = json_encode($decoded_data);

        $data = $decoded_data['data'];
        $pin = $data['pin'];
        $status_scan = $data['status_scan'];
        $scan = $data['scan'];


        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $karyawan = Karyawan::where('pin', $pin)->first();

        if ($karyawan == null) {
            return response()->json([
                'status' => false,
                'message' => 'Karyawan Tidak Ditemukan',
            ]);
            $nik = "";
        }
        else {
            $nik = $karyawan->nik;
        }

        $tanggal_sekarang = date("Y-m-d", strtotime($scan));
        $jam_sekarang = date("H:i", strtotime($scan));
        $tanggal_kemarin = date("Y-m-d", strtotime("-1 day", strtotime($tanggal_sekarang)));

        $tanggal_besok = date("Y-m-d", strtotime("+1 day", strtotime($tanggal_sekarang)));

        //Cek Presensi Kemarin
        $presensi_kemarin = Presensi::where('nik', $karyawan->nik)
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_kemarin)->first();

        $lintas_hari = $presensi_kemarin ? $presensi_kemarin->lintashari : 0;
        $batas_presensi_lintashari = $generalsetting->batas_presensi_lintashari;

        if (presensiLintasHariTerlewat($presensi_kemarin, $jam_sekarang, $batas_presensi_lintashari)) {
            return response()->json([
                'status' => false,
                'message' => pesanBatasPresensiLintasHari($batas_presensi_lintashari),
                'notifikasi' => 'notifikasi_batas_lintashari',
            ], 400);
        }

        //Jika Presensi Kemarin Status Lintas Hari nya 1 Makan Tanggal Presensi Sekarang adalah Tanggal Kemarin
        $tanggal_presensi = $lintas_hari == 1 ? $tanggal_kemarin : $tanggal_sekarang;
        $tanggal_pulang = $lintas_hari == 1 ? $tanggal_besok : $tanggal_sekarang;


        $namahari = getnamaHari(date('D', strtotime($tanggal_presensi)));
        //Cek Jam Kerja By Date
        $jamkerja = Setjamkerjabydate::join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)
            ->first();

        //Jika Tidak Memiliki Jam Kerja By Date
        if ($jamkerja == null) {

            $cek_group = GrupDetail::where('nik', $karyawan->nik)->first();
            if ($cek_group) {
                $jamkerja = GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)
                    ->where('tanggal', $tanggal_presensi)
                    ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->first();
            }
            else {
                $jamkerja = null;
            }

            if ($jamkerja == null) {
                //Cek Jam Kerja harian / Jam Kerja Khusus / Jam Kerja Per Orangannya
                $jamkerja = Setjamkerjabyday::join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('nik', $karyawan->nik)->where('hari', $namahari)->first();
            }

            // Jika Jam Kerja Harian Kosong
            if ($jamkerja == null) {
                $jamkerja = Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                    ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('kode_dept', $karyawan->kode_dept)
                    ->where('kode_cabang', $karyawan->kode_cabang)
                    ->where('hari', $namahari)->first();
            }
            // Jika Jam Kerja Harian Kosong
            if ($jamkerja == null) {
                // Fallback: pakai jam kerja yang sudah diset di data karyawan
                // (menggantikan fallback tetap `JK01`).
                if (!empty($karyawan->kode_jadwal)) {
                    $jamkerja = Jamkerja::where('kode_jam_kerja', $karyawan->kode_jadwal)->first();
                }
            }
        }

        //Cek Presensi
        $presensi = Presensi::where('nik', $karyawan->nik)->where('tanggal', $tanggal_presensi)->first();

        if ($presensi != null && $presensi->status != 'h') {
            return response()->json([
                'status' => false,
                'message' => 'Presensi Sudah Ada',
            ]);
        }
        else if ($jamkerja == null) {
            return response()->json([
                'status' => false,
                'message' => 'Jam Kerja Tidak Ditemukan',
            ]);
        }

        $kode_jam_kerja = $jamkerja->kode_jam_kerja;
        $jam_kerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();

        $jam_presensi = $tanggal_sekarang . " " . $jam_sekarang;

        $jam_masuk = $tanggal_presensi . " " . date('H:i', strtotime($jam_kerja->jam_masuk));
        $jam_pulang = $tanggal_pulang . " " . date('H:i', strtotime($jam_kerja->jam_pulang));

        $presensi_hariini = Presensi::where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)
            ->first();

        if (in_array($status_scan, [0, 2, 4, 6, 8])) {
            if ($presensi_hariini && $presensi_hariini->jam_in != null) {
                return response()->json(['status' => false, 'message' => 'Anda Sudah Absen Masuk Hari Ini', 'notifikasi' => 'notifikasi_sudahabsen'], 400);
            }
            else {
                try {
                    if ($presensi_hariini != null) {
                        Presensi::where('id', $presensi_hariini->id)->update([
                            'jam_in' => $jam_presensi,
                            'tipe_presensi' => 'fingerprint'
                        ]);
                    }
                    else {
                        Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => $jam_presensi,
                            'jam_out' => null,
                            'lokasi_out' => null,
                            'foto_out' => null,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h',
                            'tipe_presensi' => 'fingerprint'
                        ]);
                    }
                    // Kirim Notifikasi Ke WA (dibungkus try-catch agar error WA tidak mempengaruhi response sukses)
                    if ($karyawan->no_hp != null || $karyawan->no_hp != "" && $generalsetting->notifikasi_wa == 1) {
                        try {
                            $is_terlambat = strtotime($jam_presensi) > strtotime($jam_masuk);
                            $terlambat_menit = $is_terlambat ? floor((strtotime($jam_presensi) - strtotime($jam_masuk)) / 60) : 0;

                            // Get attendance record for tipe_presensi
                            $attendance_record = Presensi::where('nik', $karyawan->nik)
                                ->where('tanggal', $tanggal_presensi)
                                ->first();
                            
                            $tipe_presensi_text = 'via: Fingerprint';

                            $message = "📢 INFO ABSEN MASUK\n\n"
                                . "👤 Nama: {$karyawan->nama_karyawan}\n"
                                . "🕒 Waktu: {$jam_presensi}\n"
                                . "📝 {$tipe_presensi_text}\n";

                            if ($is_terlambat) {
                                $message .= "⏰ Terlambat: {$terlambat_menit} menit\n";
                            }

                            $message .= "\nTelah Berhasil Tercatat\n"
                                . "Selamat Bekerja!\n\n"
                                . "_This is an automatically generated notification, please do not reply to this message._";

                            $this->sendwa($karyawan->no_hp, $message);
                        }
                        catch (\Exception $waException) {
                            // Log error pengiriman WA tapi tidak mempengaruhi response sukses
                            Log::error('Gagal mengirim notifikasi WA untuk absen masuk (API)', [
                                'nik' => $karyawan->nik,
                                'nama' => $karyawan->nama_karyawan,
                                'error' => $waException->getMessage(),
                                'trace' => $waException->getTraceAsString()
                            ]);
                        }
                    }

                    return response()->json(['status' => true, 'message' => 'Berhasil Absen Masuk', 'notifikasi' => 'notifikasi_absenmasuk'], 200);
                }
                catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
                }
            }
        }
        else {
            try {
                if ($presensi_hariini != null) {
                    Presensi::where('id', $presensi_hariini->id)->update([
                        'jam_out' => $jam_presensi,
                        'tipe_presensi' => 'fingerprint'
                    ]);
                }
                else {
                    Presensi::create([
                        'nik' => $karyawan->nik,
                        'tanggal' => $tanggal_presensi,
                        'jam_in' => null,
                        'jam_out' => $jam_presensi,
                        'lokasi_in' => null,
                        'foto_in' => null,
                        'kode_jam_kerja' => $kode_jam_kerja,
                        'status' => 'h',
                        'tipe_presensi' => 'fingerprint'
                    ]);
                }
                // Kirim Notifikasi Ke WA (dibungkus try-catch agar error WA tidak mempengaruhi response sukses)
                if ($karyawan->no_hp != null || $karyawan->no_hp != "" && $generalsetting->notifikasi_wa == 1) {
                    try {
                        // Get attendance record for tipe_presensi
                        $attendance_record = Presensi::where('nik', $karyawan->nik)
                            ->where('tanggal', $tanggal_presensi)
                            ->first();
                        
                        $tipe_presensi_text = 'via: Fingerprint';

                        // Deteksi pulang cepat — sama dengan perhitungan laporan (hitungpulangcepat)
                        $lintashari_flag = ($presensi_kemarin && $presensi_kemarin->lintashari == 1)
                            ? 1
                            : (int) ($jam_kerja->lintashari ?? 0);
                        $jadwal_pulang = ($presensi_kemarin && $lintashari_flag == 1)
                            ? $presensi_kemarin
                            : $jam_kerja;
                        $jam_pulang_jadwal = $jadwal_pulang->jam_pulang;
                        $pulang_cepat = deteksiPulangCepatNotifikasi(
                            $tanggal_presensi,
                            $jam_presensi,
                            $jam_pulang_jadwal,
                            $lintashari_flag,
                            $jadwal_pulang->istirahat ?? 0,
                            $jadwal_pulang->jam_awal_istirahat ?? '00:00:00',
                            $jadwal_pulang->jam_akhir_istirahat ?? '00:00:00'
                        );
                        $is_pulang_cepat = $pulang_cepat['is_pulang_cepat'];
                        $pulang_cepat_menit = $pulang_cepat['menit'];

                        $message = "📢 INFO ABSEN PULANG\n\n"
                                . "👤 Nama: {$karyawan->nama_karyawan}\n"
                                . "🕒 Waktu: {$jam_presensi}\n"
                                . "📝 {$tipe_presensi_text}\n";

                        if ($is_pulang_cepat) {
                            $message .= "⏰ Pulang Cepat: {$pulang_cepat_menit} menit\n";
                        }

                        $message .= "\nTelah Berhasil Tercatat\n"
                                . "Sampai Jumpa Besok!\n\n"
                                . "_This is an automatically generated notification, please do not reply to this message._";
                        $this->sendwa($karyawan->no_hp, $message);
                    }
                    catch (\Exception $waException) {
                        // Log error pengiriman WA tapi tidak mempengaruhi response sukses
                        Log::error('Gagal mengirim notifikasi WA untuk absen pulang (API)', [
                            'nik' => $karyawan->nik,
                            'nama' => $karyawan->nama_karyawan,
                            'error' => $waException->getMessage(),
                            'trace' => $waException->getTraceAsString()
                        ]);
                    }
                }
                return response()->json(['status' => true, 'message' => 'Berhasil Absen Pulang', 'notifikasi' => 'notifikasi_absenpulang'], 200);
            }
            catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
            }
        }
    }


    function sendwa($no_hp, $message)
    {
        dispatch(new SendWaMessage($no_hp, $message));
    }

/**
 * Menerima data dari mesin Fingerspot REVO melalui ADMS
 * Data akan disimpan ke file txt untuk keperluan debugging dan logging
 * Response disesuaikan agar mesin tidak terus mengirim request
 */
// public function receiveRevoData(Request $request)
// {
//     try {
//         // Ambil raw data dari request
//         $rawData = file_get_contents('php://input');

//         // Ambil semua data dari request (termasuk form data dan JSON)
//         $requestData = $request->all();

//         // Buat hash dari raw data untuk mencegah duplikasi
//         $dataHash = md5($rawData . $request->ip() . microtime(true));
//         $cacheKey = 'revo_data_' . $dataHash;

//         // Cek apakah data ini sudah pernah diterima (dalam 5 detik terakhir)
//         if (Cache::has($cacheKey)) {
//             // Data duplikat, langsung return OK tanpa proses ulang
//             Log::info('Data REVO duplikat terdeteksi, skip processing', [
//                 'hash' => $dataHash,
//                 'ip' => $request->ip()
//             ]);

//             $responseText = 'OK';
//             return response($responseText, 200)
//                 ->header('Content-Type', 'text/plain')
//                 ->header('Content-Length', strlen($responseText))
//                 ->header('Connection', 'close');
//         }

//         // Set cache untuk 5 detik
//         Cache::put($cacheKey, true, 5);

//         // Buat timestamp untuk nama file
//         $timestamp = date('Y-m-d_H-i-s');
//         $dateFolder = date('Y-m-d');

//         // Buat folder berdasarkan tanggal jika belum ada
//         $folderPath = storage_path('app/public/revo_logs/' . $dateFolder);
//         if (!file_exists($folderPath)) {
//             mkdir($folderPath, 0755, true);
//         }

//         // Nama file dengan timestamp dan random string untuk menghindari duplikasi
//         $fileName = 'revo_' . $timestamp . '_' . uniqid() . '.txt';
//         $filePath = $folderPath . '/' . $fileName;

//         // Siapkan konten untuk disimpan
//         $content = "=== DATA REVO DARI ADMS ===\n";
//         $content .= "Tanggal: " . date('Y-m-d H:i:s') . "\n";
//         $content .= "IP Address: " . $request->ip() . "\n";
//         $content .= "User Agent: " . ($request->userAgent() ?? 'N/A') . "\n";
//         $content .= "Method: " . $request->method() . "\n";
//         $content .= "URL: " . $request->fullUrl() . "\n";
//         $content .= "Data Hash: " . $dataHash . "\n";
//         $content .= "\n--- RAW DATA (HEX) ---\n";
//         $content .= bin2hex($rawData) . "\n";
//         $content .= "\n--- RAW DATA (STRING) ---\n";
//         $content .= $rawData . "\n";
//         $content .= "\n--- PARSED DATA ---\n";
//         $content .= json_encode($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
//         $content .= "\n--- HEADERS ---\n";
//         $content .= json_encode($request->headers->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
//         $content .= "\n=== END OF DATA ===\n";

//         // Simpan ke file
//         file_put_contents($filePath, $content);

//         // Parse JSON dari raw data jika ada
//         $jsonData = null;
//         $parsedJson = null;
//         if (!empty($rawData)) {
//             // Coba extract JSON dari raw data (skip binary header jika ada)
//             $jsonStart = strpos($rawData, '{');
//             if ($jsonStart !== false) {
//                 $jsonString = substr($rawData, $jsonStart);
//                 $parsedJson = json_decode($jsonString, true);
//             }
//         }

//         // Log juga ke Laravel log untuk tracking
//         Log::info('Data REVO diterima dari ADMS', [
//             'file' => $fileName,
//             'ip' => $request->ip(),
//             'data_count' => count($requestData),
//             'raw_length' => strlen($rawData),
//             'hash' => $dataHash,
//             'request_code' => $request->header('request-code'),
//             'dev_id' => $request->header('dev-id'),
//             'trans_id' => $request->header('trans-id'),
//             'parsed_json' => $parsedJson
//         ]);

//         // Ambil header dari request
//         $requestCode = $request->header('request-code', '');
//         $devId = $request->header('dev-id', '');
//         $transId = $request->header('trans-id', '');
//         $contentType = $request->header('Content-Type', '');

//         // Response untuk realtime_glog - format binary/hex yang diharapkan ADMS
//         if ($requestCode === 'realtime_glog') {
//             // Response string "OK" dalam format binary/hex
//             // "OK" dalam hex = 0x4F 0x4B
//             $responseBinary = 'OK';

//             // Log response untuk debugging
//             Log::info('Response REVO realtime_glog', [
//                 'request_code' => $requestCode,
//                 'response_hex' => bin2hex($responseBinary),
//                 'response_string' => $responseBinary,
//                 'response_length' => strlen($responseBinary),
//                 'response_format' => 'ok_string_hex'
//             ]);

//             return response($responseBinary, 200)
//                 ->header('Content-Type', 'application/octet-stream')
//                 ->header('Content-Length', strlen($responseBinary))
//                 ->header('Connection', 'close');
//         }

//         // Response untuk receive_cmd - format binary/hex yang diharapkan ADMS
//         if ($requestCode === 'receive_cmd') {
//             // Response string "OK" dalam format binary/hex
//             // "OK" dalam hex = 0x4F 0x4B
//             $responseBinary = 'OK';

//             // Log response untuk debugging
//             Log::info('Response REVO receive_cmd', [
//                 'request_code' => $requestCode,
//                 'response_hex' => bin2hex($responseBinary),
//                 'response_string' => $responseBinary,
//                 'response_length' => strlen($responseBinary),
//                 'response_format' => 'ok_string_hex'
//             ]);

//             return response($responseBinary, 200)
//                 ->header('Content-Type', 'application/octet-stream')
//                 ->header('Content-Length', strlen($responseBinary))
//                 ->header('Connection', 'close');
//         }

//         // Jika content-type adalah application/octet-stream, return "OK" dalam hex
//         if ($contentType === 'application/octet-stream') {
//             // Response string "OK" dalam format binary/hex
//             $responseBinary = 'OK';

//             return response($responseBinary, 200)
//                 ->header('Content-Type', 'application/octet-stream')
//                 ->header('Content-Length', strlen($responseBinary))
//                 ->header('Connection', 'close');
//         }

//         // Default: Response "OK" dalam format binary/hex
//         $responseBinary = 'OK';

//         return response($responseBinary, 200)
//             ->header('Content-Type', 'application/octet-stream')
//             ->header('Content-Length', strlen($responseBinary))
//             ->header('Connection', 'close');
//     } catch (\Exception $e) {
//         // Log error
//         Log::error('Error menerima data REVO dari ADMS', [
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString(),
//             'ip' => $request->ip()
//         ]);

//         // Tetap return response sukses agar mesin tidak terus mengirim
//         // Format "OK" dalam hex sesuai protokol ADMS
//         $responseBinary = 'OK';

//         return response($responseBinary, 200)
//             ->header('Content-Type', 'application/octet-stream')
//             ->header('Content-Length', strlen($responseBinary))
//             ->header('Connection', 'close');
//     }
// }

    /**
     * API: Get attendance history for a user
     * @param Request $request
     * @param string $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request, $userId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan || $userkaryawan->nik !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this history'
            ], 403);
        }

        $records = Presensi::where('nik', $userId)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * API: Log presence (checkin / checkout) for a user via mobile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function log(Request $request)
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return response()->json([
                'status' => false,
                'message' => 'User ini bukan karyawan'
            ], 403);
        }

        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        if (!$karyawan) {
            return response()->json([
                'status' => false,
                'message' => 'Data karyawan tidak ditemukan'
            ], 404);
        }

        // Cek apakah face recognition diaktifkan admin dan karyawan sudah mendaftar
        if ($generalsetting && $generalsetting->face_recognition == 1) {
            $wajahCount = \App\Models\Facerecognition::where('nik', $karyawan->nik)->count();
            if ($wajahCount == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Presensi ditolak. Admin mewajibkan verifikasi wajah, harap daftarkan Face ID / Face Recognition terlebih dahulu!',
                    'notifikasi' => 'notifikasi_faceid_required'
                ], 400);
            }
        }

        // Cek apakah karyawan menggunakan Fake GPS / Mock Location
        if ($request->is_mock == 1 || $request->is_mock == true || $request->is_mock == "true") {
            return response()->json([
                'status' => false,
                'message' => 'Presensi ditolak. Terdeteksi penggunaan GPS Palsu (Mock Location) pada perangkat Anda!',
                'notifikasi' => 'notifikasi_mock_gps'
            ], 400);
        }

        $status_lock_location = $karyawan->lock_location;
        $status = $request->status; // 1 = Check-in, 2 = Check-out
        $lokasi = $request->lokasi; // "lat,lng"
        $kode_jam_kerja = $request->kode_jam_kerja;

        $cabang = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
        $lokasi_kantor = $cabang->lokasi_cabang ?? '';

        $timezone_cabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');

        $carbon_now = Carbon::now($timezone_cabang);
        $tanggal_sekarang = $carbon_now->format('Y-m-d');
        $jam_sekarang = $carbon_now->format('H:i');
        $tanggal_kemarin = $carbon_now->copy()->subDay()->format('Y-m-d');
        $tanggal_besok = $carbon_now->copy()->addDay()->format('Y-m-d');

        // Cek Presensi Kemarin
        $presensi_kemarin = Presensi::where('nik', $karyawan->nik)
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('tanggal', $tanggal_kemarin)->first();

        $lintas_hari = $presensi_kemarin ? $presensi_kemarin->lintashari : 0;
        $batas_presensi_lintashari = $generalsetting->batas_presensi_lintashari;

        if (presensiLintasHariTerlewat($presensi_kemarin, $jam_sekarang, $batas_presensi_lintashari)) {
            return response()->json([
                'status' => false,
                'message' => pesanBatasPresensiLintasHari($batas_presensi_lintashari),
                'notifikasi' => 'notifikasi_batas_lintashari',
            ], 400);
        }

        $tanggal_presensi = $lintas_hari == 1 ? $tanggal_kemarin : $tanggal_sekarang;
        $tanggal_pulang = $lintas_hari == 1 ? $tanggal_besok : $tanggal_sekarang;

        // hitung radius jika lokasi di-lock dan koordinat dikirim
        $radius = 0;
        if ($lokasi && $lokasi_kantor) {
            $koordinat_user = explode(",", $lokasi);
            $latitude_user = $koordinat_user[0];
            $longitude_user = $koordinat_user[1];

            $koordinat_kantor = explode(",", $lokasi_kantor);
            $latitude_kantor = $koordinat_kantor[0];
            $longitude_kantor = $koordinat_kantor[1];

            $jarak = hitungjarak($latitude_kantor, $longitude_kantor, $latitude_user, $longitude_user);
            $radius = round($jarak["meters"]);
        }

        $in_out = $status == 1 ? "in" : "out";
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath, 0775, true);
            $path = Storage::path($folderPath);
            chmod($path, 0775);
        }

        $jam_kerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();
        if (!$jam_kerja) {
            return response()->json([
                'status' => false,
                'message' => 'Jam kerja tidak ditemukan'
            ], 400);
        }

        $jam_presensi = $tanggal_sekarang . " " . $jam_sekarang;
        $batas_jam_absen = $generalsetting->batas_jam_absen * 60;
        $batas_jam_absen_pulang = $generalsetting->batas_jam_absen_pulang * 60;

        if ($presensi_kemarin != null) {
            if ($presensi_kemarin->lintashari == 1) {
                if ($jam_sekarang > $generalsetting->batas_presensi_lintashari) {
                    $tanggal_pulang = $tanggal_besok;
                    $jam_kerja_pulang = $jam_kerja->jam_pulang;
                    $tanggal_presensi = $tanggal_sekarang;
                } else {
                    $tanggal_pulang = $tanggal_sekarang;
                    $jam_kerja_pulang = $presensi_kemarin->jam_pulang;
                    $tanggal_presensi = $tanggal_kemarin;
                }
            } else {
                if ($jam_kerja->lintashari == 1) {
                    $tanggal_pulang = $tanggal_besok;
                    $jam_kerja_pulang = $jam_kerja->jam_pulang;
                    $tanggal_presensi = $tanggal_sekarang;
                } else {
                    $tanggal_pulang = $tanggal_sekarang;
                    $jam_kerja_pulang = $jam_kerja->jam_pulang;
                    $tanggal_presensi = $tanggal_sekarang;
                }
            }
        } else {
            if ($jam_kerja->lintashari == 1) {
                $tanggal_pulang = $tanggal_besok;
                $jam_kerja_pulang = $jam_kerja->jam_pulang;
                $tanggal_presensi = $tanggal_sekarang;
            } else {
                $tanggal_pulang = $tanggal_sekarang;
                $jam_kerja_pulang = $jam_kerja->jam_pulang;
                $tanggal_presensi = $tanggal_sekarang;
            }
        }

        $formatName = $karyawan->nik . "-" . $tanggal_presensi . "-" . $in_out;
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($image) {
            if ($request->hasFile('image')) {
                Storage::put($file, file_get_contents($request->file('image')));
            } else {
                if (str_contains($image, ';base64')) {
                    $image_parts = explode(";base64", $image);
                    $image_base64 = base64_decode($image_parts[1]);
                    Storage::put($file, $image_base64);
                } else {
                    Storage::put($file, base64_decode($image));
                }
            }
        }

        $jam_masuk_string = $tanggal_presensi . " " . $jam_kerja->jam_masuk;
        $jam_masuk_carbon = Carbon::parse($jam_masuk_string, $timezone_cabang);
        $jam_masuk = $jam_masuk_carbon->format('Y-m-d H:i');

        $jam_mulai_masuk_carbon = $jam_masuk_carbon->copy()->subMinutes($batas_jam_absen);
        $jam_mulai_masuk = $jam_mulai_masuk_carbon->format('Y-m-d H:i');

        $jam_akhir_masuk_carbon = $jam_masuk_carbon->copy()->addMinutes($batas_jam_absen);
        $jam_akhir_masuk = $jam_akhir_masuk_carbon->format('Y-m-d H:i');

        if ($jam_akhir_masuk_carbon->format('H:i') >= '00:00' && $jam_akhir_masuk_carbon->day != $jam_masuk_carbon->day) {
            $jam_akhir_masuk = $jam_akhir_masuk_carbon->format('Y-m-d H:i');
        }

        $jam_pulang_string = $tanggal_pulang . " " . $jam_kerja_pulang;
        $jam_pulang_carbon = Carbon::parse($jam_pulang_string, $timezone_cabang);
        $jam_pulang = $jam_pulang_carbon->format('Y-m-d H:i');

        $jam_mulai_pulang_carbon = $jam_pulang_carbon->copy()->subMinutes($batas_jam_absen_pulang);
        $jam_mulai_pulang = $jam_mulai_pulang_carbon->format('Y-m-d H:i');

        $izin_dinas = Izindinas::where('nik', $karyawan->nik)
            ->where('status', 1)
            ->where('dari', '<=', $tanggal_presensi)
            ->where('sampai', '>=', $tanggal_presensi)
            ->first();

        if ($izin_dinas) {
            $status_lock_location = 0;
        }

        if ($status == 2) { // Checkout
            if ($lintas_hari == 1) {
                $presensi_hariini = Presensi::where('nik', $karyawan->nik)
                    ->whereIn('tanggal', [$tanggal_kemarin, $tanggal_sekarang])
                    ->whereNull('jam_out')
                    ->orderBy('tanggal', 'desc')
                    ->first();
            } else {
                $presensi_hariini = Presensi::where('nik', $karyawan->nik)
                    ->where('tanggal', $tanggal_presensi)
                    ->first();
            }
        } else { // Checkin
            $presensi_hariini = Presensi::where('nik', $karyawan->nik)
                ->where('tanggal', $tanggal_presensi)
                ->first();
        }

        $jam_presensi_carbon = Carbon::parse($jam_presensi, $timezone_cabang);
        $jam_mulai_masuk_carbon = Carbon::parse($jam_mulai_masuk, $timezone_cabang);
        $jam_akhir_masuk_carbon = Carbon::parse($jam_akhir_masuk, $timezone_cabang);
        $jam_mulai_pulang_carbon = Carbon::parse($jam_mulai_pulang, $timezone_cabang);

        if ($status_lock_location == 1 && $cabang && $radius > $cabang->radius_cabang) {
            return response()->json([
                'status' => false,
                'message' => 'Anda Berada Di Luar Radius Kantor, Jarak Anda ' . formatAngka($radius) . ' Meters Dari Kantor',
                'notifikasi' => 'notifikasi_radius'
            ], 400);
        }

        if ($status == 1) { // Check-in
            if ($presensi_hariini && $presensi_hariini->jam_in != null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda Sudah Absen Masuk Hari Ini',
                    'notifikasi' => 'notifikasi_sudahabsen'
                ], 400);
            } else if ($jam_presensi_carbon->lt($jam_mulai_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maaf Belum Waktunya Absen Masuk, Waktu Absen Dimulai Pukul ' . formatIndo3($jam_mulai_masuk),
                    'notifikasi' => 'notifikasi_mulaiabsen'
                ], 400);
            } else if ($jam_presensi_carbon->gt($jam_akhir_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maaf Waktu Absen Masuk Sudah Habis ',
                    'notifikasi' => 'notifikasi_akhirabsen'
                ], 400);
            } else {
                try {
                    if ($presensi_hariini != null) {
                        Presensi::where('id', $presensi_hariini->id)->update([
                            'jam_in' => $jam_presensi,
                            'lokasi_in' => $lokasi,
                            'foto_in' => $fileName,
                            'tipe_presensi' => 'mobile'
                        ]);
                    } else {
                        Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => $jam_presensi,
                            'jam_out' => null,
                            'lokasi_in' => $lokasi,
                            'lokasi_out' => null,
                            'foto_in' => $fileName,
                            'foto_out' => null,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h',
                            'tipe_presensi' => 'mobile'
                        ]);
                    }

                    if ($generalsetting->notifikasi_wa == 1) {
                        try {
                            $is_terlambat = $jam_presensi_carbon->gt($jam_masuk_carbon);
                            $terlambat_menit = $is_terlambat ? $jam_presensi_carbon->diffInMinutes($jam_masuk_carbon) : 0;

                            $tipe_presensi_text = 'via: Aplikasi Mobile';
                            $message = "📢 INFO ABSEN MASUK (MOBILE)\n\n"
                                . "👤 Nama: {$karyawan->nama_karyawan}\n"
                                . "🕒 Waktu: {$jam_presensi}\n"
                                . "📝 {$tipe_presensi_text}\n";

                            if ($is_terlambat) {
                                $message .= "⏰ Terlambat: {$terlambat_menit} menit\n";
                            }

                            $message .= "\nTelah Berhasil Tercatat\n"
                                . "Selamat Bekerja!";

                            if ($generalsetting->tujuan_notifikasi_wa == 0) {
                                if ($karyawan->no_hp != "") {
                                    $this->sendwa($karyawan->no_hp, $message);
                                }
                            } else {
                                $this->sendwa($generalsetting->id_group_wa, $message);
                            }
                        } catch (\Exception $waException) {
                            Log::error('Gagal mengirim notifikasi WA untuk absen masuk mobile', [
                                'nik' => $karyawan->nik,
                                'error' => $waException->getMessage()
                            ]);
                        }
                    }
                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil Absen Masuk',
                        'notifikasi' => 'notifikasi_absenmasuk'
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
                }
            }
        } else { // Check-out
            if ($presensi_hariini && $presensi_hariini->jam_out != null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda Sudah Absen Pulang Hari Ini',
                    'notifikasi' => 'notifikasi_sudahabsen'
                ], 400);
            } else if ($jam_presensi_carbon->lt($jam_mulai_pulang_carbon) && $generalsetting->batasi_absen == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maaf Belum Waktunya Absen Pulang, Waktu Absen Dimulai Pukul ' . formatIndo3($jam_mulai_pulang),
                    'notifikasi' => 'notifikasi_mulaiabsen'
                ], 400);
            } else {
                try {
                    if ($presensi_hariini != null) {
                        Presensi::where('id', $presensi_hariini->id)->update([
                            'jam_out' => $jam_presensi,
                            'lokasi_out' => $lokasi,
                            'foto_out' => $fileName,
                            'tipe_presensi' => 'mobile'
                        ]);
                    } else {
                        $tanggal_checkout = ($lintas_hari == 1 && $jam_sekarang < $generalsetting->batas_presensi_lintashari) 
                            ? $tanggal_kemarin 
                            : $tanggal_presensi;

                        Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_checkout,
                            'jam_in' => null,
                            'jam_out' => $jam_presensi,
                            'lokasi_in' => null,
                            'lokasi_out' => $lokasi,
                            'foto_in' => null,
                            'foto_out' => $fileName,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h',
                            'tipe_presensi' => 'mobile'
                        ]);
                    }

                    if ($generalsetting->notifikasi_wa == 1) {
                        try {
                            $lintashari_flag = ($presensi_kemarin && $presensi_kemarin->lintashari == 1) ? 1 : (int) ($jam_kerja->lintashari ?? 0);
                            $jadwal_pulang = ($presensi_kemarin && $lintashari_flag == 1) ? $presensi_kemarin : $jam_kerja;
                            
                            $pulang_cepat = deteksiPulangCepatNotifikasi(
                                $tanggal_presensi,
                                $jam_presensi,
                                $jam_kerja_pulang,
                                $lintashari_flag,
                                $jadwal_pulang->istirahat ?? 0,
                                $jadwal_pulang->jam_awal_istirahat ?? '00:00:00',
                                $jadwal_pulang->jam_akhir_istirahat ?? '00:00:00'
                            );
                            $is_pulang_cepat = $pulang_cepat['is_pulang_cepat'];
                            $pulang_cepat_menit = $pulang_cepat['menit'];

                            $tipe_presensi_text = 'via: Aplikasi Mobile';
                            $message = "📢 INFO ABSEN PULANG (MOBILE)\n\n"
                                . "👤 Nama: {$karyawan->nama_karyawan}\n"
                                . "🕒 Waktu: {$jam_presensi}\n"
                                . "📝 {$tipe_presensi_text}\n";

                            if ($is_pulang_cepat) {
                                $message .= "⏰ Pulang Cepat: {$pulang_cepat_menit} menit\n";
                            }

                            $message .= "\nTelah Berhasil Tercatat\n"
                                . "Sampai Jumpa Besok!";

                            if ($generalsetting->tujuan_notifikasi_wa == 0) {
                                if ($karyawan->no_hp != "") {
                                    $this->sendwa($karyawan->no_hp, $message);
                                }
                            } else {
                                $this->sendwa($generalsetting->id_group_wa, $message);
                            }
                        } catch (\Exception $waException) {
                            Log::error('Gagal mengirim notifikasi WA untuk absen pulang mobile', [
                                'nik' => $karyawan->nik,
                                'error' => $waException->getMessage()
                            ]);
                        }
                    }
                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil Absen Pulang',
                        'notifikasi' => 'notifikasi_absenpulang'
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
                }
            }
        }
    }

    private function getTipePresensiText($tipe_presensi)
    {
        switch ($tipe_presensi) {
            case 'fingerprint':
                return 'via: Mesin Fingerprint';
            case 'mobile':
                return 'via: Aplikasi Mobile';
            case 'PWA':
                return 'via: Web PWA';
            default:
                return 'via: Sistem';
        }
    }
}
