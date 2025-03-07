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
        'is_visited',
        'type',
        'team_id',
    ];

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
        'type' => LocationType::class,
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
