<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShiftPatternPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Pola Shift']);

        $perms = [
            'shiftpattern.index',
            'shiftpattern.create',
            'shiftpattern.edit',
            'shiftpattern.delete',
        ];

        foreach ($perms as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $permissiongroup->id]);
        }

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();
        
        // Give to super admin and role 1
        $roles = Role::whereIn('name', ['super admin', 'admin'])->orWhere('id', 1)->get();
        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
