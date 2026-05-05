@echo off
title SIGAP Setup

:: Memastikan script dijalankan dengan hak akses Administrator.
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Harap Klik Kanan file ini lalu pilih "Run as Administrator"
    pause
    exit /b
)

cls
echo ==================================================
echo    SIGAP Agent - Installer
echo ==================================================
echo.

:: Input konfigurasi dari pengguna.
set /p SERVER_IP="Masukkan IP Server (contoh: 192.168.1.100): "
set /p ROOM_NAME="Masukkan Nama Ruangan (sesuai Dashboard): "
echo.
echo Sedang memproses instalasi, mohon tunggu...
echo.

:: Menjalankan script installer utama menggunakan PowerShell.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install-agent.ps1" -ServerIP "%SERVER_IP%" -RoomName "%ROOM_NAME%"

echo.
echo Jika proses selesai, tekan tombol apa saja untuk keluar.
pause
