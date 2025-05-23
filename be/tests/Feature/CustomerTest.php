<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
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

    public function test_admin_can_create_customer()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/customers', [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '0123456789',
                'status' => 'active',
                'note' => 'Test Customer Note',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'status',
                    'note',
                ],
            ]);
    }

    public function test_manager_can_create_customer()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/customers', [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '0123456789',
                'status' => 'active',
                'note' => 'Test Customer Note',
            ]);

        $response->assertStatus(201);
    }

    public function test_staff_cannot_create_customer()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/customers', [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '0123456789',
                'status' => 'active',
                'note' => 'Test Customer Note',
            ]);

        $response->assertStatus(403);
    }

    public function test_any_user_can_view_customers()
    {
        Customer::factory()->count(3)->create();

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/customers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone',
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

    public function test_admin_can_update_customer()
    {
        $customer = Customer::factory()->create();
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/customers/{$customer->id}", [
                'name' => 'Updated Customer',
                'email' => 'updated@example.com',
                'phone' => '0987654321',
                'status' => 'inactive',
                'note' => 'Updated Customer Note',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Customer',
                    'email' => 'updated@example.com',
                    'phone' => '0987654321',
                    'status' => 'inactive',
                    'note' => 'Updated Customer Note',
                ],
            ]);
    }

    public function test_admin_can_delete_customer()
    {
        $customer = Customer::factory()->create();
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/customers/{$customer->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($customer);
    }

    public function test_can_search_customers_by_name()
    {
        Customer::factory()->create([
            'name' => 'John Doe',
        ]);

        Customer::factory()->create([
            'name' => 'Jane Smith',
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/customers?search=John');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'name' => 'John Doe',
                    ],
                ],
            ]);
    }

    public function test_can_filter_customers_by_status()
    {
        Customer::factory()->create([
            'status' => 'active',
        ]);

        Customer::factory()->create([
            'status' => 'inactive',
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/customers?status=active');

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

    public function test_can_view_customer_contracts()
    {
        $customer = Customer::factory()->create();
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/customers/{$customer->id}/contracts");

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

    public function test_can_view_customer_invoices()
    {
        $customer = Customer::factory()->create();
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/customers/{$customer->id}/invoices");

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
                    ],
                ],
            ]);
    }
}
