<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditTrailController;
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

Route::get('/user', function (Request $request) {
    return $request->user()->load(['roles', 'division']);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Profile Endpoints
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);

    // Dashboard Endpoints
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/upcoming-agenda', [DashboardController::class, 'upcomingAgenda']);

    // --- READ-ONLY / GENERAL ENDPOINTS ---
    // (Aman diakses semua role yang login untuk keperluan fetch data)
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::get('/meeting-attendances', [MeetingAttendanceController::class, 'index']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/warnings', [WarningController::class, 'index']);
    Route::get('/finances', [FinanceController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/divisions', [DivisionController::class, 'index']);
    Route::get('/event-committees', [EventCommitteeController::class, 'index']);

    // --- CONTEXTUAL AUTH RESOURCES ---
    // (Bisa di-POST/PUT oleh Admin DAN anggota BPH Event via Authorization Policy)
    Route::post('/meeting-attendances', [MeetingAttendanceController::class, 'store']);
    Route::post('/meeting-attendances/bulk', [MeetingAttendanceController::class, 'bulkStore']);
    Route::apiResource('finances', FinanceController::class)->except(['create', 'edit', 'index']);
    Route::post('/documents/sync', [DocumentController::class, 'sync']);
    Route::apiResource('documents', DocumentController::class)->except(['create', 'edit', 'index']);
    Route::apiResource('meetings', MeetingController::class)->except(['create', 'edit', 'index']);
    Route::apiResource('events', EventController::class)->except(['create', 'edit', 'index']);

    // --- STRICT ADMIN WRITE ENDPOINTS ---
    // (HANYA boleh diakses oleh Administrator BPH Pusat)
    Route::middleware('role:admin')->group(function () {
        // Audit Trails
        Route::get('/audit-trails', [AuditTrailController::class, 'index']);

        // Master Data
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::post('/divisions', [DivisionController::class, 'store']);
        Route::put('/divisions/{division}', [DivisionController::class, 'update']);
        Route::delete('/divisions/{division}', [DivisionController::class, 'destroy']);
        
        // Panitia & Kehadiran
        Route::post('/event-committees', [EventCommitteeController::class, 'store']);
        Route::delete('/event-committees/{eventCommittee}', [EventCommitteeController::class, 'destroy']);

        // Peringatan Organisasi
        Route::post('/warnings', [WarningController::class, 'store']);
        Route::put('/warnings/{warning}', [WarningController::class, 'update']);
        Route::delete('/warnings/{warning}', [WarningController::class, 'destroy']);
    });
});
