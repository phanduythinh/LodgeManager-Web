<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
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

        // Fake storage
        Storage::fake('public');
    }

    public function test_admin_can_view_users()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'avatar',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_can_view_users()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'avatar',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_staff_cannot_view_users()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users', [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'staff',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'New User',
                'email' => 'new@example.com',
                'role' => 'staff',
            ]);
    }

    public function test_manager_can_create_user()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users', [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'staff',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'New User',
                'email' => 'new@example.com',
                'role' => 'staff',
            ]);
    }

    public function test_staff_cannot_create_user()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users', [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'staff',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/users/' . $this->staff->id, [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'manager',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'manager',
            ]);
    }

    public function test_manager_can_update_user()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/users/' . $this->staff->id, [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'staff',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'staff',
            ]);
    }

    public function test_staff_cannot_update_user()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/users/' . $this->manager->id, [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'staff',
            ]);

        $response->assertStatus(403);
    }

    public function test_manager_cannot_update_user_role()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/users/' . $this->staff->id, [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'admin',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'staff',
            ]);
    }

    public function test_admin_can_delete_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->staff->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted($this->staff);
    }

    public function test_manager_can_delete_user()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->staff->id);

        $response->assertStatus(204);
        $this->assertSoftDeleted($this->staff);
    }

    public function test_staff_cannot_delete_user()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->manager->id);

        $response->assertStatus(403);
    }

    public function test_manager_cannot_delete_admin()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->admin->id);

        $response->assertStatus(403);
    }

    public function test_admin_can_restore_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $this->staff->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users/' . $this->staff->id . '/restore');

        $response->assertStatus(200);
        $this->assertNotSoftDeleted($this->staff);
    }

    public function test_manager_can_restore_user()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $this->staff->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users/' . $this->staff->id . '/restore');

        $response->assertStatus(200);
        $this->assertNotSoftDeleted($this->staff);
    }

    public function test_staff_cannot_restore_user()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $this->manager->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users/' . $this->manager->id . '/restore');

        $response->assertStatus(403);
    }

    public function test_manager_cannot_restore_admin()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $this->admin->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users/' . $this->admin->id . '/restore');

        $response->assertStatus(403);
    }

    public function test_admin_can_force_delete_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $this->staff->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->staff->id . '/force');

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $this->staff->id]);
    }

    public function test_manager_cannot_force_delete_user()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $this->staff->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->staff->id . '/force');

        $response->assertStatus(403);
    }

    public function test_staff_cannot_force_delete_user()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $this->manager->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->manager->id . '/force');

        $response->assertStatus(403);
    }

    public function test_manager_cannot_force_delete_admin()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $this->admin->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/users/' . $this->admin->id . '/force');

        $response->assertStatus(403);
    }
}
