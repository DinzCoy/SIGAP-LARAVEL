@echo off
title SIGAP Agent - Setup
chcp 65001 >nul

net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo  [ERROR] Script ini harus dijalankan sebagai Administrator!
    echo.
    echo  Caranya:
    echo    1. Klik KANAN file setup.bat ini
    echo    2. Pilih "Run as Administrator"
    echo.
    pause
    exit /b
)

cls
echo.
echo  ==================================================
echo     SIGAP Agent - Installer
echo  ==================================================
echo.
echo  Script ini akan mendaftarkan PC ini ke sistem SIGAP
echo  agar dapat mengirim laporan diagnostik secara otomatis.
echo.
echo  --------------------------------------------------
echo.

echo  [1/2] Alamat Server SIGAP
echo        Contoh IP lokal  : 192.168.20.69
echo        Contoh domain    : sigap.kantorku.id
echo.
set /p SERVER_IP="        Masukkan alamat server: "

echo.
echo  [2/2] Nama Ruangan PC Ini
echo        Harus sama persis dengan nama di Dashboard SIGAP.
echo        Contoh: Ruangan Tata Usaha, Ruangan Server, Lab Komputer
echo.
set /p ROOM_NAME="        Masukkan nama ruangan: "

echo.
echo  --------------------------------------------------
echo  Konfigurasi:
echo    Server  : %SERVER_IP%
echo    Ruangan : %ROOM_NAME%
echo  --------------------------------------------------
echo.
echo  Sedang memproses instalasi, mohon tunggu...
echo.

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install-agent.ps1" -ServerIP "%SERVER_IP%" -RoomName "%ROOM_NAME%"

echo.
echo  Instalasi selesai. Tekan tombol apa saja untuk keluar.
pause >nul
