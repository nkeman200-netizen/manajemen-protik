<?php

namespace Tests\Feature;

use App\Models\CommitteePosition;
use App\Models\Event;
use App\Models\EventCommittee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckEventBPHTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

        Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckEventBPH::class])
            ->get('/api/test-event-bph', fn() => response()->json(['success' => true]));
    }

    public function test_admin_can_bypass_check_event_bph(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/test-event-bph?event_id=1');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_member_with_bph_position_is_allowed(): void
    {
        $member = User::factory()->create();
        $member->assignRole('member');

        $event = Event::factory()->create();
        $bphPos = CommitteePosition::create([
            'name'   => 'Ketua Pelaksana',
            'is_bph' => true,
        ]);

        EventCommittee::create([
            'event_id'    => $event->id,
            'user_id'     => $member->id,
            'position_id' => $bphPos->id,
        ]);

        $response = $this->actingAs($member, 'sanctum')
            ->getJson("/api/test-event-bph?event_id={$event->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_member_with_non_bph_position_is_rejected(): void
    {
        $member = User::factory()->create();
        $member->assignRole('member');

        $event = Event::factory()->create();
        $nonBphPos = CommitteePosition::create([
            'name'   => 'Anggota Acara',
            'is_bph' => false,
        ]);

        EventCommittee::create([
            'event_id'    => $event->id,
            'user_id'     => $member->id,
            'position_id' => $nonBphPos->id,
        ]);

        $response = $this->actingAs($member, 'sanctum')
            ->getJson("/api/test-event-bph?event_id={$event->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki wewenang BPH pada Event ini.',
            ]);
    }
}
