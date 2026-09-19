<?php

namespace App\Livewire\Components;

use App\Models\Student;
use Livewire\Attributes\On;
use Livewire\Component;

class QrCodeCard extends Component
{

    public ?Student $student = null;

    public function mount()
    {
        $students = \App\Services\StudentDataService::getActiveStudentsForUser(auth()->id());
        $this->student = $students->first();
    }

    #[On('studentSelected')]
    public function loadStudent($studentId)
    {
        $students = \App\Services\StudentDataService::getActiveStudentsForUser(auth()->id());
        $this->student = $students->firstWhere('id', $studentId);
    }

    public function render()
    {
        return view('livewire.components.qr-code-card');
    }
}
