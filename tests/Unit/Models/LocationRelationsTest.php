<?php

namespace Tests\Unit\Models;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_belongs_to_team(): void
    {
        $team = Team::factory()->create();
        $location = Location::factory()->forTeam($team)->create();

        $this->assertTrue($location->team->is($team));
    }

    public function test_type_cast_returns_location_type_enum(): void
    {
        $team = Team::factory()->create();
        $location = Location::factory()->forTeam($team)->create(['type' => LocationType::hotel]);

        $this->assertInstanceOf(LocationType::class, $location->fresh()->type);
        $this->assertSame(LocationType::hotel, $location->fresh()->type);
    }

    public function test_from_and_to_are_cast_to_datetime(): void
    {
        $team = Team::factory()->create();
        $location = Location::factory()->forTeam($team)->create([
            'from' => '2025-06-01 09:00:00',
            'to' => '2025-06-01 11:00:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $location->fresh()->from);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $location->fresh()->to);
    }

    public function test_is_visited_defaults_to_false(): void
    {
        $team = Team::factory()->create();
        $location = Location::factory()->forTeam($team)->create();

        $this->assertFalse($location->is_visited);
    }

    public function test_is_visited_can_be_toggled(): void
    {
        $team = Team::factory()->create();
        $location = Location::factory()->forTeam($team)->create(['is_visited' => false]);

        $location->update(['is_visited' => true]);

        $this->assertTrue($location->fresh()->is_visited);
    }
}
