<?php

namespace App\Models;

use App\Enums\ExpensesType;
use App\Models\Pivot\ExpenseUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Expenses extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'name',
        'transaction_date',
        'team_id',
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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'expense_user', 'expenses_id', 'user_id')
            ->using(ExpenseUser::class);
    }
}
