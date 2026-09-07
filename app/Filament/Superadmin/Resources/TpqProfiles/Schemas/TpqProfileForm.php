<?php

namespace App\Filament\Superadmin\Resources\TpqProfiles\Schemas;

use App\Models\TpqProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TpqProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                                TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($record) => $record?->status === TpqProfile::STATUS_ACTIVE)
                    ->helperText('Slug tidak dapat diubah setelah TPQ aktif.'),

                TextInput::make('name')
                    ->label('Nama TPQ')
                    ->required()
                    ->maxLength(255),

                TextInput::make('registration_number')
                    ->label('Nomor Induk TPQ (NITQ)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                Textarea::make('address')
                    ->label('Alamat')
                    ->required()
                    ->rows(3),

                TextInput::make('contact_number')
                    ->label('Nomor Kontak')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        TpqProfile::STATUS_ACTIVE    => 'Aktif',
                        TpqProfile::STATUS_SUSPENDED => 'Ditangguhkan',
                    ])
                    ->required(),
            ]);
    }
}
