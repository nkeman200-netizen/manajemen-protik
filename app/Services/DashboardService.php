<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use Carbon\Carbon;

class DashboardService
{
    public function getStatistics(): array
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        $totalIncome  = (float) Finance::where('type', 'income')->sum('amount');
        $totalExpense = (float) Finance::where('type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        $incomeThisMonth = (float) Finance::where('type', 'income')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $expenseThisMonth = (float) Finance::where('type', 'expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $activeEventsCount = Event::where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->where('end_date', '>=', $today)
                      ->orWhereNull('end_date');
            })
            ->count();

        $documentsIssuedThisMonth = Document::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $agendasThisMonth = Agenda::whereMonth('start_date', $now->month)
            ->whereYear('start_date', $now->year)
            ->count();

        return [
            'financial_health' => [
                'total_balance'      => $totalBalance,
                'income_this_month'  => $incomeThisMonth,
                'expense_this_month' => $expenseThisMonth,
            ],
            'event_performance' => [
                'active_events_count' => $activeEventsCount,
            ],
            'organizational_activity' => [
                'documents_issued_this_month' => $documentsIssuedThisMonth,
                'meetings_this_month'         => $agendasThisMonth,
            ],
        ];
    }

    public function getUpcomingAgenda(): array
    {
        $today = Carbon::now()->startOfDay();

        $upcomingEvents = Event::where('start_date', '>=', $today->toDateString())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        $upcomingAgendas = Agenda::where('start_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return [
            'upcoming_events'   => $upcomingEvents,
            'upcoming_meetings' => $upcomingAgendas,
        ];
    }
}
