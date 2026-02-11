<?php

namespace App\Http\Controllers;

use App\Models\ApprovalFeature;
use App\Models\ApprovalLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class ApprovalFeatureController extends Controller
{
    public function index()
    {
        $features = ApprovalFeature::orderBy('feature')->get();
        return view('konfigurasi.approvalfeature.index', compact('features'));
    }

    public function create()
    {
        return view('konfigurasi.approvalfeature.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'feature' => 'required|unique:approval_features,feature',
            'name' => 'required',
        ]);

        try {
            ApprovalFeature::create([
                'feature' => strtoupper($request->feature),
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return Redirect::route('approvalfeature.index')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $feature = ApprovalFeature::findOrFail($id);
        return view('konfigurasi.approvalfeature.edit', compact('feature'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'feature' => 'required|unique:approval_features,feature,' . $id,
            'name' => 'required',
        ]);

        try {
            $feature = ApprovalFeature::findOrFail($id);
            $feature->update([
                'feature' => strtoupper($request->feature),
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return Redirect::route('approvalfeature.index')->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $feature = ApprovalFeature::findOrFail($id);
            ApprovalLayer::where('feature', $feature->feature)->delete();
            $feature->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function showConfig($id)
    {
        $feature = ApprovalFeature::findOrFail($id);
        $approvalLayers = ApprovalLayer::where('feature', $feature->feature)
            ->orderBy('level')
            ->get();
        $roles = Role::all();
        // Filter roles that are already assigned to layers to avoid duplication in 'available roles' if needed
        // But for simplicity, we might just list all roles in a "Available" bucket and let user drag them.
        // Actually, a better UX for this specific case ("drag to order") is:
        // 1. List of ALL available roles (draggable source).
        // 2. List of Active Layers (draggable target).
        // OR simply one list if we just want to reorder existing ones.
        // The user said: "list roles nya gitu yang tinggal drag and drop" -> implies selecting from roles.
        
        return view('konfigurasi.approvalfeature.config', compact('feature', 'approvalLayers', 'roles'));
    }

    public function updateConfig(Request $request, $id)
    {
        $feature = ApprovalFeature::findOrFail($id);
        $roles = $request->roles; // Array of role names

        DB::beginTransaction();
        try {
            // delete existing layers for this feature
            ApprovalLayer::where('feature', $feature->feature)->delete();

            if (!empty($roles)) {
                foreach ($roles as $index => $roleName) {
                    ApprovalLayer::create([
                        'feature' => $feature->feature,
                        'level' => $index + 1,
                        'role_name' => $roleName,
                        'kode_dept' => null, // Global for now as per simplicity
                        'kode_cabang' => null
                    ]);
                }
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Konfigurasi Approval Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
