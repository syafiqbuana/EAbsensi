<?php

namespace App\Filament\Admin\Resources\TpqProfiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class TpqProfileForm
{
    public static function configure(Schema $schema, bool $isEditing = false): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi TPQ')
                    ->description('Detail profil dan kontak Taman Pendidikan Al-Qur\'an.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama TPQ')
                                ->required()
                                ->maxLength(255)
                                ->disabled(!$isEditing),

                            TextInput::make('registration_number')
                                ->label('Nomor Registrasi (NSPQ)')
                                ->required()
                                ->maxLength(255)
                                ->disabled(!$isEditing || $isEditing),

                            TextInput::make('contact_number')
                                ->label('Nomor Kontak / Telepon')
                                ->tel()
                                ->required()
                                ->maxLength(255)
                                ->disabled(!$isEditing),
                            Textarea::make('address')
                                ->label('Alamat Lengkap')
                                ->required()
                                ->autosize()
                                ->disabled(!$isEditing),
                            FileUpload::make('logo_path')
                                ->label('Logo TPQ')
                                ->disk('public')
                                ->directory('tpq-logos')
                                ->image()
                                ->columnSpanFull()
                                ->visible(fn() => $isEditing),

                        ]),
                        Fieldset::make('Informasi Kepala TPQ')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('tpq_head_name')
                                    ->label('Nama Kepala TPQ')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(!$isEditing),

                                TextInput::make('tpq_head_email')
                                    ->label('Email Kepala TPQ')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(!$isEditing),

                                TextInput::make('tpq_head_phone_number')
                                    ->label('Nomor Telepon Kepala TPQ')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(!$isEditing),

                                Textarea::make('tpq_head_address')
                                    ->label('Alamat Kepala TPQ')
                                    ->required()
                                    ->autosize()
                                    ->disabled(!$isEditing),
                                FileUpload::make('tpq_head_photo_path')
                                    ->label('Foto Kepala TPQ')
                                    ->disk('public')
                                    ->directory('tpq-head-photos')
                                    ->image()
                                    ->visible(fn() => $isEditing),
                                Fieldset::make('')
                                    ->columnSpanFull()
                                    ->contained(false)
                                    ->schema([
                                        Placeholder::make('foto_preview')
                                            ->label('Foto Kepala TPQ')
                                            ->content(function ($get) {

                                                $path = $get('tpq_head_photo_path');
                                                if (!$path) {
                                                    return new HtmlString('<span class="text-sm text-gray-500 italic">Belum ada foto</span>');
                                                }
                                                $url = Storage::url($path);
                                                return new HtmlString('<img src="' . $url . '" alt="Foto Kepala TPQ" class="h-32 w-32 object-cover rounded-sm" />');
                                            })->visible(fn() => !$isEditing),
                                        Placeholder::make('logo_preview')
                                            ->label('Logo TPQ')
                                            ->content(function ($get) {
                                                $path = $get('logo_path');
                                                if (!$path) {
                                                    return new HtmlString('<span class="text-sm text-gray-500 italic">Belum ada logo</span>');
                                                }
                                                $url = Storage::url($path);
                                                return new HtmlString('<img src="' . $url . '" alt="Logo TPQ" class="h-32 w-32 object-cover rounded-sm" />');
                                            })->visible(fn() => !$isEditing),
                                    ])->disabled(!$isEditing),
                            ])->disabled(!$isEditing),
                    ])
                ,


            ]);
    }
}