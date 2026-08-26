<?php

namespace Tests\Feature;

use App\Models\CommitteePosition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommitteePositionFeatureTest extends TestCase
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

    public function test_admin_can_list_committee_positions(): void
    {
        CommitteePosition::create([
            'name'   => 'Ketua Pelaksana',
            'is_bph' => true,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/committee-positions');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_create_committee_position(): void
    {
        $payload = [
            'name'   => 'Koordinator Acara',
            'is_bph' => false,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/committee-positions', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Jabatan kepanitiaan berhasil ditambahkan.',
            ]);

        $this->assertDatabaseHas('committee_positions', [
            'name'   => 'Koordinator Acara',
            'is_bph' => false,
        ]);
    }

    public function test_admin_can_update_committee_position(): void
    {
        $position = CommitteePosition::create([
            'name'   => 'Koordinator Humas Lama',
            'is_bph' => false,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/committee-positions/{$position->id}", [
                'name'   => 'Koordinator Humas Baru',
                'is_bph' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data jabatan berhasil diperbarui.',
            ]);

        $this->assertDatabaseHas('committee_positions', [
            'id'     => $position->id,
            'name'   => 'Koordinator Humas Baru',
            'is_bph' => true,
        ]);
    }

    public function test_admin_can_delete_committee_position(): void
    {
        $position = CommitteePosition::create([
            'name'   => 'Jabatan Hapus',
            'is_bph' => false,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/committee-positions/{$position->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data jabatan berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('committee_positions', [
            'id' => $position->id,
        ]);
    }

    public function test_member_cannot_access_committee_positions(): void
    {
        $response = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/committee-positions');

        $response->assertStatus(403);
    }
}
