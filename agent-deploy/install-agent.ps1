param(
    [Parameter(Mandatory = $true)]
    [string]$ServerIP,
    [Parameter(Mandatory = $true)]
    [string]$RoomName,
    [string]$ApiKey = "BPS-SULSEL-SECRET-2026",
    [int]$StartHour = 7,
    [int]$EndHour = 17
)

# ============================================================
# Memeriksa hak akses Administrator.
# ============================================================
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "ERROR: Harus dijalankan sebagai Administrator!" -ForegroundColor Red
    pause
    exit 1
}

$InstallDir        = "C:\bps-guardian"
$TaskNameStartup   = "SIGAP (Startup)"
$TaskNameScheduled = "SIGAP (Scheduled)"
$ScriptDir         = Split-Path -Parent $MyInvocation.MyCommand.Path

# ============================================================
# Fungsi: Membangun Base URL yang benar secara otomatis.
#   - Jika sudah ada skema (http:// / https://) → pakai apa adanya.
#   - Jika input adalah alamat IP (misal 192.168.1.1 atau 192.168.1.1:8080) → gunakan http://
#   - Jika input adalah nama domain → gunakan https:// (lebih aman untuk hosting)
#   - Selalu hapus trailing slash agar URL API tidak dobel garis miring.
# ============================================================
function Get-BaseUrl {
    param([string]$RawInput)

    $Cleaned = $RawInput.Trim().TrimEnd('/')

    # Jika sudah ada protokol eksplisit, hormati pilihan user.
    if ($Cleaned -match "^https?://") {
        return $Cleaned
    }

    # Deteksi alamat IPv4 (dengan atau tanpa port, misal: 192.168.20.24 atau 10.0.0.1:8080).
    if ($Cleaned -match "^\d{1,3}(\.\d{1,3}){3}(:\d+)?$") {
        return "http://$Cleaned"
    }

    # Selain itu dianggap nama domain → wajib HTTPS (standar hosting/cPanel).
    return "https://$Cleaned"
}

$BaseUrl = Get-BaseUrl -RawInput $ServerIP

Write-Host "=================================================="
Write-Host "   SIGAP Agent - Installer"
Write-Host "=================================================="
Write-Host "  Server    : $BaseUrl"
Write-Host "  Ruangan   : $RoomName"
Write-Host "  API Key   : $ApiKey"
Write-Host "  Jadwal    : $StartHour.00 - $EndHour.00 WIT (setiap jam)"
Write-Host ""

# ============================================================
# Proses 1: Validasi keberadaan semua file sumber.
# Installer tidak akan lanjut jika ada file yang kurang.
# ============================================================
Write-Host "[1/6] Memeriksa kelengkapan file sumber..." -ForegroundColor Yellow
$RequiredFiles = @("sigap-agent.ps1", "sigap-startup.vbs", "sigap-scheduled.vbs")
$MissingFiles  = @()

foreach ($File in $RequiredFiles) {
    if (-not (Test-Path "$ScriptDir\$File")) {
        $MissingFiles += $File
    }
}

if ($MissingFiles.Count -gt 0) {
    Write-Host "  [GAGAL] Gagal menemukan script instalasi! File berikut hilang:" -ForegroundColor Red
    foreach ($Missing in $MissingFiles) {
        Write-Host "    - $Missing" -ForegroundColor Red
    }
    Write-Host "  SOLUSI: Pastikan Anda telah meng-ekstrak semua file (jangan di-run langsung dari .zip)" -ForegroundColor Red
    Write-Host "  dan semua file agent berada dalam satu folder yang sama." -ForegroundColor Red
    pause
    exit 1
}

Write-Host "  [OK] Semua file sumber tersedia." -ForegroundColor Green

# ============================================================
# Proses 2: Membuat direktori instalasi dan menyalin file.
# ============================================================
Write-Host "[2/6] Menyalin file ke direktori instalasi..." -ForegroundColor Yellow

try {
    if (-not (Test-Path $InstallDir)) {
        New-Item -ItemType Directory -Path $InstallDir -Force | Out-Null
    }
    Copy-Item "$ScriptDir\sigap-agent.ps1"     "$InstallDir\" -Force -ErrorAction Stop
    Copy-Item "$ScriptDir\sigap-startup.vbs"   "$InstallDir\" -Force -ErrorAction Stop
    Copy-Item "$ScriptDir\sigap-scheduled.vbs" "$InstallDir\" -Force -ErrorAction Stop
    Write-Host "  [OK] File berhasil disalin ke $InstallDir" -ForegroundColor Green
} catch {
    Write-Host "  [GAGAL] Terjadi kesalahan saat menyalin file: $($_.Exception.Message)" -ForegroundColor Red
    pause
    exit 1
}

# ============================================================
# Proses 3: Menulis konfigurasi (URL server & ruangan) ke agent.
# Menggunakan encoding UTF8 agar karakter khusus tidak rusak.
# ============================================================
Write-Host "[3/6] Menyusun konfigurasi agent..." -ForegroundColor Yellow

