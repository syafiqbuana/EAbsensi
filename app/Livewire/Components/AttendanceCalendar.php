<?php

namespace App\Livewire\Components;

use App\Models\Attendance;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;

class AttendanceCalendar extends Component
{
    public $selectedStudentId;

    public $selectedMonth;

    // Array penyimpan data presensi:
    // ['Y-m-d' => 'status']
    public $attendanceMap = [];

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');

        // Tidak perlu query Student di sini.
        // selectedStudentId akan dikirim oleh ChildCardSelector
        // melalui event studentSelected.
    }

    #[On('studentSelected')]
    public function updateStudent($studentId): void
    {
        $this->selectedStudentId = (int) $studentId;

        $this->loadCalendarData();
    }

    #[On('filterMonthUpdated')]
    public function updateMonth($month): void
    {
        $this->selectedMonth = $month;

        $this->loadCalendarData();
    }

    // Kalender tetap menampilkan semua status dalam satu bulan.
    public function loadCalendarData(): void
    {
        if (!$this->selectedStudentId) {
            $this->attendanceMap = [];

            return;
        }

        $date = Carbon::createFromFormat(
            'Y-m',
            $this->selectedMonth
        );

        $attendances = Attendance::query()
            ->where('student_id', $this->selectedStudentId)
            ->whereBetween('date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ])
            ->get([
                'date',
                'created_at',
                'status',
            ]);

        $this->attendanceMap = $attendances
            ->mapWithKeys(function ($item) {
                // Prioritaskan date.
                // Fallback ke created_at jika date null.
                $dateString = $item->date
                    ? $item->date->format('Y-m-d')
                    : $item->created_at->format('Y-m-d');

                return [
                    $dateString => $item->status,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        $targetDate = Carbon::createFromFormat(
            'Y-m',
            $this->selectedMonth
        );

        return view('livewire.components.attendance-calendar', [
            'daysInMonth' => $targetDate->daysInMonth,

            // 1 = Senin, 7 = Minggu
            // Dikurangi 1 agar offset dimulai dari 0.
            'firstDayOffset' =>
                $targetDate->copy()->startOfMonth()->isoWeekday() - 1,

            'currentMonthName' =>
                $targetDate->translatedFormat('F Y'),

            'yearMonthPrefix' =>
                $targetDate->format('Y-m-'),
        ]);
    }
}