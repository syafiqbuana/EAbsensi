<?php

namespace App\Livewire\Components;

use Livewire\Component;

class StatusAttendanceFilter extends Component
{
    public $selectedStatus = 'all';

    public function updatedSelectedStatus($value)
    {
        $this->dispatch('filterStatusUpdated', status: $value);
    }
    public function render()
    {
        return view('livewire.components.status-attendance-filter');
    }
}
