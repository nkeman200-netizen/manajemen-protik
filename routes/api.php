<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MeetingAttendanceController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\WarningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
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
