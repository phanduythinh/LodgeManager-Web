<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $staff;
    protected $building;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        // Create a building for testing
        $this->building = Building::factory()->create();
    }

    public function test_admin_can_create_room()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/rooms', [
                'building_id' => $this->building->id,
                'room_number' => '101',
                'floor' => 1,
                'area' => 50.5,
                'price' => 1000000,
                'status' => 'available',
                'description' => 'Test Room',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'building_id',
                    'room_number',
                    'floor',
                    'area',
                    'price',
                    'status',
                    'description',
                ],
            ]);
    }

    public function test_manager_can_create_room()
    {
        $token = $this->manager->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/rooms', [
                'building_id' => $this->building->id,
                'room_number' => '102',
                'floor' => 1,
                'area' => 50.5,
                'price' => 1000000,
                'status' => 'available',
                'description' => 'Test Room',
            ]);

        $response->assertStatus(201);
    }

    public function test_staff_cannot_create_room()
    {
        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/rooms', [
                'building_id' => $this->building->id,
                'room_number' => '103',
                'floor' => 1,
                'area' => 50.5,
                'price' => 1000000,
                'status' => 'available',
                'description' => 'Test Room',
            ]);

        $response->assertStatus(403);
    }

    public function test_any_user_can_view_rooms()
    {
        Room::factory()->count(3)->create([
            'building_id' => $this->building->id,
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/rooms');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'building_id',
                        'room_number',
                        'floor',
                        'area',
                        'price',
                        'status',
                        'description',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'total',
                ],
            ]);
    }

    public function test_admin_can_update_room()
    {
        $room = Room::factory()->create([
            'building_id' => $this->building->id,
        ]);
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/rooms/{$room->id}", [
                'building_id' => $this->building->id,
                'room_number' => '201',
                'floor' => 2,
                'area' => 60.5,
                'price' => 1200000,
                'status' => 'occupied',
                'description' => 'Updated Room',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'room_number' => '201',
                    'floor' => 2,
                    'area' => 60.5,
                    'price' => 1200000,
                    'status' => 'occupied',
                    'description' => 'Updated Room',
                ],
            ]);
    }

    public function test_admin_can_delete_room()
    {
        $room = Room::factory()->create([
            'building_id' => $this->building->id,
        ]);
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/rooms/{$room->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($room);
    }

    public function test_can_filter_rooms_by_building()
    {
        Room::factory()->count(3)->create([
            'building_id' => $this->building->id,
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/rooms?building_id={$this->building->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_rooms_by_status()
    {
        Room::factory()->create([
            'building_id' => $this->building->id,
            'status' => 'available',
        ]);

        Room::factory()->create([
            'building_id' => $this->building->id,
            'status' => 'occupied',
        ]);

        $token = $this->staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/rooms?status=available');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'status' => 'available',
                    ],
                ],
            ]);
    }
}
