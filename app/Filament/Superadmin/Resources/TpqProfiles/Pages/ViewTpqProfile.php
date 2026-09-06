<?php

namespace App\Filament\Superadmin\Resources\TpqProfiles\Pages;

use App\Filament\Superadmin\Resources\TpqProfiles\TpqProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTpqProfile extends ViewRecord
{
    protected static string $resource = TpqProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
