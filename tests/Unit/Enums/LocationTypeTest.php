<?php

namespace Tests\Unit\Enums;

use App\Enums\LocationType;
use PHPUnit\Framework\TestCase;

class LocationTypeTest extends TestCase
{
    public function test_every_case_has_non_empty_label(): void
    {
        foreach (LocationType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel(), "Case {$case->name} has empty label");
        }
    }

    public function test_every_case_returns_color_array_with_500_key(): void
    {
        foreach (LocationType::cases() as $case) {
            $color = $case->getColor();

            $this->assertIsArray($color, "Case {$case->name} getColor() must return array");
            $this->assertArrayHasKey(500, $color, "Case {$case->name} color array missing key 500 (used by CalendarWidget)");
        }
    }

    public function test_all_expected_cases_exist(): void
    {
        $names = array_column(LocationType::cases(), 'name');

        foreach (['hotel', 'restaurant', 'attraction', 'activity', 'transport', 'other'] as $expected) {
            $this->assertContains($expected, $names, "Missing enum case {$expected}");
        }
    }
}
