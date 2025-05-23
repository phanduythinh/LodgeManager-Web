<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FileUploadTest extends TestCase
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

        // Fake storage
        Storage::fake('public');
    }

    public function test_admin_can_upload_avatar()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('avatars/' . $file->hashName()));
    }

    public function test_manager_can_upload_avatar()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('avatars/' . $file->hashName()));
    }

    public function test_staff_can_upload_avatar()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('avatars/' . $file->hashName()));
    }

    public function test_admin_can_upload_contract_document()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/contract', [
                'document' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('contracts/' . $file->hashName()));
    }

    public function test_manager_can_upload_contract_document()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/contract', [
                'document' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('contracts/' . $file->hashName()));
    }

    public function test_staff_cannot_upload_contract_document()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('contract.pdf', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/contract', [
                'document' => $file,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_upload_invoice_document()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('invoice.pdf', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/invoice', [
                'document' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('invoices/' . $file->hashName()));
    }

    public function test_manager_can_upload_invoice_document()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('invoice.pdf', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/invoice', [
                'document' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('invoices/' . $file->hashName()));
    }

    public function test_staff_cannot_upload_invoice_document()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('invoice.pdf', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/invoice', [
                'document' => $file,
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_upload_invalid_file_type()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('document.exe', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/contract', [
                'document' => $file,
            ]);

        $response->assertStatus(422);
    }

    public function test_cannot_upload_file_exceeding_size_limit()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('document.pdf', 10241); // 10MB + 1KB

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload/contract', [
                'document' => $file,
            ]);

        $response->assertStatus(422);
    }
}
