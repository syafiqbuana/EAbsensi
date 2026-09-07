<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'superadmin',
            'email' => 'test@example.com',
            'is_superadmin' => true,
            'password' => Hash::make('123456789'),
        ]);

        $tpqProfile = \App\Models\TpqProfile::create([
            'slug' => \App\Models\TpqProfile::generateSlug('12345678', 'TPQ Al-Ikhlas'),
            'name' => 'TPQ Al-Ikhlas',
            'registration_number' => '12345678',
            'address' => 'Jl. Kebon Jeruk No. 1',
            'contact_number' => '081234567890',
            'status' => 'active',
        ]);

        $this->callWith(RolesAndPermissionsSeeder::class, ['tpqProfile' => $tpqProfile]);
        $this->callWith(ClassesSeeder::class, ['tpqProfile' => $tpqProfile]);
        $this->callWith(StudentSeeder::class, ['tpqProfile' => $tpqProfile]);
        $this->callWith(ScheduleSeeder::class, ['tpqProfile' => $tpqProfile]);
    }
}
