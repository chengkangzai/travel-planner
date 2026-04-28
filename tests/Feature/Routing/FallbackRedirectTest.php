<?php

namespace Tests\Feature\Routing;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FallbackRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_guest_to_filament_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect();
        $this->assertStringContainsString('admin', $response->headers->get('Location'));
    }

    public function test_root_redirects_authenticated_user_with_no_team_to_register_team(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertRedirect();
    }

    public function test_root_redirects_authenticated_user_with_team_to_panel(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team);
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertRedirect();
    }
}
