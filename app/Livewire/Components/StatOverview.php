<?php

namespace App\Livewire\Components;

use App\Models\Attendance;
use Livewire\Component;

class StatOverview extends Component
{
    public $students;
    public $schedules;

    public $totalAbsence;

    public function mount()
    {
        $user = auth()->user()->load('students.classes.schedules');

        $this->students = $user->students->count();

        $this->schedules = $user->students
            ->pluck('classes')
            ->filter()
            ->flatMap->schedules
            ->unique('id')
            ->count();

        $studentIds = $user->students->pluck('id');

        $this->totalAbsence = Attendance::whereIn('student_id',$studentIds)->whereIn('status',['sick','permission','absent'])->count();
    }

    public function render()
    {
        return view('livewire.components.stat-overview');
    }
}