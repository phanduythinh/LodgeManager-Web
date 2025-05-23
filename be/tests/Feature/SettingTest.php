<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingTest extends TestCase
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

    public function test_admin_can_view_settings()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'value',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_can_view_settings()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/settings');

        $response->assertStatus(200);
    }

    public function test_staff_cannot_view_settings()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/settings');

        $response->assertStatus(403);
    }

    public function test_admin_can_update_settings()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/settings', [
                'site_name' => 'New Site Name',
                'site_description' => 'New Site Description',
                'contact_email' => 'new@example.com',
                'contact_phone' => '1234567890',
                'address' => 'New Address',
                'currency' => 'USD',
                'tax_rate' => 10,
                'invoice_prefix' => 'INV-',
                'contract_prefix' => 'CON-',
                'maintenance_notification_days' => 7,
                'rent_due_notification_days' => 3,
                'default_contract_duration' => 12,
                'default_rent_due_day' => 1,
                'default_late_fee_rate' => 5,
                'default_security_deposit' => 1000,
                'default_cleaning_fee' => 100,
                'default_utility_fee' => 50,
                'default_internet_fee' => 30,
                'default_parking_fee' => 20,
                'default_other_fee' => 0,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'value',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_cannot_update_settings()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/settings', [
                'site_name' => 'New Site Name',
                'site_description' => 'New Site Description',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_individual_setting()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/settings/site_name', [
                'value' => 'New Site Name',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'key',
                'value',
                'description',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'key' => 'site_name',
                'value' => 'New Site Name',
            ]);
    }

    public function test_manager_cannot_update_individual_setting()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/settings/site_name', [
                'value' => 'New Site Name',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_update_nonexistent_setting()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/settings/nonexistent_setting', [
                'value' => 'New Value',
            ]);

        $response->assertStatus(404);
    }

    public function test_admin_can_reset_settings_to_default()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/settings/reset');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'value',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_cannot_reset_settings()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/settings/reset');

        $response->assertStatus(403);
    }

    public function test_admin_can_export_settings()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/settings/export');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_manager_cannot_export_settings()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/settings/export');

        $response->assertStatus(403);
    }

    public function test_admin_can_import_settings()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/settings/import', [
                'settings' => [
                    'site_name' => 'Imported Site Name',
                    'site_description' => 'Imported Site Description',
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'value',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_cannot_import_settings()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/settings/import', [
                'settings' => [
                    'site_name' => 'Imported Site Name',
                    'site_description' => 'Imported Site Description',
                ],
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_import_invalid_settings()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/settings/import', [
                'settings' => [
                    'nonexistent_setting' => 'Invalid Value',
                ],
            ]);

        $response->assertStatus(422);
    }
}
