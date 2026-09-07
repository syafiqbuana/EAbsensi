<?php

namespace App\Filament\Superadmin\Resources\Users\Pages;

use App\Filament\Superadmin\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

        //Tabs
    public function getTabs() :array {

        return [
            'parents' => Tab::make('Orang Tua')
                ->badge(User::query()->whereHas('roles', function ($query) {
                    $query->where('name', User::PARENT_ROLE);
                })->where('is_active', true)->count())
                ->query(fn ($query) => $query->role(User::PARENT_ROLE)),
            'admins' => Tab::make('Admin')
                ->badge(User::query()->whereHas('roles', function ($query) {
                    $query->where('name', User::TEACHER_ROLE);
                })->where('is_active', true)->count())
                ->query(fn ($query) => $query->role(User::TEACHER_ROLE))
        ];
    }
}
