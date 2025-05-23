<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'avatar',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->admin->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_cannot_login_with_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_logout()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_refresh_token()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function test_user_can_get_own_profile()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'role',
                'avatar',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_user_cannot_access_protected_route_without_token()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_user_cannot_access_protected_route_with_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_user_cannot_access_protected_route_with_expired_token()
    {
        $token = $this->admin->createToken('test-token', ['*'], now()->subDay())->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'avatar',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'New User',
            'email' => $this->admin->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_register_with_unmatched_password_confirmation()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_request_password_reset()
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => $this->admin->email,
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_request_password_reset_with_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_reset_password()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/reset-password', [
                'token' => 'reset-token',
                'email' => $this->admin->email,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_reset_password_with_invalid_token()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/reset-password', [
                'token' => 'invalid-token',
                'email' => $this->admin->email,
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_reset_password_with_unmatched_confirmation()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/reset-password', [
                'token' => 'reset-token',
                'email' => $this->admin->email,
                'password' => 'newpassword',
                'password_confirmation' => 'differentpassword',
            ]);

        $response->assertStatus(422);
    }
}
