<?php

namespace Tests\Feature\Filament\Resources\Location;

use App\Enums\LocationType;
use App\Filament\Resources\LocationResource;
use App\Filament\Resources\LocationResource\Pages\EditLocation;
use App\Models\Location;
use App\Models\Team;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class EditLocationTest extends FilamentTestCase
{
    public function test_edit_page_loads_for_own_tenant_location(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->create();

        $this->get(LocationResource::getUrl('edit', ['record' => $location, 'tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_edit_page_blocked_for_other_tenant_location(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $otherTeam = Team::factory()->create();
        $otherLocation = Location::factory()->forTeam($otherTeam)->create();

        $response = $this->get(LocationResource::getUrl('edit', ['record' => $otherLocation, 'tenant' => $otherTeam]));

        $this->assertNotSame(200, $response->getStatusCode());
    }

    public function test_form_pre_fills_existing_name(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->create(['name' => 'Petronas Tower']);

        Livewire::test(EditLocation::class, ['record' => $location->getRouteKey()])
            ->assertFormSet(['name' => 'Petronas Tower']);
    }

    public function test_saving_updates_name(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->create(['name' => 'Old Hotel']);

        Livewire::test(EditLocation::class, ['record' => $location->getRouteKey()])
            ->fillForm(['name' => 'New Hotel'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'New Hotel',
        ]);
    }

    public function test_saving_updates_type(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->create(['type' => LocationType::hotel]);

        Livewire::test(EditLocation::class, ['record' => $location->getRouteKey()])
            ->fillForm(['type' => LocationType::restaurant])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(LocationType::restaurant, $location->fresh()->type);
    }

    public function test_name_cannot_be_emptied_on_edit(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->create();

        Livewire::test(EditLocation::class, ['record' => $location->getRouteKey()])
            ->fillForm(['name' => ''])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_delete_removes_location_from_database(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->create();

        $location->delete();

        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_visited_factory_state_creates_visited_location(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->visited()->create();

        $this->assertTrue($location->is_visited);
    }

    public function test_from_before_to_in_factory(): void
    {
        [, $team] = $this->loginAsTenantMember();
        $location = Location::factory()->forTeam($team)->create();

        $this->assertTrue($location->from->lt($location->to));
    }
}
