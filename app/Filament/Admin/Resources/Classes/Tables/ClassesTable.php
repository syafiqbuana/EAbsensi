<?php

namespace App\Filament\Admin\Resources\Classes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Kelas')->sortable()->searchable(),
                TextColumn::make('schedules.name')->label('Jadwal')->sortable()->searchable()
                    ->placeholder('Jadwal belum diatur'),
                TextColumn::make('students_count')
                    ->badge()
                    ->counts('students')
                    ->label('Jumlah Murid'),
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
