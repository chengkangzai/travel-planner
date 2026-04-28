<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_panel_always_returns_true(): void
    {
        $user = User::factory()->create();
        $panel = Filament::getPanel('admin');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_get_tenants_returns_attached_teams(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();
        $user->teams()->attach([$teamA->id, $teamB->id]);

        $panel = Filament::getPanel('admin');
        $tenants = $user->getTenants($panel);

        $this->assertCount(2, $tenants);
        $this->assertTrue($tenants->contains($teamA));
        $this->assertTrue($tenants->contains($teamB));
    }

    public function test_can_access_tenant_true_for_attached_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team);

        $this->assertTrue($user->canAccessTenant($team));
    }

    public function test_can_access_tenant_false_for_foreign_team(): void
    {
        $user = User::factory()->create();
        $foreignTeam = Team::factory()->create();

        $this->assertFalse($user->canAccessTenant($foreignTeam));
    }

    public function test_user_can_belong_to_multiple_teams(): void
    {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams->pluck('id'));

        $this->assertCount(3, $user->fresh()->teams);
    }
}
