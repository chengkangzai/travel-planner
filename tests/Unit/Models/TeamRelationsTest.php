<?php

namespace Tests\Unit\Models;

use App\Enums\ExpensesType;
use App\Enums\LocationType;
use App\Models\Expenses;
use App\Models\Location;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_has_many_locations(): void
    {
        $team = Team::factory()->create();
        Location::factory()->forTeam($team)->count(3)->create();

        $this->assertCount(3, $team->fresh()->locations);
    }

    public function test_team_has_many_expenses(): void
    {
        $team = Team::factory()->create();
        Expenses::factory()->forTeam($team)->count(2)->create();

        $this->assertCount(2, $team->fresh()->expenses);
    }

    public function test_team_belongs_to_many_users(): void
    {
        $team = Team::factory()->create();
        $users = User::factory()->count(3)->create();
        $team->users()->attach($users->pluck('id'));

        $this->assertCount(3, $team->fresh()->users);
    }

    public function test_deleting_team_cascades_to_locations(): void
    {
        $team = Team::factory()->create();
        $location = Location::factory()->forTeam($team)->create();

        $team->delete();

        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_deleting_team_cascades_to_expenses(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::factory()->forTeam($team)->create();

        $team->delete();

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_location_factory_creates_valid_location_for_team(): void
    {
        $team = Team::factory()->create();
        $location = Location::factory()->forTeam($team)->create();

        $this->assertSame($team->id, $location->team_id);
        $this->assertInstanceOf(LocationType::class, $location->type);
        $this->assertNotEmpty($location->name);
        $this->assertFalse($location->is_visited);
    }

    public function test_expense_factory_creates_valid_expense_for_team(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::factory()->forTeam($team)->create();

        $this->assertSame($team->id, $expense->team_id);
        $this->assertInstanceOf(ExpensesType::class, $expense->type);
        $this->assertNotEmpty($expense->name);
        $this->assertGreaterThan(0, $expense->amount);
    }
}
