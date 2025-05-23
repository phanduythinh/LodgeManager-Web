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

class ReportTest extends TestCase
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

    public function test_admin_can_generate_revenue_report()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/revenue?' . http_build_query([
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'format' => 'pdf',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_manager_can_generate_revenue_report()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/revenue?' . http_build_query([
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'format' => 'pdf',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_staff_cannot_generate_revenue_report()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/revenue?' . http_build_query([
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'format' => 'pdf',
            ]));

        $response->assertStatus(403);
    }

    public function test_admin_can_generate_occupancy_report()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/occupancy?' . http_build_query([
                'building_id' => $this->building->id,
                'format' => 'pdf',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_generate_contract_report()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/contracts?' . http_build_query([
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'status' => 'active',
                'format' => 'pdf',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_generate_invoice_report()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/invoices?' . http_build_query([
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'status' => 'paid',
                'format' => 'pdf',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_generate_customer_report()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/customers?' . http_build_query([
                'status' => 'active',
                'format' => 'pdf',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_generate_service_report()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/services?' . http_build_query([
                'status' => 'active',
                'format' => 'pdf',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_can_generate_report_in_excel_format()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/revenue?' . http_build_query([
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'format' => 'excel',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_generate_report_in_csv_format()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/revenue?' . http_build_query([
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'format' => 'csv',
            ]));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv');
    }
}
