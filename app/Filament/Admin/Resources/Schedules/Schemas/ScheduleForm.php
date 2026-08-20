<?php

namespace App\Filament\Admin\Resources\Schedules\Schemas;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Jadwal')
                    ->required()
                    ->maxLength(255),
                TimePicker::make('time_open')
                    ->label('Jam Mulai')
                    ->required()
                    ->datalist([
                        '07:00',
                        '07:30',
                        '08:00',
                        '08:30',
                        '13:00',
                        '13:30',
                        '14:00',
                        '14:30',
                        '15:00',
                        '15:30',
                        '16:00',
                        '16:30'
                    ])
                    ->native(false),

                TimePicker::make('time_close')
                    ->label('Jam Selesai')
                    ->required()
                    ->native(false)
                    ->rule(function ($get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            // Bug #2 fix: null check dulu sebelum parse
                            $rawTimeOpen = $get('time_open');
                            if (!$rawTimeOpen) {
                                $fail('Jam mulai harus diisi terlebih dahulu.');
                                return;
                            }

                            $timeClose = Carbon::parse($value)->format('H:i');
                            $timeOpen = Carbon::parse($rawTimeOpen)->format('H:i');

                            // Bug #1 fix: time_close harus lebih besar dari time_open
                            if ($timeClose <= $timeOpen) {
                                $fail('Jam selesai harus lebih besar dari jam mulai.');
                                return;
                            }
                        };
                    }),
                Select::make('classes')
                    ->label('Kelas')
                    ->multiple()
                    ->relationship('classes', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('day')
                    ->native(false)
                    ->label('Hari')

                    ->options([
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ])
                    ->columns(3)
                    ->required(),
            ]);
    }
}
