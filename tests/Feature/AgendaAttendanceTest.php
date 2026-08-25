<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\AgendaAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaAttendanceTest extends TestCase
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

    public function test_can_fetch_and_bulk_sync_agenda_attendances(): void
    {
        $agenda = Agenda::create([
            'title'      => 'Rapat Pleno',
            'start_date' => now(),
        ]);

        $attendee = User::factory()->create();

        $payload = [
            'agenda_id'   => $agenda->id,
            'attendances' => [
                [
                    'user_id' => $attendee->id,
                    'status'  => 'present',
                ],
            ],
        ];

        $postResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/agenda-attendances/bulk', $payload);

        $postResponse->assertStatus(200);
        $this->assertDatabaseHas('agenda_attendances', [
            'agenda_id' => $agenda->id,
            'user_id'   => $attendee->id,
            'status'    => 'present',
        ]);

        $getResponse = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/agenda-attendances?agenda_id=' . $agenda->id);

        $getResponse->assertStatus(200);
        $getResponse->assertJsonCount(1);
    }
}
