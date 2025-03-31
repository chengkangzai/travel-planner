<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExpensesType: string implements HasColor, HasLabel
{
    case FOOD = "Food";
    case TICKET = "Ticket";
    case OTHER = "Other";
    case TRAVEL = "Travel";
    case COMMUNICATION = "Communication";
    case SHOPPING = "Shopping";
    case ACCOMMODATION = "Accommodation";

    case LEISURE = "Entertainment";

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FOOD => 'gray',
            self::TRAVEL => 'purple',
            self::TICKET => 'success',
            self::OTHER => 'indigo',
            self::ACCOMMODATION => 'danger',
            self::COMMUNICATION => 'info',
            self::LEISURE => 'warning',
            self::SHOPPING => 'primary',
        };
    }


    public function getLabel(): ?string
    {
        return match ($this) {
            self::FOOD => 'Food',
            self::TRAVEL => 'Travel',
            self::TICKET => 'Ticket',
            self::OTHER => 'Other',
            self::ACCOMMODATION => 'Accommodation',
            self::COMMUNICATION => 'Comms',
            self::LEISURE => 'Leisure',
            self::SHOPPING => 'Shopping',
        };
    }
}
