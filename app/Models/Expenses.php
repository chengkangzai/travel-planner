<?php

namespace App\Models;

use App\Enums\ExpensesType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'name',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'type' => ExpensesType::class,
            'transaction_date'=>'datetime'
        ];
    }

    public function amount()
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }
}
