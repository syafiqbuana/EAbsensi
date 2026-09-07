<?php

namespace App\Filament\Superadmin\Resources\TpqRegistrations\Pages;

use App\Filament\Superadmin\Resources\TpqRegistrations\TpqRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTpqRegistrations extends ListRecords
{
    protected static string $resource = TpqRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
