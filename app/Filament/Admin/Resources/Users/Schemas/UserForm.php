<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\Student;
use App\Support\CurrentTpq;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden; // <-- Jangan lupa import ini
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pengguna')
                    ->columnSpanFull()
                    ->schema([
                        Fieldset::make('Kredensial')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Panggilan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
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
                                    ->dehydrated(false),
                            ]),
                        Fieldset::make('Profil Pengguna')
                            ->relationship('profile') 
                            ->schema([
                                TextInput::make('full_name')
                                    ->label('Nama Lengkap')
                                    ->required(),
                                TextInput::make('phone_number')
                                    ->label('Nomor Whatsapp Aktif')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(15),
                                Textarea::make('address')
                                    ->label('Alamat')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                        
                        Fieldset::make('Data Murid')
                            ->schema([
                                Select::make('students')
                                    ->label('Murid')
                                    ->columnSpanFull()
                                    ->required()
                                    ->maxItems(5)
                                    ->relationship('students', 'name')
                                    ->options(CurrentTpq::where(Student::query())->orderBy('name')->pluck('name', 'id'))
                                    ->multiple()
                                    ->preload(),
                            ]),
                    ]),
            ]);
    }
}