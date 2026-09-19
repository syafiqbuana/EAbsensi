<?php

namespace App\Livewire\Components;

use Livewire\Component;

class MonthRangeFilter extends Component
{
    public $selectedMonth;

    public function mount()
    {
        // Default ke bulan ini (Format: YYYY-MM)
        $this->selectedMonth = now()->format('Y-m');
    }

    public function updatedSelectedMonth($value)
    {
        // Pancarkan event saat dropdown berubah
        $this->dispatch('filterMonthUpdated', month: $value);
    }
    public function render()
    {
        return view('livewire.components.month-range-filter');
    }
}
