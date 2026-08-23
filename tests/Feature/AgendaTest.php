<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole('member');
    }

    public function test_can_list_agendas_with_strict_event_filtering(): void
    {
        $event = Event::factory()->create();

        Agenda::create([
            'title'      => 'BPH Pusat Agenda',
            'start_date' => now(),
            'event_id'   => null,
        ]);

        Agenda::create([
            'title'      => 'Event Agenda',
            'start_date' => now(),
            'event_id'   => $event->id,
        ]);

        // Query without event_id -> should return BPH Pusat agenda only
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/agendas');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'BPH Pusat Agenda']);

        // Query with event_id -> should return event agenda only
        $responseEvent = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/agendas?event_id=' . $event->id);

        $responseEvent->assertStatus(200);
        $responseEvent->assertJsonCount(1, 'data');
        $responseEvent->assertJsonFragment(['title' => 'Event Agenda']);
    }
}
