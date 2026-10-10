<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class JadwalKerjaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Group: Periode Jadwal
        $groupPeriode = Permission_group::firstOrCreate(['name' => 'Periode Jadwal']);
        $permsPeriode = [
            'jadwalperiode.index',
            'jadwalperiode.create',
            'jadwalperiode.edit',
            'jadwalperiode.publish',
            'jadwalperiode.delete',
        ];
        foreach ($permsPeriode as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $groupPeriode->id]);
        }

        // 2. Group: Roster Board
        $groupRoster = Permission_group::firstOrCreate(['name' => 'Roster Board']);
        $permsRoster = [
            'roster.index',
            'roster.edit',
        ];
        foreach ($permsRoster as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $groupRoster->id]);
        }

        // 3. Group: Minimum Personel Shift
        $groupMinPersonel = Permission_group::firstOrCreate(['name' => 'Minimum Personel Shift']);
        $permsMinPersonel = [
            'minpersonelshift.index',
        ];
        foreach ($permsMinPersonel as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $groupMinPersonel->id]);
        }

        // Ambil semua permissions yang baru dibuat
        $allJadwalPerms = array_merge($permsPeriode, $permsRoster, $permsMinPersonel);
        $permissions = Permission::whereIn('name', $allJadwalPerms)->get();

        // Assign to Super Admin and Admin (Role ID 1 or names)
        $adminRoles = Role::whereIn('name', ['super admin', 'admin', 'administrator', 'hrd'])->orWhere('id', 1)->get();
        foreach ($adminRoles as $role) {
            foreach ($permissions as $permission) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        // Role Supervisor (roster.index & ajuanjadwal)
        $roleSupervisor = Role::where('name', 'supervisor')->first();
        if ($roleSupervisor) {
            $supervisorPerms = Permission::whereIn('name', ['roster.index'])->get();
            foreach ($supervisorPerms as $perm) {
                if (!$roleSupervisor->hasPermissionTo($perm)) {
                    $roleSupervisor->givePermissionTo($perm);
                }
            }
        }
    }
}
