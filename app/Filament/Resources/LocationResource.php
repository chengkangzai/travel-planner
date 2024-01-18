<?php

namespace App\Filament\Resources;

use App\Enums\LocationType;
use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $slug = 'locations';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Select::make('day_id')
                        ->relationship('day', 'name')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required(),

                            DatePicker::make('date')
                                ->required(),
                        ])
                        ->required(),

                    Radio::make('type')
                        ->columns(2)
                        ->options(LocationType::class)
                        ->required(),
                ]),

                Section::make([
                    TextInput::make('name')
                        ->helperText('Location Name Eg. "Airport"/"Hotel"')
                        ->required(),

                    TextInput::make('title')
                        ->helperText('Event Title Eg. "Lunch"/"Driving'),

                    TextInput::make('google_map_link')
                        ->columnSpanFull()
                        ->reactive()
                        ->url()
                        ->suffixAction(fn ($state) => $state == null ? null : Action::make('Open Google Map')
                            ->url($state)
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->openUrlInNewTab()
                        ),
                ]),

                Section::make([
                    DateTimePicker::make('from')
                        ->reactive()
                        ->afterStateUpdated(fn (string$context, Set $set,string $state) => $context=='create' ? $set('to', $state) : null)
                        ->seconds(false),

                    DateTimePicker::make('to')
                        ->seconds(false),
                ]),

                RichEditor::make('remarks'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Location $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Location $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('google_map_link'),

                TextColumn::make('from')
                    ->date(),

                TextColumn::make('to'),

                TextColumn::make('order_column'),

                TextColumn::make('remarks'),

                TextColumn::make('type'),

                TextColumn::make('day_id'),
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
