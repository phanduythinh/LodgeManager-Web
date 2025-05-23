<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityLogTest extends TestCase
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
    protected $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        // Create test data
        $this->building = Building::factory()->create();
        $this->room = Room::factory()->create(['building_id' => $this->building->id]);
        $this->customer = Customer::factory()->create();
        $this->contract = Contract::factory()->create([
            'room_id' => $this->room->id,
            'customer_id' => $this->customer->id,
        ]);
        $this->service = Service::factory()->create();
        $this->invoice = Invoice::factory()->create([
            'contract_id' => $this->contract->id,
        ]);
    }

    public function test_admin_can_view_activity_logs()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'action',
                        'model_type',
                        'model_id',
                        'old_values',
                        'new_values',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_can_view_activity_logs()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs');

        $response->assertStatus(200);
    }

    public function test_staff_cannot_view_activity_logs()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_activity_logs_by_user()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs?user_id=' . $this->admin->id);

        $response->assertStatus(200);
    }

    public function test_admin_can_view_activity_logs_by_action()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs?action=created');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_activity_logs_by_model()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs?model_type=App\\Models\\Building');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_activity_logs_by_date_range()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs?start_date=' . now()->subDay()->format('Y-m-d') . '&end_date=' . now()->format('Y-m-d'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_activity_logs_by_combined_filters()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs?user_id=' . $this->admin->id . '&action=created&model_type=App\\Models\\Building&start_date=' . now()->subDay()->format('Y-m-d') . '&end_date=' . now()->format('Y-m-d'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_activity_log_details()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create an activity log
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/buildings', [
                'name' => 'New Building',
                'address' => 'New Address',
                'description' => 'New Description',
            ]);

        // Get the activity log ID
        $activityLog = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs')
            ->json('data.0');

        // View activity log details
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/' . $activityLog['id']);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'old_values',
                'new_values',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_manager_cannot_view_activity_log_details()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/1');

        $response->assertStatus(403);
    }

    public function test_admin_can_export_activity_logs()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/export');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_manager_cannot_export_activity_logs()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/export');

        $response->assertStatus(403);
    }

    public function test_admin_can_export_activity_logs_by_filters()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/export?user_id=' . $this->admin->id . '&action=created&model_type=App\\Models\\Building&start_date=' . now()->subDay()->format('Y-m-d') . '&end_date=' . now()->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_export_activity_logs_in_different_formats()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // Export as Excel
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/export?format=xlsx');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Export as CSV
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/export?format=csv');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv');

        // Export as PDF
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/activity-logs/export?format=pdf');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
