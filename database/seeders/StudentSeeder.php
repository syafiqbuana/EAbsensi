<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(\App\Models\TpqProfile $tpqProfile): void
    {
        // Initialize Faker with the Indonesian locale
        $faker = Faker::create('id_ID');

        // Fetch existing class IDs to ensure the foreign key (class_id) is valid
        $classIds = DB::table('classes')->pluck('id')->toArray();

        // Fallback in case the classes table is currently empty
        if (empty($classIds)) {
            $classIds = [1, 2, 3]; 
        }

        $students = [];

        for ($i = 0; $i < 30; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            
            $students[] = [
                'tpq_profile_id' => $tpqProfile->id,
                'name'        => $faker->name($gender),
                'birth_date'  => $faker->date('Y-m-d', 'now'),
                'birth_place' => $faker->city(),
                'class_id'    => $faker->randomElement($classIds),
                'gender'      => $gender,
                'qr_token'    => uniqid(), // Added here because insert() bypasses the model's booted() method
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        // Batch insert for better performance
        Student::insert($students);
    }
}