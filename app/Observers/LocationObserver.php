<?php

namespace App\Observers;

use App\Models\Location;

class LocationObserver
{
    public function creating(Location $location): void
    {
        if (auth()->hasUser()) {
            $location->team_id = auth()->user()->team_id;
        }
    }
}
