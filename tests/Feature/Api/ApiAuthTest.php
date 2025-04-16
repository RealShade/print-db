<?php

namespace Tests\Feature\Api;

use App\Enums\UserStatus;
use App\Models\ApiToken;
use App\Models\User;

class ApiAuthTest extends TestCase
{

    public function test_api_auth_fails_no_token() : void
    {
        $response = $this->withHeaders([
        ])->post('/api');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_fails_with_empty_token()
    {
        $response = $this->getJson('/api', ['Authorization' => 'Bearer ']);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_fails_with_invalid_token()
    {
        $response = $this->getJson('/api', ['Authorization' => 'Bearer invalid_token']);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_succeeds_with_valid_token()
    {
        $response = $this->getJson('/api', ['Authorization' => 'Bearer ' . $this->apiToken->token]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'profile'  => [
                        'id'    => $this->userActive->id,
                        'name'  => $this->userActive->name,
                        'email' => $this->userActive->email,
                    ],
                    'printers' => [],
                ],
            ]);
    }

    public function test_api_auth_fails_for_blocked_user()
    {
        $userBlocked = User::factory()->create(['status' => UserStatus::BLOCKED]);

        $token = ApiToken::factory()->create(['user_id' => $userBlocked->id]);

        $response = $this->getJson('/api', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(403)
            ->assertJson(['error' => __('auth.account_inactive')]);
    }

    public function test_api_auth_fails_for_deleted_user()
    {
        $user = User::factory()->create(['status' => UserStatus::BLOCKED]);

        $token = ApiToken::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(403)
            ->assertJson(['error' => __('auth.account_inactive')]);
    }
}
