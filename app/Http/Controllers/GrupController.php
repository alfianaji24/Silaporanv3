<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use App\Models\GrupDetail;
use App\Models\Karyawan;
use App\Models\ShiftPattern;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class GrupController extends Controller
{

    public function index(Request $request)
    {
        $query = Grup::withCount('karyawan');
        if (!empty($request->nama_grup)) {
            $query->where(function($q) use ($request) {
                $q->where('nama_grup', 'like', '%' . $request->nama_grup . '%')
                  ->orWhere('kode_grup', 'like', '%' . $request->nama_grup . '%');
            });
        }
        $data['grup'] = $query->orderBy('kode_grup')->get();
        $data['total_grup'] = Grup::count();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.grup.index', $data);
        }

        return view('datamaster.grup.index', $data);
    }

    public function create()
    {
        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.grup.create');
        }

        return view('datamaster.grup.create');
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_grup' => [
                'required',
                'string',
                'max:3',
                'unique:grup,kode_grup'
            ],
            'nama_grup' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'kode_grup.required' => 'Kode Grup wajib diisi',
            'kode_grup.max' => 'Kode Grup maksimal 3 karakter',
            'kode_grup.unique' => 'Kode Grup sudah digunakan, silakan gunakan kode lain',
            'nama_grup.required' => 'Nama Grup wajib diisi',
            'nama_grup.max' => 'Nama Grup maksimal 50 karakter'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Grup::where('kode_grup', $request->kode_grup)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Grup sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_grup = strtoupper(trim($request->kode_grup));
            $nama_grup = trim($request->nama_grup);

            // Validasi panjang setelah trim
            if (strlen($kode_grup) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Grup maksimal 3 karakter'));
            }

            if (strlen($nama_grup) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Grup maksimal 50 karakter'));
            }

            // Simpan Data Grup
            Grup::create([
                'kode_grup' => $kode_grup,
                'nama_grup' => $nama_grup
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Grup sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Kode maksimal 3 karakter, Nama maksimal 50 karakter'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }


    public function edit($kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);
        $data['grup'] = Grup::where('kode_grup', $kode_grup)->first();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.grup.edit', $data);
        }

        return view('datamaster.grup.edit', $data);
    }

    public function update($kode_grup, Request $request)
    {
        $kode_grup_old = Crypt::decrypt($kode_grup);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_grup' => [
                'required',
                'string',
                'max:3',
                'unique:grup,kode_grup,' . $kode_grup_old . ',kode_grup'
            ],
            'nama_grup' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'kode_grup.required' => 'Kode Grup wajib diisi',
            'kode_grup.max' => 'Kode Grup maksimal 3 karakter',
            'kode_grup.unique' => 'Kode Grup sudah digunakan, silakan gunakan kode lain',
            'nama_grup.required' => 'Nama Grup wajib diisi',
            'nama_grup.max' => 'Nama Grup maksimal 50 karakter'
        ]);

        try {
            // Trim whitespace
            $kode_grup_new = strtoupper(trim($request->kode_grup));
            $nama_grup = trim($request->nama_grup);

            // Validasi panjang setelah trim
            if (strlen($kode_grup_new) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Grup maksimal 3 karakter'));
            }

            if (strlen($nama_grup) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Grup maksimal 50 karakter'));
            }

            // Cek duplicate jika kode berubah
            if ($kode_grup_new !== $kode_grup_old) {
                $existing = Grup::where('kode_grup', $kode_grup_new)->first();
                if ($existing) {
                    return Redirect::back()
                        ->withInput()
                        ->with(messageError('Kode Grup sudah digunakan, silakan gunakan kode lain'));
                }
            }

            // Update Data Grup
            Grup::where('kode_grup', $kode_grup_old)->update([
                'kode_grup' => $kode_grup_new,
                'nama_grup' => $nama_grup
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Grup sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Kode maksimal 3 karakter, Nama maksimal 50 karakter'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }

    public function delete($kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);
        try {
            Grup::where('kode_grup', $kode_grup)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function detail($kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);
        $grup = Grup::where('kode_grup', $kode_grup)->first();

        // Ambil karyawan yang sudah ada di grup menggunakan join
        $karyawanInGrup = DB::table('grup_detail')
            ->join('karyawan', 'grup_detail.nik', '=', 'karyawan.nik')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('grup_detail.kode_grup', $kode_grup)
            ->select(
                'grup_detail.id',
                'karyawan.nik',
                'karyawan.nama_karyawan',
                'karyawan.foto',
                'jabatan.nama_jabatan',
                'departemen.nama_dept'
            )
            ->get();

        // Ambil semua karyawan untuk dropdown
        $allKaryawan = Karyawan::where('status_aktif_karyawan', '1')->get();

        $data = [
            'grup' => $grup,
            'karyawanInGrup' => $karyawanInGrup,
            'allKaryawan' => $allKaryawan
        ];

        // Cek keberadaan file foto untuk setiap karyawan
        $karyawanInGrup = $karyawanInGrup->map(function ($karyawan) {
            $karyawan->foto_exists = false;
            if (!empty($karyawan->foto)) {
                $karyawan->foto_exists = Storage::disk('public')->exists('karyawan/' . $karyawan->foto);
            }
            return $karyawan;
        });

        $data = [
            'grup' => $grup,
            'karyawanInGrup' => $karyawanInGrup,
            'allKaryawan' => $allKaryawan
        ];

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.grup.detail', $data);
        }

        return view('datamaster.grup.detail', $data);
    }

    public function addKaryawan(Request $request)
    {
        $request->validate([
            'kode_grup' => 'required',
            'nik' => 'required'
        ]);

        try {
            // Cek apakah karyawan sudah ada di grup
            $existing = GrupDetail::where('kode_grup', $request->kode_grup)
                ->where('nik', $request->nik)
                ->first();

            if ($existing) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Karyawan sudah ada di grup ini'], 400);
                }
                return Redirect::back()->with(messageError('Karyawan sudah ada di grup ini'));
            }

            GrupDetail::create([
                'kode_grup' => $request->kode_grup,
                'nik' => $request->nik
            ]);

            if ($request->ajax()) {
                return response()->json(['message' => 'Karyawan berhasil ditambahkan ke grup']);
            }
            return Redirect::back()->with(messageSuccess('Karyawan berhasil ditambahkan ke grup'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function removeKaryawan(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $id = Crypt::decrypt($request->id);
        try {
            GrupDetail::where('id', $id)->delete();

            if ($request->ajax()) {
                return response()->json(['message' => 'Karyawan berhasil dihapus dari grup']);
            }
            return Redirect::back()->with(messageSuccess('Karyawan berhasil dihapus dari grup'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function createKaryawanForm($kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);
        $grup = Grup::where('kode_grup', $kode_grup)->first();

        // Ambil data departemen dan cabang untuk filter
        $departemen = DB::table('departemen')->orderBy('nama_dept')->get();
        $cabang = DB::table('cabang')->orderBy('nama_cabang')->get();

        $data = [
            'grup' => $grup,
            'departemen' => $departemen,
            'cabang' => $cabang
        ];

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.grup.create-karyawan-form', $data);
        }

        return view('datamaster.grup.create-karyawan-form', $data);
    }

    public function getAnggotaGrup($kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);

        // Ambil karyawan yang sudah ada di grup menggunakan join
        $karyawanInGrup = DB::table('grup_detail')
            ->join('karyawan', 'grup_detail.nik', '=', 'karyawan.nik')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('grup_detail.kode_grup', $kode_grup)
            ->select(
                'grup_detail.id',
                'karyawan.nik',
                'karyawan.nama_karyawan',
                'karyawan.foto',
                'jabatan.nama_jabatan',
                'departemen.nama_dept'
            )
            ->get();

        // Cek keberadaan file foto untuk setiap karyawan
        $karyawanInGrup = $karyawanInGrup->map(function ($karyawan) {
            $karyawan->foto_exists = false;
            if (!empty($karyawan->foto)) {
                $karyawan->foto_exists = Storage::disk('public')->exists('karyawan/' . $karyawan->foto);
            }
            return $karyawan;
        });

        $theme = session('admin_theme', 'v2');
        $viewPath = $theme === 'v2' ? 'v2.grup.partials.anggota-list' : 'datamaster.grup.partials.anggota-list';
        $html = view($viewPath, compact('karyawanInGrup'))->render();

        return response()->json([
            'html' => $html,
            'count' => $karyawanInGrup->count(),
            'data' => $karyawanInGrup->toArray()
        ]);
    }

    public function setJamKerja($kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);
        $grup = Grup::where('kode_grup', $kode_grup)->first();

        if (!$grup) {
            return redirect()->route('grup.index')->with(messageError('Grup tidak ditemukan'));
        }

        // Ambil data jam kerja yang tersedia
        $jamKerja = DB::table('presensi_jamkerja')->orderBy('nama_jam_kerja')->get();
        // Ambil semua pola shift aktif
        $shiftPatterns = ShiftPattern::with('details.jamKerja')->where('is_active', true)->orderBy('nama_pola')->get();

        // Ambil data jam kerja by date yang sudah ada untuk grup ini
        $jamKerjaBydate = DB::table('grup_jamkerja_bydate')
            ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('grup_jamkerja_bydate.kode_grup', $kode_grup)
            ->select(
                'grup_jamkerja_bydate.id',
                'grup_jamkerja_bydate.tanggal',
                'grup_jamkerja_bydate.kode_jam_kerja',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang'
            )
            ->orderBy('grup_jamkerja_bydate.tanggal', 'desc')
            ->get();

        // Encrypt ID untuk setiap record
        $jamKerjaBydate = $jamKerjaBydate->map(function ($item) {
            $item->encrypted_id = Crypt::encrypt($item->id);
            return $item;
        });

        $data = [
            'grup' => $grup,
            'jamKerja' => $jamKerja,
            'jamKerjaBydate' => $jamKerjaBydate,
            'shiftPatterns' => $shiftPatterns,
        ];

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.grup.set-jam-kerja', $data);
        }

        return view('datamaster.grup.set-jam-kerja', $data);
    }

    public function generateDariPola(Request $request, $kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);
        $grup = Grup::where('kode_grup', $kode_grup)->first();

        if (!$grup) {
            return redirect()->back()->with(messageError('Grup tidak ditemukan'));
        }

        $request->validate([
            'kode_pola' => 'required|exists:shift_patterns,kode_pola',
            'tanggal_mulai_pola' => 'required|date',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2050',
            'overwrite' => 'nullable|boolean',
        ]);

        try {
            $pattern = ShiftPattern::with('details')->where('kode_pola', $request->kode_pola)->first();
            if (!$pattern || $pattern->details->isEmpty()) {
                return redirect()->back()->with(messageError('Pola shift tidak memiliki alokasi hari'));
            }

            // Simpan pola dan tanggal mulai di data grup jika diminta
            $grup->update([
                'kode_pola' => $request->kode_pola,
                'tanggal_mulai_pola' => $request->tanggal_mulai_pola,
            ]);

            $detailsByUrutan = $pattern->details->keyBy('urutan_hari');
            $siklus = $pattern->jumlah_hari_siklus;
            $startDate = Carbon::parse($request->tanggal_mulai_pola)->startOfDay();

            $bulan = (int) $request->bulan;
            $tahun = (int) $request->tahun;
            $firstDayOfMonth = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $daysInMonth = $firstDayOfMonth->daysInMonth;

            DB::beginTransaction();

            // Jika overwrite dicentang, bersihkan data bulan ini terlebih dahulu
            if ($request->has('overwrite') && $request->overwrite) {
                DB::table('grup_jamkerja_bydate')
                    ->where('kode_grup', $kode_grup)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->delete();
            }

            $countInserted = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $targetDate = Carbon::create($tahun, $bulan, $d)->startOfDay();
                $diffDays = $startDate->diffInDays($targetDate, false);
                $dayIndex = (($diffDays % $siklus) + $siklus) % $siklus + 1;

                $detail = $detailsByUrutan->get($dayIndex);
                $kodeJamKerja = $detail ? $detail->kode_jam_kerja : null;

                $dateString = $targetDate->format('Y-m-d');

                // Jika ada jam kerja (bukan libur), masukkan/update ke grup_jamkerja_bydate
                if (!empty($kodeJamKerja)) {
                    DB::table('grup_jamkerja_bydate')->updateOrInsert(
                        ['kode_grup' => $kode_grup, 'tanggal' => $dateString],
                        ['kode_jam_kerja' => $kodeJamKerja, 'updated_at' => now(), 'created_at' => now()]
                    );
                    $countInserted++;
                } else {
                    // Jika libur dan sebelumnya ada record untuk tanggal ini, kita hapus (menandakan libur)
                    DB::table('grup_jamkerja_bydate')
                        ->where('kode_grup', $kode_grup)
                        ->where('tanggal', $dateString)
                        ->delete();
                }
            }

            DB::commit();
            return redirect()->back()->with(messageSuccess("Jadwal grup {$grup->nama_grup} berhasil di-generate dari pola {$pattern->nama_pola} ({$countInserted} hari kerja dijadwalkan)."));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(messageError('Gagal generate jadwal: ' . $e->getMessage()));
        }
    }

    public function updateJamKerja(Request $request, $kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);

        $request->validate([
            'tanggal' => 'required|date',
            'kode_jam_kerja' => 'required|exists:presensi_jamkerja,kode_jam_kerja'
        ]);

        try {
            // Cek apakah sudah ada jam kerja untuk tanggal dan grup ini
            $existing = DB::table('grup_jamkerja_bydate')
                ->where('kode_grup', $kode_grup)
                ->where('tanggal', $request->tanggal)
                ->first();

            if ($existing) {
                // Update jika sudah ada
                DB::table('grup_jamkerja_bydate')
                    ->where('id', $existing->id)
                    ->update([
                        'kode_jam_kerja' => $request->kode_jam_kerja,
                        'updated_at' => now()
                    ]);
                $message = "Jam kerja untuk tanggal {$request->tanggal} berhasil diupdate";
            } else {
                // Insert jika belum ada
                DB::table('grup_jamkerja_bydate')->insert([
                    'kode_grup' => $kode_grup,
                    'tanggal' => $request->tanggal,
                    'kode_jam_kerja' => $request->kode_jam_kerja,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $message = "Jam kerja untuk tanggal {$request->tanggal} berhasil ditambahkan";
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message
                ]);
            }

            return redirect()->back()->with(messageSuccess($message));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }

    public function deleteJamKerjaBydate(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        try {
            $id = Crypt::decrypt($request->id);
            $deleted = DB::table('grup_jamkerja_bydate')->where('id', $id)->delete();

            if ($deleted > 0) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Jam kerja berhasil dihapus']);
                }
                return redirect()->back()->with(messageSuccess('Jam kerja berhasil dihapus'));
            } else {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Data tidak ditemukan'], 404);
                }
                return redirect()->back()->with(messageError('Data tidak ditemukan'));
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with(messageError($e->getMessage()));
        }
    }

    public function getJamKerjaBydate(Request $request, $kode_grup)
    {
        $kode_grup = Crypt::decrypt($kode_grup);

        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|string'
        ]);

        try {
            $bulan = $request->bulan;
            $tahun = $request->tahun;

            // Ambil data jam kerja by date untuk bulan dan tahun tertentu
            $jamKerjaBydate = DB::table('grup_jamkerja_bydate')
                ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('grup_jamkerja_bydate.kode_grup', $kode_grup)
                ->whereMonth('grup_jamkerja_bydate.tanggal', $bulan)
                ->whereYear('grup_jamkerja_bydate.tanggal', $tahun)
                ->select(
                    'grup_jamkerja_bydate.id',
                    'grup_jamkerja_bydate.tanggal',
                    'grup_jamkerja_bydate.kode_jam_kerja',
                    'presensi_jamkerja.nama_jam_kerja',
                    'presensi_jamkerja.jam_masuk',
                    'presensi_jamkerja.jam_pulang'
                )
                ->get();

            // Encrypt ID untuk setiap record
            $jamKerjaBydate = $jamKerjaBydate->map(function ($item) {
                $item->encrypted_id = Crypt::encrypt($item->id);
                return $item;
            });

            return response()->json($jamKerjaBydate);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function searchKaryawan(Request $request)
    {
        try {
            $search = $request->get('search');
            $kode_grup = $request->get('kode_grup');
            $kode_dept = $request->get('kode_dept');
            $kode_cabang = $request->get('kode_cabang');

            // Validasi parameter
            if (!$kode_grup) {
                return response()->json([
                    'error' => 'Parameter kode_grup diperlukan'
                ], 400);
            }

            // Ambil semua karyawan yang sudah ada di grup manapun (bukan hanya grup yang sedang diedit)
            $karyawanInGrup = DB::table('grup_detail')
                ->distinct()
                ->pluck('nik')
                ->toArray();

            // Query pencarian karyawan
            $query = DB::table('karyawan')
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->where('karyawan.status_aktif_karyawan', '1');

            // Exclude semua karyawan yang sudah ada di grup manapun (hanya jika ada karyawan di grup)
            if (!empty($karyawanInGrup)) {
                $query->whereNotIn('karyawan.nik', $karyawanInGrup);
            }

            // Filter berdasarkan departemen
            if ($kode_dept) {
                $query->where('karyawan.kode_dept', $kode_dept);
            }

            // Filter berdasarkan cabang
            if ($kode_cabang) {
                $query->where('karyawan.kode_cabang', $kode_cabang);
            }

            // Pencarian berdasarkan keyword
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('karyawan.nik', 'like', '%' . $search . '%')
                        ->orWhere('karyawan.nama_karyawan', 'like', '%' . $search . '%')
                        ->orWhere('jabatan.nama_jabatan', 'like', '%' . $search . '%')
                        ->orWhere('departemen.nama_dept', 'like', '%' . $search . '%')
                        ->orWhere('cabang.nama_cabang', 'like', '%' . $search . '%');
                });
            }

            $karyawan = $query->select(
                'karyawan.nik',
                'karyawan.nama_karyawan',
                'karyawan.foto',
                'jabatan.nama_jabatan',
                'departemen.nama_dept',
                'cabang.nama_cabang'
            )->limit(20)->get();

            // Cek keberadaan file foto untuk setiap karyawan
            $karyawan = $karyawan->map(function ($k) {
                $k->foto_exists = false;
                if (!empty($k->foto)) {
                    $k->foto_exists = Storage::disk('public')->exists('karyawan/' . $k->foto);
                }
                return $k;
            });

            return response()->json([
                'karyawan' => $karyawan,
                'count' => $karyawan->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
