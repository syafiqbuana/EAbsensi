<?php

use App\Models\Student;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist{

    public static function configure(Schema $schema){
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
                        ImageEntry::make('qr_code')
                            ->label('QR Code')
                            ->state(fn (Student $record) => $record->qr_code) // pakai accessor di atas
                            ->square()
                            ->imageWidth(200)
                            ->imageHeight(200),
                    ]),
                
            ]); 
    }
}