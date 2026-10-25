<?php

namespace App\Filament\Superadmin\Resources\TpqProfiles\Schemas;

use App\Models\TpqProfile;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Storage;

class TpqProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas TPQ')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('logo_path')
                                    ->label('Logo TPQ')
                                    ->state(fn (TpqProfile $record): ?string => $record->logo_path
                                        ? Storage::disk('public')->url($record->logo_path)
                                        : null)
                                    ->placeholder('Belum ada logo')
                                    ->square()
                                    ->imageWidth(120)
                                    ->imageHeight(120),

                                Grid::make(2)
                                    ->columnSpan(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Nama TPQ')
                                            ->size(TextSize::Large)
                                            ->weight('bold'),

                                        TextEntry::make('registration_number')
                                            ->label('Nomor Induk TPQ (NITQ)')
                                            ->copyable(),

                                        TextEntry::make('slug')
                                            ->label('Slug (URL)')
                                            ->copyable(),

                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                TpqProfile::STATUS_ACTIVE => 'success',
                                                TpqProfile::STATUS_SUSPENDED => 'warning',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                TpqProfile::STATUS_ACTIVE => 'Aktif',
                                                TpqProfile::STATUS_SUSPENDED => 'Ditangguhkan',
                                                default => $state,
                                            }),
                                    ]),
                            ]),
                    ]),

                Section::make('Kontak & Alamat')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('contact_number')
                                    ->label('Nomor Kontak')
                                    ->icon('heroicon-o-phone')
                                    ->copyable(),

                                TextEntry::make('address')
                                    ->label('Alamat')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Ringkasan Data')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('students_count')
                                    ->label('Jumlah Santri')
                                    ->state(fn (TpqProfile $record): int => $record->students()->count()),

                                TextEntry::make('classes_count')
                                    ->label('Jumlah Kelas')
                                    ->state(fn (TpqProfile $record): int => $record->classes()->count()),

                                TextEntry::make('schedules_count')
                                    ->label('Jumlah Jadwal')
                                    ->state(fn (TpqProfile $record): int => $record->schedules()->count()),

                                TextEntry::make('registrations_count')
                                    ->label('Riwayat Pendaftaran')
                                    ->state(fn (TpqProfile $record): int => $record->registrations()->count()),
                            ]),
                    ]),

                Section::make('Informasi Sistem')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y, H:i'),

                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i'),

                                TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->dateTime('d M Y, H:i')
                                    ->placeholder('Tidak dihapus')
                                    ->color(fn ($state): string => filled($state) ? 'danger' : 'gray'),
                            ]),
                    ]),
            ]);
    }
}
