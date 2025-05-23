<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionTest extends TestCase
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

    public function test_admin_can_view_permissions()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_cannot_view_permissions()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }

    public function test_staff_cannot_view_permissions()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_permission()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is a new permission',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_manager_cannot_create_permission()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is a new permission',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_create_permission_with_existing_name()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a permission
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is a new permission',
            ]);

        // Try to create another permission with the same name
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is another permission',
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_permission()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a permission
        $permission = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is a new permission',
            ])
            ->json();

        // Update the permission
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/permissions/' . $permission['id'], [
                'name' => 'updated_permission',
                'description' => 'This is an updated permission',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'name' => 'updated_permission',
                'description' => 'This is an updated permission',
            ]);
    }

    public function test_manager_cannot_update_permission()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/permissions/1', [
                'name' => 'updated_permission',
                'description' => 'This is an updated permission',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_permission()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a permission
        $permission = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is a new permission',
            ])
            ->json();

        // Delete the permission
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/permissions/' . $permission['id']);

        $response->assertStatus(204);
    }

    public function test_manager_cannot_delete_permission()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/permissions/1');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_delete_default_permissions()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/permissions/1'); // Assuming 1 is the ID of a default permission

        $response->assertStatus(422);
    }

    public function test_admin_can_assign_permission_to_role()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a role
        $role = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => [],
            ])
            ->json();

        // First create a permission
        $permission = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is a new permission',
            ])
            ->json();

        // Assign permission to role
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions/' . $permission['id'] . '/assign', [
                'role_id' => $role['id'],
            ]);

        $response->assertStatus(200);
    }

    public function test_manager_cannot_assign_permission_to_role()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions/1/assign', [
                'role_id' => 1,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_remove_permission_from_role()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a role
        $role = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/roles', [
                'name' => 'New Role',
                'permissions' => [],
            ])
            ->json();

        // First create a permission
        $permission = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions', [
                'name' => 'new_permission',
                'description' => 'This is a new permission',
            ])
            ->json();

        // Assign permission to role
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/permissions/' . $permission['id'] . '/assign', [
                'role_id' => $role['id'],
            ]);

        // Remove permission from role
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/permissions/' . $permission['id'] . '/assign', [
                'role_id' => $role['id'],
            ]);

        $response->assertStatus(200);
    }

    public function test_manager_cannot_remove_permission_from_role()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/permissions/1/assign', [
                'role_id' => 1,
            ]);

        $response->assertStatus(403);
    }
}
