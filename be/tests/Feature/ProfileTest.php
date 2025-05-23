<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
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

    public function test_user_can_view_own_profile()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'role',
                'avatar',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_user_can_update_own_profile()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);
    }

    public function test_user_can_update_own_password()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile/password', [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_update_own_password_with_invalid_current_password()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile/password', [
                'current_password' => 'wrongpassword',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_update_own_password_with_unmatched_confirmation()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile/password', [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'differentpassword',
            ]);

        $response->assertStatus(422);
    }

    public function test_user_can_upload_own_avatar()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('avatars/' . $file->hashName()));
    }

    public function test_user_cannot_upload_invalid_avatar()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_upload_avatar_exceeding_size_limit()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->image('avatar.jpg')->size(2049); // 2MB + 1KB

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/profile/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(422);
    }

    public function test_user_can_delete_own_avatar()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;
        $file = UploadedFile::fake()->image('avatar.jpg');
        $this->admin->update(['avatar' => 'avatars/' . $file->hashName()]);
        Storage::disk('public')->put('avatars/' . $file->hashName(), $file->getContent());

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/profile/avatar');

        $response->assertStatus(200);
        $this->assertFalse(Storage::disk('public')->exists('avatars/' . $file->hashName()));
    }

    public function test_user_cannot_update_own_email_to_existing_one()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile', [
                'name' => 'Updated Name',
                'email' => $this->manager->email,
            ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_update_own_role()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'admin',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'staff',
            ]);
    }
}
