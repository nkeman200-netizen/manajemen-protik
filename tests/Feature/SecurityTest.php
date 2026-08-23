<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advisor', 'guard_name' => 'web']);
    }

    public function test_member_is_forbidden_from_creating_divisions(): void
    {
        // Arrange
        $member = User::factory()->create();
        $member->assignRole('member');

        // Act
        $response = $this->actingAs($member, 'sanctum')
            ->postJson('/api/divisions', [
                'name'        => 'Divisi Ilegal',
                'description' => 'Divisi Test',
            ]);

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseMissing('divisions', ['name' => 'Divisi Ilegal']);
    }

    public function test_admin_can_create_divisions(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'name'        => 'Divisi Humas',
            'description' => 'Divisi Hubungan Masyarakat',
        ];

        // Act
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/divisions', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('divisions', ['name' => 'Divisi Humas']);
    }

    public function test_member_can_only_see_own_warnings(): void
    {
        // Arrange
        $member = User::factory()->create();
        $member->assignRole('member');
        $otherUser = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Warning milik member
        Warning::factory()->count(2)->create([
            'user_id'  => $member->id,
            'admin_id' => $admin->id,
        ]);

        // Warning milik orang lain
        Warning::factory()->count(3)->create([
            'user_id'  => $otherUser->id,
            'admin_id' => $admin->id,
        ]);

        // Act
        $response = $this->actingAs($member, 'sanctum')
            ->getJson('/api/warnings');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $returnedUserIds = collect($response->json('data'))->pluck('user_id')->unique()->values();
        $this->assertEquals([$member->id], $returnedUserIds->toArray());
    }
}
