<?php

namespace App\Http\Controllers;

use App\Models\Permission_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission::query();
        $query->select('permissions.id', 'permissions.name', 'permission_groups.name as group_name');
        $query->join('permission_groups', 'permissions.id_permission_group', '=', 'permission_groups.id');
        
        if (!empty($request->name)) {
            $query->where('permissions.name', 'like', '%' . $request->name . '%');
        }

        if (!empty($request->id_permission_group)) {
            $query->where('id_permission_group', $request->id_permission_group);
        }

        $query->orderBy('id_permission_group')->orderBy('permissions.name');
        $permissions = $query->paginate(15);
        $permissions->appends($request->all());

        $permission_groups = Permission_group::orderBy('name')->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.permissions.index' : 'settings.permissions.index';
        return view($view, compact('permissions', 'permission_groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permission_groups = Permission_group::orderBy('name')->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.permissions.create' : 'settings.permissions.create';
        return view($view, compact('permission_groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'id_permission_group' => 'required'
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah digunakan.',
            'id_permission_group.required' => 'Pilih group modul permission.',
        ]);

        try {
            Permission::create([
                'name' => strtolower($request->name),
                'id_permission_group' => $request->id_permission_group
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $permission = Permission::where('id', $decryptedId)->firstOrFail();
        } catch (\Exception $e) {
            $permission = Permission::where('id', $id)->firstOrFail();
        }

        $permission_groups = Permission_group::orderBy('name')->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.permissions.edit' : 'settings.permissions.edit';
        return view($view, compact('permission_groups', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $request->validate([
            'name' => 'required|unique:permissions,name,' . $decryptedId,
            'id_permission_group' => 'required'
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah digunakan.',
            'id_permission_group.required' => 'Pilih group modul permission.',
        ]);

        try {
            Permission::where('id', $decryptedId)->update([
                'name' => strtolower($request->name),
                'id_permission_group' => $request->id_permission_group
            ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        try {
            Permission::where('id', $decryptedId)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }
}

