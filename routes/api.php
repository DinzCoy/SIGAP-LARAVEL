<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PcReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// BPS-PC Guardian Agent Reporting Endpoint
// Throttled: max 60 requests per minute per IP
Route::post('/pc-report', [PcReportController::class, 'store'])->middleware('throttle:60,1');