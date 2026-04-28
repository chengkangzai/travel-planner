<?php

namespace Tests\Unit\Models;

use App\Enums\ExpensesType;
use App\Models\Expenses;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpensesRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_belongs_to_team(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::factory()->forTeam($team)->create();

        $this->assertTrue($expense->team->is($team));
    }

    public function test_users_belong_to_many_via_expense_user_pivot(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::factory()->forTeam($team)->create();
        $user = User::factory()->create();

        $expense->users()->attach($user->id);

        $this->assertTrue($expense->users->contains($user));
    }

    public function test_attaching_user_writes_expenses_id_pivot_column(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::factory()->forTeam($team)->create();
        $user = User::factory()->create();

        $expense->users()->attach($user->id);

        $this->assertDatabaseHas('expense_user', [
            'expenses_id' => $expense->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_type_cast_returns_enum_instance(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::factory()->forTeam($team)->create(['type' => ExpensesType::FOOD]);

        $this->assertInstanceOf(ExpensesType::class, $expense->fresh()->type);
        $this->assertSame(ExpensesType::FOOD, $expense->fresh()->type);
    }

    public function test_transaction_date_is_cast_to_datetime(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::factory()->forTeam($team)->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $expense->fresh()->transaction_date);
    }
}
