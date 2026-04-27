<?php

namespace Tests\Feature\Routing;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_user_endpoint_returns_401_when_unauthenticated(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }

    public function test_api_user_endpoint_returns_user_with_valid_sanctum_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonFragment(['email' => $user->email]);
    }
}
