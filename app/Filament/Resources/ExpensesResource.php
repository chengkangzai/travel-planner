<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ExpensesResource\Pages\ListExpenses;
use App\Filament\Resources\ExpensesResource\Pages\CreateExpenses;
use App\Filament\Resources\ExpensesResource\Pages\EditExpenses;
use App\Enums\ExpensesType;
use App\Filament\Resources\ExpensesResource\Pages;
use App\Filament\Resources\ExpensesResource\Widgets\ExpensesByTypeChart;
use App\Models\Expenses;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesResource extends Resource
{
    protected static ?string $model = Expenses::class;

    protected static ?string $slug = 'expenses';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->required(),

                    TextInput::make('amount')
                        ->prefix('RM')
                        ->required()
                        ->numeric(),

                    DateTimePicker::make('transaction_date')
                        ->default(now()),

                    Select::make('users')
                        ->preload()
                        ->relationship('users', 'name')
                        ->multiple(),
                ])->heading('Information')->columnSpan(2),

                Fieldset::make('')
                    ->schema([
                        Radio::make('type')
                            ->columns(2)
                            ->options(ExpensesType::class)
                            ->required(),
                    ])
                    ->columnSpan(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('amount')
                    ->sortable()
                    ->money('MYR')
                    ->summarize(Sum::make()->money('MYR')),

                TextColumn::make('transaction_date')
                    ->date(),

                TextColumn::make('users.name')
                    ->badge()
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(ExpensesType::class),

                SelectFilter::make('users')
                    ->relationship('users', 'name')
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenses::route('/'),
            'create' => CreateExpenses::route('/create'),
            'edit' => EditExpenses::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getWidgets(): array
    {
        return [
            ExpensesByTypeChart::class,
        ];
    }
}
