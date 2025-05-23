<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $staff;
    protected $building;
    protected $room;
    protected $customer;
    protected $contract;
    protected $service;

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

        // Create a contract for testing
        $this->contract = Contract::factory()->create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
        ]);

        // Create a service for testing
        $this->service = Service::factory()->create();
    }

    public function test_admin_can_view_dashboard_statistics()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_buildings',
                    'total_rooms',
                    'total_customers',
                    'total_contracts',
                    'total_services',
                    'total_invoices',
                    'total_revenue',
                    'total_pending_invoices',
                    'total_paid_invoices',
                    'occupancy_rate',
                ],
            ]);
    }

    public function test_manager_can_view_dashboard_statistics()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/dashboard');

        $response->assertStatus(200);
    }

    public function test_staff_cannot_view_dashboard_statistics()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_revenue_statistics()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/revenue');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_revenue',
                    'revenue_by_month' => [
                        '*' => [
                            'month',
                            'revenue',
                        ],
                    ],
                    'revenue_by_service' => [
                        '*' => [
                            'service_id',
                            'service_name',
                            'revenue',
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_can_view_occupancy_statistics()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/occupancy');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_rooms',
                    'occupied_rooms',
                    'available_rooms',
                    'occupancy_rate',
                    'occupancy_by_building' => [
                        '*' => [
                            'building_id',
                            'building_name',
                            'total_rooms',
                            'occupied_rooms',
                            'occupancy_rate',
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_can_view_contract_statistics()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/contracts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_contracts',
                    'active_contracts',
                    'expired_contracts',
                    'contracts_by_status' => [
                        '*' => [
                            'status',
                            'count',
                        ],
                    ],
                    'contracts_by_month' => [
                        '*' => [
                            'month',
                            'count',
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_can_view_invoice_statistics()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/invoices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_invoices',
                    'total_revenue',
                    'total_pending_amount',
                    'invoices_by_status' => [
                        '*' => [
                            'status',
                            'count',
                            'amount',
                        ],
                    ],
                    'invoices_by_month' => [
                        '*' => [
                            'month',
                            'count',
                            'amount',
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_can_view_customer_statistics()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/customers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_customers',
                    'active_customers',
                    'inactive_customers',
                    'customers_by_status' => [
                        '*' => [
                            'status',
                            'count',
                        ],
                    ],
                    'customers_by_month' => [
                        '*' => [
                            'month',
                            'count',
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_can_view_service_statistics()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/statistics/services');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_services',
                    'active_services',
                    'inactive_services',
                    'services_by_status' => [
                        '*' => [
                            'status',
                            'count',
                        ],
                    ],
                    'services_by_revenue' => [
                        '*' => [
                            'service_id',
                            'service_name',
                            'revenue',
                        ],
                    ],
                ],
            ]);
    }
}
