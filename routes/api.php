<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventCommitteeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MeetingAttendanceController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarningController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new UserResource($request->user()->load(['roles', 'division']));
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Profile endpoints
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);

    // Dashboard endpoints
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/upcoming-agenda', [DashboardController::class, 'upcomingAgenda']);

    // Read-only / General endpoints
    Route::get('/meeting-attendances', [MeetingAttendanceController::class, 'index']);
    Route::get('/warnings', [WarningController::class, 'index']);
    Route::get('/events', [EventController::class, 'index']);

    // Contextual Auth Resources (Finances, Documents, Meetings)
    Route::apiResource('finances', FinanceController::class)->except(['create', 'edit']);
    Route::apiResource('documents', DocumentController::class)->except(['create', 'edit']);
    Route::apiResource('meetings', MeetingController::class)->except(['create', 'edit']);

    // Write: Hanya role:admin
    Route::middleware('role:admin')->group(function () {
        Route::post('/meeting-attendances', [MeetingAttendanceController::class, 'store']);

        Route::post('/warnings', [WarningController::class, 'store']);
        Route::put('/warnings/{warning}', [WarningController::class, 'update']);
        Route::delete('/warnings/{warning}', [WarningController::class, 'destroy']);

        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
    });
});

// Master Data & Kepanitiaan: hanya admin global
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('roles', RoleController::class)->only(['index', 'store']);
    Route::apiResource('divisions', DivisionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('users', UserController::class)->only(['index', 'update']);
    Route::apiResource('event-committees', EventCommitteeController::class)->only(['index', 'store', 'destroy']);
});
