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
    /**
     * MESIN WAKTU PROTIK (PERBAIKAN LOGIKA BACKEND)
     * Mengembalikan jumlah bulan yang SUDAH BERJALAN dalam periode aktif (Okt - Jun).
     */
    private function calculateActiveMonthsPassed(Carbon $now): int
    {
        $m = $now->month;
        
        // Masa Transisi (Juli, Agustus, September). Periode belum dimulai = 0 tagihan.
        if ($m >= 7 && $m <= 9) return 0;
        
        // Oktober(10) sampai Desember(12)
        if ($m >= 10 && $m <= 12) return $m - 9;
        
        // Januari(1) sampai Juni(6)
        if ($m >= 1 && $m <= 6) return $m + 3;
        
        return 0;
    }

    public function getStatistics(): array
    {
        $now  = Carbon::now();
        $user = Auth::user();

        // 1. HITUNG TUNGGAKAN KAS PENGURUS (Personal)
        $unpaidMonths = 0;
        
        if ($user && !$user->hasRole('advisor')) {
            $monthsPassed = $this->calculateActiveMonthsPassed($now);
            
            // Hanya hitung tunggakan jika periode sudah dimulai (> 0)
            if ($monthsPassed > 0) {
                $currentYear  = $now->year;
                $paidMonthsCount = MonthlyDue::where('user_id', $user->id)
                    ->where(function ($query) use ($currentYear) {
                        // Mencakup tahun kalender saat ini dan sebelumnya untuk periode Okt-Jun
                        $query->where('year', $currentYear)
                              ->orWhere('year', $currentYear - 1);
                    })->count();

                $maxObligation = min($monthsPassed, 9); // Maksimal 9 bulan (Okt-Jun)
                $unpaidMonths  = max(0, $maxObligation - $paidMonthsCount);
            }
        }

        // 2. HITUNG PARTISIPASI AGENDA
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
            'agenda_participation' => $participationList,
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
        $agendas = Agenda::orderBy('start_date', 'asc')->get();

        return [
            'upcoming_meetings' => $agendas,
        ];
    }
}
