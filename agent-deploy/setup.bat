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

echo  [1/1] Alamat Server SIGAP
echo        Secara otomatis diarahkan ke domain produksi.
echo.
set SERVER_IP=sigap.makagang.stat7300.net

echo.
echo  --------------------------------------------------
echo  Konfigurasi:
echo    Server  : %SERVER_IP%
echo  --------------------------------------------------
echo.
echo  Sedang memproses instalasi, mohon tunggu...
echo.

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install-agent.ps1" -ServerIP "%SERVER_IP%"

echo.
echo  Instalasi selesai. Tekan tombol apa saja untuk keluar.
pause >nul
