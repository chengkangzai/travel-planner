<?php

namespace Database\Factories;

use App\Enums\ExpensesType;
use App\Models\Expenses;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExpensesFactory extends Factory
{
    protected $model = Expenses::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(100, 50000) / 100,
            'type' => $this->faker->randomElement(ExpensesType::cases()),
            'name' => $this->faker->words(3, true),
            'transaction_date' => Carbon::now(),
            'team_id' => Team::factory(),
        ];
    }

    public function forTeam(Team $team): static
    {
        return $this->state(['team_id' => $team->id]);
    }
}
