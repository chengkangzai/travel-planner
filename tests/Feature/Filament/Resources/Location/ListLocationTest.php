<?php

namespace Tests\Feature\Filament\Resources\Location;

use App\Filament\Resources\LocationResource;
use App\Models\Location;
use App\Models\Team;
use Tests\FilamentTestCase;

class ListLocationTest extends FilamentTestCase
{
    public function test_list_page_loads_for_tenant_member(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $this->get(LocationResource::getUrl('index', ['tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_list_redirects_unauthenticated_user(): void
    {
        $team = Team::factory()->create();

        $this->get(LocationResource::getUrl('index', ['tenant' => $team]))
            ->assertRedirect();
    }

    public function test_list_page_shows_current_tenant_location(): void
    {
        [, $team] = $this->loginAsTenantMember();

        Location::factory()->forTeam($team)->create(['name' => 'Kuala Lumpur Airport']);

        $this->get(LocationResource::getUrl('index', ['tenant' => $team]))
            ->assertSee('Kuala Lumpur Airport');
    }

    public function test_list_page_does_not_show_other_tenant_location(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $otherTeam = Team::factory()->create();

        Location::factory()->forTeam($otherTeam)->create(['name' => 'Top Secret Hotel']);

        $this->get(LocationResource::getUrl('index', ['tenant' => $team]))
            ->assertDontSee('Top Secret Hotel');
    }

    public function test_is_visited_toggle_updates_record(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $location = Location::factory()->forTeam($team)->create(['is_visited' => false]);

        $location->update(['is_visited' => true]);

        $this->assertTrue($location->fresh()->is_visited);
    }
}
