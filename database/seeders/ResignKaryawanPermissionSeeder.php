<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ResignKaryawanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Resign Karyawan']);

        $permissions_to_seed = [
            'resign.index',
            'resign.create',
            'resign.store',
            'resign.delete',
        ];

        foreach ($permissions_to_seed as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $permissiongroup->id]);
        }

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();
        $roleID = 1;
        $role = Role::findById($roleID);
        foreach ($permissions as $permission) {
             if ($role && !$role->hasPermissionTo($permission)) {
                 $role->givePermissionTo($permission);
             }
        }
    }
}
