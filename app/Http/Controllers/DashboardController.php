<?php
namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function statistics(): JsonResponse
    {
        return response()->json([
            'message' => 'Success',
            'data'    => $this->dashboardService->getStatistics(),
        ]);
    }

    public function upcomingAgenda(): JsonResponse
    {
        return response()->json([
            'message' => 'Success',
            'data'    => $this->dashboardService->getUpcomingAgenda(),
        ]);
    }
}
