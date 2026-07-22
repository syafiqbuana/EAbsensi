<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Jobs\SendLeaveRequestNotification;
use Flux\Flux; // Tambahkan facade Flux di sini

class LeaveRequestForm extends Component
{
    use WithFileUploads;

    #[Validate('required|exists:students,id')]
    public $student_id = '';

    #[Validate('required|date|after_or_equal:today')]
    public $date = '';

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
        // Validasi akan otomatis menghentikan eksekusi jika gagal
        // dan mengembalikan pesan error ke tampilan Blade
        $this->validate();

        try {
            $proofPath = $this->proof ? $this->proof->store('proofs', 'public') : null;

            $leaveRequest = LeaveRequest::create([
                'student_id' => $this->student_id,
                'date' => $this->date,
                'type' => $this->type,
                'description' => $this->description,
                'proof' => $proofPath,
                'status' => 'pending',
            ]);

            SendLeaveRequestNotification::dispatch($leaveRequest);

            // Toast Sukses
            Flux::toast(
                text: 'Permohonan izin Anda sedang menunggu persetujuan admin.',
                heading: 'Berhasil Terkirim!',
                variant: 'success',
            );

            $this->redirect(route('leaveRequest'), navigate: true);

        } catch (\Exception $e) {
            // Mencatat error ke file log Laravel (opsional tapi sangat disarankan)
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan permohonan izin: ' . $e->getMessage());

            // Toast Error menggunakan Flux
            Flux::toast(
                text: 'Terjadi kesalahan sistem saat mengirim permohonan. Silakan coba lagi nanti.',
                heading: 'Gagal Terkirim!',
                variant: 'danger', // Flux umumnya menggunakan 'danger' atau 'error' untuk peringatan
            );
        }
    }

    public function messages()
    {
        return [
            'student_id.required' => 'Silakan pilih nama siswa.',
            'date.required' => 'Tanggal izin wajib diisi.',
            'date.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
            'type.required' => 'Silakan pilih jenis izin (Sakit/Izin).',
            'description.required' => 'Alasan izin harus dijelaskan.',
            'description.max' => 'Penjelasan terlalu panjang (maksimal 500 karakter).',
            'proof.image' => 'Bukti harus berupa gambar (JPG, PNG).',
            'proof.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }

    public function render()
    {
        return view('livewire.components.leave-request-form');
    }
}