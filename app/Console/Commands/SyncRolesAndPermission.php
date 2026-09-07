<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRolesAndPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

        public function handle(): int
    {
        $this->info('🔄 Syncing roles dan permissions ke database...');
        $this->newLine();

        // Bersihkan cache Spatie Permission agar perubahan langsung terbaca
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definisikan daftar permission sesuai dengan skema database Anda[cite: 1]
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

        // 2. Definisikan mapping role dan permission-nya[cite: 1]
        $rolesMapping = [
            'head_tpq' => $permissions, // head_tpq mendapatkan semua permission
            'teacher'  => [
                'manage students',
                'scan attendance',
                'input study records',
                'view reports',
            ],
            'parent'   => [], // Role parent belum memiliki spesifik permission berdasarkan skema
        ];

        $createdPermissions = 0;
        $existingPermissions = 0;

        // 3. Eksekusi Sync Permissions
        foreach ($permissions as $perm) {
            $permission = Permission::where('name', $perm)->where('guard_name', 'web')->first();
            
            if (! $permission) {
                Permission::create(['name' => $perm, 'guard_name' => 'web']);
                $this->line("   + Permission baru dibuat: {$perm}");
                $createdPermissions++;
            } else {
                $existingPermissions++;
            }
        }

        if ($existingPermissions > 0) {
            $this->line("   📋 Permission sudah ada: {$existingPermissions} permission");
        }

        $this->newLine();
        
        // 4. Eksekusi Sync Roles & Assign Permissions
        $this->info('🔄 Syncing Roles dan Assign Permissions...');
        foreach ($rolesMapping as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            
            // Sync permission ke role (otomatis menghapus yang tidak ada di array dan menambah yang baru)
            $role->syncPermissions($rolePermissions);
            
            $this->line("   + Role disync: {$roleName} (" . count($rolePermissions) . " permissions)");
        }

        $this->newLine();
        $this->info(sprintf(
            '✅ Selesai! Total Permission: %d baru, %d sudah ada.',
            $createdPermissions,
            $existingPermissions
        ));

        return self::SUCCESS;
    }
}
