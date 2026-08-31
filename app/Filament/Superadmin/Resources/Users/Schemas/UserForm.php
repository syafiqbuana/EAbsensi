<?php

namespace App\Filament\Superadmin\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Textarea;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Namq')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Textarea::make('address')
                    ->label('Alamat')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(fn(string $operation) => $operation === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => bcrypt($state)),
                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(fn(string $operation) => $operation === 'create')
                    ->same('password')
                    ->dehydrated(false)

            ]);
    }
}
