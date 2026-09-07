<?php 

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Classes;

class ClassesSeeder extends Seeder
{
    public function run(\App\Models\TpqProfile $tpqProfile): void
    {
        Classes::insert([
            ['tpq_profile_id' => $tpqProfile->id, 'name' => 'Kelas 1', 'order' => 1],
            ['tpq_profile_id' => $tpqProfile->id, 'name' => 'Kelas 2', 'order' => 2],
            ['tpq_profile_id' => $tpqProfile->id, 'name' => 'Kelas 3', 'order' => 3],
            ['tpq_profile_id' => $tpqProfile->id, 'name' => 'Kelas 4', 'order' => 4],
            ['tpq_profile_id' => $tpqProfile->id, 'name' => 'Kelas 5', 'order' => 5],
            ['tpq_profile_id' => $tpqProfile->id, 'name' => 'Kelas 6', 'order' => 6],
        ]);
    }
}