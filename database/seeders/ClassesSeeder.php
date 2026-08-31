<?php 

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Classes;

class ClassesSeeder extends Seeder
{
    public function run(): void
    {
        Classes::insert([
            ['name' => 'Kelas 1', 'order' => 1],
            ['name' => 'Kelas 2', 'order' => 2],
            ['name' => 'Kelas 3', 'order' => 3],
            ['name' => 'Kelas 4', 'order' => 4],
            ['name' => 'Kelas 5', 'order' => 5],
            ['name' => 'Kelas 6', 'order' => 6],
        ]);
    }
}