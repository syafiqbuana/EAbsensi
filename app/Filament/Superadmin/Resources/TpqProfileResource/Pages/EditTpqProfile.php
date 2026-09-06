<?php

namespace App\Filament\Superadmin\Resources\TpqProfileResource\Pages;

use App\Filament\Superadmin\Resources\TpqProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTpqProfile extends EditRecord
{
    protected static string $resource = TpqProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
