<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaAttendanceController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\CommitteePositionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentGeneratorController;
use App\Http\Controllers\EventCommitteeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MonthlyDueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
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
    Route::get('/agendas', [AgendaController::class, 'index']);
    Route::get('/agendas/filters', [AgendaController::class, 'filters']);
    Route::get('/agenda-attendances', [AgendaAttendanceController::class, 'index']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/filters', [DocumentController::class, 'filters']);
    Route::get('/warnings', [WarningController::class, 'index']);
    Route::patch('/warnings/{warning}/read', [WarningController::class, 'markAsRead']);
    Route::get('/finances', [FinanceController::class, 'index']);
    Route::get('/finances/filters', [FinanceController::class, 'filters']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/filters', [UserController::class, 'filters']);
    Route::get('/divisions', [DivisionController::class, 'index']);
    Route::get('/event-committees', [EventCommitteeController::class, 'index']);
    Route::get('/monthly-dues', [MonthlyDueController::class, 'index']);

    // Settings & Archives
    Route::get('/settings', [SettingController::class, 'index']);
    Route::apiResource('archives', ArchiveController::class);

    // --- CONTEXTUAL AUTH RESOURCES ---
    // (Bisa di-POST/PUT oleh Admin DAN anggota BPH Event via Authorization Policy)
    Route::post('/agendas/sync', [AgendaController::class, 'sync']);
    Route::post('/agendas/{id}/targets', [AgendaController::class, 'setTargets']);
    Route::post('/agenda-attendances/bulk', [AgendaAttendanceController::class, 'bulkSync']);
    Route::post('/finances/sync', [FinanceController::class, 'sync']);
    Route::apiResource('finances', FinanceController::class)->except(['create', 'edit', 'index']);
    Route::get('/documents/generate-number', [DocumentController::class, 'generateNumber']);
    Route::post('/documents/sync', [DocumentController::class, 'sync']);
    Route::post('/documents/generate', [DocumentGeneratorController::class, 'generate']);
    Route::apiResource('documents', DocumentController::class)->except(['create', 'edit', 'index']);
    Route::apiResource('events', EventController::class)->except(['create', 'edit', 'index']);

    // --- STRICT ADMIN WRITE ENDPOINTS ---
    // (HANYA boleh diakses oleh Administrator BPH Pusat)
    Route::middleware('role:admin')->group(function () {
        // Settings Batch Update, Logo Upload & Template Upload
        Route::post('/settings/batch', [SettingController::class, 'updateBatch']);
        Route::post('/settings/logo', [SettingController::class, 'uploadLogo']);
        Route::post('/settings/templates', [SettingController::class, 'uploadTemplate']);

        // Master Data Jabatan Kepanitiaan
        Route::apiResource('committee-positions', CommitteePositionController::class)->except(['show']);

        // Audit Trails
        Route::get('/audit-trails', [AuditTrailController::class, 'index']);

        // Kas Pengurus (Monthly Dues)
        Route::post('/monthly-dues/sync', [MonthlyDueController::class, 'sync']);

        // Master Data Pengurus (Sync & Mutasi)
        Route::post('/users/sync', [UserController::class, 'sync']);

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
