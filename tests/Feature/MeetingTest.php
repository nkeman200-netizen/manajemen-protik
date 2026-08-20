<?php
namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MeetingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_can_list_meetings(): void
    {
        // Arrange
        Meeting::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/meetings');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [['id', 'title', 'date', 'minutes_url']],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
    }

    public function test_can_store_valid_meeting(): void
    {
        // Arrange
        $payload = [
            'title'       => 'Rapat Koordinasi Bulanan',
            'date'        => '2026-08-20 10:00:00',
            'minutes_url' => 'https://drive.google.com/notulen-rapat',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/meetings', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Success');
        $this->assertDatabaseHas('meetings', [
            'title' => 'Rapat Koordinasi Bulanan',
        ]);
    }
}
