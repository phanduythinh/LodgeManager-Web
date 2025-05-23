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

class InvoiceTest extends TestCase
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

    public function test_admin_can_create_invoice()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/invoices', [
                'invoice_number' => 'INV-001',
                'customer_id' => $this->customer->id,
                'contract_id' => $this->contract->id,
                'issue_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'total_amount' => 1000000,
                'status' => 'pending',
                'services' => [
                    [
                        'service_id' => $this->service->id,
                        'quantity' => 1,
                        'price' => 100000,
                    ],
                ],
                'note' => 'Test Invoice',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'invoice_number',
                    'customer_id',
                    'contract_id',
                    'issue_date',
                    'due_date',
                    'total_amount',
                    'status',
                    'services',
                    'note',
                ],
            ]);
    }

    public function test_manager_can_create_invoice()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/invoices', [
                'invoice_number' => 'INV-002',
                'customer_id' => $this->customer->id,
                'contract_id' => $this->contract->id,
                'issue_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'total_amount' => 1000000,
                'status' => 'pending',
                'services' => [
                    [
                        'service_id' => $this->service->id,
                        'quantity' => 1,
                        'price' => 100000,
                    ],
                ],
                'note' => 'Test Invoice',
            ]);

        $response->assertStatus(201);
    }

    public function test_staff_cannot_create_invoice()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/invoices', [
                'invoice_number' => 'INV-003',
                'customer_id' => $this->customer->id,
                'contract_id' => $this->contract->id,
                'issue_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'total_amount' => 1000000,
                'status' => 'pending',
                'services' => [
                    [
                        'service_id' => $this->service->id,
                        'quantity' => 1,
                        'price' => 100000,
                    ],
                ],
                'note' => 'Test Invoice',
            ]);

        $response->assertStatus(403);
    }

    public function test_any_user_can_view_invoices()
    {
        Invoice::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/invoices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'invoice_number',
                        'customer_id',
                        'contract_id',
                        'issue_date',
                        'due_date',
                        'total_amount',
                        'status',
                        'services',
                        'note',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'total',
                ],
            ]);
    }

    public function test_admin_can_update_invoice()
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
        ]);
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/invoices/{$invoice->id}", [
                'invoice_number' => 'INV-004',
                'customer_id' => $this->customer->id,
                'contract_id' => $this->contract->id,
                'issue_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(14)->format('Y-m-d'),
                'total_amount' => 1500000,
                'status' => 'paid',
                'services' => [
                    [
                        'service_id' => $this->service->id,
                        'quantity' => 2,
                        'price' => 150000,
                    ],
                ],
                'note' => 'Updated Invoice',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'invoice_number' => 'INV-004',
                    'due_date' => now()->addDays(14)->format('Y-m-d'),
                    'total_amount' => 1500000,
                    'status' => 'paid',
                    'note' => 'Updated Invoice',
                ],
            ]);
    }

    public function test_admin_can_delete_invoice()
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
        ]);
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($invoice);
    }

    public function test_can_filter_invoices_by_customer()
    {
        Invoice::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/invoices?customer_id={$this->customer->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_invoices_by_status()
    {
        Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
            'status' => 'pending',
        ]);

        Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
            'status' => 'paid',
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/invoices?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'status' => 'pending',
                    ],
                ],
            ]);
    }

    public function test_can_filter_invoices_by_date_range()
    {
        Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
            'issue_date' => now()->subMonths(6),
            'due_date' => now()->subMonths(5),
        ]);

        Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
            'issue_date' => now()->addMonths(6),
            'due_date' => now()->addMonths(7),
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/invoices?start_date=' . now()->format('Y-m-d') . '&end_date=' . now()->addMonths(12)->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_mark_invoice_as_paid()
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'contract_id' => $this->contract->id,
            'status' => 'pending',
        ]);
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/invoices/{$invoice->id}/mark-as-paid", [
                'payment_date' => now()->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'transaction_id' => 'TRX-001',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'paid',
                    'payment_date' => now()->format('Y-m-d'),
                    'payment_method' => 'bank_transfer',
                    'transaction_id' => 'TRX-001',
                ],
            ]);
    }
}
