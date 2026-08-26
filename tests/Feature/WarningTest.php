<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WarningTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->member = User::factory()->create();
        $this->member->assignRole('member');
    }

    public function test_can_list_warnings(): void
    {
        // Arrange
        Warning::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/warnings');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [['id', 'user_id', 'admin_id', 'reason', 'date', 'read_at']],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
    }

    public function test_can_store_valid_warning(): void
    {
        // Arrange
        $user = User::factory()->create();

        $payload = [
            'user_id'  => $user->id,
            'admin_id' => $this->admin->id,
            'reason'   => 'Tidak hadir 3 kali berturut-turut tanpa keterangan.',
            'date'     => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/warnings', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Success');
        $this->assertDatabaseHas('warnings', [
            'user_id'  => $user->id,
            'admin_id' => $this->admin->id,
            'reason'   => 'Tidak hadir 3 kali berturut-turut tanpa keterangan.',
        ]);
    }

    public function test_owner_can_mark_warning_as_read(): void
    {
        $warning = Warning::factory()->create([
            'user_id'  => $this->member->id,
            'admin_id' => $this->admin->id,
            'read_at'  => null,
        ]);

        $response = $this->actingAs($this->member, 'sanctum')
            ->patchJson("/api/warnings/{$warning->id}/read");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Success']);

        $this->assertNotNull($warning->fresh()->read_at);
    }

    public function test_other_user_cannot_mark_warning_as_read(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('member');

        $warning = Warning::factory()->create([
            'user_id'  => $this->member->id,
            'admin_id' => $this->admin->id,
            'read_at'  => null,
        ]);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->patchJson("/api/warnings/{$warning->id}/read");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Anda tidak berhak mengakses ini.']);

        $this->assertNull($warning->fresh()->read_at);
    }
}
