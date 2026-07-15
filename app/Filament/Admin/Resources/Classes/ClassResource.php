<?php

namespace App\Filament\Admin\Resources\Classes;

use App\Filament\Admin\Resources\Classes\Pages\CreateClass;
use App\Filament\Admin\Resources\Classes\Pages\EditClass;
use App\Filament\Admin\Resources\Classes\Pages\ListClasses;
use App\Filament\Admin\Resources\Classes\Schemas\ClassForm;
use App\Filament\Admin\Resources\Classes\Tables\ClassesTable;
use App\Models\Classes;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ClassResource extends Resource
{
    protected static ?string $model = Classes::class;

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?string $pluralModelLabel = 'Kelas';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder {
        return parent::getEloquentQuery()
            ->orderBy('order','asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClasses::route('/'),      
        ];
    }
}
