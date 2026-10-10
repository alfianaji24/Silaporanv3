<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PenjadwalanCardPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Penjadwalan Card']);

        $perms = [
            'penjadwalancard.index',
            'penjadwalancard.create',
            'penjadwalancard.edit',
            'penjadwalancard.delete',
            'penjadwalancard.atur',
        ];

        foreach ($perms as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm],
                ['id_permission_group' => $permissiongroup->id]
            );
        }

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();

        // Assign permissions to Admin & Super Admin roles
        $roles = Role::whereIn('name', ['super admin', 'admin', 'administrator', 'hrd', 'master admin'])->orWhere('id', 1)->get();
        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
