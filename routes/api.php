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
// Rute yang BISA diakses tanpa login (tapi TETAP butuh API KEY)
Route::middleware(['api.key'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Rute yang BUTUH LOGIN & BUTUH API KEY
Route::middleware(['api.key', 'auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::post('/user/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);

    // Notifications
    Route::post('/user/fcm-token', [AuthController::class, 'updateFcmToken']);
    Route::get('/user/notifications', [AuthController::class, 'notifications']);
    Route::post('/user/notifications/{id}/read', [AuthController::class, 'markNotificationAsRead']);

    // User Dashboard Endpoint
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard']);

    // Asset Management
    Route::post('/asset/scan', [AssetController::class, 'scan']);
    Route::get('/admin/assets', [AssetController::class, 'index']);
    Route::post('/admin/assets', [AssetController::class, 'store']);
    Route::post('/asset/transfer', [AssetController::class, 'transfer']);

    // Tickets
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/user/tickets', [TicketController::class, 'myTickets']);
    Route::get('/admin/tickets', [TicketController::class, 'index']);
    Route::post('/admin/tickets/{id}/status', [TicketController::class, 'updateStatus']);

    // Loans
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/user/loans', [LoanController::class, 'myLoans']);
    Route::get('/admin/loans', [LoanController::class, 'index']);
    Route::post('/admin/loans/{id}/approve', [LoanController::class, 'approve']);
    Route::post('/loans/{id}/return', [LoanController::class, 'returnAsset']);
    Route::put('/loans/{id}/return', [LoanController::class, 'returnAsset']);
});

// SIGAP Agent Reporting Endpoint (tidak butuh API Key mobile)
Route::post('/pc-report', [PcReportController::class, 'store'])->middleware(['throttle:60,1']);

// Agent Schedule Configuration Endpoint
Route::get('/agent-config', [AgentConfigController::class, 'schedule'])->middleware(['throttle:120,1']);
