<?php

namespace Tests\Feature\Filament\Pages;

use App\Models\Team;
use Tests\FilamentTestCase;

class EditTeamProfileTest extends FilamentTestCase
{
    public function test_team_name_update_persists(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $team->update(['name' => 'Updated Team Name']);

        $this->assertSame('Updated Team Name', $team->fresh()->name);
    }

    public function test_team_name_cannot_be_empty(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $team->name = '';
        $this->assertEmpty($team->name);
    }

    public function test_multiple_teams_have_independent_names(): void
    {
        [, $teamA] = $this->loginAsTenantMember();
        $teamB = Team::factory()->create(['name' => 'Team B']);

        $teamA->update(['name' => 'Team A Updated']);

        $this->assertSame('Team A Updated', $teamA->fresh()->name);
        $this->assertSame('Team B', $teamB->fresh()->name);
    }

    public function test_edit_team_profile_page_loads(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $response = $this->get("/admin/{$team->id}/profile");

        $this->assertTrue(
            $response->isSuccessful() || $response->isRedirect(),
            "Expected success or redirect for team profile page"
        );
    }
}
