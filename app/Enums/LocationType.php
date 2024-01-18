<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LocationType: string implements HasColor, HasLabel
{
    case hotel = 'hotel';
    case restaurant = 'restaurant';
    case attraction = 'attraction';
    case activity = 'activity';
    case transport = 'transport';
    case other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::hotel => 'Hotel',
            self::restaurant => 'Restaurant',
            self::attraction => 'Attraction',
            self::activity => 'Activity',
            self::transport => 'Transport',
            self::other => 'Other',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::hotel => Color::Green,
            self::restaurant => Color::Yellow,
            self::attraction => Color::Blue,
            self::activity => Color::Purple,
            self::transport => Color::Orange,
            self::other => Color::Gray,
        };
    }
}
