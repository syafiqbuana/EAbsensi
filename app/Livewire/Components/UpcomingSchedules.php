<?php

namespace App\Livewire\Components;

use App\Support\CurrentTpq;
use Carbon\Carbon;
use Livewire\Component;

class UpcomingSchedules extends Component
{
    public array $upcomingSchedules = [];

    public $time;

    public $today;

    public function mount()
    {
        $now = now();

        $this->time = $now->format('H:i:s');
        $this->today = strtolower($now->englishDayOfWeek);

        // Batas maksimal: besok jam 23:59:59
        $endOfTomorrow = Carbon::tomorrow()->endOfDay();

        $dayTranslations = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        $user = auth()->user();

        /*
         * Ambil hanya student yang berada
         * pada TPQ aktif.
         */
        $students = CurrentTpq::where(
            $user->students()
        )
            ->with('classes.schedules')
            ->get();

        $this->upcomingSchedules = $students
            ->filter(fn ($student) => $student->classes)
            ->flatMap(function ($student) use (
                $now,
                $endOfTomorrow,
                $dayTranslations
            ) {
                return $student->classes->schedules
                    ->flatMap(function ($schedule) use (
                        $now,
                        $endOfTomorrow,
                        $student,
                        $dayTranslations
                    ) {
                        $days = $schedule->day;

                        $occurrences = [];

                        foreach ($days as $dayName) {
                            $dayName = trim($dayName);

                            if (empty($dayName)) {
                                continue;
                            }

                            try {
                                $scheduleTime = Carbon::parse(
                                    "{$dayName} {$schedule->time_open}"
                                );

                                // Abaikan jadwal hari ini
                                if ($scheduleTime->isToday()) {
                                    continue;
                                }

                                // Jika sudah lewat, geser ke minggu depan
                                if ($scheduleTime->isPast()) {
                                    $scheduleTime->addWeek();
                                }

                                // Abaikan jika lebih dari besok
                                if ($scheduleTime->greaterThan($endOfTomorrow)) {
                                    continue;
                                }

                                $occurrences[] = [
                                    '_key' => $schedule->id . '|' . $scheduleTime->timestamp,
                                    'name' => $schedule->name,
                                    'next_day' => $dayTranslations[strtolower($dayName)]
                                        ?? ucfirst($dayName),
                                    'next_ts' => $scheduleTime->timestamp,
                                    'next_date' => $scheduleTime->translatedFormat('d F Y'),
                                    'time_open' => Carbon::parse(
                                        $schedule->time_open
                                    )->format('H:i'),
                                    'time_close' => Carbon::parse(
                                        $schedule->time_close
                                    )->format('H:i'),
                                    'student_name' => $student->name,
                                ];
                            } catch (\Exception $e) {
                                //
                            }
                        }

                        return $occurrences;
                    });
            })
            ->sortBy('next_ts')
            ->groupBy('_key')
            ->map(function ($group) {
                $first = $group->first();

                $first['student_names'] = $group
                    ->pluck('student_name')
                    ->unique()
                    ->values()
                    ->join(' & ');

                return $first;
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.components.upcoming-schedules');
    }
}