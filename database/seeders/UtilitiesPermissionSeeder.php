<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UtilitiesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permission Group
        $permissiongroup = Permission_group::firstOrCreate(
            ['name' => 'Utilities']
        );

        // 2. Create Permissions
        $permissionsList = [
            'users.index',
            'users.create',
            'users.edit',
            'users.update',
            'users.delete',
            'roles.index',
            'roles.create',
            'roles.edit',
            'roles.update',
            'roles.delete',
            'permissions.index',
            'permissions.create',
            'permissions.edit',
            'permissions.update',
            'permissions.delete',
            'permissiongroups.index',
            'permissiongroups.create',
            'permissiongroups.edit',
            'permissiongroups.update',
            'permissiongroups.delete',
            'resetdata.index',
            'resetdata.reset'
        ];

        foreach ($permissionsList as $permName) {
            Permission::firstOrCreate(
                ['name' => $permName],
                ['id_permission_group' => $permissiongroup->id]
            );
        }

        // 3. Assign to Super Admin
        $role_super_admin = Role::findById(1);
        if ($role_super_admin) {
            foreach ($permissionsList as $permName) {
                if (!$role_super_admin->hasPermissionTo($permName)) {
                    $role_super_admin->givePermissionTo($permName);
                }
            }
        }
    }
}
