<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LocationResource;
use App\Models\Day;
use App\Models\Location;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public string|null|Model $model = Location::class;

    public function fetchEvents(array $info): array
    {
        return Location::query()
            ->get()
            ->map(fn(Location $location) => [
                'id' => $location->id,
                'title' => $location->name,
                'start' => $location->from,
                'end' => $location->to,

//                'url' => LocationResource::getUrl('edit',  ['record' => $location]),
//                'shouldOpenUrlInNewTab' => true
            ])
            ->all();
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(fn(Form $form, array $arguments) => $form->fill([
                    'day_id' => Day::where('date', $arguments['event']['start'] ?? $arguments['event']['end'] ?? 0)->first()->id ?? null,
                    'from' => $arguments['start'] ?? null,
                    'to' => $arguments['end'] ?? null
                ]))
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(fn(Location $record, Form $form, array $arguments) => $form->fill([
                    'name' => $record->name,
                    'from' => $arguments['event']['start'] ?? $record->from,
                    'to' => $arguments['event']['end'] ?? $record->to,
                ])),
            DeleteAction::make(),
        ];
    }

    public function getFormSchema(): array
    {
        return LocationResource::getFormSchema();
    }
}
