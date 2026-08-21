<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventCommitteeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MeetingAttendanceController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user()->load(['roles', 'division']);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard endpoints
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/upcoming-agenda', [DashboardController::class, 'upcomingAgenda']);

    // Read-only: semua role yang login bisa akses
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::get('/meeting-attendances', [MeetingAttendanceController::class, 'index']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/warnings', [WarningController::class, 'index']);
    Route::get('/finances', [FinanceController::class, 'index'])->withoutMiddleware('role:admin');

    // Write: hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::post('/meetings', [MeetingController::class, 'store']);
        Route::post('/meeting-attendances', [MeetingAttendanceController::class, 'store']);
        Route::post('/documents', [DocumentController::class, 'store']);
        Route::post('/warnings', [WarningController::class, 'store']);
        Route::post('/finances', [FinanceController::class, 'store']);
    });
});

// Master Data & Kepanitiaan: hanya admin global
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('roles', RoleController::class)->only(['index', 'store']);
    Route::apiResource('divisions', DivisionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('users', UserController::class)->only(['index', 'update']);
    Route::apiResource('event-committees', EventCommitteeController::class)->only(['index', 'store', 'destroy']);
});
