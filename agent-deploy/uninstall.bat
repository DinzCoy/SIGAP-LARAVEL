@echo off
title SIGAP Agent Uninstaller
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Harap Klik Kanan file ini lalu pilih "Run as Administrator"
    pause
    exit /b
)

cls
echo Sedang memproses penghapusan agent...
echo.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0uninstall-core.ps1"
