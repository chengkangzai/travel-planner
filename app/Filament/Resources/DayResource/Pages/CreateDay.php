<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDay extends CreateRecord
{
    protected static string $resource = DayResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
