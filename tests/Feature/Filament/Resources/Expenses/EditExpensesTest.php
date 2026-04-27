<?php

namespace Tests\Feature\Filament\Resources\Expenses;

use App\Filament\Resources\ExpensesResource;
use App\Filament\Resources\ExpensesResource\Pages\EditExpenses;
use App\Models\Expenses;
use App\Models\Team;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class EditExpensesTest extends FilamentTestCase
{
    public function test_edit_page_loads_for_own_tenant_expense(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $expense = Expenses::factory()->forTeam($team)->create();

        $this->get(ExpensesResource::getUrl('edit', ['record' => $expense, 'tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_edit_page_blocked_for_other_tenant_expense(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $otherTeam = Team::factory()->create();
        $otherExpense = Expenses::factory()->forTeam($otherTeam)->create();

        $response = $this->get(ExpensesResource::getUrl('edit', ['record' => $otherExpense, 'tenant' => $otherTeam]));

        $this->assertNotSame(200, $response->getStatusCode());
    }

    public function test_form_pre_fills_amount_as_decimal_not_cents(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $expense = Expenses::factory()->forTeam($team)->create(['amount' => 45.50]);

        Livewire::test(EditExpenses::class, ['record' => $expense->getRouteKey()])
            ->assertFormSet(['amount' => 45.5]);
    }

    public function test_saving_updates_amount_as_cents_in_db(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $expense = Expenses::factory()->forTeam($team)->create(['amount' => 10.00]);

        Livewire::test(EditExpenses::class, ['record' => $expense->getRouteKey()])
            ->fillForm(['amount' => 99.99])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 9999,
        ]);
    }

    public function test_saving_updates_name(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $expense = Expenses::factory()->forTeam($team)->create(['name' => 'Old Name']);

        Livewire::test(EditExpenses::class, ['record' => $expense->getRouteKey()])
            ->fillForm(['name' => 'Updated Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_name_cannot_be_emptied_on_edit(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $expense = Expenses::factory()->forTeam($team)->create();

        Livewire::test(EditExpenses::class, ['record' => $expense->getRouteKey()])
            ->fillForm(['name' => ''])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_delete_removes_expense_from_database(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $expense = Expenses::factory()->forTeam($team)->create();

        $expense->delete();

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
