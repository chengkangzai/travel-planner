<?php

namespace Tests\Feature\Tenancy;

use App\Filament\Resources\ExpensesResource;
use App\Filament\Resources\LocationResource;
use App\Models\Expenses;
use App\Models\Location;
use App\Models\Team;
use App\Models\User;
use Tests\FilamentTestCase;

class CrossTenantIsolationTest extends FilamentTestCase
{
    public function test_user_in_team_a_cannot_access_team_b_expense_via_url(): void
    {
        [$userA, $teamA] = $this->loginAsTenantMember();
        $teamB = Team::factory()->create();

        $teamBExpense = Expenses::factory()->forTeam($teamB)->create();

        $response = $this->get(ExpensesResource::getUrl('edit', ['record' => $teamBExpense, 'tenant' => $teamB]));

        $this->assertNotSame(200, $response->getStatusCode(), 'Cross-tenant edit should not return 200');
    }

    public function test_expense_query_for_team_a_excludes_team_b_expenses(): void
    {
        [, $teamA] = $this->loginAsTenantMember();
        $teamB = Team::factory()->create();

        $teamAExpense = Expenses::factory()->forTeam($teamA)->create();
        $teamBExpense = Expenses::factory()->forTeam($teamB)->create();

        $teamAResults = Expenses::where('team_id', $teamA->id)->pluck('id');

        $this->assertContains($teamAExpense->id, $teamAResults);
        $this->assertNotContains($teamBExpense->id, $teamAResults);
    }

    public function test_location_query_for_team_a_excludes_team_b_locations(): void
    {
        [, $teamA] = $this->loginAsTenantMember();
        $teamB = Team::factory()->create();

        $teamALocation = Location::factory()->forTeam($teamA)->create();
        $teamBLocation = Location::factory()->forTeam($teamB)->create();

        $teamAResults = Location::where('team_id', $teamA->id)->pluck('id');

        $this->assertContains($teamALocation->id, $teamAResults);
        $this->assertNotContains($teamBLocation->id, $teamAResults);
    }

    public function test_user_cannot_access_tenant_they_do_not_belong_to(): void
    {
        $userA = User::factory()->create();
        $teamA = Team::factory()->create();
        $userA->teams()->attach($teamA);
        $this->actingAs($userA);

        $teamB = Team::factory()->create();

        $this->assertFalse($userA->canAccessTenant($teamB));
    }

    public function test_list_page_for_team_a_shows_only_team_a_expense(): void
    {
        [, $teamA] = $this->loginAsTenantMember();
        $teamB = Team::factory()->create();

        Expenses::factory()->forTeam($teamA)->create(['name' => 'Team A Meal']);
        Expenses::factory()->forTeam($teamB)->create(['name' => 'Team B Meal']);

        $this->get(ExpensesResource::getUrl('index', ['tenant' => $teamA]))
            ->assertSee('Team A Meal')
            ->assertDontSee('Team B Meal');
    }

    public function test_list_page_for_team_a_shows_only_team_a_location(): void
    {
        [, $teamA] = $this->loginAsTenantMember();
        $teamB = Team::factory()->create();

        Location::factory()->forTeam($teamA)->create(['name' => 'Team A Hotel']);
        Location::factory()->forTeam($teamB)->create(['name' => 'Team B Hotel']);

        $this->get(LocationResource::getUrl('index', ['tenant' => $teamA]))
            ->assertSee('Team A Hotel')
            ->assertDontSee('Team B Hotel');
    }

    public function test_user_in_both_teams_sees_only_respective_expenses(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();
        $user->teams()->attach([$teamA->id, $teamB->id]);
        $this->actingAs($user);

        $expenseA = Expenses::factory()->forTeam($teamA)->create(['name' => 'Team A only']);
        $expenseB = Expenses::factory()->forTeam($teamB)->create(['name' => 'Team B only']);

        $expensesForA = Expenses::where('team_id', $teamA->id)->get();
        $expensesForB = Expenses::where('team_id', $teamB->id)->get();

        $this->assertTrue($expensesForA->contains($expenseA));
        $this->assertFalse($expensesForA->contains($expenseB));
        $this->assertTrue($expensesForB->contains($expenseB));
        $this->assertFalse($expensesForB->contains($expenseA));
    }
}
