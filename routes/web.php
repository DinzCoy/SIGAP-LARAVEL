<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceNameController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WebReportController;
use Illuminate\Support\Facades\Route;

// ─── Root Redirect ────────────────────────────────────────────────────────────

Route::get('/', fn () => redirect()->route('login'));

// ─── General Dashboard (role-based dispatcher) ────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [WebReportController::class, 'adminIndex'])->name('admin.dashboard');
    Route::get('/reports/export', [WebReportController::class, 'export'])->name('admin.reports.export');
    Route::get('/reports/{id}', [WebReportController::class, 'show'])->name('admin.reports.show');
    Route::delete('/reports/{id}', [WebReportController::class, 'destroy'])->name('admin.reports.destroy');
    Route::patch('/reports/{id}/room', [WebReportController::class, 'updateRoom'])->name('reports.updateRoom');

    // Link PC device to an Asset record — Admin-only action
    Route::post('/assets/{asset_id}/link', [AssetController::class, 'linkDevice'])->name('asset.link');
});

// ─── Settings (Admin) ─────────────────────────────────────────────────────────

Route::middleware(['auth', 'is_admin'])->prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/password', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    Route::post('/system', [SettingsController::class, 'updateSystem'])->name('settings.updateSystem');
    Route::post('/thresholds', [SettingsController::class, 'updateThresholds'])->name('settings.updateThresholds');
    Route::post('/whitelist', [SettingsController::class, 'addWhitelistIp'])->name('settings.addWhitelistIp');
    Route::delete('/whitelist/{id}', [SettingsController::class, 'removeWhitelistIp'])->name('settings.removeWhitelistIp');
    Route::get('/export', [SettingsController::class, 'exportData'])->name('settings.exportData');
    Route::post('/backup', [SettingsController::class, 'backupDatabase'])->name('settings.backupDatabase');
    Route::post('/clean-logs', [SettingsController::class, 'cleanOldLogs'])->name('settings.cleanOldLogs');
    Route::post('/retention', [SettingsController::class, 'updateRetention'])->name('settings.updateRetention');
});

// ─── Pimpinan (Role 1) ────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:1'])->prefix('pimpinan')->group(function () {
    Route::get('/dashboard', [PimpinanController::class, 'dashboard'])->name('pimpinan.dashboard');
});

// ─── Teknisi (Role 3) ─────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:3'])->prefix('teknisi')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teknisi'])->name('teknisi.dashboard');
});

// ─── Pengelola Ruangan (Role 5) ───────────────────────────────────────────────

Route::middleware(['auth', 'role:5'])->prefix('ruangan')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'ruangan'])->name('ruangan.dashboard');
});

// ─── User Biasa (Role 6) ──────────────────────────────────────────────────────

Route::middleware(['auth', 'role:6'])->prefix('user')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
});

// ─── Pengelola Aset (Role 4) ──────────────────────────────────────────────────

Route::middleware(['auth', 'role:4'])->prefix('asset-manager')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pengelolaAset'])->name('pengelola_aset.dashboard');
    Route::get('/aset', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/store', [AssetController::class, 'store'])->name('asset.store');
    Route::put('/{id}', [AssetController::class, 'update'])->name('asset.update');
    Route::delete('/{id}', [AssetController::class, 'destroy'])->name('asset.destroy');

    // Master Ruangan
    Route::resource('rooms', RoomController::class)->except(['show']);

    // Master Nama Perangkat
    Route::resource('device-names', DeviceNameController::class);
});

// ─── Tickets (All Authenticated Users) ───────────────────────────────────────

Route::middleware('auth')->prefix('tickets')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::patch('/{id}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::post('/{id}/reply', [TicketController::class, 'addReply'])->name('tickets.reply');
});

// ─── Profile ──────────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── User Management (Admin) ──────────────────────────────────────────────────

Route::middleware(['auth', 'is_admin'])->prefix('user-management')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::put('/{user}/roles', [UserManagementController::class, 'updateRoles'])->name('users.updateRoles');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';