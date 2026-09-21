<?php

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Schemas\UserForm;
use App\Filament\Admin\Resources\Users\Tables\UsersTable;
use App\Models\User;
use App\Support\CurrentTpq;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $pluralModelLabel = 'Akun Walisantri';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
        ->with(['roles', 'profile'])
        ->where(['is_superadmin' => false])
        ->whereHas('roles', fn($q) => $q->where('name', 'parent'), '=', 1)
        ->whereHas('students', fn($q) => $q->where('students.tpq_profile_id', CurrentTpq::id()))
        ->whereKeyNot(auth()->id());
}

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create'=> CreateUser::route('/create'),
            'edit' =>  EditUser::route('/{record}/edit')
        ];
    }
}
