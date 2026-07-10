<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Anak')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama lengkap')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->maxDate(now())
                            ->native(false)
                            ->required(),
                        TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(255),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->native(false)
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ])
                            ->required(),
                        Select::make('users')
                            ->label('Orang Tua / Wali')
                            ->multiple()
                            ->maxItems(2)
                            ->relationship('users', 'name')
                            ->options(User::query()->whereHas('roles', function ($query) {
                                $query->where('name', 'parent');
                            })->pluck('name', 'id'))
                            ->preload()
                            ->required(),
                    ]),

            ]);
    }
}
