# Memeriksa hak akses Administrator.
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "ERROR: Script ini harus dijalankan sebagai Administrator!" -ForegroundColor Red
    pause
    exit 1
}

$InstallDir = "C:\bps-guardian"

Write-Host "=================================================="
Write-Host "   SIGAP Agent - Uninstaller"
Write-Host "=================================================="
Write-Host ""

# Proses penghapusan Task Scheduler.
Write-Host "Menghapus otomatisasi Task Scheduler..." -ForegroundColor Yellow
Unregister-ScheduledTask -TaskName "SIGAP (Startup)" -Confirm:$false -ErrorAction SilentlyContinue
Unregister-ScheduledTask -TaskName "SIGAP (Scheduled)" -Confirm:$false -ErrorAction SilentlyContinue
Write-Host "  Otomatisasi berhasil dihapus." -ForegroundColor Green


Write-Host "Menghapus pengecualian Windows Defender..." -ForegroundColor Yellow
try {
    Remove-MpPreference -ExclusionPath $InstallDir -ErrorAction SilentlyContinue
    Write-Host "  Pengecualian berhasil dihapus." -ForegroundColor Green
} catch {
    Write-Host "  Gagal menghapus pengecualian (mungkin sudah dihapus manual)." -ForegroundColor DarkGray
}

# Proses penghapusan folder instalasi.
if (Test-Path $InstallDir) {
    Write-Host "Menghapus direktori sistem $InstallDir..." -ForegroundColor Yellow
    Remove-Item -Path $InstallDir -Recurse -Force
    Write-Host "  Direktori berhasil dihapus." -ForegroundColor Green
} else {
    Write-Host "  Direktori sistem tidak ditemukan (sudah bersih)." -ForegroundColor DarkGray
}

Write-Host ""
Write-Host "Proses uninstall selesai. Agent telah dihapus sepenuhnya." -ForegroundColor Green
pause
