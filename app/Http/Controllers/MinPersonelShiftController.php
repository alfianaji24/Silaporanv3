<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Grup;
use App\Models\Jamkerja;
use App\Models\MinPersonelShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class MinPersonelShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = MinPersonelShift::with(['cabang', 'departemen', 'grup', 'jamKerja']);

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }

        if (!empty($request->kode_grup)) {
            $query->where('kode_grup', $request->kode_grup);
        }

        $data['rules'] = $query->orderBy('kode_cabang')->orderBy('kode_dept')->orderBy('kode_grup')->paginate(20);
        $data['cabang'] = Cabang::orderBy('nama_cabang')->get();
        $data['departemen'] = Departemen::orderBy('nama_dept')->get();
        $data['grups'] = Grup::orderBy('nama_grup')->get();
        $data['jamkerja'] = Jamkerja::orderBy('nama_jam_kerja')->get();

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.minpersonel.index' : 'minpersonel.index';
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'nullable|exists:cabang,kode_cabang',
            'kode_dept' => 'required|exists:departemen,kode_dept',
            'kode_grup' => 'nullable|exists:grup,kode_grup',
            'kode_jam_kerja' => 'required|exists:presensi_jamkerja,kode_jam_kerja',
            'min_personel' => 'required|integer|min:1|max:999',
        ]);

        try {
            MinPersonelShift::updateOrCreate(
                [
                    'kode_cabang' => $request->kode_cabang ?: null,
                    'kode_dept' => $request->kode_dept,
                    'kode_grup' => $request->kode_grup ?: null,
                    'kode_jam_kerja' => $request->kode_jam_kerja,
                ],
                [
                    'min_personel' => (int) $request->min_personel,
                ]
            );

            return Redirect::back()->with(messageSuccess('Aturan minimum personel shift berhasil disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Gagal menyimpan aturan: ' . $e->getMessage()));
        }
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        try {
            MinPersonelShift::findOrFail($id)->delete();
            return Redirect::back()->with(messageSuccess('Aturan berhasil dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Gagal menghapus aturan: ' . $e->getMessage()));
        }
    }
}
