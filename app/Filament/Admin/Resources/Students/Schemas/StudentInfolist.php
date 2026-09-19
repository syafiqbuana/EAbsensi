<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use App\Models\Student;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class StudentInfolist
{

    public static function configure(Schema $schema)
    {
        return $schema
            ->components([
                Section::make('Informasi Anak')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama'),
                                TextEntry::make('birth_date')
                                    ->label('Tanggal Lahir')->date('Y-m-d'),
                                TextEntry::make('count_age')
                                    ->label('Umur'),
                                TextEntry::make('birth_place')
                                    ->label('Tempat Lahir'),
                                TextEntry::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'male' => 'Laki-laki',
                                        'female' => 'Perempuan',
                                        default => $state,
                                    }),
                                TextEntry::make('users.name')
                                    ->label('Orang Tua / Wali'),
                            ]),
                        Fieldset::make('')->contained(false)->schema([
                            Grid::make(2)->schema([
                                 ImageEntry::make('qr_code')
                                ->label('QR Code')
                                ->state(fn(Student $record) => $record->qr_code) // pakai accessor di atas
                                ->square()
                                ->imageWidth(100)
                                ->imageHeight(100),
                            ImageEntry::make('photo_path')
                                ->label('Foto Murid')
                                ->square()
                                ->state(fn($record) => Storage::disk('public')->url($record->photo_path))
                                ->imageWidth(100)
                                ->imageHeight(100)
                            ])
                        ])
                    ]),
            ]);
    }
}