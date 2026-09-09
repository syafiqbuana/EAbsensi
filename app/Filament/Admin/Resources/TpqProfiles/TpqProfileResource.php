<?php

namespace App\Filament\Admin\Resources\TpqProfiles;

use App\Filament\Admin\Resources\TpqProfiles\Schemas\TpqProfileForm;
use App\Models\TpqProfile;
use BackedEnum;
use App\Filament\Admin\Resources\TpqProfiles\Pages\ViewTpqProfile;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TpqProfileResource extends Resource
{
    protected static ?string $model = TpqProfile::class;

    protected static ?string $slug = 'profil-tpq';

    protected static ?string $navigationLabel = 'Profil TPQ';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    public static function form(Schema $schema): Schema
    {
        return TpqProfileForm::configure($schema);
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
            'index' => ViewTpqProfile::route('/'),
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
