<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Schedules;
use App\Models\Holiday;
use App\Models\TpqProfile;
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
    protected $description = 'Generate sesi attendance 5 menit setelah time_open jadwal (mendukung multi-tenant & hari libur)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $dayName = strtolower($now->format('l'));
        $todayDate = $now->toDateString();

        // 1. Ambil semua TPQ yang berstatus aktif
        $tpqProfiles = TpqProfile::where('status', 'active')->get();
        $totalCreated = 0;

        if ($tpqProfiles->isEmpty()) {
            $this->info("Tidak ada TPQ yang aktif saat ini.");
            return self::SUCCESS;
        }

        foreach ($tpqProfiles as $tpq) {
            $this->info("Memproses TPQ: {$tpq->name}");

            // 2. Cek apakah hari ini adalah hari Libur Global KHUSUS untuk TPQ ini
            $isGlobalHoliday = Holiday::where('tpq_profile_id', $tpq->id)
                ->where('is_global', 1)
                ->whereDate('start_date', '<=', $todayDate)
                ->whereDate('end_date', '>=', $todayDate)
                ->exists();

            if ($isGlobalHoliday) {
                $this->info(" -> Hari ini adalah libur global untuk {$tpq->name}. Skip.");
                continue; 
            }

            // 3. Ambil jadwal beserta relasi kelas, siswa (dengan scopeActive), dan libur spesifik untuk TPQ ini
            $schedules = Schedules::where('tpq_profile_id', $tpq->id)
                ->whereRaw("FIND_IN_SET(?, day)", [$dayName])
                ->with([
                    'classes.students' => function ($query) {
                        // Menggunakan scopeActive yang telah Anda buat di model Student
                        $query->active(); 
                    }, 
                    'holidays' => function ($query) use ($todayDate) {
                        $query->whereDate('start_date', '<=', $todayDate)
                              ->whereDate('end_date', '>=', $todayDate);
                    }
                ])
                ->get();

            if ($schedules->isEmpty()) {
                $this->info(" -> Tidak ada jadwal yang cocok pada hari {$dayName}.");
                continue;
            }

            foreach ($schedules as $schedule) {
                // 4. Cek apakah jadwal ini sedang libur spesifik
                if ($schedule->holidays->isNotEmpty()) {
                    $this->info("   > Schedule #{$schedule->id} ({$schedule->name}) sedang diliburkan. Skip.");
                    continue;
                }

                $studentIds = $schedule->classes
                    ->flatMap(fn ($class) => $class->students)
                    ->pluck('id')
                    ->unique();

                if ($studentIds->isEmpty()) {
                    continue;
                }

                // 5. Cek siapa saja yang sudah absen hari ini di TPQ dan jadwal ini
                $existingAttendances = Attendance::where('tpq_profile_id', $tpq->id)
                    ->where('schedule_id', $schedule->id)
                    ->where('date', $todayDate)
                    ->whereIn('student_id', $studentIds)
                    ->pluck('student_id')
                    ->toArray();

                // Cari siswa yang BELUM ada di tabel attendance
                $missingStudentIds = array_diff($studentIds->toArray(), $existingAttendances);

                if (!empty($missingStudentIds)) {
                    $insertData = [];
                    foreach ($missingStudentIds as $studentId) {
                        $insertData[] = [
                            'tpq_profile_id' => $tpq->id, // Penambahan foreign key tenant
                            'schedule_id'    => $schedule->id,
                            'student_id'     => $studentId,
                            'date'           => $todayDate,
                            'status'         => 'pending', 
                            'created_at'     => $now,
                            'updated_at'     => $now,
                        ];
                    }

                    // Gunakan array_chunk untuk optimasi insert jika data siswa sangat banyak
                    foreach (array_chunk($insertData, 500) as $chunk) {
                        Attendance::insert($chunk);
                    }
                    
                    $created = count($insertData);
                    $totalCreated += $created;
                    $this->info("   > Schedule #{$schedule->id} ({$schedule->name}): {$created} siswa diproses.");
                }
            }
        }

        $this->info("Selesai. Total attendance baru dibuat lintas tenant: {$totalCreated}");
        return self::SUCCESS;
    }
}