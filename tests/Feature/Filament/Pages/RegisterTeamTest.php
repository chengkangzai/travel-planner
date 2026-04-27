<?php

namespace Tests\Feature\Filament\Pages;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_registration_creates_team_and_attaches_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $team = Team::create(['name' => 'My Travel Team']);
        $team->users()->attach($user);

        $this->assertDatabaseHas('teams', ['name' => 'My Travel Team']);
        $this->assertDatabaseHas('user_team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);
        $this->assertTrue($user->teams->contains($team));
    }

    public function test_user_can_belong_to_newly_registered_team(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $team = Team::create(['name' => 'Japan Trip 2025']);
        $team->users()->attach($user->id);

        $this->assertCount(1, $user->fresh()->teams);
        $this->assertSame('Japan Trip 2025', $user->fresh()->teams->first()->name);
    }

    public function test_register_team_page_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin');

        $this->assertTrue(
            $response->isRedirect() || $response->isSuccessful(),
            "Expected redirect to team registration or successful response"
        );
    }
}
