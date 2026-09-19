<?php

namespace App\Livewire\Components;

use App\Models\Student;
use Livewire\Attributes\On;
use Livewire\Component;

class ChildProfileCard extends Component
{
    public ?Student $student = null;

    public function mount(): void
    {
        $students = \App\Services\StudentDataService::getActiveStudentsForUser(auth()->id());
        $this->student = $students->first();

        if ($this->student) {
            $this->student->load(['attendances' => fn ($query) => $query->whereDate('date', today())]);
        }
    }

    #[On('studentSelected')]
    public function loadStudent($studentId): void
    {
        $students = \App\Services\StudentDataService::getActiveStudentsForUser(auth()->id());
        $this->student = $students->firstWhere('id', $studentId);

        if ($this->student) {
            $this->student->load(['attendances' => fn ($query) => $query->whereDate('date', today())]);
        }
    }


    public function render()
    {
        return view('livewire.components.child-profile-card');
    }
}