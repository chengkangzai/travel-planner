<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\ExpensesRelationManager;
use App\Models\Pivot\UserTeam;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class UserResource extends Resource
{

    protected static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->required(),
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
                TextColumn::make('expenses_sum')
                    ->sum('expenses','amount')
                    ->numeric()
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                Action::make('Kick out')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn(User $record) => $record->id !== auth()->id())
                    ->action(fn(UserTeam $record) => $record->delete())
            ])
            ->bulkActions([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
