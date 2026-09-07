<?php

namespace App\Filament\Superadmin\Resources\TpqRegistrations\Tables;

use App\Actions\ApproveTpqRegistration;
use App\Models\TpqRegistration;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TpqRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->fontFamily('mono'),

                TextColumn::make('tpq_name')
                    ->label('Nama TPQ')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TpqRegistration $record): string => $record->tpq_address ?? ''),

                TextColumn::make('applicant.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TpqRegistration $record): string => $record->tpq_contact_number ?? ''),

                TextColumn::make('tpq_registration_number')
                    ->label('No. Statistik (NSPP)')
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => TpqRegistration::STATUS_PENDING,
                        'success' => TpqRegistration::STATUS_APPROVED,
                        'danger'  => TpqRegistration::STATUS_REJECTED,
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        TpqRegistration::STATUS_PENDING  => 'Menunggu',
                        TpqRegistration::STATUS_APPROVED => 'Disetujui',
                        TpqRegistration::STATUS_REJECTED => 'Ditolak',
                        default                          => $state,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pendaftaran')
                    ->options([
                        TpqRegistration::STATUS_PENDING  => 'Menunggu Persetujuan',
                        TpqRegistration::STATUS_APPROVED => 'Disetujui',
                        TpqRegistration::STATUS_REJECTED => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Action Button: Setujui Pendaftaran
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pendaftaran TPQ')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pendaftaran ini? Tindakan ini akan membuat profil TPQ baru dan memberikan hak akses kepada pemohon.')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->visible(fn (TpqRegistration $record): bool => $record->isPending())
                    ->action(function (TpqRegistration $record): void {
                        try {
                            ApproveTpqRegistration::execute($record, Auth::user());

                            Notification::make()
                                ->title('Pendaftaran Berhasil Disetujui')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Menyetujui Pendaftaran')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // Action Button: Tolak Pendaftaran
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('rejected_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Masukkan alasan mengapa pendaftaran ini ditolak...'),
                    ])
                    ->modalHeading('Tolak Pendaftaran TPQ')
                    ->modalSubmitActionLabel('Tolak Pendaftaran')
                    ->visible(fn (TpqRegistration $record): bool => $record->isPending())
                    ->action(function (TpqRegistration $record, array $data): void {
                        ApproveTpqRegistration::reject(
                            $record, 
                            Auth::user(), 
                            $data['rejected_reason'] ?? null
                        );

                        Notification::make()
                            ->title('Pendaftaran Ditolak')
                            ->warning()
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