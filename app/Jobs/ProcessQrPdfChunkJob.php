<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\TpqProfile;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessQrPdfChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 1;

    public function __construct(
        public User $user,
        public int $tpqProfileId,
        public array $studentIds,
        public int $partNumber,
        public int $totalParts,
        public int $timestamp,
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $tpqProfile = TpqProfile::find($this->tpqProfileId);

        $students = Student::whereIn('id', $this->studentIds)
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            return;
        }

        $pdf = Pdf::loadView('pdf.qr-codes', [
            'students' => $students,
            'tpqProfile' => $tpqProfile,
        ])->setPaper('a4', 'portrait');

        $slug = Str::slug($tpqProfile?->name ?? 'tpq');
        $path = "exports/qr-codes-{$slug}-part-{$this->partNumber}-{$this->timestamp}.pdf";

        Storage::disk('public')->makeDirectory('exports');
        Storage::disk('public')->put($path, $pdf->output());
    }
}