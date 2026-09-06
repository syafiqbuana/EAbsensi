<?php

namespace App\Filament\Admin\Resources\Holidays\Pages;

use App\Filament\Admin\Resources\Holidays\HolidayResource;
use App\Support\CurrentTpq;
use Filament\Resources\Pages\CreateRecord;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tpq_profile_id'] = CurrentTpq::id();

        return $data;
    }
}
