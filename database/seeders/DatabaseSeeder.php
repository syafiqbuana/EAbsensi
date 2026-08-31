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

        
        $this->call(RoleSeeder::class);
        $this->call(ClassesSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(ScheduleSeeder::class);
    }
}
