<?php

namespace Tests\Feature\Filament\Widgets;

use App\Enums\ExpensesType;
use App\Filament\Resources\ExpensesResource\Widgets\ExpensesByTypeChart;
use App\Models\Expenses;
use App\Models\Team;
use ReflectionMethod;
use Tests\FilamentTestCase;

class ExpensesByTypeChartTest extends FilamentTestCase
{
    private function callGetData(ExpensesByTypeChart $widget): array
    {
        return (new ReflectionMethod(ExpensesByTypeChart::class, 'getData'))
            ->invoke($widget);
    }

    public function test_can_view_returns_false_when_no_expenses_for_tenant(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $this->assertFalse(ExpensesByTypeChart::canView());
    }

    public function test_can_view_returns_true_when_expenses_exist_for_tenant(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Expenses::factory()->forTeam($team)->create();

        $this->assertTrue(ExpensesByTypeChart::canView());
    }

    public function test_can_view_returns_false_for_expenses_on_other_tenant(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $otherTeam = Team::factory()->create();
        Expenses::factory()->forTeam($otherTeam)->create();

        $this->assertFalse(ExpensesByTypeChart::canView());
    }

    public function test_data_excludes_other_tenant_expenses(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Expenses::factory()->forTeam($team)->create([
            'type' => ExpensesType::FOOD,
            'amount' => 50.00,
        ]);

        $otherTeam = Team::factory()->create();
        Expenses::factory()->forTeam($otherTeam)->create([
            'type' => ExpensesType::SHOPPING,
            'amount' => 999.99,
        ]);

        $data = $this->callGetData(new ExpensesByTypeChart());

        $labels = $data['labels'];

        $shoppingIndex = array_search('Shopping', $labels);
        $this->assertFalse($shoppingIndex, 'Other tenant Shopping expense must not appear');
    }

    public function test_data_groups_by_type_with_correct_label_and_sum(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Expenses::factory()->forTeam($team)->create(['type' => ExpensesType::FOOD, 'amount' => 10.00]);
        Expenses::factory()->forTeam($team)->create(['type' => ExpensesType::FOOD, 'amount' => 20.00]);

        $data = $this->callGetData(new ExpensesByTypeChart());

        $labels = $data['labels'];
        $amounts = $data['datasets'][0]['data'];

        $foodIndex = array_search('Food', $labels);
        $this->assertNotFalse($foodIndex, 'Food label must exist in chart data');

        $this->assertSame(3000, (int) $amounts[$foodIndex]);
    }

    public function test_chart_type_is_pie(): void
    {
        $this->loginAsTenantMember();

        $this->assertSame('pie', (new ReflectionMethod(ExpensesByTypeChart::class, 'getType'))
            ->invoke(new ExpensesByTypeChart()));
    }
}
