<?php

namespace Database\Factories;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'title' => $this->faker->sentence(3),
            'type' => $this->faker->randomElement(LocationType::cases()),
            'from' => now()->addDays($this->faker->numberBetween(1, 30)),
            'to' => now()->addDays($this->faker->numberBetween(31, 60)),
            'is_visited' => false,
            'team_id' => Team::factory(),
        ];
    }

    public function forTeam(Team $team): static
    {
        return $this->state(['team_id' => $team->id]);
    }

    public function visited(): static
    {
        return $this->state(['is_visited' => true]);
    }
}
