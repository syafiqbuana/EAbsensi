<?php

namespace App\Filament\Superadmin\Resources\TpqRegistrations\Pages;

use App\Filament\Superadmin\Resources\TpqRegistrations\TpqRegistrationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTpqRegistration extends ViewRecord
{
    protected static string $resource = TpqRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
