<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
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

    public function test_admin_can_create_service()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/services', [
                'name' => 'Test Service',
                'description' => 'Test Service Description',
                'price' => 100000,
                'status' => 'active',
                'note' => 'Test Service Note',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'status',
                    'note',
                ],
            ]);
    }

    public function test_manager_can_create_service()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/services', [
                'name' => 'Test Service',
                'description' => 'Test Service Description',
                'price' => 100000,
                'status' => 'active',
                'note' => 'Test Service Note',
            ]);

        $response->assertStatus(201);
    }

    public function test_staff_cannot_create_service()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/services', [
                'name' => 'Test Service',
                'description' => 'Test Service Description',
                'price' => 100000,
                'status' => 'active',
                'note' => 'Test Service Note',
            ]);

        $response->assertStatus(403);
    }

    public function test_any_user_can_view_services()
    {
        Service::factory()->count(3)->create();

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/services');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'status',
                        'note',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'total',
                ],
            ]);
    }

    public function test_admin_can_update_service()
    {
        $service = Service::factory()->create();
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/services/{$service->id}", [
                'name' => 'Updated Service',
                'description' => 'Updated Service Description',
                'price' => 150000,
                'status' => 'inactive',
                'note' => 'Updated Service Note',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Service',
                    'description' => 'Updated Service Description',
                    'price' => 150000,
                    'status' => 'inactive',
                    'note' => 'Updated Service Note',
                ],
            ]);
    }

    public function test_admin_can_delete_service()
    {
        $service = Service::factory()->create();
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/services/{$service->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($service);
    }

    public function test_can_search_services_by_name()
    {
        Service::factory()->create([
            'name' => 'Cleaning Service',
        ]);

        Service::factory()->create([
            'name' => 'Laundry Service',
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/services?search=Cleaning');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'name' => 'Cleaning Service',
                    ],
                ],
            ]);
    }

    public function test_can_filter_services_by_status()
    {
        Service::factory()->create([
            'status' => 'active',
        ]);

        Service::factory()->create([
            'status' => 'inactive',
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/services?status=active');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'status' => 'active',
                    ],
                ],
            ]);
    }

    public function test_can_filter_services_by_price_range()
    {
        Service::factory()->create([
            'price' => 100000,
        ]);

        Service::factory()->create([
            'price' => 200000,
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/services?min_price=50000&max_price=150000');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'price' => 100000,
                    ],
                ],
            ]);
    }

    public function test_can_view_service_contracts()
    {
        $service = Service::factory()->create();
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/services/{$service->id}/contracts");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'customer_id',
                        'room_id',
                        'start_date',
                        'end_date',
                        'rent_amount',
                        'deposit_amount',
                        'status',
                        'note',
                    ],
                ],
            ]);
    }
}
