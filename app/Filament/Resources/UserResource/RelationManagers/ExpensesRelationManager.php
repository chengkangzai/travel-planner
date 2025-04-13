<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Expenses;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')
                    ->required()
                    ->integer(),

                TextInput::make('type')
                    ->required(),

                TextInput::make('name')
                    ->required(),

                DatePicker::make('transaction_date'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Expenses $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Expenses $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('amount')
                    ->money('MYR')
                    ->summarize(Sum::make()->money('MYR')),

                TextColumn::make('transaction_date')
                    ->date(),
            ])
            ->filters([
                //
            ]);
    }
}
