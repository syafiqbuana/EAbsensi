<?php

namespace App\Filament\Admin\Resources\Attendances\Pages;

use App\Filament\Admin\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
        
        ];
    }

    public function getTabs(): array 
    {
        return [
            'all' => Tab::make('Semua'),
            'today' => Tab::make('Hari Ini')->modifyQueryUsing(fn (Builder $query) =>
                $query->whereToday('created_at')
            ),         
        ];
    }
}
