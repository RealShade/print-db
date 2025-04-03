<?php
namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $activeUser;
    protected User $blockedUser;
    protected User $deletedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activeUser = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $this->blockedUser = User::factory()->create(['status' => UserStatus::BLOCKED]);
        $this->deletedUser = User::factory()->create(['status' => UserStatus::DELETED]);
    }

    public function test_login_fails_with_empty_email()
    {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_fails_with_empty_password()
    {
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_login_fails_for_blocked_user()
    {
        $response = $this->post('/login', [
            'email' => $this->blockedUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_fails_for_deleted_user()
    {
        $response = $this->post('/login', [
            'email' => $this->deletedUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_api_auth_fails_with_empty_token()
    {
        $response = $this->getJson('/api/tasks', ['Authorization' => 'Bearer ']);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_fails_with_invalid_token()
    {
        $response = $this->getJson('/api/tasks', ['Authorization' => 'Bearer invalid_token']);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_succeeds_with_valid_token()
    {
        $token = ApiToken::factory()->create(['user_id' => $this->activeUser->id]);

        $response = $this->getJson('/api/tasks', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(200);
    }

    public function test_api_auth_fails_for_blocked_user()
    {
        $token = ApiToken::factory()->create(['user_id' => $this->blockedUser->id]);

        $response = $this->getJson('/api/tasks', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(403)
                 ->assertJson(['error' => __('auth.account_inactive')]);
    }

    public function test_api_auth_fails_for_deleted_user()
    {
        $token = ApiToken::factory()->create(['user_id' => $this->deletedUser->id]);

        $response = $this->getJson('/api/tasks', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(403)
                 ->assertJson(['error' => __('auth.account_inactive')]);
    }

    public function test_redirect_to_login_for_unauthenticated_user()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_session_auth_succeeds_with_valid_session()
    {
        $this->actingAs($this->activeUser);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_session_auth_fails_with_no_session()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_session_auth_fails_for_blocked_user()
    {
        $this->actingAs($this->blockedUser);

        $response = $this->get('/');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_session_auth_fails_for_deleted_user()
    {
        $this->actingAs($this->deletedUser);

        $response = $this->get('/');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_session_auth_fails_with_empty_session()
    {
        $response = $this->withSession([])->get('/');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_users_index()
    {
        $nonAdminUser = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $this->actingAs($nonAdminUser);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_active_user_can_access_home()
    {
        $this->actingAs($this->activeUser);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_user_can_access_admin_pages()
    {
        $adminUser = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $adminUser->assignRole(UserRole::ADMIN);
        $this->actingAs($adminUser);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
    }
}
