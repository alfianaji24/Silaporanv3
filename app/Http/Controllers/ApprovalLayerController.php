<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLayer;
use App\Models\Cabang;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;

class ApprovalLayerController extends Controller
{
    public function index()
    {
        $approvalLayers = ApprovalLayer::orderBy('feature')
            ->orderBy('level')
            ->get();
        return view('konfigurasi.approvallayer.index', compact('approvalLayers'));
    }

    public function create()
    {
        $roles = Role::all();
        $departemen = Departemen::all();
        $cabang = Cabang::all();
        $features = \App\Models\ApprovalFeature::all();
        return view('konfigurasi.approvallayer.create', compact('roles', 'departemen', 'cabang', 'features'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'feature' => 'required',
            'level' => 'required|integer',
            'role_name' => 'required',
        ]);

        try {
            ApprovalLayer::create([
                'feature' => $request->feature,
                'level' => $request->level,
                'role_name' => $request->role_name,
                'kode_dept' => $request->kode_dept,
                'kode_cabang' => $request->kode_cabang,
            ]);

            return Redirect::route('approvallayer.index')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $approvalLayer = ApprovalLayer::findOrFail($id);
        $roles = Role::all();
        $departemen = Departemen::all();
        $cabang = Cabang::all();
        $features = \App\Models\ApprovalFeature::all();
        return view('konfigurasi.approvallayer.edit', compact('approvalLayer', 'roles', 'departemen', 'cabang', 'features'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'feature' => 'required',
            'level' => 'required|integer',
            'role_name' => 'required',
        ]);

        try {
            $approvalLayer = ApprovalLayer::findOrFail($id);
            $approvalLayer->update([
                'feature' => $request->feature,
                'level' => $request->level,
                'role_name' => $request->role_name,
                'kode_dept' => $request->kode_dept,
                'kode_cabang' => $request->kode_cabang,
            ]);

            return Redirect::route('approvallayer.index')->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $approvalLayer = ApprovalLayer::findOrFail($id);
            $approvalLayer->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
