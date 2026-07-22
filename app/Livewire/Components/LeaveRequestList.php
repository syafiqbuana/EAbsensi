<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\LeaveRequest;

class LeaveRequestList extends Component
{
    public function render()
    {
        // Mengambil data izin khusus untuk anak dari parent yang sedang login
        $leaveRequests = LeaveRequest::with('student')
            ->whereHas('student.users', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('livewire.components.leave-request-list', [
            'leaveRequests' => $leaveRequests
        ]);
    }
}