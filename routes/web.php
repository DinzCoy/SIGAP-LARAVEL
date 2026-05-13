<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceNameController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WebReportController;
use Illuminate\Support\Facades\Route;

//Redirect Halaman Utama

Route::get('/', fn() => redirect()->route('login'));

//Dashboard Umum (Dispatcher Berbasis Role)

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-role/{roleId}', [DashboardController::class, 'switchRole'])->name('switch.role');
});

//Rute Administrator (Role 2)

Route::middleware(['auth', 'role:2'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [WebReportController::class, 'adminIndex'])->name('admin.dashboard');
    Route::get('/reports/export', [WebReportController::class, 'export'])->name('admin.reports.export');
    Route::get('/reports/{id}', [WebReportController::class, 'show'])->name('admin.reports.show');
    Route::delete('/reports/{id}', [WebReportController::class, 'destroy'])->name('admin.reports.destroy');
    Route::patch('/reports/{id}/room', [WebReportController::class, 'updateRoom'])->name('reports.updateRoom');

    // Tautkan perangkat PC ke aset BMN — Khusus Admin
    Route::post('/assets/{asset_id}/link', [AssetController::class, 'linkDevice'])->name('asset.link');
});

//Pengaturan Sistem (Admin)

Route::middleware(['auth', 'role:2'])->prefix('settings')->group(function () {
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
    Route::post('/agent-schedule', [SettingsController::class, 'updateAgentSchedule'])->name('settings.updateAgentSchedule');
    Route::post('/room-order', [SettingsController::class, 'updateRoomOrder'])->name('settings.updateRoomOrder');
});

//Pimpinan (Role 1)

Route::middleware(['auth', 'role:1'])->prefix('pimpinan')->group(function () {
    Route::get('/dashboard', [PimpinanController::class, 'dashboard'])->name('pimpinan.dashboard');
});

//Teknisi (Role 3)

Route::middleware(['auth', 'role:3'])->prefix('teknisi')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teknisi'])->name('teknisi.dashboard');
});

//Pengelola Ruangan (Role 5)

Route::middleware(['auth', 'role:5'])->prefix('ruangan')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'ruangan'])->name('ruangan.dashboard');
    Route::get('/lapor', [TicketController::class, 'createRuangan'])->name('ruangan.lapor');
    Route::post('/lapor', [TicketController::class, 'storeRuangan'])->name('ruangan.lapor.store');
});

//User Biasa (Role 6)

Route::middleware(['auth', 'role:6'])->prefix('user')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
});

//Ketua Tim (Role 7)

Route::middleware(['auth', 'role:7'])->prefix('ketua-tim')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'ketuaTim'])->name('ketua_tim.dashboard');
});

//Pengelola Aset (Role 4)

Route::middleware(['auth', 'role:2,4,7'])->prefix('asset-manager')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pengelolaAset'])->name('pengelola_aset.dashboard');
    Route::get('/aset', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/store', [AssetController::class, 'store'])->name('asset.store');
    Route::put('/{id}', [AssetController::class, 'update'])->name('asset.update');
    Route::delete('/{id}', [AssetController::class, 'destroy'])->name('asset.destroy');

    // Master Ruangan — index dapat diakses Role 2,4,7; operasi lain hanya 2,4
    Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index')->middleware('role:2,4,7');
    Route::resource('rooms', RoomController::class)->except(['index', 'show'])->middleware('role:2,4');

    // Master Nama Perangkat — index dapat diakses Role 2,4,7; operasi lain hanya 2,4
    Route::get('device-names', [DeviceNameController::class, 'index'])->name('device-names.index')->middleware('role:2,4,7');
    Route::resource('device-names', DeviceNameController::class)->except(['index'])->middleware('role:2,4');

    Route::get('/master-aset', [AssetController::class, 'masterAset'])->name('master-aset.index')->middleware('role:2,4,7');
});

//Tracking QR Aset (Semua User Terautentikasi)
Route::middleware('auth')->prefix('assets')->group(function () {
    Route::get('/{id}/scan', [AssetController::class, 'scan'])->name('assets.scan');
    Route::post('/{id}/takeover', [AssetController::class, 'takeover'])->name('assets.takeover');
    Route::post('/{id}/loan', [AssetController::class, 'loan'])->name('assets.loan');
    Route::post('/loan/{id}/approve', [AssetController::class, 'approveLoan'])->name('assets.loan.approve');
    Route::post('/loan/{id}/reject', [AssetController::class, 'rejectLoan'])->name('assets.loan.reject');
    Route::post('/{id}/return', [AssetController::class, 'returnLoan'])->name('assets.return');
    Route::get('/{id}/print', [AssetController::class, 'print'])->name('assets.print');
});

//Tiket & Penanganan (Semua User Terautentikasi)

Route::middleware('auth')->prefix('tickets')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::patch('/{id}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::post('/{id}/reply', [TicketController::class, 'addReply'])->name('tickets.reply');
});

//Profil Pengguna

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
});

//Manajemen Pengguna (Admin)

Route::middleware(['auth', 'role:2'])->prefix('user-management')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.resetPassword');
});

//FAQ / Knowledge Base (Semua Authenticated Users)

Route::middleware('auth')->prefix('faq')->name('faq.')->group(function () {
    Route::get('/', [FaqController::class, 'publicIndex'])->name('index');
    Route::post('/{id}/feedback', [FaqController::class, 'publicFeedback'])->name('feedback');
    Route::get('/{id}', [FaqController::class, 'publicShow'])->name('show');
});

//Manajemen FAQ (Admin)

Route::middleware(['auth', 'role:2'])->prefix('admin/faq')->name('admin.faq.')->group(function () {
    Route::resource('categories', FaqCategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('articles', FaqController::class)->except(['show']);
});

require __DIR__ . '/auth.php';