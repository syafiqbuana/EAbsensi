<?php

namespace App\Jobs;

use App\Models\LeaveRequest;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendLeaveRequestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public LeaveRequest $leaveRequest;

    /**
     * Create a new job instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        // Menyimpan instance LeaveRequest agar bisa diakses di method handle
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(FonnteService $fonnteService): void
    {
        // Load data murid untuk ditampilkan di pesan
        $this->leaveRequest->load('student');
        
        $studentName = $this->leaveRequest->student->name;
        $date = $this->leaveRequest->date->format('d F Y');
        $type = $this->leaveRequest->type === 'sick' ? 'Sakit' : 'Izin';
        $reason = $this->leaveRequest->description;

        // Format pesan WhatsApp
        $message = "*PENGAJUAN IZIN BARU*\n\n";
        $message .= "Nama Murid: {$studentName}\n";
        $message .= "Tanggal: {$date}\n";
        $message .= "Keterangan: {$type}\n";
        $message .= "Alasan: {$reason}\n\n";
        $message .= "Silakan login ke panel admin untuk menyetujui atau menolak pengajuan ini.";
       $adminNumber = config('services.fonnte.admin_whatsapp');

        if ($adminNumber) {
            $fonnteService->sendMessage($adminNumber, $message);
        }
    }
}