<?php

namespace App\Filament\Admin\Pages;

use App\Services\AttendanceScanService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ScanAttendance extends Page
{
    protected string $view = 'filament.admin.pages.scan-attendance';

    protected static ?string $navigationLabel = 'Scan Absensi';

    protected static ?string $slug = 'scan-absensi';

    protected static ?string $title = 'Scan Absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QrCode;

    public function processQrScan(string $qrToken): void
    {
        $result = app(AttendanceScanService::class)->scan($qrToken);

        Notification::make()
            ->title($result->message)
            ->{$result->level}()
            ->send();
    }
}
