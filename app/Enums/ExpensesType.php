<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExpensesType: string implements HasColor, HasLabel
{
    case FOOD = "Food";
    case TRANSPORTATION = "Transportation";
    case TICKET = "Ticket";
    case OTHER = "Other";
    case ACCOMMODATION = "Accommodation";
    case COMMUNICATION = "Communication";

    case ENTERTAINMENT = "Entertainment";
    case SHOPPING = "Shopping";

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FOOD => 'gray',
            self::TRANSPORTATION => 'blue',
            self::TICKET => 'green',
            self::OTHER => 'danger',
            self::ACCOMMODATION => 'purple',
            self::COMMUNICATION => 'info',
            self::ENTERTAINMENT => 'warning',
            self::SHOPPING => 'primary',
        };
    }


    public function getLabel(): ?string
    {
        return match ($this) {
            self::FOOD => 'Food',
            self::TRANSPORTATION => 'Transportation',
            self::TICKET => 'Ticket',
            self::OTHER => 'Other',
            self::ACCOMMODATION => 'Accommodation',
            self::COMMUNICATION => 'Communication',
            self::ENTERTAINMENT => 'Entertainment',
            self::SHOPPING => 'Shop',
        };
    }
}
