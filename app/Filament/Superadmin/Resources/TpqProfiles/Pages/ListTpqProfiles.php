<?php

namespace App\Filament\Superadmin\Resources\TpqProfiles\Pages;

use App\Filament\Superadmin\Resources\TpqProfiles\TpqProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTpqProfiles extends ListRecords
{
    protected static string $resource = TpqProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
