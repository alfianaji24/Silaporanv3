<?php

namespace App\Http\Controllers;

use App\Models\Jamkerja;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\PresensiJamkerjaJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class JamkerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = Jamkerja::query();
        if (!empty($request->nama_jam_kerja_search)) {
            $query->where('nama_jam_kerja', 'like', '%' . $request->nama_jam_kerja_search . '%');
        }
        $data['jamkerja'] = $query->withCount('jabatanTerbatas')->get();
        $data['total_jabatan'] = Jamkerja::hitungJabatanDefinisi();

        return view('datamaster.jamkerja.index', $data);
    }

    /**
     * JSON: jam kerja yang boleh dipilih untuk NIK (sesuai batasan jabatan).
     */
    public function opsiUntukNik(Request $request)
    {
        abort_unless(
            $request->user()->can('ajuanjadwal.create')
                || $request->user()->can('karyawan.setjamkerja')
                || $request->user()->can('presensi.edit'),
            403
        );
        $request->validate(['nik' => 'required|string']);
        $karyawan = Karyawan::where('nik', $request->nik)->firstOrFail();
        $jamkerja = Jamkerja::query()
            ->visibleUntukJabatanKaryawan($karyawan->kode_jabatan)
            ->orderBy('nama_jam_kerja')
            ->get(['kode_jam_kerja', 'nama_jam_kerja', 'jam_masuk', 'jam_pulang']);

        return response()->json($jamkerja);
    }

    /**
     * JSON: jam kerja untuk seting jadwal departemen (gabungan jabatan di cabang+dept).
     */
    public function opsiUntukCabangDepartemen(Request $request)
    {
        abort_unless(
            $request->user()->can('jamkerjabydept.create') || $request->user()->can('jamkerjabydept.edit'),
            403
        );
        $request->validate([
            'kode_cabang' => 'required|string',
            'kode_dept' => 'required|string',
        ]);
        $jamkerja = Jamkerja::query()
            ->visibleUntukDepartemenCabang($request->kode_cabang, $request->kode_dept)
            ->orderBy('kode_jam_kerja')
            ->get(['kode_jam_kerja', 'nama_jam_kerja', 'jam_masuk', 'jam_pulang']);

        return response()->json($jamkerja);
    }

    public function create()
    {
        $data['jabatan'] = Jabatan::orderBy('nama_jabatan')->get();

        return view('datamaster.jamkerja.create', $data);
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_jam_kerja' => [
                'required',
                'string',
                'max:4',
                'unique:presensi_jamkerja,kode_jam_kerja'
            ],
            'nama_jam_kerja' => [
                'required',
                'string',
                'max:50'
            ],
            'jam_masuk' => [
                'required',
                'date_format:H:i'
            ],
            'jam_pulang' => [
                'required',
                'date_format:H:i'
            ],
            'istirahat' => [
                'required',
                'in:0,1'
            ],
            'lintashari' => [
                'required',
                'in:0,1'
            ],
            'total_jam' => [
                'required',
                'integer',
                'min:1',
                'max:24'
            ],
            'jam_awal_istirahat' => [
                'required_if:istirahat,1',
                'nullable',
                'date_format:H:i'
            ],
            'jam_akhir_istirahat' => [
                'required_if:istirahat,1',
                'nullable',
                'date_format:H:i'
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:255'
            ],
            'color' => [
                'nullable',
                'string',
                'max:7'
            ],
            'kode_jabatan_terbatas' => ['nullable', 'array'],
            'kode_jabatan_terbatas.*' => ['string', 'exists:jabatan,kode_jabatan'],
        ], [
            'kode_jam_kerja.required' => 'Kode Jam Kerja wajib diisi',
            'kode_jam_kerja.max' => 'Kode Jam Kerja maksimal 4 karakter',
            'kode_jam_kerja.unique' => 'Kode Jam Kerja sudah digunakan, silakan gunakan kode lain',
            'nama_jam_kerja.required' => 'Nama Jam Kerja wajib diisi',
            'nama_jam_kerja.max' => 'Nama Jam Kerja maksimal 50 karakter',
            'jam_masuk.required' => 'Jam Masuk wajib diisi',
            'jam_masuk.date_format' => 'Format Jam Masuk harus HH:mm',
            'jam_pulang.required' => 'Jam Pulang wajib diisi',
            'jam_pulang.date_format' => 'Format Jam Pulang harus HH:mm',
            'istirahat.required' => 'Istirahat wajib dipilih',
            'istirahat.in' => 'Nilai Istirahat tidak valid',
            'lintashari.required' => 'Lintas Hari wajib dipilih',
            'lintashari.in' => 'Nilai Lintas Hari tidak valid',
            'total_jam.required' => 'Total Jam wajib diisi',
            'total_jam.integer' => 'Total Jam harus berupa angka',
            'total_jam.min' => 'Total Jam minimal 1 jam',
            'total_jam.max' => 'Total Jam maksimal 24 jam',
            'jam_awal_istirahat.required_if' => 'Jam Awal Istirahat wajib diisi jika istirahat dipilih',
            'jam_awal_istirahat.date_format' => 'Format Jam Awal Istirahat harus HH:mm',
            'jam_akhir_istirahat.required_if' => 'Jam Akhir Istirahat wajib diisi jika istirahat dipilih',
            'jam_akhir_istirahat.date_format' => 'Format Jam Akhir Istirahat harus HH:mm',
            'keterangan.max' => 'Keterangan maksimal 255 karakter'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Jamkerja::where('kode_jam_kerja', $request->kode_jam_kerja)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jam Kerja sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_jam_kerja = strtoupper(trim($request->kode_jam_kerja));
            $nama_jam_kerja = trim($request->nama_jam_kerja);
            $keterangan = $request->keterangan ? trim($request->keterangan) : null;

            // Validasi panjang setelah trim
            if (strlen($kode_jam_kerja) > 4) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jam Kerja maksimal 4 karakter'));
            }

            if (strlen($nama_jam_kerja) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Jam Kerja maksimal 50 karakter'));
            }

            if ($keterangan && strlen($keterangan) > 255) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Keterangan maksimal 255 karakter'));
            }

            // Validasi total jam
            $total_jam = (int)$request->total_jam;
            if ($total_jam < 1 || $total_jam > 24) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Total Jam harus antara 1 sampai 24 jam'));
            }

            // Simpan Data Jam Kerja
            Jamkerja::create([
                'kode_jam_kerja' => $kode_jam_kerja,
                'nama_jam_kerja' => $nama_jam_kerja,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'istirahat' => $request->istirahat,
                'lintashari' => $request->lintashari,
                'total_jam' => $total_jam,
                'jam_awal_istirahat' => $request->istirahat == '1' ? $request->jam_awal_istirahat : null,
                'jam_akhir_istirahat' => $request->istirahat == '1' ? $request->jam_akhir_istirahat : null,
                'keterangan' => $keterangan,
                'color' => $request->color
            ]);

            $this->syncJabatanTerbatas($kode_jam_kerja, $request->input('kode_jabatan_terbatas', []));

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jam Kerja sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Pastikan panjang data sesuai batas maksimal'));
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

    public function edit($kode_jam_kerja)
    {
        $kode_jam_kerja = Crypt::decrypt($kode_jam_kerja);
        $data['jamkerja'] = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();
        $data['jabatan'] = Jabatan::orderBy('nama_jabatan')->get();
        $data['kode_jabatan_terpilih'] = PresensiJamkerjaJabatan::where('kode_jam_kerja', $kode_jam_kerja)
            ->pluck('kode_jabatan')
            ->all();
        return view('datamaster.jamkerja.edit', $data);
    }


    public function update(Request $request, $kode_jam_kerja)
    {
        $kode_jam_kerja = Crypt::decrypt($kode_jam_kerja);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'nama_jam_kerja' => [
                'required',
                'string',
                'max:50'
            ],
            'jam_masuk' => [
                'required',
                'date_format:H:i'
            ],
            'jam_pulang' => [
                'required',
                'date_format:H:i'
            ],
            'istirahat' => [
                'required',
                'in:0,1'
            ],
            'lintashari' => [
                'required',
                'in:0,1'
            ],
            'total_jam' => [
                'required',
                'integer',
                'min:1',
                'max:24'
            ],
            'jam_awal_istirahat' => [
                'required_if:istirahat,1',
                'nullable',
                'date_format:H:i'
            ],
            'jam_akhir_istirahat' => [
                'required_if:istirahat,1',
                'nullable',
                'date_format:H:i'
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:255'
            ],
            'color' => [
                'nullable',
                'string',
                'max:7'
            ],
            'kode_jabatan_terbatas' => ['nullable', 'array'],
            'kode_jabatan_terbatas.*' => ['string', 'exists:jabatan,kode_jabatan'],
        ], [
            'nama_jam_kerja.required' => 'Nama Jam Kerja wajib diisi',
            'nama_jam_kerja.max' => 'Nama Jam Kerja maksimal 50 karakter',
            'jam_masuk.required' => 'Jam Masuk wajib diisi',
            'jam_masuk.date_format' => 'Format Jam Masuk harus HH:mm',
            'jam_pulang.required' => 'Jam Pulang wajib diisi',
            'jam_pulang.date_format' => 'Format Jam Pulang harus HH:mm',
            'istirahat.required' => 'Istirahat wajib dipilih',
            'istirahat.in' => 'Nilai Istirahat tidak valid',
            'lintashari.required' => 'Lintas Hari wajib dipilih',
            'lintashari.in' => 'Nilai Lintas Hari tidak valid',
            'total_jam.required' => 'Total Jam wajib diisi',
            'total_jam.integer' => 'Total Jam harus berupa angka',
            'total_jam.min' => 'Total Jam minimal 1 jam',
            'total_jam.max' => 'Total Jam maksimal 24 jam',
            'jam_awal_istirahat.required_if' => 'Jam Awal Istirahat wajib diisi jika istirahat dipilih',
            'jam_awal_istirahat.date_format' => 'Format Jam Awal Istirahat harus HH:mm',
            'jam_akhir_istirahat.required_if' => 'Jam Akhir Istirahat wajib diisi jika istirahat dipilih',
            'jam_akhir_istirahat.date_format' => 'Format Jam Akhir Istirahat harus HH:mm',
            'keterangan.max' => 'Keterangan maksimal 255 karakter'
        ]);

        try {
            // Trim whitespace
            $nama_jam_kerja = trim($request->nama_jam_kerja);
            $keterangan = $request->keterangan ? trim($request->keterangan) : null;

            // Validasi panjang setelah trim
            if (strlen($nama_jam_kerja) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Jam Kerja maksimal 50 karakter'));
            }

            if ($keterangan && strlen($keterangan) > 255) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Keterangan maksimal 255 karakter'));
            }

            // Validasi total jam
            $total_jam = (int)$request->total_jam;
            if ($total_jam < 1 || $total_jam > 24) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Total Jam harus antara 1 sampai 24 jam'));
            }

            // Update Data Jam Kerja
            $jamkerja = Jamkerja::find($kode_jam_kerja);
            $jamkerja->update([
                'nama_jam_kerja' => $nama_jam_kerja,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'istirahat' => $request->istirahat,
                'lintashari' => $request->lintashari,
                'total_jam' => $total_jam,
                'jam_awal_istirahat' => $request->istirahat == '1' ? $request->jam_awal_istirahat : null,
                'jam_akhir_istirahat' => $request->istirahat == '1' ? $request->jam_akhir_istirahat : null,
                'keterangan' => $keterangan,
                'color' => $request->color
            ]);

            $this->syncJabatanTerbatas($kode_jam_kerja, $request->input('kode_jabatan_terbatas', []));

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Pastikan panjang data sesuai batas maksimal'));
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

    public function destroy($kode_jam_kerja)
    {
        $kode_jam_kerja = Crypt::decrypt($kode_jam_kerja);
        try {
            Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Kosong = tidak ada yang melihat jam kerja ini; isi = hanya jabatan terdaftar;
     * jika jumlah sama jumlah definisi jabatan master = setara "semua jabatan".
     */
    private function syncJabatanTerbatas(string $kodeJamKerja, ?array $kodeJabatanList): void
    {
        PresensiJamkerjaJabatan::where('kode_jam_kerja', $kodeJamKerja)->delete();
        if (empty($kodeJabatanList) || !is_array($kodeJabatanList)) {
            return;
        }
        $seen = [];
        foreach ($kodeJabatanList as $kode) {
            $kode = is_string($kode) ? trim($kode) : '';
            if ($kode === '' || isset($seen[$kode])) {
                continue;
            }
            $seen[$kode] = true;
            PresensiJamkerjaJabatan::create([
                'kode_jam_kerja' => $kodeJamKerja,
                'kode_jabatan' => $kode,
            ]);
        }
    }
}
