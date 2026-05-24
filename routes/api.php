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

// ============================================================
// Mobile App API Routes
// ============================================================

// Rute yang BISA diakses tanpa login (tapi TETAP butuh API KEY)
Route::middleware(['api.key'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Rute yang BUTUH LOGIN & BUTUH API KEY
Route::middleware(['api.key', 'auth:sanctum'])->group(function () {

    // --- Auth ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- User Profile ---
    Route::post('/user/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/change-password',  [AuthController::class, 'changePassword']);

    // --- Push Notifications (FCM) ---
    Route::post('/user/fcm-token',                [AuthController::class, 'updateFcmToken']);
    Route::get('/user/notifications',             [AuthController::class, 'notifications']);
    Route::post('/user/notifications/{id}/read',  [AuthController::class, 'markNotificationAsRead']);
    Route::post('/user/notifications/read-all',   [AuthController::class, 'markAllNotificationsAsRead']);

    // ─── Dashboards (per role) ───────────────────────────────
    Route::get('/user/dashboard',        [DashboardController::class, 'userDashboard']);
    Route::get('/admin/dashboard',       [DashboardController::class, 'adminDashboard']);
    Route::get('/technician/dashboard',  [DashboardController::class, 'technicianDashboard']);

    // ─── Asset Management ────────────────────────────────────────────────────
    Route::post('/asset/scan',        [AssetController::class, 'scan']);
    Route::get('/user/assets',        [AssetController::class, 'userAssets']);
    Route::get('/admin/assets',       [AssetController::class, 'index']);
    Route::post('/admin/assets',      [AssetController::class, 'store']);
    Route::post('/asset/transfer',    [AssetController::class, 'transfer']);
    Route::get('/assets/available',   [AssetController::class, 'available']);   // Katalog aset tersedia (semua role)

    // ─── Tickets ─────────────────────────────────────────────────────────────
    Route::get('/tickets',                          [TicketController::class, 'index']);             // User: tiket sendiri (formatted)
    Route::post('/tickets',                         [TicketController::class, 'store']);             // Buat tiket baru
    Route::get('/user/tickets',                     [TicketController::class, 'myTickets']);         // User: tiket sendiri (raw)
    Route::get('/admin/tickets',                    [TicketController::class, 'adminIndex']);        // Admin: semua tiket
    Route::post('/admin/tickets/{id}/status',       [TicketController::class, 'updateStatus']);      // Admin: update status
    Route::get('/technician/maintenance',           [TicketController::class, 'maintenanceHistory']); // Teknisi: riwayat maintenance
    Route::get('/technicians',                      [TicketController::class, 'listTechnicians']);   // Ambil daftar semua teknisi
    Route::get('/technicians/leaderboard',          [TicketController::class, 'leaderboard']);       // Leaderboard gamifikasi

    // ─── Asset Loans ─────────────────────────────────────────
    Route::post('/loans',                        [LoanController::class, 'store']);         // Ajukan peminjaman
    Route::get('/user/loans',                    [LoanController::class, 'myLoans']);       // User: pinjaman sendiri
    Route::get('/admin/loans',                   [LoanController::class, 'index']);         // Admin: semua pinjaman (paginated)
    Route::post('/admin/loans/{id}/approve',     [LoanController::class, 'approve']);       // Admin: setujui/tolak
    Route::post('/loans/{id}/return',            [LoanController::class, 'returnAsset']);   // Kembalikan aset
    Route::put('/loans/{id}/return',             [LoanController::class, 'returnAsset']);   // Kembalikan aset (PUT alias)
});

// ============================================================
// SIGAP Agent Endpoints (tanpa API Key mobile, throttled)
// ============================================================

// PC Guardian Agent: kirim laporan hardware
Route::post('/pc-report', [PcReportController::class, 'store'])->middleware(['throttle:60,1']);

// Agent: ambil jadwal pelaporan
Route::get('/agent-config', [AgentConfigController::class, 'schedule'])->middleware(['throttle:120,1']);
