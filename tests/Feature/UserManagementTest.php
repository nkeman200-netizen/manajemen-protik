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

    public function test_can_list_users_with_default_pagination(): void
    {
        User::factory()->count(20)->create();

        $response = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(15, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'email', 'nim', 'phone', 'prodi',
                        'angkatan', 'address', 'status', 'is_coordinator',
                        'division', 'roles'
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.total', 22); // 20 + admin + member in setUp
    }

    public function test_can_list_users_with_custom_per_page(): void
    {
        User::factory()->count(10)->create();

        $response = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/users?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 12);
    }

    public function test_can_list_all_users_without_pagination(): void
    {
        User::factory()->count(20)->create();

        // Menggunakan ?all=true
        $responseAll = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/users?all=true');

        $responseAll->assertStatus(200)
            ->assertJsonCount(22, 'data')
            ->assertJsonMissing(['meta']);

        // Menggunakan ?paginate=false
        $responseNoPaginate = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/users?paginate=false');

        $responseNoPaginate->assertStatus(200)
            ->assertJsonCount(22, 'data')
            ->assertJsonMissing(['meta']);
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
