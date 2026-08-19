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

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
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
            'message',
            'data' => [['id', 'user_id', 'admin_id', 'reason', 'date']],
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
}
