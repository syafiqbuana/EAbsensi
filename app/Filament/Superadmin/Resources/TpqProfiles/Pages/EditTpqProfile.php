<?php

namespace App\Filament\Superadmin\Resources\TpqProfiles\Pages;

use App\Filament\Superadmin\Resources\TpqProfiles\TpqProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTpqProfile extends EditRecord
{
    protected static string $resource = TpqProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
