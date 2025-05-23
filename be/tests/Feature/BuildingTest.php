<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingTest extends TestCase
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

    public function test_admin_can_create_building()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/buildings', [
                'name' => 'Test Building',
                'address' => '123 Test St',
                'description' => 'Test Description',
                'status' => 'active',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'address',
                    'description',
                    'status',
                ],
            ]);
    }

    public function test_manager_can_create_building()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/buildings', [
                'name' => 'Test Building',
                'address' => '123 Test St',
                'description' => 'Test Description',
                'status' => 'active',
            ]);

        $response->assertStatus(201);
    }

    public function test_staff_cannot_create_building()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/buildings', [
                'name' => 'Test Building',
                'address' => '123 Test St',
                'description' => 'Test Description',
                'status' => 'active',
            ]);

        $response->assertStatus(403);
    }

    public function test_any_user_can_view_buildings()
    {
        Building::factory()->count(3)->create();

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/buildings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'address',
                        'description',
                        'status',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'total',
                ],
            ]);
    }

    public function test_admin_can_update_building()
    {
        $building = Building::factory()->create();
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/buildings/{$building->id}", [
                'name' => 'Updated Building',
                'address' => '456 Updated St',
                'description' => 'Updated Description',
                'status' => 'inactive',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Building',
                    'address' => '456 Updated St',
                    'description' => 'Updated Description',
                    'status' => 'inactive',
                ],
            ]);
    }

    public function test_admin_can_delete_building()
    {
        $building = Building::factory()->create();
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/buildings/{$building->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($building);
    }
}
