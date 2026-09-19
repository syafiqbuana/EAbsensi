<?php

namespace App\Livewire\Components;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ScheduleList extends Component
{
    public function render()
    {   
        return view('livewire.components.schedule-list', [
            'schedulesData' => $this->buildSchedulesData(),
        ]);
    }

    private function buildSchedulesData(): array
    {
        $userId       = auth()->id();
        $now          = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        $rawSchedules = DB::table('schedules')
            ->join('schedule_class', 'schedules.id', '=', 'schedule_class.schedule_id')
            ->join('students', 'schedule_class.class_id', '=', 'students.class_id')
            ->join('student_user', 'students.id', '=', 'student_user.student_id')
            ->where('student_user.user_id', $userId)
            ->where('students.status',Student::STATUS_ACTIVE)
            ->select(
                'schedules.id',
                'schedules.name',
                'schedules.day',
                'schedules.time_open',
                'schedules.time_close',
                'students.name as student_name'
            )
            ->get();

        if ($rawSchedules->isEmpty()) {
            return [];
        }

        // Group by schedule ID → jadwal sama = 1 kalender
        $groupedSchedules = $rawSchedules->groupBy('id');
        $scheduleIds      = $groupedSchedules->keys()->toArray();

        $holidays = DB::table('holidays')
            ->leftJoin('holiday_schedule', 'holidays.id', '=', 'holiday_schedule.holiday_id')
            ->where(function ($q) use ($scheduleIds) {
                $q->where('holidays.is_global', 1)
                  ->orWhereIn('holiday_schedule.schedule_id', $scheduleIds);
            })
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('start_date', '<=', $endOfMonth->toDateString())
                  ->where('end_date', '>=', $startOfMonth->toDateString());
            })
            ->select('holidays.*', 'holiday_schedule.schedule_id')
            ->get();

        $daysMap = [
            'monday'    => 'Senin',   'tuesday'   => 'Selasa',
            'wednesday' => 'Rabu',    'thursday'  => 'Kamis',
            'friday'    => 'Jumat',   'saturday'  => 'Sabtu',
            'sunday'    => 'Minggu',
        ];

        return $groupedSchedules
            ->map(function ($group) use ($startOfMonth, $endOfMonth, $holidays, $daysMap) {
                $base = $group->first();

                // Kumpulkan semua nama murid unik dalam jadwal ini
                $studentNames = $group
                    ->pluck('student_name')
                    ->unique()
                    ->values()
                    ->toArray();

                $scheduleDaysRaw  = explode(',', $base->day);
                $scheduleDaysIndo = array_map(fn($d) => $daysMap[trim($d)] ?? $d, $scheduleDaysRaw);
                $timeFormatted    = Carbon::parse($base->time_open)->format('H:i')
                                  . ' - '
                                  . Carbon::parse($base->time_close)->format('H:i');

                // Build calendar grid
                $calendar       = [];
                $startDayOfWeek = $startOfMonth->dayOfWeekIso;

                // Padding awal bulan
                for ($i = 1; $i < $startDayOfWeek; $i++) {
                    $calendar[] = ['date' => null, 'is_scheduled' => false, 'is_holiday' => false, 'holiday_name' => null];
                }

                for ($day = 1; $day <= $endOfMonth->daysInMonth; $day++) {
                    $currentDate = $startOfMonth->copy()->addDays($day - 1);
                    $dateString  = $currentDate->toDateString();
                    $englishDay  = strtolower($currentDate->format('l'));
                    $isScheduled = in_array($englishDay, $scheduleDaysRaw);
                    $isHoliday   = false;
                    $holidayName = null;

                    if ($isScheduled) {
                        $match = $holidays->first(function ($h) use ($dateString, $base) {
                            return ($h->is_global == 1 || $h->schedule_id == $base->id)
                                && $dateString >= $h->start_date
                                && $dateString <= $h->end_date;
                        });

                        if ($match) {
                            $isHoliday   = true;
                            $holidayName = $match->name;
                        }
                    }

                    $calendar[] = [
                        'date'         => $day,
                        'is_scheduled' => $isScheduled,
                        'is_holiday'   => $isHoliday,
                        'holiday_name' => $holidayName,
                    ];
                }

                return [
                    'id'            => $base->id,
                    'name'          => $base->name,
                    'student_names' => $studentNames, // ← array, plural
                    'days_text'     => implode(', ', $scheduleDaysIndo),
                    'time_text'     => $timeFormatted,
                    'calendar'      => $calendar,
                ];
            })
            ->values()
            ->toArray();
    }
}