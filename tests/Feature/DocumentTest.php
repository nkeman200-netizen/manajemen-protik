<?php
namespace Tests\Feature;

use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_documents(): void
    {
        // Arrange
        $user = User::factory()->create();
        Document::factory()->count(3)->create(['created_by' => $user->id]);

        // Act
        $response = $this->getJson('/api/documents');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'message',
            'data' => [['id', 'created_by', 'letter_number', 'title', 'drive_url']],
        ]);
    }

    public function test_can_store_valid_document(): void
    {
        // Arrange
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $payload = [
            'created_by'    => $user->id,
            'event_id'      => $event->id,
            'letter_number' => 'SK-001/PROTIK/2026',
            'title'         => 'Surat Keputusan Panitia',
            'drive_url'     => 'https://drive.google.com/sk-panitia',
        ];

        // Act
        $response = $this->postJson('/api/documents', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Success');
        $this->assertDatabaseHas('documents', [
            'letter_number' => 'SK-001/PROTIK/2026',
            'created_by'    => $user->id,
        ]);
    }
}
