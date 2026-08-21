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
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'income',
            'title'      => 'Sponsorship Tech Conference',
            'qty'        => 1,
            'unit'       => 'Paket',
            'unit_price' => 500.00,
            'amount'     => 500.00,
            'notes'      => 'Sponsorship utama',
            'date'       => '2026-08-15',
        ]);

        Finance::create([
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Beli ATK',
            'qty'        => 2,
            'unit'       => 'Rim',
            'unit_price' => 50.00,
            'amount'     => 100.00,
            'notes'      => 'Kertas HVS A4',
            'date'       => '2026-08-18',
        ]);

        // Act & Assert: List all
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/finances');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [['id', 'user_id', 'event_id', 'type', 'title', 'qty', 'unit', 'unit_price', 'amount', 'notes', 'date']],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);

        // Act & Assert: Filter search
        $searchResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/finances?search=Tech');
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonCount(1, 'data');
        $searchResponse->assertJsonFragment(['title' => 'Sponsorship Tech Conference']);
    }

    public function test_admin_can_record_income_without_budget_limit(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        $payload = [
            'user_id'     => $this->admin->id,
            'event_id'    => $event->id,
            'type'        => 'income',
            'title'       => 'Dana sponsor masuk',
            'qty'         => 1,
            'unit'        => 'Ls',
            'unit_price'  => 999999.99,
            'notes'       => 'Dana masuk sponsorship',
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
            'title'    => 'Dana sponsor masuk',
            'amount'   => 999999.99,
        ]);
    }

    public function test_expense_is_rejected_when_exceeding_event_budget(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 500.00]);

        Finance::create([
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Sewa tempat',
            'qty'        => 1,
            'unit'       => 'Hari',
            'unit_price' => 400.00,
            'amount'     => 400.00,
            'date'       => '2026-08-19',
        ]);

        $payload = [
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Konsumsi rapat',
            'qty'        => 20,
            'unit'       => 'Kotak',
            'unit_price' => 10.00,
            'date'       => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('amount');
        $this->assertDatabaseMissing('finances', [
            'title' => 'Konsumsi rapat',
        ]);
    }

    public function test_expense_within_budget_is_accepted(): void
    {
        // Arrange
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        Finance::create([
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Sewa tempat',
            'qty'        => 1,
            'unit'       => 'Hari',
            'unit_price' => 400.00,
            'amount'     => 400.00,
            'date'       => '2026-08-19',
        ]);

        $payload = [
            'user_id'    => $this->admin->id,
            'event_id'   => $event->id,
            'type'       => 'expense',
            'title'      => 'Dekorasi',
            'qty'        => 3,
            'unit'       => 'Paket',
            'unit_price' => 200.00,
            'date'       => '2026-08-20',
        ];

        // Act
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('finances', [
            'title'  => 'Dekorasi',
            'amount' => 600.00,
        ]);
    }
}
