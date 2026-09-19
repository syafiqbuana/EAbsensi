<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Schedules;
use App\Models\TpqProfile;
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
    protected $description = 'Set attendance status to absent when status is pending and time_close is passed (multi-tenant)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $dayName = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');
        $todayDate = $now->toDateString();

        // 1. Ambil semua ID TPQ yang berstatus aktif
        $activeTpqIds = TpqProfile::where('status', 'active')->pluck('id');

        if ($activeTpqIds->isEmpty()) {
            $this->info("Tidak ada TPQ yang aktif saat ini.");
            return self::SUCCESS;
        }
        $pastUpdated = Attendance::whereIn('tpq_profile_id', $activeTpqIds)
            ->where('status', 'pending')
            ->where('date', '<', $todayDate)
            ->update(['status' => 'absent']);

        $scheduleIds = Schedules::whereIn('tpq_profile_id', $activeTpqIds)
            ->where('time_close', '<=', $currentTime)
            ->whereRaw("FIND_IN_SET(?, day)", [$dayName])
            ->pluck('id');

        $todayUpdated = 0;
        if ($scheduleIds->isNotEmpty()) {
            $todayUpdated = Attendance::whereIn('schedule_id', $scheduleIds)
                ->where('date', $todayDate)
                ->where('status', 'pending')
                ->update(['status' => 'absent']);
        }

        $total = $pastUpdated + $todayUpdated;

        $this->info("Berhasil mengubah {$total} status pending menjadi absent.");
        $this->info("  └─ Tanggal lampau : {$pastUpdated}");
        $this->info("  └─ Hari ini        : {$todayUpdated}");

        return self::SUCCESS;
    }
}