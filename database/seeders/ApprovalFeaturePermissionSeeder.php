<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ApprovalFeaturePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permission Group
        $permissiongroup = Permission_group::firstOrCreate(
            ['name' => 'Approval Feature']
        );

        // 2. Create Permissions
        $permissionsList = [
            'approvalfeature.index',
            'approvalfeature.create',
            'approvalfeature.store',
            'approvalfeature.edit',
            'approvalfeature.update',
            'approvalfeature.delete'
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
