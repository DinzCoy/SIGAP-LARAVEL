# Dokumentasi Perubahan Laravel — Untuk Penyelarasan Flutter

> **Tanggal**: 2026-05-07  
> **Tujuan**: Dokumen ini mencatat semua perubahan yang dilakukan pada sisi Laravel (server) agar bisa diselaraskan dengan proyek Flutter di local.

---

## Ringkasan Perubahan

| File | Aksi | Keterangan |
|---|---|---|
| `app/Http/Middleware/VerifyApiKey.php` | ✅ DIBUAT BARU | Middleware validasi API Key dari Flutter |
| `bootstrap/app.php` | ✅ DIUBAH | Mendaftarkan alias `api.key` |
| `routes/api.php` | ✅ DIUBAH | Semua rute mobile kini dilindungi `api.key` |
| `.env` | ✅ DIUBAH | Menambahkan variabel `APP_MOBILE_API_KEY` |

---

## 1. Middleware Baru: `VerifyApiKey`

**Path**: `app/Http/Middleware/VerifyApiKey.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Nilai ini HARUS sama dengan AppConfig.apiKey di Flutter
        $expectedKey = env('APP_MOBILE_API_KEY', 'SIGAP_SECRET_API_KEY_2026');

        if ($request->header('SUPER-API-KEY') !== $expectedKey) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Invalid API Key',
            ], 401);
        }

        return $next($request);
    }
}
```

**Cara kerjanya**: Setiap request dari Flutter **wajib** membawa header `SUPER-API-KEY` dengan nilai yang cocok dengan `APP_MOBILE_API_KEY` di file `.env`.

---

## 2. Registrasi Middleware: `bootstrap/app.php`

Alias `api.key` telah didaftarkan agar bisa dipakai di `routes/api.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'    => \App\Http\Middleware\CekPeran::class,
        'api.key' => \App\Http\Middleware\VerifyApiKey::class, // ← BARU
    ]);
})
```

---

## 3. Perubahan Routes: `routes/api.php`

Struktur rute API mobile kini dibagi dua group:

```php
// GROUP 1: Tanpa login, tapi WAJIB kirim API Key
Route::middleware(['api.key'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// GROUP 2: Wajib login + wajib kirim API Key
Route::middleware(['api.key', 'auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard']);
    Route::post('/asset/scan', [AssetController::class, 'scan']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/user/loans', [LoanController::class, 'myLoans']);
});
```

> **Catatan**: Rute agent (`/pc-report`, `/agent-config`) **tidak** menggunakan `api.key` karena diakses oleh agen PowerShell, bukan Flutter.

---

## 4. Variabel `.env` yang Ditambahkan

```env
APP_MOBILE_API_KEY=SIGAP_SECRET_API_KEY_2026
```

**Letak**: Di file `.env` server, tepat setelah baris `APP_URL`.

---

## Kontrak yang Harus Dipatuhi Flutter

### A. Header Wajib di Setiap Request

Flutter **harus** selalu menyertakan header berikut di **semua** request ke endpoint API:

| Header | Nilai |
|---|---|
| `Content-Type` | `application/json` |
| `Accept` | `application/json` |
| `SUPER-API-KEY` | `SIGAP_SECRET_API_KEY_2026` |
| `Authorization` | `Bearer <token>` *(hanya untuk endpoint yang butuh login)* |

### B. Daftar Endpoint yang Tersedia

| Method | Endpoint | Auth? | Keterangan |
|---|---|---|---|
| `POST` | `/api/login` | ❌ (hanya API Key) | Login, dapatkan token |
| `POST` | `/api/logout` | ✅ | Hapus token di server |
| `GET` | `/api/user/dashboard` | ✅ | Statistik dashboard user |
| `POST` | `/api/asset/scan` | ✅ | Scan QR aset, body: `{asset_code}` |
| `POST` | `/api/tickets` | ✅ | Buat tiket, body: `{title, description, type, asset_id?}` |
| `POST` | `/api/loans` | ✅ | Buat peminjaman, body: `{asset_id, loan_reason, due_date}` |
| `GET` | `/api/user/loans` | ✅ | List peminjaman milik user |

### C. Format Response Standar dari Laravel

```json
{
  "status": "success",
  "message": "Pesan keterangan",
  "data": { }
}
```

Atau jika error:

```json
{
  "status": "error",
  "message": "Pesan error"
}
```

### D. Format Response Login (Khusus)

```json
{
  "status": "success",
  "message": "Login berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Nama User",
      "email": "user@example.com",
      "username": "username",
      "roles": [
        { "id": 1, "name": "NamaRole" }
      ]
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz..."
  }
}
```

---

## Apa yang Harus Dibuat di Flutter (Local)

Buat atau update file-file berikut di proyek Flutter lokal kamu:

### 1. `lib/config/app_config.dart` *(BUAT BARU)*
Berisi `baseUrl` ke server dan nilai `apiKey`.

```dart
class AppConfig {
  static const String baseUrl = 'http://192.168.20.37/api';
  static const String apiKey  = 'SIGAP_SECRET_API_KEY_2026';
}
```

> **Penting**: Nilai `apiKey` di Flutter **harus identik** dengan `APP_MOBILE_API_KEY` di `.env` Laravel.

### 2. `lib/services/api_client.dart` *(BUAT BARU)*
HTTP client terpusat yang otomatis menempelkan `SUPER-API-KEY` dan `Bearer token`.

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';

class ApiClient {
  static Future<Map<String, String>> _buildHeaders({bool withAuth = false}) async {
    final headers = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'SUPER-API-KEY': AppConfig.apiKey,
    };
    if (withAuth) {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      if (token != null) headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  /// GET dengan auth
  static Future<http.Response> get(String endpoint) async {
    final url = Uri.parse('${AppConfig.baseUrl}$endpoint');
    return http.get(url, headers: await _buildHeaders(withAuth: true));
  }

  /// POST tanpa auth (untuk login)
  static Future<http.Response> postPublic(String endpoint, Map<String, dynamic> body) async {
    final url = Uri.parse('${AppConfig.baseUrl}$endpoint');
    return http.post(url, headers: await _buildHeaders(), body: jsonEncode(body));
  }

  /// POST dengan auth (untuk endpoint yang butuh login)
  static Future<http.Response> post(String endpoint, Map<String, dynamic> body) async {
    final url = Uri.parse('${AppConfig.baseUrl}$endpoint');
    return http.post(url, headers: await _buildHeaders(withAuth: true), body: jsonEncode(body));
  }
}
```

### 3. Update service-service yang sudah ada
Ganti penggunaan `http.get/post` langsung dengan `ApiClient.get/post/postPublic` agar header API Key otomatis ikut terkirim.

---

## Troubleshooting

| Error | Penyebab | Solusi |
|---|---|---|
| `401 Unauthorized: Invalid API Key` | Header `SUPER-API-KEY` tidak dikirim atau nilainya salah | Pastikan `AppConfig.apiKey` = `SIGAP_SECRET_API_KEY_2026` |
| `401 Unauthenticated` | Token Sanctum tidak valid/kadaluarsa | Login ulang agar dapat token baru |
| `Connection Refused` | IP/port salah | Pastikan Flutter dan server satu jaringan WiFi, cek `baseUrl` |
| `CORS Error` | Hanya terjadi di Flutter Web | Aktifkan CORS di Laravel: `php artisan vendor:publish --tag=laravel-cors` |
