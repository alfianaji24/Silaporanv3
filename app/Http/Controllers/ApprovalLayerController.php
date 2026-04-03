<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLayer;
use App\Models\Cabang;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class ApprovalLayerController extends Controller
{
    public function index()
    {
        $approvalLayers = ApprovalLayer::orderBy('feature')
            ->orderBy('level')
            ->get();
        $cabangs = Cabang::all();
        $departemens = Departemen::all();
        $jabatans = \App\Models\Jabatan::all();

        return view('konfigurasi.approvallayer.index', compact('approvalLayers', 'cabangs', 'departemens', 'jabatans'));
    }

    private function applyTargetFilters($query, $kode_cabang, $kode_dept, $kode_jabatan)
    {
        if (Schema::hasColumn('approval_layers', 'kode_cabang')) {
            if (!empty($kode_cabang) && $kode_cabang !== 'ALL') {
                $query->where('kode_cabang', $kode_cabang);
            } else {
                $query->whereNull('kode_cabang');
            }
        }

        if (Schema::hasColumn('approval_layers', 'kode_dept')) {
            if (!empty($kode_dept) && $kode_dept !== 'ALL') {
                $query->where('kode_dept', $kode_dept);
            } else {
                $query->whereNull('kode_dept');
            }
        }

        if (Schema::hasColumn('approval_layers', 'kode_jabatan')) {
            if (!empty($kode_jabatan) && $kode_jabatan !== 'ALL') {
                $query->where('kode_jabatan', $kode_jabatan);
            } else {
                $query->whereNull('kode_jabatan');
            }
        }

        return $query;
    }

    public function create()
    {
        $roles = Role::all();
        $departemen = Departemen::all();
        $cabang = Cabang::all();
        $jabatan = \App\Models\Jabatan::all();
        return view('konfigurasi.approvallayer.create', compact('roles', 'departemen', 'cabang', 'jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_names' => 'required|array|min:1',
            'role_names.*' => 'required|string',
        ]);

        try {
            // Delete existing configurations for this combination to prevent duplicates
            $query = ApprovalLayer::where('feature', 'IZIN');
            $query = $this->applyTargetFilters($query, $request->kode_cabang, $request->kode_dept, $request->kode_jabatan);
            $query->delete();

            $level = 1;
            foreach ($request->role_names as $role) {
                $payload = [
                    'feature' => 'IZIN',
                    'level' => $level,
                    'role_name' => $role,
                ];

                if (Schema::hasColumn('approval_layers', 'kode_cabang')) {
                    $payload['kode_cabang'] = !empty($request->kode_cabang) ? $request->kode_cabang : null;
                }
                if (Schema::hasColumn('approval_layers', 'kode_dept')) {
                    $payload['kode_dept'] = !empty($request->kode_dept) ? $request->kode_dept : null;
                }
                if (Schema::hasColumn('approval_layers', 'kode_jabatan')) {
                    $payload['kode_jabatan'] = !empty($request->kode_jabatan) ? $request->kode_jabatan : null;
                }

                ApprovalLayer::create($payload);
                $level++;
            }

            return Redirect::route('approvallayer.index')->with(['success' => 'Data Konfigurasi Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function editGroup(Request $request)
    {
        $kode_cabang = $request->query('cabang') ?? 'ALL';
        $kode_dept = $request->query('dept') ?? 'ALL';
        $kode_jabatan = $request->query('jabatan') ?? 'ALL';

        $query = ApprovalLayer::where('feature', 'IZIN');
        $query = $this->applyTargetFilters($query, $kode_cabang, $kode_dept, $kode_jabatan);

        $approvalLayers = $query->orderBy('level')->get();

        $roles = Role::all();
        $departemen = Departemen::all();
        $cabang = Cabang::all();
        $jabatan = \App\Models\Jabatan::all();

        return view('konfigurasi.approvallayer.edit', compact(
            'approvalLayers', 'roles', 'departemen', 'cabang', 'jabatan',
            'kode_cabang', 'kode_dept', 'kode_jabatan'
        ));
    }

    public function updateGroup(Request $request)
    {
        $request->validate([
            'role_names' => 'required|array|min:1',
            'role_names.*' => 'required|string',
        ]);

        try {
            // Convert empty strings to null (from "Semua" options in form)
            $old_cabang = !empty($request->old_kode_cabang) && $request->old_kode_cabang !== 'ALL' ? $request->old_kode_cabang : null;
            $old_dept = !empty($request->old_kode_dept) && $request->old_kode_dept !== 'ALL' ? $request->old_kode_dept : null;
            $old_jabatan = !empty($request->old_kode_jabatan) && $request->old_kode_jabatan !== 'ALL' ? $request->old_kode_jabatan : null;

            $new_cabang = !empty($request->kode_cabang) ? $request->kode_cabang : null;
            $new_dept = !empty($request->kode_dept) ? $request->kode_dept : null;
            $new_jabatan = !empty($request->kode_jabatan) ? $request->kode_jabatan : null;

            // Delete existing configs for the *original* combination being edited
            $deleteOldQuery = ApprovalLayer::where('feature', 'IZIN');
            $deleteOldQuery = $this->applyTargetFilters($deleteOldQuery, $old_cabang, $old_dept, $old_jabatan);
            $deleteOldQuery->delete();

            // Also delete if the new target already exists to prevent duplication
            $deleteNewQuery = ApprovalLayer::where('feature', 'IZIN');
            $deleteNewQuery = $this->applyTargetFilters($deleteNewQuery, $new_cabang, $new_dept, $new_jabatan);
            $deleteNewQuery->delete();

            $level = 1;
            foreach ($request->role_names as $role) {
                $payload = [
                    'feature' => 'IZIN',
                    'level' => $level,
                    'role_name' => $role,
                ];

                if (Schema::hasColumn('approval_layers', 'kode_cabang')) {
                    $payload['kode_cabang'] = $new_cabang;
                }
                if (Schema::hasColumn('approval_layers', 'kode_dept')) {
                    $payload['kode_dept'] = $new_dept;
                }
                if (Schema::hasColumn('approval_layers', 'kode_jabatan')) {
                    $payload['kode_jabatan'] = $new_jabatan;
                }

                ApprovalLayer::create($payload);
                $level++;
            }

            return Redirect::route('approvallayer.index')->with(['success' => 'Data Konfigurasi Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function destroyGroup(Request $request)
    {
        try {
            $kode_cabang = (!empty($request->query('cabang')) && $request->query('cabang') !== 'ALL') ? $request->query('cabang') : null;
            $kode_dept = (!empty($request->query('dept')) && $request->query('dept') !== 'ALL') ? $request->query('dept') : null;
            $kode_jabatan = (!empty($request->query('jabatan')) && $request->query('jabatan') !== 'ALL') ? $request->query('jabatan') : null;

            $deleteQuery = ApprovalLayer::where('feature', 'IZIN');
            $deleteQuery = $this->applyTargetFilters($deleteQuery, $kode_cabang, $kode_dept, $kode_jabatan);
            $deleteQuery->delete();

            return Redirect::back()->with(['success' => 'Data Konfigurasi Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
