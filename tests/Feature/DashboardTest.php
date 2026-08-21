<?php
namespace Tests\Feature;

use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Meeting;
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

        // 1. Finance - Bulan Ini
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'income',
            'title'      => 'Income Bulan Ini',
            'qty'        => 1,
            'unit_price' => 1000.00,
            'amount'     => 1000.00,
            'date'       => $now->toDateString(),
        ]);
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'expense',
            'title'      => 'Expense Bulan Ini',
            'qty'        => 1,
            'unit_price' => 300.00,
            'amount'     => 300.00,
            'date'       => $now->toDateString(),
        ]);

        // Finance - Bulan Lalu
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'income',
            'title'      => 'Income Bulan Lalu',
            'qty'        => 1,
            'unit_price' => 500.00,
            'amount'     => 500.00,
            'date'       => $lastMonth->toDateString(),
        ]);
        Finance::create([
            'user_id'    => $this->user->id,
            'type'       => 'expense',
            'title'      => 'Expense Bulan Lalu',
            'qty'        => 1,
            'unit_price' => 200.00,
            'amount'     => 200.00,
            'date'       => $lastMonth->toDateString(),
        ]);

        // 2. Events - Aktif & Tidak Aktif
        // Aktif: start_date <= today AND end_date >= today
        Event::factory()->create([
            'start_date' => $now->copy()->subDays(2)->toDateString(),
            'end_date'   => $now->copy()->addDays(3)->toDateString(),
        ]);
        // Aktif: start_date <= today AND end_date IS NULL
        Event::factory()->create([
            'start_date' => $now->copy()->subDays(1)->toDateString(),
            'end_date'   => null,
        ]);
        // Tidak Aktif: sudah lewat
        Event::factory()->create([
            'start_date' => $now->copy()->subMonth()->toDateString(),
            'end_date'   => $now->copy()->subMonth()->addDays(5)->toDateString(),
        ]);
        // Tidak Aktif: belum mulai (start_date > today)
        Event::factory()->create([
            'start_date' => $now->copy()->addMonth()->toDateString(),
            'end_date'   => $now->copy()->addMonth()->addDays(5)->toDateString(),
        ]);

        // 3. Documents - Bulan Ini vs Bulan Lalu
        Document::factory()->create([
            'created_by' => $this->user->id,
            'created_at' => $now,
        ]);
        Document::factory()->create([
            'created_by' => $this->user->id,
            'created_at' => $lastMonth,
        ]);

        // 4. Meetings - Bulan Ini vs Bulan Lalu
        Meeting::factory()->create([
            'date' => $now->toDateTimeString(),
        ]);
        Meeting::factory()->create([
            'date' => $lastMonth->toDateTimeString(),
        ]);

        // Act
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/statistics');

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Success',
            'data' => [
                'financial_health' => [
                    'total_balance'      => 1000.00, // (1000 + 500) - (300 + 200) = 1000
                    'income_this_month'  => 1000.00,
                    'expense_this_month' => 300.00,
                ],
                'event_performance' => [
                    'active_events_count' => 2,
                ],
                'organizational_activity' => [
                    'documents_issued_this_month' => 1,
                    'meetings_this_month'         => 1,
                ],
            ],
        ]);
    }

    public function test_upcoming_agenda_only_shows_future_dates(): void
    {
        // Arrange
        $now = Carbon::now();

        // Past Events
        Event::factory()->create([
            'name'       => 'Past Event 1',
            'start_date' => $now->copy()->subDays(10)->toDateString(),
        ]);

        // Future Events (7 events to test limit 5 and sorting)
        for ($i = 1; $i <= 7; $i++) {
            Event::factory()->create([
                'name'       => "Future Event $i",
                'start_date' => $now->copy()->addDays($i)->toDateString(),
            ]);
        }

        // Past Meetings
        Meeting::factory()->create([
            'title' => 'Past Meeting 1',
            'date'  => $now->copy()->subDays(5)->toDateTimeString(),
        ]);

        // Future Meetings (7 meetings to test limit 5 and sorting)
        for ($i = 1; $i <= 7; $i++) {
            Meeting::factory()->create([
                'title' => "Future Meeting $i",
                'date'  => $now->copy()->addDays($i)->toDateTimeString(),
            ]);
        }

        // Act
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/upcoming-agenda');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.upcoming_events');
        $response->assertJsonCount(5, 'data.upcoming_meetings');

        $events = $response->json('data.upcoming_events');
        $this->assertEquals('Future Event 1', $events[0]['name']);
        $this->assertEquals('Future Event 5', $events[4]['name']);

        $meetings = $response->json('data.upcoming_meetings');
        $this->assertEquals('Future Meeting 1', $meetings[0]['title']);
        $this->assertEquals('Future Meeting 5', $meetings[4]['title']);
    }
}
