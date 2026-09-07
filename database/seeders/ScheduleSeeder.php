<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Schedules;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(\App\Models\TpqProfile $tpqProfile): void
    {
        $faker = Faker::create();
        $classes = Classes::all();

        // Butuh minimal 2 kelas untuk mendemonstrasikan Many-to-Many dengan baik
        if ($classes->count() < 2) {
            $this->command->warn('Minimal butuh 2 data kelas. Silakan seed tabel classes terlebih dahulu.');
            return;
        }

        // 1. Definisikan blueprint jadwal yang tidak saling bersinggungan.
        // Dengan memisahkan hari atau shift (pagi/siang), kita menjamin tidak ada overlap.
        $scheduleBlueprints = [
            ['days' => ['monday', 'wednesday'], 'is_morning' => true],
            ['days' => ['monday', 'wednesday'], 'is_morning' => false],
            ['days' => ['tuesday', 'thursday'], 'is_morning' => true],
            ['days' => ['tuesday', 'thursday'], 'is_morning' => false],
            ['days' => ['friday'], 'is_morning' => true],
        ];

        foreach ($scheduleBlueprints as $blueprint) {
            
            // 2. Terapkan Rules Waktu Pagi (07:00 - 11:00) & Siang (13:00 - 17:00)
            if ($blueprint['is_morning']) {
                $openHour = rand(7, 9); 
                $openMinute = rand(0, 59);
                $openTime = Carbon::createFromTime($openHour, $openMinute);
                
                $closeTime = (clone $openTime)->addMinutes(rand(45, 120));
                $maxClose = Carbon::createFromTime(11, 0);
            } else {
                $openHour = rand(13, 15);
                $openMinute = rand(0, 59);
                $openTime = Carbon::createFromTime($openHour, $openMinute);
                
                $closeTime = (clone $openTime)->addMinutes(rand(45, 120));
                $maxClose = Carbon::createFromTime(17, 0);
            }

            // 3. Batasi maximum time_close agar tidak melewati batas rules
            if ($closeTime->greaterThan($maxClose)) {
                $closeTime = $maxClose;
            }

            // 4. Buat Record Jadwal (Schedules)
            $schedule = Schedules::create([
                'tpq_profile_id' => $tpqProfile->id,
                'name' => 'Jadwal ' . ($blueprint['is_morning'] ? 'Pagi ' : 'Siang ') . ucfirst($faker->word),
                'day' => implode(',', $blueprint['days']), // Format tipe data SET MySQL
                'time_open' => $openTime->format('H:i:s'),
                'time_close' => $closeTime->format('H:i:s'),
            ]);

            // 5. Pilih beberapa kelas secara acak (Many-to-Many Relationship)
            // Mengambil 2 sampai 4 kelas secara acak dari database
            $randomClassesCount = rand(2, min(4, $classes->count()));
            $randomClassIds = $classes->random($randomClassesCount)->pluck('id')->toArray();
            
            // Attach kelas-kelas tersebut ke jadwal yang baru dibuat
            $schedule->classes()->attach($randomClassIds);
        }

        $this->command->info('Berhasil membuat jadwal Many-to-Many (Strict Pagi & Siang) tanpa overlap!');
    }
}