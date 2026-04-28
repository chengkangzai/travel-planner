<?php

namespace Tests\Feature\Filament\Widgets;

use App\Enums\LocationType;
use App\Filament\Widgets\CalendarWidget;
use App\Models\Location;
use App\Models\Team;
use Tests\FilamentTestCase;

class CalendarWidgetTest extends FilamentTestCase
{
    public function test_fetch_events_returns_only_current_tenant_locations(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $myLocation = Location::factory()->forTeam($team)->create(['name' => 'My Hotel']);
        $otherTeam = Team::factory()->create();
        Location::factory()->forTeam($otherTeam)->create(['name' => 'Other Hotel']);

        $widget = new CalendarWidget();
        $events = $widget->fetchEvents([]);

        $ids = array_column($events, 'id');

        $this->assertContains($myLocation->id, $ids);
        $this->assertCount(1, $events, 'Only current tenant locations should appear');
    }

    public function test_fetch_events_maps_name_to_title(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Location::factory()->forTeam($team)->create(['name' => 'KLIA Terminal 2']);

        $widget = new CalendarWidget();
        $events = $widget->fetchEvents([]);

        $this->assertSame('KLIA Terminal 2', $events[0]['title']);
    }

    public function test_fetch_events_maps_from_to_start(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $from = now()->addDay()->setSecond(0);
        Location::factory()->forTeam($team)->create(['from' => $from]);

        $widget = new CalendarWidget();
        $events = $widget->fetchEvents([]);

        $this->assertArrayHasKey('start', $events[0]);
        $this->assertSame(
            $from->format('Y-m-d H:i:s'),
            $events[0]['start']->format('Y-m-d H:i:s')
        );
    }

    public function test_fetch_events_includes_background_color_from_location_type(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Location::factory()->forTeam($team)->create(['type' => LocationType::hotel]);

        $widget = new CalendarWidget();
        $events = $widget->fetchEvents([]);

        $this->assertArrayHasKey('backgroundColor', $events[0]);
        $this->assertStringStartsWith('rgb(', $events[0]['backgroundColor']);

        $expectedColor = 'rgb(' . LocationType::hotel->getColor()[500] . ')';
        $this->assertSame($expectedColor, $events[0]['backgroundColor']);
    }

    public function test_fetch_events_returns_empty_for_tenant_with_no_locations(): void
    {
        $this->loginAsTenantMember();

        $widget = new CalendarWidget();
        $events = $widget->fetchEvents([]);

        $this->assertEmpty($events);
    }

    public function test_event_has_required_calendar_keys(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Location::factory()->forTeam($team)->create();

        $widget = new CalendarWidget();
        $events = $widget->fetchEvents([]);

        $this->assertArrayHasKey('id', $events[0]);
        $this->assertArrayHasKey('title', $events[0]);
        $this->assertArrayHasKey('start', $events[0]);
        $this->assertArrayHasKey('end', $events[0]);
        $this->assertArrayHasKey('backgroundColor', $events[0]);
        $this->assertArrayHasKey('borderColor', $events[0]);
    }
}
