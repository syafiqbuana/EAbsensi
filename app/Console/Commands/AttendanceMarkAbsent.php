<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Schedules;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AttendanceMarkAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set attendance status to absent when status is pending and time_close is passed';


    /**
     * Execute the console command.
     */
public function handle()
{
    $now = Carbon::now();
    $dayName = strtolower($now->format('l'));
    $currentTime = $now->format('H:i:s');

    // 1. Tanggal LAMPAU: semua pending otomatis jadi absent
    //    (tidak perlu cek time_close karena harinya sudah lewat)
    $pastUpdated = Attendance::where('status', 'pending')
        ->where('date', '<', $now->toDateString())
        ->update(['status' => 'absent']);

    // 2. Hari INI: hanya yang jadwalnya sudah lewat time_close
    $scheduleIds = Schedules::where('time_close', '<=', $currentTime)
        ->whereRaw("FIND_IN_SET(?, day)", [$dayName])
        ->pluck('id');

    $todayUpdated = 0;
    if ($scheduleIds->isNotEmpty()) {
        $todayUpdated = Attendance::whereIn('schedule_id', $scheduleIds)
            ->where('date', $now->toDateString())
            ->where('status', 'pending')
            ->update(['status' => 'absent']);
    }

    $total = $pastUpdated + $todayUpdated;

    $this->info("Berhasil mengubah {$total} status pending menjadi alfa.");
    $this->info("  └─ Tanggal lampau : {$pastUpdated}");
    $this->info("  └─ Hari ini        : {$todayUpdated}");

    return self::SUCCESS;
}
}
