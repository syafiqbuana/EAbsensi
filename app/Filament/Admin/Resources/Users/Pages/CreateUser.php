<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Support\CurrentTpq;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    public function getTitle(): string
    {
        return 'Buat Akun Walisantri';
    }

    protected function afterCreate(): void
    {
        setPermissionsTeamId(CurrentTpq::id());
        $this->record->assignRole('parent');
    }
}