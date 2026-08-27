<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ExportQrCode extends Page
{
    protected string $view = 'filament.admin.pages.export-qr-code';
    protected static ?string $slug = 'ekspor-kode-qr';

    protected static ?string $navigationLabel= 'Ekspor Kode QR';

    protected static ?string $title = 'Ekspor Kode QR';
    

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Printer;
}
