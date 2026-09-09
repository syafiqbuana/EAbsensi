<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use App\Models\Classes;
use App\Models\Student;
use App\Support\CurrentTpq;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
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
                TextColumn::make('classes.name')->label('Kelas'),
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
                    ->form([
                        Select::make('new_class_id')
                            ->label('Kelas Baru')
                            ->relationship('classes', 'name')
                            ->options(Classes::query()->byTpqProfile(CurrentTpq::id())->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->native(false),
                    ])->modalHeading('Pindah Kelas')->modalButton('Pindahkan')
                    ->modalSubheading('Pilih kelas baru untuk siswa ini.')
                    ->action(function (Student $student, array $data) {
                        $student->update([
                            'class_id' => $data['new_class_id'],
                        ]);
                        Notification::make()
                            ->title('Murid berhasil dipindahkan ke kelas baru.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
