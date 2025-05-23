<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $staff;
    protected $building;
    protected $room;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        // Create a building and room for testing
        $this->building = Building::factory()->create();
        $this->room = Room::factory()->create([
            'building_id' => $this->building->id,
        ]);

        // Create a customer for testing
        $this->customer = Customer::factory()->create();
    }

    public function test_admin_can_create_contract()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/contracts', [
                'customer_id' => $this->customer->id,
                'room_id' => $this->room->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(12)->format('Y-m-d'),
                'rent_amount' => 1000000,
                'deposit_amount' => 2000000,
                'status' => 'active',
                'note' => 'Test Contract',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
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
            ]);
    }

    public function test_manager_can_create_contract()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/contracts', [
                'customer_id' => $this->customer->id,
                'room_id' => $this->room->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(12)->format('Y-m-d'),
                'rent_amount' => 1000000,
                'deposit_amount' => 2000000,
                'status' => 'active',
                'note' => 'Test Contract',
            ]);

        $response->assertStatus(201);
    }

    public function test_staff_cannot_create_contract()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/contracts', [
                'customer_id' => $this->customer->id,
                'room_id' => $this->room->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(12)->format('Y-m-d'),
                'rent_amount' => 1000000,
                'deposit_amount' => 2000000,
                'status' => 'active',
                'note' => 'Test Contract',
            ]);

        $response->assertStatus(403);
    }

    public function test_any_user_can_view_contracts()
    {
        Contract::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/contracts');

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
                'meta' => [
                    'current_page',
                    'total',
                ],
            ]);
    }

    public function test_admin_can_update_contract()
    {
        $contract = Contract::factory()->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
        ]);
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/contracts/{$contract->id}", [
                'customer_id' => $this->customer->id,
                'room_id' => $this->room->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(24)->format('Y-m-d'),
                'rent_amount' => 1200000,
                'deposit_amount' => 2400000,
                'status' => 'inactive',
                'note' => 'Updated Contract',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'end_date' => now()->addMonths(24)->format('Y-m-d'),
                    'rent_amount' => 1200000,
                    'deposit_amount' => 2400000,
                    'status' => 'inactive',
                    'note' => 'Updated Contract',
                ],
            ]);
    }

    public function test_admin_can_delete_contract()
    {
        $contract = Contract::factory()->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
        ]);
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/contracts/{$contract->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($contract);
    }

    public function test_can_filter_contracts_by_customer()
    {
        Contract::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/contracts?customer_id={$this->customer->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_contracts_by_status()
    {
        Contract::factory()->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
            'status' => 'active',
        ]);

        Contract::factory()->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
            'status' => 'inactive',
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/contracts?status=active');

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

    public function test_can_filter_contracts_by_date_range()
    {
        Contract::factory()->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
        ]);

        Contract::factory()->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
            'start_date' => now()->addMonths(6),
            'end_date' => now()->addMonths(18),
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/contracts?start_date=' . now()->format('Y-m-d') . '&end_date=' . now()->addMonths(12)->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
