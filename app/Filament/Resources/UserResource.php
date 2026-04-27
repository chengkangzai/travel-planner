<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\ExpensesRelationManager;
use App\Models\Pivot\UserTeam;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class UserResource extends Resource
{

    protected static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->required()
                    ->confirmed()
                    ->dehydrated(fn($state) => !empty($state))
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('expenses_sum_amount')
                    ->money('MYR')
                    ->sum('expenses','amount')
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('Kick out')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn(User $record) => $record->id !== auth()->id())
                    ->action(fn(UserTeam $record) => $record->delete())
            ])
            ->toolbarActions([
                BulkAction::make('bulk_click_out')
                    ->action(function (Collection $records) {
                        $records
                            ->reject(fn($record) => $record->user_id == auth()->id())
                            ->each(function (UserTeam $record) {
                                $record->delete();
                            });
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
