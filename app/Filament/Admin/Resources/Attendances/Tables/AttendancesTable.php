<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')->label('Nama Murid')->sortable()->searchable(),
                TextColumn::make('schedule.name')->label('Jadwal')->sortable()->searchable(),
                TextColumn::make('time_in')->label('Jam Masuk')->placeholder('00.00.00'),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'present' => 'Hadir',
                        'permission' => 'Izin',
                        'sick' => 'Sakit',
                        'absent' => 'Alfa',
                        'pending' => 'Belum Absen',
                        default => $state,
                    })
                ->color(function ($state) {
                    return match ($state) {
                        'present' => 'success',
                        'permission' => 'warning',
                        'sick' => 'danger',
                        'absent' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary',
                    };
                })
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
