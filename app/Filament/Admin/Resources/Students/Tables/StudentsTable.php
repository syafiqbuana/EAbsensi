<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('qr_code')
                    ->label('QR Code')
                    ->state(fn(Student $record) => $record->qr_code) 
                    ->square(),
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('birth_date')->label('Tanggal Lahir')->sortable()->date('Y-m-d'),
                TextColumn::make('count_age')->label('Umur')->sortable(),
                TextColumn::make('birth_place')->label('Tempat Lahir')->sortable()->searchable(),
                TextColumn::make('users.address')
                    ->label('Alamat'),
                TextColumn::make('gender')->badge()->formatStateUsing(fn(string $state): string => match ($state) {
                    'male' => 'Laki-laki',
                    'female' => 'Perempuan',
                    default => $state,
                }),
                TextColumn::make('users.name')
                    ->label('Orang Tua / Wali')
                    ->sortable()
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make()
                    ->label('Lihat')
                    ->icon('heroicon-o-eye'),
                Action::make('change_class')
                    ->label('Pindah Kelas')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
