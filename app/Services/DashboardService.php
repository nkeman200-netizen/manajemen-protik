<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Event;
use App\Models\Finance;
use App\Models\MonthlyDue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getStatistics(): array
    {
        $now  = Carbon::now();
        $user = Auth::user();

        // 1. HITUNG TUNGGAKAN KAS PENGURUS (Personal)
        $unpaidMonths = 0;
        
        // PENGECUALIAN: Pembina (Advisor) tidak ditagih kas
        if ($user && !$user->hasRole('advisor')) {
            $startMonth   = 10;
            $currentMonth = $now->month;
            $currentYear  = $now->year;
            
            if ($currentMonth >= 10) {
                $monthsPassed = $currentMonth - 10 + 1; // Okt, Nov, Des
            } else {
                $monthsPassed = (12 - 10 + 1) + $currentMonth; // Okt - Des + Jan - Current
            }
            
            $paidMonthsCount = MonthlyDue::where('user_id', $user->id)
                ->where(function ($query) use ($currentYear) {
                    $query->where('year', $currentYear)
                          ->orWhere('year', $currentYear - 1);
                })->count();

            if ($monthsPassed > 0) {
                $maxObligation = min($monthsPassed, 9);
                $unpaidMonths  = max(0, $maxObligation - $paidMonthsCount);
            }
        }

        // 2. HITUNG PARTISIPASI AGENDA (Daftar 5 Agenda Terakhir - Gamifikasi)
        // OPTIMASI: Menggunakan agregasi SQL (withCount) untuk menghindari Out of Memory (OOM)
        $lastAgendas = Agenda::withCount([
                'attendances',
                'attendances as present_count' => fn($q) => $q->whereIn('status', ['present', 'permit'])
            ])
            ->whereHas('attendances')
            ->where('start_date', '<=', $now)
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        $participationList = $lastAgendas->map(function ($agenda) {
            $totalParticipants = $agenda->attendances_count;
            $presentCount      = $agenda->present_count;
            $rate              = $totalParticipants > 0 ? (int) round(($presentCount / $totalParticipants) * 100) : 0;
            
            return [
                'title' => $agenda->title,
                'rate'  => $rate,
            ];
        });

        // 3. STATISTIK KAS UMUM
        $totalIncome  = (float) Finance::where('type', 'income')->whereNull('event_id')->sum('amount');
        $totalExpense = (float) Finance::where('type', 'expense')->whereNull('event_id')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        return [
            'personal_dues' => [
                'unpaid_months' => $unpaidMonths,
            ],
            'agenda_participation' => $participationList, // Mengirimkan Array Daftar Agenda
            'financial_health' => [
                'total_balance' => $totalBalance,
                'chart_data'    => $this->getChartData($now),
            ],
        ];
    }

    private function getChartData(Carbon $now): array
    {
        $chartData = [
            'Kas Umum' => $this->generateTimeSeriesData(null, $now)
        ];

        // Ambil semua Event yang memiliki transaksi keuangan (Kas Event)
        $eventsWithFinances = Event::whereHas('finances')->get();

        foreach ($eventsWithFinances as $event) {
            $chartData[$event->name] = $this->generateTimeSeriesData($event->id, $now);
        }

        return $chartData;
    }

    private function generateTimeSeriesData(?int $eventId, Carbon $now): array
    {
        $series = [];
        for ($i = 5; $i >= 0; $i--) {
            $targetDate = $now->copy()->subMonths($i);
            $queryIncome = Finance::where('type', 'income')
                ->whereMonth('date', $targetDate->month)
                ->whereYear('date', $targetDate->year);
            $queryExpense = Finance::where('type', 'expense')
                ->whereMonth('date', $targetDate->month)
                ->whereYear('date', $targetDate->year);

            if ($eventId) {
                $queryIncome->where('event_id', $eventId);
                $queryExpense->where('event_id', $eventId);
            } else {
                $queryIncome->whereNull('event_id');
                $queryExpense->whereNull('event_id');
            }

            $series[] = [
                'name'        => $targetDate->translatedFormat('M Y'),
                'Pemasukan'   => (float) $queryIncome->sum('amount'),
                'Pengeluaran' => (float) $queryExpense->sum('amount'),
            ];
        }
        return $series;
    }

    public function getUpcomingAgenda(): array
    {
        // Ambil SEMUA agenda agar Kalender Frontend bisa menggeser bulan ke masa lalu dan masa depan
        $agendas = Agenda::orderBy('start_date', 'asc')->get();

        return [
            'upcoming_meetings' => $agendas, // Tetap menggunakan key ini agar kompatibel dengan state Frontend
        ];
    }
}
