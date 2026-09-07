<?php

namespace App\Filament\Superadmin\Resources\TpqProfiles\Tables;

use App\Models\TpqProfile;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TpqProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('name')
                    ->label('Nama TPQ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('registration_number')
                    ->label('Nomor Induk')
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                TextColumn::make('contact_number')
                    ->label('Kontak'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        TpqProfile::STATUS_ACTIVE    => 'success',
                        TpqProfile::STATUS_SUSPENDED => 'warning',
                        default                       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        TpqProfile::STATUS_ACTIVE    => 'Aktif',
                        TpqProfile::STATUS_SUSPENDED => 'Ditangguhkan',
                        default                       => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        TpqProfile::STATUS_ACTIVE    => 'Aktif',
                        TpqProfile::STATUS_SUSPENDED => 'Ditangguhkan',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
