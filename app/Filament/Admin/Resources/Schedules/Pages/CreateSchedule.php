<?php

namespace App\Filament\Admin\Resources\Schedules\Pages;

use App\Filament\Admin\Resources\Schedules\ScheduleResource;
use App\Models\Schedules;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Artisan;


class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function beforeCreate(): void
    {
        $formData = $this->data;

        $days = $formData['day'] ?? [];       // kemungkinan array dari multi-select
        $timeOpen = $formData['time_open'] ?? null;
        $timeClose = $formData['time_close'] ?? null;
        $selectedClassIds = $formData['classes'] ?? [];

        if (!is_array($selectedClassIds)) {
            $selectedClassIds = [$selectedClassIds];
        }

        // Normalisasi $days → selalu array
        if (!is_array($days)) {
            $days = array_filter(explode(',', $days)); // handle string 'senin,rabu'
        }

        if (empty($days) || !$timeOpen || !$timeClose || empty($selectedClassIds)) {
            return;
        }

        $isOverlapping = Schedules::query()
            // Cek apakah ada hari yang sama (pakai FIND_IN_SET karena kolom SET)
            ->where(function ($query) use ($days) {
                foreach ($days as $day) {
                    $query->orWhereRaw('FIND_IN_SET(?, day) > 0', [trim($day)]);
                }
            })
            // Cek tumpang tindih waktu
            ->where(function ($query) use ($timeOpen, $timeClose) {
                $query->where('time_open', '<', $timeClose)
                    ->where('time_close', '>', $timeOpen);
            })
            // Cek apakah melibatkan kelas yang sama
            ->whereHas('classes', function ($query) use ($selectedClassIds) {
                $query->whereIn('classes.id', $selectedClassIds);
            })
            ->exists();

        if ($isOverlapping) {
            Notification::make()
                ->title('Gagal Menyimpan Jadwal')
                ->body('Terjadi bentrok! Salah satu kelas yang dipilih sudah memiliki jadwal lain pada hari dan jam yang tumpang tindih.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    /**
     * Hook yang berjalan SETELAH data sukses tersimpan (dari diskusi sebelumnya)
     */
    protected function afterCreate(): void
    {
        $todayName = strtolower(now()->format('l'));

        if ($this->record->day === $todayName) {
            Artisan::call('attendance:generate');
        }
    }
}
