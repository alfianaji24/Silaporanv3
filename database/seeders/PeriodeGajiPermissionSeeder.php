<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PeriodeGajiPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Periode Gaji']);

        Permission::firstOrCreate(['name' => 'periodegaji.index'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'periodegaji.create'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'periodegaji.edit'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'periodegaji.delete'], ['id_permission_group' => $permissiongroup->id]);
        Permission::firstOrCreate(['name' => 'periodegaji.setting'], ['id_permission_group' => $permissiongroup->id]);

        $permissions = Permission::where('id_permission_group', $permissiongroup->id)->get();

        // Assign ke Super Admin (Role ID 1 / 'super admin')
        $roleSuperAdmin = Role::where('name', 'super admin')->orWhere('id', 1)->first();
        if ($roleSuperAdmin) {
            foreach ($permissions as $permission) {
                if (!$roleSuperAdmin->hasPermissionTo($permission)) {
                    $roleSuperAdmin->givePermissionTo($permission);
                }
            }
            $this->command->info('Permissions periode gaji diberikan ke role: ' . $roleSuperAdmin->name);
        }

        // Assign ke Master Admin (Role ID 4 / 'master admin')
        $roleMasterAdmin = Role::where('name', 'master admin')->orWhere('id', 4)->first();
        if ($roleMasterAdmin) {
            foreach ($permissions as $permission) {
                if (!$roleMasterAdmin->hasPermissionTo($permission)) {
                    $roleMasterAdmin->givePermissionTo($permission);
                }
            }
            $this->command->info('Permissions periode gaji diberikan ke role: ' . $roleMasterAdmin->name);
        }
    }
}
