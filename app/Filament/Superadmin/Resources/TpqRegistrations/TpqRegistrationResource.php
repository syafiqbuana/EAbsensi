<?php

namespace App\Filament\Superadmin\Resources\TpqRegistrations;

use App\Filament\Superadmin\Resources\TpqRegistrations\Pages\CreateTpqRegistration;
use App\Filament\Superadmin\Resources\TpqRegistrations\Pages\EditTpqRegistration;
use App\Filament\Superadmin\Resources\TpqRegistrations\Pages\ListTpqRegistrations;
use App\Filament\Superadmin\Resources\TpqRegistrations\Pages\ViewTpqRegistration;
use App\Filament\Superadmin\Resources\TpqRegistrations\Schemas\TpqRegistrationForm;
use App\Filament\Superadmin\Resources\TpqRegistrations\Schemas\TpqRegistrationInfolist;
use App\Filament\Superadmin\Resources\TpqRegistrations\Tables\TpqRegistrationsTable;
use App\Models\TpqRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TpqRegistrationResource extends Resource
{
    protected static ?string $model = TpqRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TpqRegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TpqRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TpqRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTpqRegistrations::route('/'),
            'view' => ViewTpqRegistration::route('/{record}'),
            'edit' => EditTpqRegistration::route('/{record}/edit'),
        ];
    }
}
