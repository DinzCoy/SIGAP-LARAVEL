# BPS PC Guardian

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38bdf8?style=for-the-badge&logo=tailwindcss" alt="Tailwind">
  <img src="https://img.shields.io/badge/License-Internal-gray?style=for-the-badge" alt="License">
</p>

Sistem monitoring dan manajemen aset PC berbasis web untuk **Badan Pusat Statistik (BPS)**. Aplikasi ini menerima laporan real-time dari Windows Agent (PowerShell) yang berjalan di setiap PC client, dan menyediakan dashboard terpusat untuk pemantauan status perangkat, manajemen tiket, aset BMN, dan pengaturan sistem.

---

## ✨ Fitur Utama

| Modul | Deskripsi |
|---|---|
| **PC Monitor** | Dashboard real-time status online/offline, RAM, disk, OS per PC |
| **Anomaly Detection** | Deteksi otomatis PC bermasalah berdasarkan threshold yang dapat dikonfigurasi |
| **Ticketing System** | Sistem tiket untuk perbaikan aset BMN dan troubleshooting umum |
| **Asset Management** | Manajemen aset dengan nomor BMN, pengelola ruangan, dan alokasi pengguna |
| **Role-based Access** | 7 peran: Pimpinan, Admin, Teknisi, Pengelola Barang, Pengelola Ruangan, Ketua Tim, User |
| **Windows Agent** | Script PowerShell untuk pelaporan otomatis dari PC client |
| **Export Excel** | Ekspor data PC ke `.xlsx` dengan filter dan styling otomatis |
| **Settings Panel** | Konfigurasi API key, threshold anomali, retensi log, dan whitelist IP |

---

## 🏗️ Arsitektur

```
bps-pc-guardian/
├── agent-deploy/         # Script instalasi & Windows Agent
│   ├── sigap-agent.ps1
│   └── install-agent.ps1
├── app/
│   ├── Exports/          # Maatwebsite Excel exports
│   ├── Http/
│   │   ├── Controllers/  # Web & API controllers
│   │   └── Middleware/   # CekPeran, dll.
│   └── Models/           # Eloquent models
├── database/
│   ├── migrations/       # Skema database
│   └── seeders/          # Data awal (roles, admin)
├── resources/views/      # Blade templates (Tailwind CSS)
└── routes/
    ├── web.php           # Web routes (auth + RBAC)
    └── api.php           # API endpoint untuk Windows Agent
```

---

## 🚀 Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & NPM

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/DinzCoy/bps-pc-guardian.git
cd bps-pc-guardian

# 2. Install dependencies
composer install
npm install

# 3. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bps_guardian
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 5. Jalankan migrasi dan seeder
php artisan migrate --seed

# 6. Build assets
npm run build

# 7. Jalankan server
php artisan serve
```

---

## 🤖 Windows Agent

Deploy file yang ada di dalam folder `agent-deploy` ke setiap PC client. Agent (`sigap-agent.ps1`) akan mengirim laporan secara periodik ke endpoint `/api/pc-report` menggunakan `X-API-KEY` header.

Gunakan `install-agent.ps1` atau file batch/vbs terkait untuk instalasi otomatis sebagai Windows Scheduled Task.

---

## 🔐 Keamanan

- `.env` **tidak pernah** di-commit ke repository ini
- API endpoint dilindungi dengan API Key kustom (via `SystemSetting`)
- Role-based middleware untuk setiap route web
- Password hashing dengan Bcrypt (12 rounds)
- IP Whitelist untuk akses admin

---

## 📦 Teknologi

- **Backend**: Laravel 11, PHP 8.2
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Lucide Icons
- **Database**: MySQL
- **Excel Export**: Maatwebsite/Laravel-Excel
- **Agent**: PowerShell 5+

---

## 📄 Lisensi

Aplikasi ini dikembangkan untuk internal **BPS Sulawesi Selatan**. Tidak untuk didistribusikan secara publik.
