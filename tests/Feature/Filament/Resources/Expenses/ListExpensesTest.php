<?php

namespace Tests\Feature\Filament\Resources\Expenses;

use App\Filament\Resources\ExpensesResource;
use App\Models\Expenses;
use App\Models\Team;
use Tests\FilamentTestCase;

class ListExpensesTest extends FilamentTestCase
{
    public function test_list_page_loads_for_tenant_member(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $this->get(ExpensesResource::getUrl('index', ['tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_list_page_redirects_unauthenticated_user(): void
    {
        $team = Team::factory()->create();

        $this->get(ExpensesResource::getUrl('index', ['tenant' => $team]))
            ->assertRedirect();
    }

    public function test_team_a_expense_exists_in_db_and_not_in_team_b(): void
    {
        [, $teamA] = $this->loginAsTenantMember();
        $teamB = Team::factory()->create();

        $teamAExpense = Expenses::factory()->forTeam($teamA)->create();
        $teamBExpense = Expenses::factory()->forTeam($teamB)->create();

        $teamAExpenses = Expenses::where('team_id', $teamA->id)->pluck('id');
        $teamBExpenses = Expenses::where('team_id', $teamB->id)->pluck('id');

        $this->assertContains($teamAExpense->id, $teamAExpenses);
        $this->assertNotContains($teamBExpense->id, $teamAExpenses);
        $this->assertContains($teamBExpense->id, $teamBExpenses);
    }

    public function test_list_page_contains_tenant_expense_name_in_response(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Expenses::factory()->forTeam($team)->create(['name' => 'My team expense']);

        $this->get(ExpensesResource::getUrl('index', ['tenant' => $team]))
            ->assertSee('My team expense');
    }

    public function test_list_page_does_not_show_other_tenant_expense(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $otherTeam = Team::factory()->create();

        Expenses::factory()->forTeam($otherTeam)->create(['name' => 'Other team expense']);

        $this->get(ExpensesResource::getUrl('index', ['tenant' => $team]))
            ->assertDontSee('Other team expense');
    }
}
