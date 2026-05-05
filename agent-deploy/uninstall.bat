@echo off
title SIGAP Agent Uninstaller

:: Memastikan script dijalankan dengan hak akses Administrator.
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Harap Klik Kanan file ini lalu pilih "Run as Administrator"
    pause
    exit /b
)

cls
echo Sedang memproses penghapusan agent...
echo.

:: Menjalankan script uninstaller utama menggunakan PowerShell.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0uninstall-core.ps1"
