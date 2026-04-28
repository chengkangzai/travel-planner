<?php

namespace Tests;

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class FilamentTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    protected function loginAsTenantMember(?Team $team = null, ?User $user = null): array
    {
        $user ??= User::factory()->create();
        $team ??= Team::factory()->create();
        $user->teams()->attach($team);
        $this->actingAs($user);
        Filament::setTenant($team);

        return [$user, $team];
    }
}
