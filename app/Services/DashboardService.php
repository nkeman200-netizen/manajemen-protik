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
        $currentMonth = $now->month;
        $currentYear  = $now->year;
        
        $monthsPassed = 0;
        if ($currentMonth >= 10) {
            $monthsPassed = $currentMonth - 10 + 1; // Okt, Nov, Des
        } else {
            $monthsPassed = (12 - 10 + 1) + $currentMonth; // Okt - Des + Jan - Current
        }
        
        // Cek berapa bulan yang sudah dibayar oleh user ini
        $paidMonthsCount = 0;
        if ($user) {
            $paidMonthsCount = MonthlyDue::where('user_id', $user->id)
                ->where(function ($query) use ($currentYear) {
                    $query->where('year', $currentYear)
                          ->orWhere('year', $currentYear - 1);
                })->count();
        }

        // Jika bulan yang lewat lebih banyak dari yang dibayar, maka ada tunggakan
        // Batasi maksimal tunggakan adalah 9 bulan (Oktober - Juni)
        if ($monthsPassed > 0) {
            $maxObligation = min($monthsPassed, 9);
            $unpaidMonths  = max(0, $maxObligation - $paidMonthsCount);
        }

        // 2. HITUNG PARTISIPASI AGENDA TERAKHIR
        $lastAgenda = Agenda::with('attendances')
            ->whereHas('attendances') // Hanya cari agenda yang sudah diabsen
            ->where('start_date', '<=', $now)
            ->orderBy('start_date', 'desc')
            ->first();

        $participationRate = 0;
        $lastAgendaTitle   = '-';
        if ($lastAgenda && $lastAgenda->attendances->count() > 0) {
            $lastAgendaTitle   = $lastAgenda->title;
            $totalParticipants = $lastAgenda->attendances->count();
            $presentCount      = $lastAgenda->attendances->whereIn('status', ['present', 'permit'])->count(); // Hadir dan Izin dihitung positif
            $participationRate = (int) round(($presentCount / $totalParticipants) * 100);
        }

        // 3. STATISTIK KAS UMUM
        $totalIncome  = (float) Finance::where('type', 'income')->whereNull('event_id')->sum('amount');
        $totalExpense = (float) Finance::where('type', 'expense')->whereNull('event_id')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        return [
            'personal_dues' => [
                'unpaid_months' => $unpaidMonths,
            ],
            'agenda_participation' => [
                'last_agenda_title' => $lastAgendaTitle,
                'rate'              => $participationRate,
            ],
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
        $today = Carbon::now()->startOfDay();

        $upcomingAgendas = Agenda::where('start_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return [
            'upcoming_meetings' => $upcomingAgendas,
        ];
    }
}
