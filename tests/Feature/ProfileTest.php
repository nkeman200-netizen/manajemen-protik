<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $this->user = User::factory()->create([
            'name'     => 'Original Name',
            'email'    => 'user@protik.com',
            'password' => Hash::make('password123'),
        ]);
        $this->user->assignRole('member');
    }

    public function test_user_can_update_profile(): void
    {
        $payload = [
            'name'     => 'Updated Name',
            'email'    => 'updated@protik.com',
            'nim'      => '123456789',
            'phone'    => '081234567890',
            'prodi'    => 'Teknologi Informasi',
            'angkatan' => '2024',
            'address'  => 'Jl. Pendidikan No. 1',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/user/profile', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name'     => 'Updated Name',
                'email'    => 'updated@protik.com',
                'nim'      => '123456789',
                'phone'    => '081234567890',
                'prodi'    => 'Teknologi Informasi',
                'angkatan' => '2024',
                'address'  => 'Jl. Pendidikan No. 1',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'id'       => $this->user->id,
            'name'     => 'Updated Name',
            'email'    => 'updated@protik.com',
            'nim'      => '123456789',
            'phone'    => '081234567890',
            'prodi'    => 'Teknologi Informasi',
            'angkatan' => '2024',
            'address'  => 'Jl. Pendidikan No. 1',
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $payload = [
            'current_password'      => 'password123',
            'password'              => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/user/password', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Kata sandi berhasil diperbarui.',
        ]);

        $this->assertTrue(Hash::check('newsecret123', $this->user->fresh()->password));
    }
}
