<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\TpqProfile;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateQrPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $tpqProfileId,
        public string $exportType,
        public array $classIds = [],
        public ?int $studentId = null
    ) {}

    public function handle(): void
    {
        $query = Student::query()
            ->where('tpq_profile_id', $this->tpqProfileId);

        if ($this->exportType === 'classes' && !empty($this->classIds)) {
            $query->whereIn('class_id', $this->classIds);
        } elseif ($this->exportType === 'student' && $this->studentId) {
            $query->where('id', $this->studentId);
        } else {
            $query->active();
        }

        $studentIds = $query->orderBy('name')->pluck('id');

        if ($studentIds->isEmpty()) {
            Notification::make()
                ->title('Ekspor Kode QR Gagal')
                ->body('Tidak ada data santri yang ditemukan untuk diekspor.')
                ->danger()
                ->sendToDatabase($this->user);

            return;
        }

        $tpqProfile = TpqProfile::find($this->tpqProfileId);
        $slug = Str::slug($tpqProfile?->name ?? 'tpq');
        $timestamp = time();
        $chunks = $studentIds->chunk(50)->values();
        $totalParts = $chunks->count();
        $totalStudents = $studentIds->count();
        $user = $this->user;

        $jobs = $chunks->map(fn ($ids, $i) => new ProcessQrPdfChunkJob(
            $this->user,
            $this->tpqProfileId,
            $ids->all(),
            $i + 1,
            $totalParts,
            $timestamp,
        ))->all();

        Bus::batch($jobs)
            ->allowFailures()
            ->finally(function (Batch $batch) use ($user, $slug, $timestamp, $totalParts, $totalStudents) {
                $disk = Storage::disk('public');

                $parts = [];
                for ($n = 1; $n <= $totalParts; $n++) {
                    $p = "exports/qr-codes-{$slug}-part-{$n}-{$timestamp}.pdf";
                    if ($disk->exists($p)) {
                        $parts[] = $p;
                    }
                }

                if (empty($parts)) {
                    Notification::make()
                        ->title('Ekspor Kode QR Gagal')
                        ->body('Tidak ada file PDF yang berhasil dibuat.')
                        ->danger()
                        ->sendToDatabase($user);

                    return;
                }

                $zipPath = "exports/qr-codes-{$slug}-{$timestamp}.zip";
                $zip = new \ZipArchive();
                $zip->open($disk->path($zipPath), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                foreach ($parts as $p) {
                    $zip->addFile($disk->path($p), basename($p));
                }
                $zip->close();

                $failed = $batch->failedJobs;

                $notification = Notification::make()
                    ->title('Ekspor Kode QR Selesai')
                    ->body("{$totalStudents} santri diproses menjadi " . count($parts) . ' file PDF.'
                        . ($failed > 0 ? " {$failed} bagian gagal, silakan ekspor ulang." : ''))
                    ->actions([
                        Action::make('download')
                            ->label('Download ZIP')
                            ->button()
                            ->url(asset('storage/' . $zipPath), shouldOpenInNewTab: true),
                    ]);

                ($failed > 0 ? $notification->warning() : $notification->success())
                    ->sendToDatabase($user);
            })
            ->dispatch();
    }
}