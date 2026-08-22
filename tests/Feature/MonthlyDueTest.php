<?php

namespace Tests\Feature;

use App\Models\MonthlyDue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MonthlyDueTest extends TestCase
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

    public function test_admin_can_fetch_monthly_dues(): void
    {
        $user = User::factory()->create();
        MonthlyDue::create([
            'user_id' => $user->id,
            'month'   => 10,
            'year'    => 2025,
            'amount'  => 20000,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/monthly-dues');

        $response->assertStatus(200);
        $response->assertJsonStructure(['users', 'dues']);
    }
}
