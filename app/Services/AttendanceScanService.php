<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Schedules;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AttendanceScanService
{
    public function scan(string $qrToken): ScanResult
    {
        $student = Student::query()
            ->with(['classes.schedules'])
            ->where('qr_token', $qrToken)
            ->first();

        if (! $student) {
            return ScanResult::danger('QR tidak dikenali. Siswa tidak ditemukan.');
        }

        $now = now();

        $schedule = $this->resolveActiveSchedule($student, $now);

        if (! $schedule) {
            return ScanResult::danger('Tidak ada jadwal yang sedang berjalan saat ini.');
        }

        if ($this->isHoliday($schedule, $now)) {
            return ScanResult::danger('Hari ini libur, absensi tidak dapat dilakukan.');
        }

        if ($this->hasActiveLeave($student, $now)) {
            return ScanResult::danger('Siswa sedang izin atau sakit, tidak dapat melakukan absensi.');
        }

        $attendance = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->where('student_id', $student->id)
            ->whereDate('date', $now->toDateString())
            ->first();

        if (! $attendance) {
            return ScanResult::danger('Belum ada sesi absensi untuk siswa ini.');
        }

        return match ($attendance->status) {
            Attendance::STATUS_PRESENT => ScanResult::info('Siswa sudah melakukan absensi hari ini.'),
            Attendance::STATUS_PERMISSION => ScanResult::danger('Siswa berstatus izin, tidak dapat melakukan absensi.'),
            Attendance::STATUS_SICK => ScanResult::danger('Siswa berstatus sakit, tidak dapat melakukan absensi.'),
            Attendance::STATUS_ABSENT => ScanResult::danger('Siswa berstatus alfa, tidak dapat melakukan absensi.'),
            Attendance::STATUS_HOLIDAY => ScanResult::danger('Hari ini libur, absensi tidak dapat dilakukan.'),
            default => $this->markPresent($attendance, $student->name),
        };
    }

    private function resolveActiveSchedule(Student $student, Carbon $now): ?Schedules
    {
        $dayName = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        $schedules = $student->classes?->schedules;

        if ($schedules === null) {
            return null;
        }

        return $schedules
            ->first(fn (Schedules $schedule) => in_array(
                $dayName,
                array_map('trim', explode(',', $schedule->day)),
            ) && $currentTime >= $schedule->time_open && $currentTime <= $schedule->time_close);
    }

    private function isHoliday(Schedules $schedule, Carbon $now): bool
    {
        return Holiday::query()
            ->whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->where(function (Builder $query) use ($schedule) {
                $query->where('is_global', true)
                    ->orWhereHas('schedules', fn (Builder $q) => $q->whereKey($schedule->id));
            })
            ->exists();
    }

    private function hasActiveLeave(Student $student, Carbon $now): bool
    {
        return $student->leaveRequests()
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->exists();
    }

    private function markPresent(Attendance $attendance, string $studentName): ScanResult
    {
        DB::transaction(function () use ($attendance) {
            Attendance::query()
                ->whereKey($attendance->getKey())
                ->lockForUpdate()
                ->firstOrFail()
                ->update([
                    'status' => Attendance::STATUS_PRESENT,
                    'time_in' => now()->format('H:i:s'),
                ]);
        });

        return ScanResult::success("Absen berhasil: {$studentName}");
    }
}
