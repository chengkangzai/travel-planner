<?php

namespace Tests\Unit\Enums;

use App\Enums\ExpensesType;
use PHPUnit\Framework\TestCase;

class ExpensesTypeTest extends TestCase
{
    public function test_every_case_has_non_empty_label(): void
    {
        foreach (ExpensesType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel(), "Case {$case->name} has empty label");
        }
    }

    public function test_every_case_has_a_color(): void
    {
        foreach (ExpensesType::cases() as $case) {
            $this->assertNotNull($case->getColor(), "Case {$case->name} has no color");
        }
    }

    public function test_communication_label_is_comms(): void
    {
        $this->assertSame('Comms', ExpensesType::COMMUNICATION->getLabel());
    }

    public function test_leisure_case_value_is_entertainment(): void
    {
        $this->assertSame('Entertainment', ExpensesType::LEISURE->value);
    }

    public function test_all_expected_cases_exist(): void
    {
        $names = array_column(ExpensesType::cases(), 'name');

        foreach (['FOOD', 'TICKET', 'OTHER', 'TRAVEL', 'COMMUNICATION', 'SHOPPING', 'ACCOMMODATION', 'LEISURE'] as $expected) {
            $this->assertContains($expected, $names, "Missing enum case {$expected}");
        }
    }
}
