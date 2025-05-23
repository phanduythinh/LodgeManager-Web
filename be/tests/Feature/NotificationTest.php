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

class NotificationTest extends TestCase
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

    public function test_admin_can_view_notifications()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'data',
                        'read_at',
                        'created_at',
                    ],
                ],
            ]);
    }

    public function test_manager_can_view_notifications()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'data',
                        'read_at',
                        'created_at',
                    ],
                ],
            ]);
    }

    public function test_staff_can_view_notifications()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'data',
                        'read_at',
                        'created_at',
                    ],
                ],
            ]);
    }

    public function test_admin_can_mark_notification_as_read()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $notification = $this->admin->notifications()->create([
            'type' => 'App\Notifications\ContractExpiring',
            'data' => [
                'contract_id' => $this->contract->id,
                'message' => 'Contract is expiring soon',
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_manager_can_mark_notification_as_read()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $notification = $this->manager->notifications()->create([
            'type' => 'App\Notifications\ContractExpiring',
            'data' => [
                'contract_id' => $this->contract->id,
                'message' => 'Contract is expiring soon',
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_staff_can_mark_notification_as_read()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $notification = $this->staff->notifications()->create([
            'type' => 'App\Notifications\ContractExpiring',
            'data' => [
                'contract_id' => $this->contract->id,
                'message' => 'Contract is expiring soon',
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_can_mark_all_notifications_as_read()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $this->admin->notifications()->createMany([
            [
                'type' => 'App\Notifications\ContractExpiring',
                'data' => [
                    'contract_id' => $this->contract->id,
                    'message' => 'Contract is expiring soon',
                ],
            ],
            [
                'type' => 'App\Notifications\InvoiceDue',
                'data' => [
                    'invoice_id' => 1,
                    'message' => 'Invoice is due soon',
                ],
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/read-all');

        $response->assertStatus(200);
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());
    }

    public function test_manager_can_mark_all_notifications_as_read()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $this->manager->notifications()->createMany([
            [
                'type' => 'App\Notifications\ContractExpiring',
                'data' => [
                    'contract_id' => $this->contract->id,
                    'message' => 'Contract is expiring soon',
                ],
            ],
            [
                'type' => 'App\Notifications\InvoiceDue',
                'data' => [
                    'invoice_id' => 1,
                    'message' => 'Invoice is due soon',
                ],
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/read-all');

        $response->assertStatus(200);
        $this->assertEquals(0, $this->manager->unreadNotifications()->count());
    }

    public function test_staff_can_mark_all_notifications_as_read()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $this->staff->notifications()->createMany([
            [
                'type' => 'App\Notifications\ContractExpiring',
                'data' => [
                    'contract_id' => $this->contract->id,
                    'message' => 'Contract is expiring soon',
                ],
            ],
            [
                'type' => 'App\Notifications\InvoiceDue',
                'data' => [
                    'invoice_id' => 1,
                    'message' => 'Invoice is due soon',
                ],
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/read-all');

        $response->assertStatus(200);
        $this->assertEquals(0, $this->staff->unreadNotifications()->count());
    }

    public function test_cannot_mark_other_user_notification_as_read()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $notification = $this->admin->notifications()->create([
            'type' => 'App\Notifications\ContractExpiring',
            'data' => [
                'contract_id' => $this->contract->id,
                'message' => 'Contract is expiring soon',
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(403);
        $this->assertNull($notification->fresh()->read_at);
    }
}
