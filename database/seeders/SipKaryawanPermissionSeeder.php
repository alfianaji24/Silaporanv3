<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SipKaryawanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionGroup = \App\Models\Permission_group::firstOrCreate(['name' => 'SIP']);

        $permission = Permission::firstOrCreate(
            ['name' => 'sip.index'],
            ['id_permission_group' => $permissionGroup->id]
        );

        $role = Role::where('name', 'karyawan')->first();

        if ($role) {
            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
                $this->command->info('Permission sip.index successfully added to role karyawan.');
            }
        } else {
            $this->command->error('Role karyawan not found.');
        }
    }
}
