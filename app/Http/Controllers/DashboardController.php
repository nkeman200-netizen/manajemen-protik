<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function statistics(): JsonResponse
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        $totalIncome = (float) Finance::where('type', 'income')->sum('amount');
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

        $meetingsThisMonth = Meeting::whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->count();

        return response()->json([
            'message' => 'Success',
            'data' => [
                'financial_health' => [
                    'total_balance' => $totalBalance,
                    'income_this_month' => $incomeThisMonth,
                    'expense_this_month' => $expenseThisMonth,
                ],
                'event_performance' => [
                    'active_events_count' => $activeEventsCount,
                ],
                'organizational_activity' => [
                    'documents_issued_this_month' => $documentsIssuedThisMonth,
                    'meetings_this_month' => $meetingsThisMonth,
                ],
            ],
        ]);
    }

    public function upcomingAgenda(): JsonResponse
    {
        $today = Carbon::now()->startOfDay();

        $upcomingEvents = Event::where('start_date', '>=', $today->toDateString())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        $upcomingMeetings = Meeting::where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        return response()->json([
            'message' => 'Success',
            'data' => [
                'upcoming_events' => $upcomingEvents,
                'upcoming_meetings' => $upcomingMeetings,
            ],
        ]);
    }
}
