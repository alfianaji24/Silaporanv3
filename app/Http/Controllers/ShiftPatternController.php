<?php

namespace App\Http\Controllers;

use App\Models\Jamkerja;
use App\Models\ShiftPattern;
use App\Models\ShiftPatternDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ShiftPatternController extends Controller
{
    public function index(Request $request)
    {
        $query = ShiftPattern::with(['details.jamKerja', 'grup']);

        if (!empty($request->nama_pola)) {
            $query->where('nama_pola', 'like', '%' . $request->nama_pola . '%')
                ->orWhere('kode_pola', 'like', '%' . $request->nama_pola . '%');
        }

        $data['patterns'] = $query->orderBy('kode_pola')->paginate(15);
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.shiftpattern.index' : 'shiftpattern.index';
        return view($view, $data);
    }

    public function create()
    {
        $data['jamkerja'] = Jamkerja::orderBy('nama_jam_kerja')->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.shiftpattern.create' : 'shiftpattern.create';
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_pola' => 'required|string|max:5|unique:shift_patterns,kode_pola',
            'nama_pola' => 'required|string|max:50',
            'jumlah_hari_siklus' => 'required|integer|min:2|max:31',
            'details' => 'required|array',
            'details.*.urutan_hari' => 'required|integer|min:1',
            'details.*.kode_jam_kerja' => 'nullable|string|exists:presensi_jamkerja,kode_jam_kerja',
        ], [
            'kode_pola.required' => 'Kode Pola wajib diisi',
            'kode_pola.max' => 'Kode Pola maksimal 5 karakter',
            'kode_pola.unique' => 'Kode Pola sudah digunakan',
            'nama_pola.required' => 'Nama Pola wajib diisi',
            'jumlah_hari_siklus.required' => 'Jumlah hari siklus wajib diisi',
            'jumlah_hari_siklus.min' => 'Jumlah hari siklus minimal 2 hari',
            'jumlah_hari_siklus.max' => 'Jumlah hari siklus maksimal 31 hari',
        ]);

        DB::beginTransaction();
        try {
            $kode_pola = strtoupper(trim($request->kode_pola));

            $pattern = ShiftPattern::create([
                'kode_pola' => $kode_pola,
                'nama_pola' => trim($request->nama_pola),
                'jumlah_hari_siklus' => (int) $request->jumlah_hari_siklus,
                'keterangan' => $request->keterangan,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            foreach ($request->details as $detail) {
                if ((int) $detail['urutan_hari'] <= (int) $request->jumlah_hari_siklus) {
                    ShiftPatternDetail::create([
                        'kode_pola' => $kode_pola,
                        'urutan_hari' => (int) $detail['urutan_hari'],
                        'kode_jam_kerja' => !empty($detail['kode_jam_kerja']) ? $detail['kode_jam_kerja'] : null,
                    ]);
                }
            }

            DB::commit();
            return Redirect::route('shiftpattern.index')->with(messageSuccess('Pola Shift berhasil disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $pattern = ShiftPattern::with('details')->findOrFail($id);
        $jamkerja = Jamkerja::orderBy('nama_jam_kerja')->get();

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.shiftpattern.edit' : 'shiftpattern.edit';
        return view($view, compact('pattern', 'jamkerja'));
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $pattern = ShiftPattern::findOrFail($id);

        $request->validate([
            'nama_pola' => 'required|string|max:50',
            'jumlah_hari_siklus' => 'required|integer|min:2|max:31',
            'details' => 'required|array',
            'details.*.urutan_hari' => 'required|integer|min:1',
            'details.*.kode_jam_kerja' => 'nullable|string|exists:presensi_jamkerja,kode_jam_kerja',
        ]);

        DB::beginTransaction();
        try {
            $pattern->update([
                'nama_pola' => trim($request->nama_pola),
                'jumlah_hari_siklus' => (int) $request->jumlah_hari_siklus,
                'keterangan' => $request->keterangan,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            // Hapus detail lama, masukkan yang baru
            ShiftPatternDetail::where('kode_pola', $pattern->kode_pola)->delete();

            foreach ($request->details as $detail) {
                if ((int) $detail['urutan_hari'] <= (int) $request->jumlah_hari_siklus) {
                    ShiftPatternDetail::create([
                        'kode_pola' => $pattern->kode_pola,
                        'urutan_hari' => (int) $detail['urutan_hari'],
                        'kode_jam_kerja' => !empty($detail['kode_jam_kerja']) ? $detail['kode_jam_kerja'] : null,
                    ]);
                }
            }

            DB::commit();
            return Redirect::route('shiftpattern.index')->with(messageSuccess('Pola Shift berhasil diperbarui'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        try {
            $pattern = ShiftPattern::findOrFail($id);
            $pattern->delete(); // details cascade delete
            return Redirect::back()->with(messageSuccess('Pola Shift berhasil dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Gagal menghapus pola: ' . $e->getMessage()));
        }
    }

    /**
     * Preview endpoint: menghasilkan simulasi penanggalan dari pola shift
     */
    public function preview(Request $request)
    {
        $request->validate([
            'kode_pola' => 'required|exists:shift_patterns,kode_pola',
            'tanggal_mulai' => 'required|date',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2050',
        ]);

        try {
            $pattern = ShiftPattern::with(['details.jamKerja'])->where('kode_pola', $request->kode_pola)->first();
            if (!$pattern || $pattern->details->isEmpty()) {
                return response()->json(['error' => 'Pola tidak ditemukan atau tidak memiliki detail'], 404);
            }

            $detailsByUrutan = $pattern->details->keyBy('urutan_hari');
            $siklus = $pattern->jumlah_hari_siklus;
            $startDate = Carbon::parse($request->tanggal_mulai)->startOfDay();

            $bulan = (int) $request->bulan;
            $tahun = (int) $request->tahun;
            $firstDayOfMonth = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $daysInMonth = $firstDayOfMonth->daysInMonth;

            $preview = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $targetDate = Carbon::create($tahun, $bulan, $d)->startOfDay();

                // Hitung selisih hari dari tanggal_mulai
                $diffDays = $startDate->diffInDays($targetDate, false);

                // Modulo formula yang aman untuk selisih negatif & positif
                $dayIndex = (($diffDays % $siklus) + $siklus) % $siklus + 1; // 1-indexed

                $detail = $detailsByUrutan->get($dayIndex);
                $jamKerja = $detail ? $detail->jamKerja : null;

                $preview[] = [
                    'tanggal' => $targetDate->format('Y-m-d'),
                    'hari' => $targetDate->translatedFormat('l'),
                    'urutan_hari' => $dayIndex,
                    'is_libur' => $jamKerja === null,
                    'kode_jam_kerja' => $jamKerja ? $jamKerja->kode_jam_kerja : null,
                    'nama_jam_kerja' => $jamKerja ? $jamKerja->nama_jam_kerja : 'LIBUR (OFF)',
                    'jam_masuk' => $jamKerja ? $jamKerja->jam_masuk : '-',
                    'jam_pulang' => $jamKerja ? $jamKerja->jam_pulang : '-',
                    'color' => $jamKerja && !empty($jamKerja->color) ? $jamKerja->color : ($jamKerja ? '#007bff' : '#6c757d'),
                ];
            }

            return response()->json([
                'success' => true,
                'pola' => [
                    'kode_pola' => $pattern->kode_pola,
                    'nama_pola' => $pattern->nama_pola,
                    'jumlah_hari_siklus' => $pattern->jumlah_hari_siklus,
                ],
                'preview' => $preview,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
