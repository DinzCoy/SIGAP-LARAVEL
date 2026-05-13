# Panduan Integrasi API: SIGAP Flutter & Laravel

Dokumen ini adalah cetak biru (blueprint) yang mendokumentasikan bagaimana aplikasi **SIGAP Mobile (Flutter)** terhubung dengan backend **SIGAP Web (Laravel)**. Baca dokumen ini saat Anda berpindah pengerjaan antara Flutter dan Laravel agar tidak kehilangan konteks.

---

## 1. Mekanisme Keamanan Utama
Sistem komunikasi ini diamankan oleh dua lapis verifikasi:
1. **API KEY (`SUPER-API-KEY`)**: Lapisan pertama untuk memastikan bahwa request benar-benar berasal dari aplikasi Flutter resmi milik **SIGAP**, bukan dari Postman atau sumber eksternal lain.
2. **Bearer Token (`Authorization: Bearer <token>`)**: Lapisan kedua (berbasis sesi/user) yang menangani identitas pengguna yang sedang login (dihasilkan oleh Laravel Sanctum atau JWT).

---

## 2. Sisi Flutter (Klien)

Di Flutter, seluruh komunikasi HTTP dipusatkan agar mudah di-maintenance.

### A. Konfigurasi Pusat (`lib/config/app_config.dart`)
*   **Base URL**: Mengatur ke mana Flutter harus mengirim request.
    *   *Catatan*: Jika menjalankan dari emulator, jangan gunakan `localhost`, gunakan IP Address mesin Anda (misal: `http://192.168.1.100:8000/api`).
*   **API Key**: Disimpan di variabel konstan. Nilai saat ini: `SIGAP_SECRET_API_KEY_2026`.

### B. Klien API (`lib/services/api_client.dart`)
Semua request `GET` dan `POST` dikirim melalui `ApiClient`. Klien ini otomatis akan menempelkan:
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json",
  "SUPER-API-KEY": "SIGAP_SECRET_API_KEY_2026",
  "Authorization": "Bearer <TOKEN_USER>"
}
```

---

## 3. Sisi Laravel (Server)

Saat Anda bekerja di proyek Laravel, Anda **WAJIB** menyiapkan hal-hal berikut agar bisa menerima request dari Flutter.

### A. Buat Middleware API Key
Jalankan perintah: `php artisan make:middleware VerifyApiKey`

Isi file `app/Http/Middleware/VerifyApiKey.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Harus sama dengan AppConfig.apiKey di Flutter
        $expectedKey = env('APP_MOBILE_API_KEY', 'SIGAP_SECRET_API_KEY_2026');

        if ($request->header('SUPER-API-KEY') !== $expectedKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid API Key'
            ], 401);
        }

        return $next($request);
    }
}
```

### B. Daftarkan Middleware
Bergantung pada versi Laravel Anda:
*   **Laravel 11+**: Daftarkan di `bootstrap/app.php` pada bagian `withMiddleware`.
*   **Laravel 10 ke bawah**: Daftarkan di `app/Http/Kernel.php` dalam `$middlewareAliases`.
    ```php
    'api.key' => \App\Http\Middleware\VerifyApiKey::class,
    ```

### C. Lindungi Routes API (`routes/api.php`)
Bungkus semua rute yang diakses Flutter dengan middleware `api.key`. Untuk rute yang butuh login, tambahkan middleware auth (seperti `auth:sanctum`).

```php
use Illuminate\Support\Facades\Route;

// Rute yang BISA diakses tanpa login (tapi TETAP butuh API KEY)
Route::middleware(['api.key'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Rute yang BUTUH LOGIN & BUTUH API KEY
Route::middleware(['api.key', 'auth:sanctum'])->group(function () {
    Route::post('/asset/scan', [AssetController::class, 'scan']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/user/loans', [LoanController::class, 'indexMyLoans']);
    Route::get('/user/dashboard', [DashboardController::class, 'userStats']);
});
```

---

## 4. Daftar Kontrak Endpoint (Routes yang sudah dipakai Flutter)

Saat ini, Flutter mengharapkan struktur Response JSON dari Laravel seperti ini (Format Standar):
```json
{
  "status": "success", // atau "error"
  "message": "Pesan balasan",
  "data": { ... } // (Opsional) Data kembalian
}
```

Berikut adalah daftar rute yang wajib Anda buat di Controller Laravel:

1.  **Auth & Login**
    *   `POST /api/login` (Body: `email`, `password`, `role`) -> Kembalikan `user` object dan `token` (Sanctum/JWT).
    *   `POST /api/reset-password` (Body: `email`, `newPassword`)
2.  **Asset Management**
    *   `POST /api/asset/scan` (Body: `asset_code`) -> Kembalikan detail aset.
3.  **Ticketing (Laporan Kerusakan)**
    *   `POST /api/tickets` (Body: `title`, `description`, `type`, `asset_id` opsional).
4.  **Peminjaman (Loans)**
    *   `POST /api/loans` (Body: `asset_id`, `loan_reason`, `due_date`).
    *   `GET /api/user/loans` -> Kembalikan array daftar peminjaman.
5.  **Dashboard**
    *   `GET /api/user/dashboard` -> Kembalikan statistik tiket/peminjaman untuk UI User Dashboard.

---

## 5. Troubleshooting (Masalah Umum)

*   **Error 401 Unauthorized (Invalid API Key)**: 
    Cek `AppConfig.apiKey` di Flutter dan samakan dengan Laravel `VerifyApiKey.php`.
*   **Error 401 Unauthenticated**:
    Token Sanctum/JWT tidak valid atau kadaluarsa. Pastikan fungsi `login` di Laravel mengembalikan `token` dan Flutter menyimpannya dengan benar.
*   **Connection Refused**:
    Ganti `localhost` di `AppConfig.baseUrl` dengan IPv4 dari mesin/laptop Anda saat mengetes di HP/Emulator (misal: jalankan `ipconfig` di CMD Windows untuk melihat IP).
*   **CORS Error**:
    (Umumnya terjadi jika Anda nge-build Flutter untuk Web). Pastikan Anda mengatur konfigurasi `cors.php` di Laravel agar mengizinkan request.
