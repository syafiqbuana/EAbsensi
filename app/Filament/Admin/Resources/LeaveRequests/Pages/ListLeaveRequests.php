<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Pages;

use App\Filament\Admin\Resources\LeaveRequests\LeaveRequestResource;
use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getTabs():array {
        return [
            'all' => Tab::make('Semua'),
            'pending' =>Tab::make('Menunggu')->modifyQueryUsing(fn(Builder $query) =>$query->where('status','pending'))->badge(LeaveRequest::where('status','pending')->count())        
        ];
    }
}
