<?php

namespace Tests\Feature\Filament\Resources\Location;

use App\Enums\LocationType;
use App\Filament\Resources\LocationResource;
use App\Filament\Resources\LocationResource\Pages\CreateLocation;
use App\Models\Location;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class CreateLocationTest extends FilamentTestCase
{
    public function test_create_page_loads(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        $this->get(LocationResource::getUrl('create', ['tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_name_is_required(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateLocation::class)
            ->fillForm([
                'name' => '',
                'type' => LocationType::hotel,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_type_is_required(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateLocation::class)
            ->fillForm([
                'name' => 'Airport',
                'type' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['type' => 'required']);
    }

    public function test_created_location_belongs_to_current_tenant(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateLocation::class)
            ->fillForm([
                'name' => 'KLIA',
                'type' => LocationType::transport,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $location = Location::where('name', 'KLIA')->first();

        $this->assertNotNull($location);
        $this->assertSame($team->id, $location->team_id);
    }

    public function test_google_map_link_must_be_a_valid_url(): void
    {
        [$user, $team] = $this->loginAsTenantMember();

        Livewire::test(CreateLocation::class)
            ->fillForm([
                'name' => 'Hotel',
                'type' => LocationType::hotel,
                'google_map_link' => 'not-a-url',
            ])
            ->call('create')
            ->assertHasFormErrors(['google_map_link' => 'url']);
    }
}
