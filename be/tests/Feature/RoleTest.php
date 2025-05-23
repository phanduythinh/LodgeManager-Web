<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
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

    public function test_admin_can_view_roles()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'permissions',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_cannot_view_roles()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/roles');

        $response->assertStatus(403);
    }

    public function test_staff_cannot_view_roles()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/roles');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_role()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_manager_cannot_create_role()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_create_role_with_existing_name()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a role
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ]);

        // Try to create another role with the same name
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_role()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a role
        $role = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ])
            ->json();

        // Update the role
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/roles/' . $role['id'], [
                'name' => 'Updated Role',
                'permissions' => ['view_buildings', 'view_rooms', 'view_contracts'],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'name' => 'Updated Role',
                'permissions' => ['view_buildings', 'view_rooms', 'view_contracts'],
            ]);
    }

    public function test_manager_cannot_update_role()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/roles/1', [
                'name' => 'Updated Role',
                'permissions' => ['view_buildings', 'view_rooms', 'view_contracts'],
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_role()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a role
        $role = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ])
            ->json();

        // Delete the role
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/roles/' . $role['id']);

        $response->assertStatus(204);
    }

    public function test_manager_cannot_delete_role()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/roles/1');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_delete_default_roles()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/roles/1'); // Assuming 1 is the ID of a default role

        $response->assertStatus(422);
    }

    public function test_admin_can_assign_role_to_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a role
        $role = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ])
            ->json();

        // Assign role to user
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles/' . $role['id'] . '/assign', [
                'user_id' => $this->staff->id,
            ]);

        $response->assertStatus(200);
    }

    public function test_manager_cannot_assign_role_to_user()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles/1/assign', [
                'user_id' => $this->staff->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_remove_role_from_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a role
        $role = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => ['view_buildings', 'view_rooms'],
            ])
            ->json();

        // Assign role to user
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles/' . $role['id'] . '/assign', [
                'user_id' => $this->staff->id,
            ]);

        // Remove role from user
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/roles/' . $role['id'] . '/assign', [
                'user_id' => $this->staff->id,
            ]);

        $response->assertStatus(200);
    }

    public function test_manager_cannot_remove_role_from_user()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/roles/1/assign', [
                'user_id' => $this->staff->id,
            ]);

        $response->assertStatus(403);
    }
}
