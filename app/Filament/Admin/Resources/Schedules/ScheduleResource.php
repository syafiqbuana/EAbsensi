<?php

namespace App\Filament\Admin\Resources\Schedules;

use App\Filament\Admin\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Admin\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Admin\Resources\Schedules\Pages\ListSchedules;
use App\Filament\Admin\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\Admin\Resources\Schedules\Tables\SchedulesTable;
use App\Models\Schedules;
use App\Support\CurrentTpq;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedules::class;
    protected static ?string $pluralModelLabel = 'Jadwal';
    protected static ?int $navigationSort = 2;   
    protected static ?string $slug = 'jadwal';
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tpq_profile_id', CurrentTpq::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
