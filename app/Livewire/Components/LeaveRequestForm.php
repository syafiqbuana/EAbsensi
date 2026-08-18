<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Jobs\SendLeaveRequestNotification;
use Flux\Flux;

class LeaveRequestForm extends Component
{
    use WithFileUploads;

    #[Validate('required|exists:students,id')]
    public $student_id = '';

    #[Validate('required|date|after_or_equal:today')]
    public $start_date = '';

    #[Validate('required|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('required|in:permission,sick')]
    public $type = '';

    #[Validate('required|string|max:500')]
    public $description = '';

    #[Validate('nullable|image|max:2048')]
    public $proof;

    public $students = [];

    public function mount()
    {
        $this->students = Student::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
    }

    public function save()
    {
        $this->validate();

        try {
            $proofPath = $this->proof ? $this->proof->store('proofs', 'public') : null;

            // Kita TIDAK memasukkan 'total_days'. 
            // Model LeaveRequest akan otomatis menghitungnya di event 'creating'
            $leaveRequest = LeaveRequest::create([
                'student_id'  => $this->student_id,
                'start_date'  => $this->start_date,
                'end_date'    => $this->end_date,
                'type'        => $this->type,
                'description' => $this->description,
                'proof'       => $proofPath,
                'status'      => 'pending',
                'created_by'  => auth()->id()
            ]);

            // SendLeaveRequestNotification::dispatch($leaveRequest);

            session()->flash('toast_heading', 'Berhasil Terkirim!');
            session()->flash('toast_text', 'Permohonan izin Anda sedang menunggu persetujuan admin.');
            session()->flash('toast_variant', 'success');

            $this->redirect(route('leaveRequest'), navigate: true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan permohonan izin: ' . $e->getMessage());

            Flux::toast(
                text: 'Terjadi kesalahan sistem saat mengirim permohonan. Silakan coba lagi nanti.',
                heading: 'Gagal Terkirim!',
                variant: 'danger',
            );
        }
    }

    public function messages()
    {
        return [
            'student_id.required'       => 'Silakan pilih nama siswa.',
            'start_date.required'       => 'Tanggal mulai izin wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'end_date.required'         => 'Tanggal akhir izin wajib diisi.',
            'end_date.after_or_equal'   => 'Tanggal akhir tidak boleh kurang dari tanggal mulai.',
            'type.required'             => 'Silakan pilih jenis izin (Sakit/Izin).',
            'description.required'      => 'Alasan izin harus dijelaskan.',
        ];
    }

    public function render()
    {
        return view('livewire.components.leave-request-form');
    }
}