<?php

namespace App\Models;

use App\Enums\LocationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'name',
        'title',
        'google_map_link',
        'from',
        'to',
        'order_column',
        'remarks',
        'type',
        'day_id',
    ];

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
        'type' => LocationType::class,
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }
}
