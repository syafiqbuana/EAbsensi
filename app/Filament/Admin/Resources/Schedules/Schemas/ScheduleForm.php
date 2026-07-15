<?php

namespace App\Filament\Admin\Resources\Schedules\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Carbon\Carbon;
use Closure;

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
                    // Opsional: Batasi pilihan jam di UI agar admin lebih mudah memilih
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
                    ])->native(false)
                    ->rule(function () {
                        return function (string $attribute, $value, Closure $fail) {
                            // Ambil format Jam:Menit saja untuk perbandingan
                            $time = Carbon::parse($value)->format('H:i');

                            // Definisikan batas sesi
                            $isMorning = $time >= '07:00' && $time <= '09:00';
                            $isAfternoon = $time >= '13:00' && $time <= '17:00';

                            if (!$isMorning && !$isAfternoon) {
                                $fail('Jam mulai harus berada di rentang sesi Pagi (07:00 - 09:00) atau sesi Siang (13:00 - 17:00).');
                            }
                        };
                    }),

                TimePicker::make('time_close')
                    ->label('Jam Selesai')
                    ->required()
                    ->native(false)
                     // Validasi bawaan Laravel: time_close harus LEBIH DARI time_open
                    ->rule(function ($get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $timeClose = Carbon::parse($value)->format('H:i');
                            $timeOpen = Carbon::parse($get('time_open'))->format('H:i');

                            $isMorning = $timeClose >= '07:00' && $timeClose <= '11:00';
                            $isAfternoon = $timeClose >= '13:00' && $timeClose <= '17:00';

                            if (!$isMorning && !$isAfternoon) {
                                $fail('Jam selesai harus berada di rentang sesi Pagi (07:00 - 11:00) atau sesi Siang (13:00 - 17:00).');
                            }

                            // Validasi Tambahan: Mencegah jadwal menyeberang sesi
                            // Misalnya: Mulai jam 08:00 (Pagi) tapi selesai jam 14:00 (Siang)
                            $startIsMorning = $timeOpen >= '07:00' && $timeOpen <= '11:00';
                            $closeIsAfternoon = $timeClose >= '13:00' && $timeClose <= '17:00';

                            if ($startIsMorning && $closeIsAfternoon) {
                                $fail('Jadwal tidak boleh menyeberang dari sesi pagi ke sesi siang.');
                            }
                        };
                    }),
                Select::make('schedules')
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
