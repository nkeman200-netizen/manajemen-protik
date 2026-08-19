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

Route::post('/finances', [FinanceController::class, 'store']);

Route::apiResource('meetings', MeetingController::class)->only(['index', 'store']);
Route::apiResource('meeting-attendances', MeetingAttendanceController::class)->only(['index', 'store']);
Route::apiResource('documents', DocumentController::class)->only(['index', 'store']);
Route::apiResource('warnings', WarningController::class)->only(['index', 'store']);

