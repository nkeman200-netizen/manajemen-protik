<?php
namespace Tests\Feature;

use App\Models\Meeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_meetings(): void
    {
        // Arrange
        Meeting::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/meetings');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'message',
            'data' => [['id', 'title', 'date', 'minutes_url']],
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
        $response = $this->postJson('/api/meetings', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Success');
        $this->assertDatabaseHas('meetings', [
            'title' => 'Rapat Koordinasi Bulanan',
        ]);
    }
}
