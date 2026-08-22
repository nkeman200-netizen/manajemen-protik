<?php

namespace Tests\Feature;

use App\Models\AuditTrail;
use App\Models\Division;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditTrailTest extends TestCase
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

    public function test_model_events_are_recorded_in_audit_trails(): void
    {
        // Act as admin
        $this->actingAs($this->admin, 'sanctum');

        $event = Event::create([
            'name'            => 'Hackathon Protik',
            'budget_approved' => 5000000,
            'start_date'      => '2026-09-01',
        ]);

        $this->assertDatabaseHas('audit_trails', [
            'user_id'        => $this->admin->id,
            'action'         => 'created',
            'auditable_type' => Event::class,
            'auditable_id'   => $event->id,
        ]);

        $event->update(['name' => 'Hackathon Protik 2026']);

        $this->assertDatabaseHas('audit_trails', [
            'user_id'        => $this->admin->id,
            'action'         => 'updated',
            'auditable_type' => Event::class,
            'auditable_id'   => $event->id,
        ]);

        $event->delete();

        $this->assertDatabaseHas('audit_trails', [
            'user_id'        => $this->admin->id,
            'action'         => 'deleted',
            'auditable_type' => Event::class,
            'auditable_id'   => $event->id,
        ]);
    }

    public function test_admin_can_fetch_audit_trails(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/audit-trails');

        $response->assertStatus(200);
    }
}
