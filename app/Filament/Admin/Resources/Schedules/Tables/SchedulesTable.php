<?php

namespace App\Filament\Admin\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Jadwal')->sortable()->searchable(),
                TextColumn::make('time_open')->label('Jam Mulai')->sortable(),
                TextColumn::make('time_close')->label('Jam Selesai')->sortable(),
                TextColumn::make('day')
                    ->label('Hari')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
                        'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                        default => $state,
                    })

                    ->separator(','),
                TextColumn::make('classes.name')
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
