<?php

namespace App\Filament\Admin\Resources\Holidays\Schemas;

use App\Models\Schedules; // Pastikan nama model sesuai
use App\Support\CurrentTpq;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Hari Libur')
                    ->required(),
                Select::make('schedules')
                    ->label('Jadwal')
                    ->multiple()
                    // Ubah required menjadi fungsi kondisional
                    ->required(fn($get) => ! $get('is_global'))
                    ->disabled(fn($get) => $get('is_global'))
                    ->relationship('schedules', 'name')
                    ->options(CurrentTpq::where(Schedules::query())->pluck('name','id'))
                    ->getOptionLabelFromRecordUsing(
                        fn(Schedules $record) => "{$record->name} - Pukul "
                        . substr($record->time_open, 0, 5)
                        . " - "
                        . substr($record->time_close, 0, 5)
                    )
                    ->preload()
                    ->searchable(),

                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->native(false)
                    ->live()
                    ->required()->minDate(now()),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->native(false)
                    ->minDate(fn($get) => $get('start_date'))
                    ->required(),

                Toggle::make('is_global')
                    ->live()
                    ->label('Terapkan ke seluruh jadwal')
                    ->afterStateUpdated(function (Set $set, $state) {
                        // Jika toggle diaktifkan (true), kosongkan pilihan di form schedules
                        if ($state) {
                            $set('schedules', []);
                        }
                    }),
                                TextArea::make('description')
                    ->label('Deskripsi')
                    ->required()
            ]);
    }
}