<?php

namespace App\Filament\Resources;

use App\Enums\ExpensesType;
use App\Filament\Resources\ExpensesResource\Pages;
use App\Models\Expenses;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesResource extends Resource
{
    protected static ?string $model = Expenses::class;

    protected static ?string $slug = 'expenses';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->required(),

                    TextInput::make('amount')
                        ->prefix('RM')
                        ->required()
                        ->numeric(),

                    DateTimePicker::make('transaction_date')
                        ->default(now()),
                ])->heading('Information')->columnSpan(2),

                Section::make([
                    Radio::make('type')
                        ->options(ExpensesType::class)
                        ->required(),
                ])->heading('Type')->columnSpan(1),
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
                    ->numeric()
                    ->summarize(Sum::make()),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpenses::route('/create'),
            'edit' => Pages\EditExpenses::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
