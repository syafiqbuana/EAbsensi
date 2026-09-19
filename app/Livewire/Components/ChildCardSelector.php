<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ChildCardSelector extends Component
{
    public function render()
    {
        $students = \App\Services\StudentDataService::getActiveStudentsForUser(auth()->id());
        $students->load([
            'attendances' => function ($query) {
                $query->whereDate('date', \Carbon\Carbon::today());
            }
        ]);
        return view('livewire.components.child-card-selector', [
            'students' => $students
        ]);

    }
}
