<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('address')->sortable()->searchable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif')
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make(),
                Action::make('deactivate')
                    ->label('Nonaktifkan Akun')
                    ->modalDescription('Apakah anda yakin ingin menonaktifkan akun ini?, Pengguna yang dinonaktifkan tidak akan bisa login ke sistem.')
                    ->action(function ($record) {
                        $record->is_active = false;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    //visible only when the user is active and has role parent
                    ->visible(fn ($record) => $record->is_active  && $record->hasRole(User::PARENT_ROLE))
                    ,
                Action::make('activate')
                    ->action(function ($record) {
                        $record->is_active = true;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->label('Aktifkan Akun')
                    ->modalDescription('Apakah anda yakin ingin mengaktifkan akun ini?, Pengguna yang diaktifkan dapat melakukan login ke sistem.')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => !$record->is_active && $record->hasRole(User::PARENT_ROlE)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
