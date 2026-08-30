<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advisor', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->member = User::factory()->create();
        $this->member->assignRole('member');
    }

    public function test_can_list_users(): void
    {
        $response = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_get_user_filters(): void
    {
        $division = Division::create(['name' => 'Pengembangan SDM']);
        $this->member->update([
            'division_id' => $division->id,
            'prodi'       => 'Teknik Informatika',
            'angkatan'    => '2023',
        ]);

        $response = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/users/filters');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'divisions' => ['Pengembangan SDM'],
                    'prodis'    => ['Teknik Informatika'],
                    'angkatans' => ['2023'],
                ]
            ]);
    }

    public function test_admin_can_update_user(): void
    {
        $division = Division::create(['name' => 'Kominfo']);
        $targetUser = User::factory()->create();
        $targetUser->assignRole('member');

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/users/{$targetUser->id}", [
                'division_id'    => $division->id,
                'status'         => 'active',
                'role'           => 'advisor',
                'is_coordinator' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pengguna berhasil diperbarui',
            ]);

        $this->assertDatabaseHas('users', [
            'id'             => $targetUser->id,
            'division_id'    => $division->id,
            'status'         => 'active',
            'is_coordinator' => 1,
        ]);

        $this->assertTrue($targetUser->fresh()->hasRole('advisor'));
    }

    public function test_sync_users_requires_configured_url(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/users/sync');

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'URL Sinkronisasi Pengurus BPH Pusat belum dikonfigurasi di Pengaturan.',
            ]);
    }
}
