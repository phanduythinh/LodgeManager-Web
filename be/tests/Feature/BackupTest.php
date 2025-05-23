<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BackupTest extends TestCase
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

    public function test_admin_can_view_backups()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/backups');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'filename',
                        'size',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_manager_cannot_view_backups()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/backups');

        $response->assertStatus(403);
    }

    public function test_staff_cannot_view_backups()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/backups');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_backup()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'filename',
                'size',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_manager_cannot_create_backup()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups');

        $response->assertStatus(403);
    }

    public function test_admin_can_download_backup()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a backup
        $backup = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups')
            ->json();

        // Download the backup
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/backups/' . $backup['id'] . '/download');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/zip');
    }

    public function test_manager_cannot_download_backup()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/backups/1/download');

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_backup()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a backup
        $backup = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups')
            ->json();

        // Delete the backup
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/backups/' . $backup['id']);

        $response->assertStatus(204);
    }

    public function test_manager_cannot_delete_backup()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/backups/1');

        $response->assertStatus(403);
    }

    public function test_admin_can_restore_backup()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        // First create a backup
        $backup = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups')
            ->json();

        // Restore the backup
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/' . $backup['id'] . '/restore');

        $response->assertStatus(200);
    }

    public function test_manager_cannot_restore_backup()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/1/restore');

        $response->assertStatus(403);
    }

    public function test_admin_can_upload_backup()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/upload', [
                'file' => 'test_backup.zip',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'filename',
                'size',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_manager_cannot_upload_backup()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/upload', [
                'file' => 'test_backup.zip',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_upload_invalid_backup()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/upload', [
                'file' => 'invalid.txt',
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_schedule_backup()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/schedule', [
                'frequency' => 'daily',
                'time' => '00:00',
                'retention' => 7,
            ]);

        $response->assertStatus(200);
    }

    public function test_manager_cannot_schedule_backup()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/schedule', [
                'frequency' => 'daily',
                'time' => '00:00',
                'retention' => 7,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_schedule_backup_with_invalid_frequency()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/backups/schedule', [
                'frequency' => 'invalid',
                'time' => '00:00',
                'retention' => 7,
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_view_backup_schedule()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/backups/schedule');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'frequency',
                'time',
                'retention',
                'last_backup',
                'next_backup',
            ]);
    }

    public function test_manager_cannot_view_backup_schedule()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/backups/schedule');

        $response->assertStatus(403);
    }
}
