<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Schedules;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAttendanceSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:generate-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sesi attendance 5 menit setelah time_open jadwal';

    /**
     * Execute the console command.
     */
public function handle()
{
$now = Carbon::now();
    $dayName = strtolower($now->format('l')); 
    
    // Waktu batas atas (5 menit yang lalu)
    $endTime = $now->copy()->subMinutes(5)->format('H:i:00'); 
    
    // Waktu batas bawah toleransi server mati (misal kita set 2 jam ke belakang)
    // Sesuaikan durasi ini dengan kebutuhanmu
    $startTime = $now->copy()->subMinutes(120)->format('H:i:00'); 

    // Cari jadwal yang time_open-nya berada di antara startTime dan endTime
    $schedules = Schedules::whereBetween('time_open', [$startTime, $endTime])
        ->whereRaw("FIND_IN_SET(?, day)", [$dayName])
        ->with('classes.students')
        ->get();

    if ($schedules->isEmpty()) {
        $this->info("Tidak ada jadwal yang cocok pada {$now->format('Y-m-d H:i')}.");
        return self::SUCCESS;
    }

    $created = 0;

    foreach ($schedules as $schedule) {
        $studentIds = $schedule->classes
            ->flatMap(fn ($class) => $class->students)
            ->pluck('id')
            ->unique();

        if ($studentIds->isEmpty()) {
            continue;
        }

        // Cek siapa saja yang sudah absen hari ini dalam 1 Query (mengurangi beban N+1 SELECT)
        $existingAttendances = Attendance::where('schedule_id', $schedule->id)
            ->where('date', $now->toDateString())
            ->whereIn('student_id', $studentIds)
            ->pluck('student_id')
            ->toArray();

        // Cari siswa yang BELUM ada di tabel attendance
        $missingStudentIds = array_diff($studentIds->toArray(), $existingAttendances);

        if (!empty($missingStudentIds)) {
            // Siapkan data untuk BULK INSERT
            $insertData = [];
            foreach ($missingStudentIds as $studentId) {
                $insertData[] = [
                    'schedule_id' => $schedule->id,
                    'student_id'  => $studentId,
                    'date'        => $now->toDateString(),
                    'status'      => 'pending',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // Lakukan 1 kali INSERT per jadwal, bukan berkali-kali!
            Attendance::insert($insertData);
            $created += count($insertData);
        }

        $this->info("Schedule #{$schedule->id} ({$schedule->name}): " . count($missingStudentIds) . " siswa diproses.");
    }

    $this->info("Selesai. Total attendance baru dibuat: {$created}");
    return self::SUCCESS;
}
}
