<?php

namespace App\Livewire\Components;

use App\Models\Student;
use App\Models\Attendance;
use App\Support\CurrentTpq;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;

class AttendanceHistoryTable extends Component
{
    use WithPagination;

    // State dari Filter
    public $selectedStudentId;
    public $selectedMonth;
    public $selectedStatus = 'all';
    public $search = '';

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');

        // Set default student seperti pada Performance Card
        $firstStudent = Student::active()
            ->where('tpq_profile_id', CurrentTpq::id())
            ->first();

        if ($firstStudent) {
            $this->selectedStudentId = $firstStudent->id;
        }
    }

    // --- EVENT LISTENERS ---

    #[On('studentSelected')]
    public function updateStudent($studentId)
    {
        $this->selectedStudentId = $studentId;
        $this->resetPage(); // Reset paginasi saat ganti anak
    }

    #[On('filterMonthUpdated')]
    public function updateMonth($month)
    {
        $this->selectedMonth = $month;
        $this->resetPage();
    }

    #[On('filterStatusUpdated')]
    public function updateStatus($status)
    {
        $this->selectedStatus = $status;
        $this->resetPage();
    }

    // Reset pagination ketika user mengetik di kotak pencarian
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Parsing bulan untuk query
        $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);

        $attendances = Attendance::query()
            // Eager loading jika ada relasi ke ustadz/pengampu untuk mencegah N+1 Query
            ->with(['schedule']) 
            ->where('student_id', $this->selectedStudentId)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            
            // Filter Status dinamis
            ->when($this->selectedStatus !== 'all', function ($query) {
                $query->where('status', $this->selectedStatus);
            })

            // Pencarian Cepat (berdasarkan nama ustadz pemindai atau catatan)
            // ->when($this->search !== '', function ($query) {
            //     $query->where(function ($subQuery) {
            //         $subQuery->whereHas('scanner', function ($q) {
            //             $q->where('name', 'like', '%' . $this->search . '%');
            //         })
            //         ->orWhere('notes', 'like', '%' . $this->search . '%');
            //     });
            // })
            
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.components.attendance-history-table', [
            'attendances' => $attendances
        ]);
    }
}