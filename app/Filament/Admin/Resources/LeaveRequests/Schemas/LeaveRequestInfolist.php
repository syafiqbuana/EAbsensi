<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Schemas;

use App\Jobs\SendLeaveRequestStatusNotification;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Forms\Components\TextArea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class LeaveRequestInfolist
{
    public static function configure(Schema $schema)
    {
        return $schema
            ->components([
                Section::make('Informasi Pengajuan')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('student.name')
                                    ->label('Nama Murid'),
                                TextEntry::make('date')
                                    ->label('Tanggal Tidak Hadir')
                                    ->date('Y-m-d'),
                                TextEntry::make('type')
                                    ->label('Jenis')
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'permission' => 'Izin',
                                        'sick' => 'Sakit',
                                        default => $state
                                    }),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger'
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => $state
                                    }),
                                TextEntry::make('description')
                                    ->label('Deskripsi'),
                                ImageEntry::make('proof')
                                    ->placeholder('Pemohon tidak melampirkan bukti')
                                    ->label('Bukti'),
                                TextEntry::make('creator.name')
                                    ->label('Pemohon')
                                    ->size(TextSize::Large),
                                TextEntry::make('rejected_reason')
                                    ->label('Alasan Penolakan')
                                    ->placeholder('-')
                            ])
                    ]),
                Actions::make([
                    Action::make('reject')
                        ->label('Tolak')
                        ->color('danger')
                        ->form([
                            TextArea::make('rejected_reason')->label('Alasan Penolakan')->required()
                        ])
                        ->action(function (array $data, $record) {
                            $record->update([
                                'status' => 'rejected',
                                'rejected_reason' => $data['rejected_reason'],
                                'approved_by' => auth()->id()
                            ]);

                            SendLeaveRequestStatusNotification::dispatch($record);
                        })
                        ->visible(fn($record) => $record->status === 'pending'),
                    Action::make('approve')
                        ->label('Setujui')
                        ->visible(fn($record) => $record->status === 'pending')
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'approved',
                                'approved_by' => auth()->id()
                            ]);

                            SendLeaveRequestStatusNotification::dispatch($record);
                        })
                        ->color('success'),
                ])
            ]);
    }
}