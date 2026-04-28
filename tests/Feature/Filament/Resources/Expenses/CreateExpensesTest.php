<?php

namespace Tests\Feature\Filament\Resources\Expenses;

use App\Enums\ExpensesType;
use App\Filament\Resources\ExpensesResource;
use App\Filament\Resources\ExpensesResource\Pages\CreateExpenses;
use App\Models\Expenses;
use App\Models\User;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class CreateExpensesTest extends FilamentTestCase
{
    public function test_create_page_loads(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        $this->get(ExpensesResource::getUrl('create', ['tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_name_is_required(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateExpenses::class)
            ->fillForm([
                'name' => '',
                'amount' => 10.00,
                'type' => ExpensesType::FOOD,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_amount_is_required(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateExpenses::class)
            ->fillForm([
                'name' => 'Lunch',
                'amount' => null,
                'type' => ExpensesType::FOOD,
            ])
            ->call('create')
            ->assertHasFormErrors(['amount' => 'required']);
    }

    public function test_type_is_required(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateExpenses::class)
            ->fillForm([
                'name' => 'Lunch',
                'amount' => 10.00,
                'type' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['type' => 'required']);
    }

    public function test_created_expense_belongs_to_current_tenant(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateExpenses::class)
            ->fillForm([
                'name' => 'Hotel booking',
                'amount' => 250.00,
                'type' => ExpensesType::ACCOMMODATION,
                'transaction_date' => now()->toDateTimeString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $expense = Expenses::where('name', 'Hotel booking')->first();

        $this->assertNotNull($expense);
        $this->assertSame($team->id, $expense->team_id);
    }

    public function test_amount_is_stored_as_cents(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateExpenses::class)
            ->fillForm([
                'name' => 'Coffee',
                'amount' => 5.50,
                'type' => ExpensesType::FOOD,
                'transaction_date' => now()->toDateTimeString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('expenses', [
            'name' => 'Coffee',
            'amount' => 550,
        ]);
    }

    public function test_users_multi_select_attaches_expense_user_pivot(): void
    {
        [$user, $team] = $this->loginAsTenantMember();
        $otherUser = User::factory()->create();
        $otherUser->teams()->attach($team);

        Livewire::test(CreateExpenses::class)
            ->fillForm([
                'name' => 'Shared dinner',
                'amount' => 100.00,
                'type' => ExpensesType::FOOD,
                'transaction_date' => now()->toDateTimeString(),
                'users' => [$user->id, $otherUser->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $expense = Expenses::where('name', 'Shared dinner')->first();

        $this->assertNotNull($expense);
        $this->assertDatabaseHas('expense_user', [
            'expenses_id' => $expense->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('expense_user', [
            'expenses_id' => $expense->id,
            'user_id' => $otherUser->id,
        ]);
    }
}
