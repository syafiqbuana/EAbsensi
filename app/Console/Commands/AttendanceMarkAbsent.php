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

    // Batas atas (waktu sekarang)
    $endTime = $now->format('H:i:00');
    
    // Batas bawah toleransi (misal 2 jam ke belakang)
    $startTime = $now->copy()->subMinutes(120)->format('H:i:00');

    // Cari ID jadwal yang sudah melewati time_close
    $scheduleIds = Schedules::whereBetween('time_close', [$startTime, $endTime])
        ->whereRaw("FIND_IN_SET(?, day)", [$dayName])
        ->pluck('id');

    if ($scheduleIds->isEmpty()) {
        $this->info("Belum ada jadwal yang melewati time_close saat ini.");
        return self::SUCCESS;
    }

    // Update massal: Ubah pending -> alfa HANYA untuk jadwal yang sudah tutup hari ini
    $updatedCount = Attendance::whereIn('schedule_id', $scheduleIds)
        ->where('date', $now->toDateString())
        ->where('status', 'pending')
        ->update(['status' => 'absent']);

    $this->info("Berhasil mengubah {$updatedCount} status pending menjadi alfa.");
    return self::SUCCESS;
}
}
