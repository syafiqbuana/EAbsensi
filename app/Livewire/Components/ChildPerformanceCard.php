<?php

namespace App\Livewire\Components;

use App\Models\Attendance;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Carbon;

class ChildPerformanceCard extends Component
{
    public $selectedStudentId;

    public $selectedMonth;

    public $attendanceStats = [
        'ratio' => 0,
        'present' => 0,
        'leave' => 0,
        'absent' => 0,
    ];

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');

        // Tidak perlu query Student di sini.
        // selectedStudentId akan dikirim oleh ChildCardSelector.
    }

    #[On('studentSelected')]
    public function updateStudent($studentId)
    {
        $this->selectedStudentId = (int) $studentId;

        $this->loadStudentStats();
    }

    #[On('filterMonthUpdated')]
    public function updateMonth($month)
    {
        $this->selectedMonth = $month;

        $this->loadStudentStats();
    }

    public function loadStudentStats()
    {
        if (!$this->selectedStudentId) {
            $this->attendanceStats = [
                'ratio' => 0,
                'present' => 0,
                'leave' => 0,
                'absent' => 0,
            ];

            return;
        }

        $date = Carbon::createFromFormat(
            'Y-m',
            $this->selectedMonth
        );

        $stats = Attendance::query()
            ->where('student_id', $this->selectedStudentId)
            ->whereBetween('date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ])
            ->selectRaw('
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as leave_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as absent_count,
                COUNT(*) as total_days
            ', [
                Attendance::STATUS_PRESENT,
                Attendance::STATUS_SICK,
                Attendance::STATUS_PERMISSION,
                Attendance::STATUS_ABSENT,
            ])
            ->first();

        $present = (int) $stats->present_count;
        $leave = (int) $stats->leave_count;
        $absent = (int) $stats->absent_count;
        $totalDays = (int) $stats->total_days;

        $ratio = $totalDays > 0
            ? round(($present / $totalDays) * 100)
            : 0;

        $this->attendanceStats = [
            'ratio' => $ratio,
            'present' => $present,
            'leave' => $leave,
            'absent' => $absent,
        ];
    }

    public function render()
    {
        return view(
            'livewire.components.child-performance-card'
        );
    }
}