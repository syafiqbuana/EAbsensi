<?php

namespace App\Filament\Superadmin\Resources\TpqRegistrations\Pages;

use App\Filament\Superadmin\Resources\TpqRegistrations\TpqRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTpqRegistration extends EditRecord
{
    protected static string $resource = TpqRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
