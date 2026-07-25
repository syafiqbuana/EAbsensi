<?php

namespace App\Jobs;

use App\Models\LeaveRequest;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendLeaveRequestStatusNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public LeaveRequest $leaveRequest;

    /**
     * Create a new job instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(FonnteService $fonnteService): void
    {
        // Load data murid DAN data users yang berelasi (karena belongsToMany)
        $this->leaveRequest->load('student.users');

        // Gunakan null-safe (?->) berjaga-jaga jika data student terhapus/kosong
        $studentName = $this->leaveRequest->student?->name ?? 'Murid Tidak Diketahui';
        $date = $this->leaveRequest->date->format('d F Y');
        $type = $this->leaveRequest->type === 'sick' ? 'SAKIT' : 'IZIN';
        $description = $this->leaveRequest->description;

        // Sesuaikan dengan nama value status di database Anda
        $statusStr = $this->leaveRequest->status === 'approved' ? 'DISETUJUI ✅' : 'DITOLAK ❌';

        // Format pesan WhatsApp untuk user
        $message = "*UPDATE PENGAJUAN IZIN*\n\n";
        $message .= "Halo,\n";
        $message .= "Pengajuan izin untuk murid berikut:\n\n";
        $message .= "Nama: {$studentName}\n";
        $message .= "Tanggal: {$date}\n";
        $message .= "Alasan: {$type}\n";
        if ($description) {
            $message .= "Deskripsi: {$description}\n";
        }
        $message .= "Status: *{$statusStr}*\n\n";

        // Jika ditolak dan ada alasan penolakan
        if ($this->leaveRequest->status === 'rejected' && $this->leaveRequest->rejected_reason) {
            $message .= "Catatan Admin: {$this->leaveRequest->rejected_reason}\n\n";
        }

        $message .= "Terima kasih.";

        // Ambil koleksi users yang terhubung dengan murid tersebut (Bisa Ayah, Ibu, dll)
        $users = $this->leaveRequest->student?->users;

        // Cek apakah ada user yang terhubung
        if ($users && $users->isNotEmpty()) {
            // Looping untuk mengirim ke semua nomor wali/user yang terhubung
            foreach ($users as $user) {
                $userNumber = $user->phone_number;

                // Kirim pesan jika nomor tersedia
                if (!empty($userNumber)) {
                    $fonnteService->sendMessage($userNumber, $message);
                }
            }
        } else {
            // (Opsional) Catat di log jika tidak ada user yang terhubung
            Log::warning("Gagal kirim WA status izin: Tidak ada user/wali yang terhubung ke murid pada Leave Request ID {$this->leaveRequest->id}");
        }
    }
}