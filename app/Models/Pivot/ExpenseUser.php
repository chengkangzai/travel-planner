<?php

namespace App\Models\Pivot;

use App\Models\Expenses;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ExpenseUser extends Pivot
{
    protected $fillable = [
        'user_id',
        'expense_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expenses::class);
    }
}
