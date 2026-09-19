<?php

namespace App\Livewire\Components;

use App\Models\Attendance;
use App\Services\StudentDataService;
use Livewire\Component;

class StatOverview extends Component
{
    public $students;
    public $schedules;

    public $totalAbsence;

    public function mount()
    {
        $userId = auth()->id();

        $students = StudentDataService::getActiveStudentsForUser($userId);

        $this->students = $students->count();

        $this->schedules = $students
            ->pluck('classes')
            ->filter()
            ->flatMap->schedules
            ->unique('id')
            ->count();

        $studentIds = $students->pluck('id');

        $this->totalAbsence = Attendance::whereIn('student_id', $studentIds)
            ->whereIn('status', [
                'sick',
                'permission',
                'absent',
            ])
            ->count();
    }

    public function render()
    {
        return view('livewire.components.stat-overview');
    }
}