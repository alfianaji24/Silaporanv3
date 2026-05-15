<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TrackingKaryawanPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Tracking Karyawan']);

        Permission::firstOrCreate(
            ['name' => 'trackingkaryawan.index'],
            ['id_permission_group' => $permissiongroup->id]
        );

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();
        $roleID = 1; // Super Admin
        $role = Role::findById($roleID);
        foreach ($permissions as $permission) {
            if ($role && !$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
