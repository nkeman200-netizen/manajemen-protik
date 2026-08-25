<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Finance;
use App\Models\Agenda;
use App\Models\AgendaAttendance;
use App\Models\MonthlyDue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole('member');
    }

    public function test_statistics_calculation_is_accurate(): void
    {
        // Arrange
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // 1. Finance - Kas Umum
        Finance::create([
            'user_id'    => $this->user->id,
            'event_id'   => null,
            'type'       => 'income',
            'title'      => 'Income Kas Umum',
            'qty'        => 1,
            'unit_price' => 1000.00,
            'amount'     => 1000.00,
            'date'       => $now->toDateString(),
        ]);
        Finance::create([
            'user_id'    => $this->user->id,
            'event_id'   => null,
            'type'       => 'expense',
            'title'      => 'Expense Kas Umum',
            'qty'        => 1,
            'unit_price' => 300.00,
            'amount'     => 300.00,
            'date'       => $now->toDateString(),
        ]);

        // 2. Agenda dengan absensi
        $agenda = Agenda::create([
            'title'      => 'Rapat Perdana',
            'start_date' => $now->copy()->subDay(),
        ]);
        AgendaAttendance::create([
            'agenda_id' => $agenda->id,
            'user_id'   => $this->user->id,
            'status'    => 'present',
        ]);

        // Act
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/statistics');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'personal_dues' => ['unpaid_months'],
                'agenda_participation' => ['last_agenda_title', 'rate'],
                'financial_health' => [
                    'total_balance',
                    'chart_data' => ['Kas Umum'],
                ],
            ],
        ]);

        $this->assertEquals(700.00, $response->json('data.financial_health.total_balance'));
        $this->assertEquals('Rapat Perdana', $response->json('data.agenda_participation.last_agenda_title'));
        $this->assertEquals(100, $response->json('data.agenda_participation.rate'));
    }

    public function test_upcoming_agenda_only_shows_future_dates(): void
    {
        // Arrange
        $now = Carbon::now();

        // Past Agendas
        Agenda::create([
            'title'      => 'Past Agenda 1',
            'start_date' => $now->copy()->subDays(5)->toDateTimeString(),
        ]);

        // Future Agendas (7 agendas to test limit 5 and sorting)
        for ($i = 1; $i <= 7; $i++) {
            Agenda::create([
                'title'      => "Future Agenda $i",
                'start_date' => $now->copy()->addDays($i)->toDateTimeString(),
            ]);
        }

        // Act
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/upcoming-agenda');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.upcoming_meetings');

        $meetings = $response->json('data.upcoming_meetings');
        $this->assertEquals('Future Agenda 1', $meetings[0]['title']);
        $this->assertEquals('Future Agenda 5', $meetings[4]['title']);
    }
}
