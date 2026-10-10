<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class KaryawanLockUnlockAksesWebPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari atau buat permission group Karyawan
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Karyawan']);

        // Pastikan permission karyawan.lockunlockaksesweb terdaftar
        $permission = Permission::firstOrCreate([
            'name' => 'karyawan.lockunlockaksesweb',
        ], [
            'id_permission_group' => $permissiongroup->id
        ]);

        $this->command->info('Permission karyawan.lockunlockaksesweb dipastikan ada.');

        // Berikan permission ke Super Admin (Role ID 1) dan Master Admin (Role ID 4) jika ada
        $roleSuperAdmin = Role::where('name', 'super admin')->orWhere('id', 1)->first();
        if ($roleSuperAdmin && !$roleSuperAdmin->hasPermissionTo($permission)) {
            $roleSuperAdmin->givePermissionTo($permission);
            $this->command->info('Permission karyawan.lockunlockaksesweb diberikan ke role: ' . $roleSuperAdmin->name);
        }

        $roleMasterAdmin = Role::where('name', 'master admin')->orWhere('id', 4)->first();
        if ($roleMasterAdmin && !$roleMasterAdmin->hasPermissionTo($permission)) {
            $roleMasterAdmin->givePermissionTo($permission);
            $this->command->info('Permission karyawan.lockunlockaksesweb diberikan ke role: ' . $roleMasterAdmin->name);
        }
    }
}
