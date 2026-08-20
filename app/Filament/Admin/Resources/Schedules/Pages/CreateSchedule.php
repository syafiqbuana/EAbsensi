<?php

namespace App\Filament\Admin\Resources\Schedules\Pages;

use App\Filament\Admin\Resources\Schedules\ScheduleResource;
use App\Models\Schedules;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;


class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function beforeCreate(): void
    {
        // Ambil dari dua sumber berbeda
        $stateData = $this->form->getState(); // time values yang bersih
        $rawData = $this->data;              // classes yang ada di sini

        $day = $stateData['day'] ?? null;
        $timeOpen = $stateData['time_open'] ?? null;   // "07:00:00" ✅
        $timeClose = $stateData['time_close'] ?? null;  // "09:00:00" ✅
        $selectedClassIds = $rawData['classes'] ?? [];          // ["2"] ✅

        if (!is_array($selectedClassIds)) {
            $selectedClassIds = array_filter([$selectedClassIds]);
        }

        $daysArray = match (true) {
            empty($day) => [],
            is_array($day) => $day,
            default => array_filter(explode(',', $day)),
        };

        if (empty($daysArray) || !$timeOpen || !$timeClose || empty($selectedClassIds)) {
            Log::debug('beforeCreate: guard hit', compact('daysArray', 'timeOpen', 'timeClose', 'selectedClassIds'));
            return;
        }

        Log::debug('beforeCreate: running overlap query', compact('daysArray', 'timeOpen', 'timeClose', 'selectedClassIds'));

        $isOverlapping = Schedules::query()
            ->where(function ($query) use ($daysArray) {
                foreach ($daysArray as $day) {
                    $query->orWhereRaw('FIND_IN_SET(?, day) > 0', [trim($day)]);
                }
            })
            ->where(function ($query) use ($timeOpen, $timeClose) {
                $query->where('time_open', '<', $timeClose)
                    ->where('time_close', '>', $timeOpen);
            })
            ->whereHas('classes', function ($query) use ($selectedClassIds) {
                $query->whereIn('classes.id', $selectedClassIds);
            })
            ->exists();

        if ($isOverlapping) {
            Notification::make()
                ->title('Gagal Menyimpan Jadwal')
                ->body('Salah satu kelas yang dipilih sudah memiliki jadwal lain pada hari dan jam yang tumpang tindih.')
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
            Artisan::call('attendance:generate-sessions');
        }
    }
}
