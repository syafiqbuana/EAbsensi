<?php

namespace App\Filament\Superadmin\Resources\TpqProfiles;

use App\Filament\Superadmin\Resources\TpqProfiles\Pages\CreateTpqProfile;
use App\Filament\Superadmin\Resources\TpqProfiles\Pages\EditTpqProfile;
use App\Filament\Superadmin\Resources\TpqProfiles\Pages\ListTpqProfiles;
use App\Filament\Superadmin\Resources\TpqProfiles\Pages\ViewTpqProfile;
use App\Filament\Superadmin\Resources\TpqProfiles\Schemas\TpqProfileForm;
use App\Filament\Superadmin\Resources\TpqProfiles\Schemas\TpqProfileInfolist;
use App\Filament\Superadmin\Resources\TpqProfiles\Tables\TpqProfilesTable;
use App\Models\TpqProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TpqProfileResource extends Resource
{
    protected static ?string $model = TpqProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TpqProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TpqProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TpqProfilesTable::configure($table);
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
            'index' => ListTpqProfiles::route('/'),
            'view' => ViewTpqProfile::route('/{record}'),
            'edit' => EditTpqProfile::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
