<?php
namespace Tests\Feature;

use App\Models\Event;
use App\Models\Finance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_income_without_budget_limit(): void
    {
        // Arrange
        $user = User::factory()->create();
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        $payload = [
            'user_id'     => $user->id,
            'event_id'    => $event->id,
            'type'        => 'income',
            'amount'      => 999999.99,
            'description' => 'Dana sponsor masuk',
            'date'        => '2026-08-20',
        ];

        // Act
        $response = $this->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('finances', [
            'user_id'  => $user->id,
            'event_id' => $event->id,
            'type'     => 'income',
            'amount'   => 999999.99,
        ]);
    }

    public function test_expense_is_rejected_when_exceeding_event_budget(): void
    {
        // Arrange
        $user = User::factory()->create();
        $event = Event::factory()->create(['budget_approved' => 500.00]);

        Finance::create([
            'user_id'     => $user->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 400.00,
            'description' => 'Sewa tempat',
            'date'        => '2026-08-19',
        ]);

        $payload = [
            'user_id'     => $user->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 200.00,
            'description' => 'Konsumsi rapat',
            'date'        => '2026-08-20',
        ];

        // Act
        $response = $this->postJson('/api/finances', $payload);

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
        $user = User::factory()->create();
        $event = Event::factory()->create(['budget_approved' => 1000.00]);

        Finance::create([
            'user_id'     => $user->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 400.00,
            'description' => 'Sewa tempat',
            'date'        => '2026-08-19',
        ]);

        $payload = [
            'user_id'     => $user->id,
            'event_id'    => $event->id,
            'type'        => 'expense',
            'amount'      => 600.00,
            'description' => 'Dekorasi',
            'date'        => '2026-08-20',
        ];

        // Act
        $response = $this->postJson('/api/finances', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('finances', [
            'description' => 'Dekorasi',
            'amount'      => 600.00,
        ]);
    }
}
