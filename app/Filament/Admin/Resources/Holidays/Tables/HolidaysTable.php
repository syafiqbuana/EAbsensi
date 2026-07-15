<?php

namespace App\Filament\Admin\Resources\Holidays\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HolidaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Hari Libur')->sortable()->searchable(),
                TextColumn::make('date_range')
                    ->label('Tanggal')
                    
                    ->getStateUsing(
                        fn($record) =>
                        Carbon::parse($record->start_date)->format('d M Y')
                        . ' - ' .
                        Carbon::parse($record->end_date)->format('d M Y')
                    ),
                TextColumn::make('')
                
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
