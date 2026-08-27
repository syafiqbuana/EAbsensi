<?php

namespace App\Filament\Admin\Resources\LeaveRequests;

use App\Filament\Admin\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Admin\Resources\LeaveRequests\Pages\ViewLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Filament\Admin\Resources\LeaveRequests\Schemas\LeaveRequestInfolist;
use App\Filament\Admin\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|UnitEnum|null $navigationGroup = 'Kehadiran';

    protected static ?string $pluralModelLabel = 'Pengajuan Izin';

    protected static ?string $slug = 'pengajuan-izin';
    
    public static function form(Schema $schema): Schema
    {
        return LeaveRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
    }

    public static function infolist(Schema $schema) :Schema{
        return LeaveRequestInfolist::configure($schema);
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
            'index' => ListLeaveRequests::route('/'),
            'view' => ViewLeaveRequest::route('view/{record}'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
