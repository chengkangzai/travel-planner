<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\LocationResource\Pages\ListLocations;
use App\Filament\Resources\LocationResource\Pages\CreateLocation;
use App\Filament\Resources\LocationResource\Pages\EditLocation;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;
use App\Enums\LocationType;
use App\Filament\Resources\LocationResource\Pages;
use App\Models\Day;
use App\Models\Location;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $slug = 'locations';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...self::getFormSchema(),
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Location $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Location $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('from')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('google_map_link')
                    ->limit(30)
                    ->url(fn($state) => $state, true),

                TextColumn::make('from')
                    ->sortable()
                    ->time(),

                TextColumn::make('to')
                    ->time(),

                IconColumn::make('is_visited')
                    ->boolean()
                    ->action(fn(Location $record) => $record->update(['is_visited' => !$record->is_visited])),

                TextColumn::make('remarks')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('day.name'),
            ])
            ->filters([
                //
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
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getFormSchema(): array
    {
        return [
            Section::make([
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
                    ->suffixAction(fn($state) => $state == null ? null : Action::make('Open Google Map')
                        ->url($state)
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->openUrlInNewTab()
                    ),
            ]),

            Section::make([
                DateTimePicker::make('from')
                    ->reactive()
                    ->afterStateUpdated(fn(string $context, Set $set, string $state) => $context == 'create' ? $set('to', $state) : null)
                    ->seconds(false),

                DateTimePicker::make('to')
                    ->seconds(false),
            ]),

            Section::make([
                RichEditor::make('remarks'),
            ])->collapsible()->collapsed()
        ];
    }
}
