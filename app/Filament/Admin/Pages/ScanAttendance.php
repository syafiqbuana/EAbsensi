<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ScanAttendance extends Page
{
    protected string $view = 'filament.admin.pages.scan-attendance';

    protected static ?string $navigationLabel= 'Scan Absensi';

    protected static ?string $title = 'Scan Absensi';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::QrCode;
}
