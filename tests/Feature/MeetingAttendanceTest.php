<?php
namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\MeetingAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MeetingAttendanceTest extends TestCase
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

    public function test_can_list_attendances(): void
    {
        // Arrange
        MeetingAttendance::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/meeting-attendances');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'message',
            'data' => [['id', 'meeting_id', 'user_id', 'status']],
        ]);
    }

    public function test_can_store_valid_attendance(): void
    {
        // Arrange
        $meeting = Meeting::factory()->create();
        $user = User::factory()->create();

        $payload = [
            'meeting_id' => $meeting->id,
            'user_id'    => $user->id,
            'status'     => 'present',
            'proof_url'  => null,
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/meeting-attendances', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Success');
        $this->assertDatabaseHas('meeting_attendances', [
            'meeting_id' => $meeting->id,
            'user_id'    => $user->id,
            'status'     => 'present',
        ]);
    }
}
