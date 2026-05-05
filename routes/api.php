<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentConfigController;
use App\Http\Controllers\PcReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\LoanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- Mobile App API Routes ---
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Dashboard Endpoint
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard']);
    
    // Asset QR Scan Endpoint
    Route::post('/asset/scan', [AssetController::class, 'scan']);
    
    // Ticket Submission Endpoint
    Route::post('/tickets', [TicketController::class, 'store']);
    
    // Asset Loan Endpoints
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/user/loans', [LoanController::class, 'myLoans']);
});

// SIGAP Agent Reporting Endpoint
Route::post('/pc-report', [PcReportController::class, 'store'])->middleware(['throttle:60,1']);

// Agent Schedule Configuration Endpoint
Route::get('/agent-config', [AgentConfigController::class, 'schedule'])->middleware(['throttle:120,1']);