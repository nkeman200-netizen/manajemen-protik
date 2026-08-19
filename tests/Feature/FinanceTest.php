<?php
namespace Tests\Feature;

use App\Models\Event;
use App\Models\Finance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinanceTest extends TestCase
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

    public function test_can_list_and_filter_finances(): void
    {
        // Arrange
        $event = Event::factory()->create();
        Finance::create([
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'income',
            'amount'      => 500.00,
            'description' => 'Sponsorship Tech Conference',
            'date'        => '2026-08-15',
        ]);

        Finance::create([
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 100.00,
            'description' => 'Beli ATK',
            'date'        => '2026-08-18',
        ]);

        // Act & Assert: List all
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/finances');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [['id', 'user_id', 'event_id', 'type', 'amount', 'description', 'date']],
            'current_page',
            'per_page',
            'total',
        ]);

        // Act & Assert: Filter search
        $searchResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/finances?search=Tech');
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonCount(1, 'data');
        $searchResponse->assertJsonFragment(['description' => 'Sponsorship Tech Conference']);
    }

    public function test_admin_can_record_income_without_budget_limit(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        $payload = [
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'income',
            'amount'      => 999999.99,
            'description' => 'Dana sponsor masuk',
            'date'        => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('finances', [
            'user_id'  => $this->admin->id,
            'event_id' => $event->id,
            'type'     => 'income',
            'amount'   => 999999.99,
        ]);
    }

    public function test_expense_is_rejected_when_exceeding_event_budget(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 500.00]);

        Finance::create([
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 400.00,
            'description' => 'Sewa tempat',
            'date'        => '2026-08-19',
        ]);

        $payload = [
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 200.00,
            'description' => 'Konsumsi rapat',
            'date'        => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('amount');
        $this->assertDatabaseMissing('finances', [
            'description' => 'Konsumsi rapat',
        ]);
    }

    public function test_expense_within_budget_is_accepted(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        Finance::create([
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 400.00,
            'description' => 'Sewa tempat',
            'date'        => '2026-08-19',
        ]);

        $payload = [
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 600.00,
            'description' => 'Dekorasi',
            'date'        => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('finances', [
            'description' => 'Dekorasi',
            'amount'      => 600.00,
        ]);
    }
}
