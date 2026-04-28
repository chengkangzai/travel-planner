<?php

namespace Tests\Unit\Models;

use App\Enums\ExpensesType;
use App\Models\Expenses;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpensesAmountCastTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_decimal_amount_stores_cents_in_database(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::create([
            'amount' => 12.50,
            'type' => ExpensesType::FOOD,
            'name' => 'Lunch',
            'transaction_date' => now(),
            'team_id' => $team->id,
        ]);

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'amount' => 1250]);
    }

    public function test_reading_amount_attribute_returns_decimal(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::create([
            'amount' => 12.50,
            'type' => ExpensesType::FOOD,
            'name' => 'Lunch',
            'transaction_date' => now(),
            'team_id' => $team->id,
        ]);

        $this->assertEquals(12.5, $expense->fresh()->amount);
    }

    public function test_zero_amount(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::create([
            'amount' => 0.00,
            'type' => ExpensesType::OTHER,
            'name' => 'Free item',
            'transaction_date' => now(),
            'team_id' => $team->id,
        ]);

        $this->assertEquals(0.0, $expense->fresh()->amount);
        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'amount' => 0]);
    }

    public function test_small_amount_stored_as_single_cent(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::create([
            'amount' => 0.01,
            'type' => ExpensesType::FOOD,
            'name' => 'Penny item',
            'transaction_date' => now(),
            'team_id' => $team->id,
        ]);

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'amount' => 1]);
        $this->assertEquals(0.01, $expense->fresh()->amount);
    }

    public function test_large_amount(): void
    {
        $team = Team::factory()->create();
        $expense = Expenses::create([
            'amount' => 9999.99,
            'type' => ExpensesType::ACCOMMODATION,
            'name' => 'Hotel stay',
            'transaction_date' => now(),
            'team_id' => $team->id,
        ]);

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'amount' => 999999]);
        $this->assertEquals(9999.99, $expense->fresh()->amount);
    }
}
