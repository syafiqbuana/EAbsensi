<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage tpq profile',
            'manage users',
            'manage classes',
            'manage schedules',
            'manage students',
            'scan attendance',
            'input study records',
            'manage holidays',
            'approve leave requests',
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $headTpq = Role::firstOrCreate(['name' => 'head_tpq', 'guard_name' => 'web']);
        $headTpq->syncPermissions($permissions); // semua permission

        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacher->syncPermissions([
            'manage students',
            'scan attendance',
            'input study records',
            'view reports',
        ]);
    }
}