try {
    $AgentPath    = "$InstallDir\sigap-agent.ps1"
    $AgentContent = Get-Content $AgentPath -Raw -Encoding UTF8

    $AgentContent = $AgentContent -replace '\$ApiUrl\s*=\s*"[^"]*"',    "`$ApiUrl      = `"$BaseUrl/api/pc-report`""
    $AgentContent = $AgentContent -replace '\$ConfigUrl\s*=\s*"[^"]*"', "`$ConfigUrl   = `"$BaseUrl/api/agent-config`""
    $AgentContent = $AgentContent -replace '\$ApiKey\s*=\s*"[^"]*"',    "`$ApiKey      = `"$ApiKey`""
    $AgentContent = $AgentContent -replace '\$RoomName\s*=\s*"[^"]*"',  "`$RoomName    = `"$RoomName`""
    $AgentContent | Set-Content $AgentPath -Force -Encoding UTF8

    Write-Host "  [OK] Konfigurasi berhasil diterapkan." -ForegroundColor Green
    Write-Host "    ApiUrl    : $BaseUrl/api/pc-report"    -ForegroundColor DarkGray
    Write-Host "    ConfigUrl : $BaseUrl/api/agent-config" -ForegroundColor DarkGray
    Write-Host "    RoomName  : $RoomName"                 -ForegroundColor DarkGray
} catch {
    Write-Host "  [GAGAL] Gagal menulis konfigurasi: $($_.Exception.Message)" -ForegroundColor Red
    pause
    exit 1
}

# ============================================================
# Proses 4: Mendaftarkan pengecualian (Exclusion) Windows Defender.
# Agar folder instalasi tidak diblokir/dihapus oleh Defender.
# ============================================================
Write-Host "[4/6] Menambahkan pengecualian Windows Defender..." -ForegroundColor Yellow

try {
    Add-MpPreference -ExclusionPath $InstallDir -ErrorAction Stop
    Write-Host "  [OK] Folder $InstallDir berhasil didaftarkan ke pengecualian Defender." -ForegroundColor Green
} catch {
    Write-Host "  [WARNING] Gagal menambahkan pengecualian Defender (bisa diabaikan jika memakai Antivirus lain): $($_.Exception.Message)" -ForegroundColor DarkYellow
}

# ============================================================
# Proses 5: Mendaftarkan Task Scheduler untuk otomatisasi.
# Hapus task lama terlebih dahulu agar tidak terjadi duplikasi.
# ============================================================
Write-Host "[5/6] Mendaftarkan otomatisasi Task Scheduler..." -ForegroundColor Yellow

try {
    Unregister-ScheduledTask -TaskName $TaskNameStartup   -Confirm:$false -ErrorAction SilentlyContinue
    Unregister-ScheduledTask -TaskName $TaskNameScheduled -Confirm:$false -ErrorAction SilentlyContinue

    # Tugas saat startup/logon (dijalankan tersembunyi via VBS).
    $ActionStartup = New-ScheduledTaskAction -Execute "wscript.exe" `
        -Argument "`"$InstallDir\sigap-startup.vbs`"" `
        -WorkingDirectory $InstallDir
    $Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
    Register-ScheduledTask -TaskName $TaskNameStartup `
        -Trigger (New-ScheduledTaskTrigger -AtLogOn) `
        -Action $ActionStartup `
        -Settings $Settings `
        -RunLevel Highest | Out-Null

    # Tugas terjadwal setiap jam (dari StartHour sampai EndHour).
    $TriggerSched = New-ScheduledTaskTrigger -Daily -At "${StartHour}:00"
    $TriggerSched.Repetition = (New-ScheduledTaskTrigger -Once -At "00:00" `
        -RepetitionInterval (New-TimeSpan -Hours 1) `
        -RepetitionDuration (New-TimeSpan -Hours ($EndHour - $StartHour))).Repetition
    $ActionSched = New-ScheduledTaskAction -Execute "wscript.exe" `
        -Argument "`"$InstallDir\sigap-scheduled.vbs`"" `
        -WorkingDirectory $InstallDir
    Register-ScheduledTask -TaskName $TaskNameScheduled `
        -Trigger $TriggerSched `
        -Action $ActionSched `
        -Settings $Settings `
        -RunLevel Highest | Out-Null

    Write-Host "  [OK] Task Scheduler berhasil didaftarkan." -ForegroundColor Green
} catch {
    Write-Host "  [GAGAL] Gagal mendaftarkan Task Scheduler: $($_.Exception.Message)" -ForegroundColor Red
    pause
    exit 1
}

# ============================================================
# Proses 6: Verifikasi akhir dan uji pengiriman laporan perdana.
# ============================================================
Write-Host "[6/6] Verifikasi instalasi dan uji coba perdana..." -ForegroundColor Yellow

if (-not (Test-Path "$InstallDir\sigap-agent.ps1")) {
    Write-Host "  [GAGAL] File agent tidak ditemukan setelah instalasi. Proses mungkin terganggu." -ForegroundColor Red
    pause
    exit 1
}

Write-Host "  [OK] Semua file terverifikasi." -ForegroundColor Green
Write-Host "  Mengirim laporan perdana ke server..." -ForegroundColor Cyan

& powershell.exe -ExecutionPolicy Bypass -File "$InstallDir\sigap-agent.ps1" -Mode startup

Write-Host ""
Write-Host "=================================================="
Write-Host "   INSTALASI SELESAI!" -ForegroundColor Green
Write-Host "=================================================="
Write-Host "  PC ini akan otomatis melapor ke $BaseUrl"
Write-Host "  setiap jam $StartHour.00 - $EndHour.00 dan saat logon."
Write-Host ""
