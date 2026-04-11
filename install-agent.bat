@echo off
echo ========================================================
echo BPS-PC Guardian Agent Installer
echo ========================================================
echo.

:: Periksa apakah dijalankan sebagai Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Harap jalankan file install-agent.bat ini sebagai Administrator (Klik Kanan -^> Run as administrator^)
    pause
    exit /b
)

:: Konfigurasi Path
set "AGENT_DIR=C:\BPS-Guardian"
set "PS1_FILE=bps-pc-guardian-agent.ps1"
set "SOURCE_PATH=%~dp0%PS1_FILE%"

echo [*] Mengecek direktori %AGENT_DIR%...
if not exist "%AGENT_DIR%" (
    mkdir "%AGENT_DIR%"
    :: Sembunyikan direktori agar tidak terlihat sembarangan oleh user
    attrib +h "%AGENT_DIR%"
)

echo [*] Menyalin script agent ke C:\...
if not exist "%SOURCE_PATH%" (
    echo [ERROR] File %PS1_FILE% tidak ditemukan di folder instalasi ini!
    echo Harap pastikan %PS1_FILE% berada di folder yang sama dengan installer ini.
    pause
    exit /b
)
copy /Y "%SOURCE_PATH%" "%AGENT_DIR%\%PS1_FILE%" >nul

echo [*] Menyiapkan File Eksekusi Siluman (Tanpa Kedip CMD)...
set "VBS_FILE=runner.vbs"
(
echo Set objShell = CreateObject^("WScript.Shell"^)
echo objShell.Run "powershell.exe -ExecutionPolicy Bypass -WindowStyle Hidden -File ""%AGENT_DIR%\%PS1_FILE%""", 0, False
) > "%AGENT_DIR%\%VBS_FILE%"

echo [*] Menambahkan Task Scheduler (Otomatis menyala tiap PC booting)...
:: Hapus task lama jika ada untuk mencegah error duplikat
schtasks /Delete /TN "BPS-PC-Guardian-Agent" /F >nul 2>&1

:: Buat Task Baru (diarahkan ke file VBScript)
:: - Menggunakan SYSTEM user (NT AUTHORITY\SYSTEM) jadi tidak butuh login dan jalan 100% di latar belakang (hidden).
schtasks /Create /TN "BPS-PC-Guardian-Agent" /RU "SYSTEM" /SC ONSTART /TR "wscript.exe \"%AGENT_DIR%\%VBS_FILE%\"" /F
:: Anda juga bisa mengganti /SC ONSTART menjadi jadwal seperti /SC DAILY /ST 08:00 (Tiap jam 8 pagi)

echo.
echo ========================================================
echo [SUCCESS] Agent BPS-PC Guardian berhasil diinstall!
echo Agent kini akan berjalan setiap kali PC di restart.
echo.
echo Untuk mengetes menjalankannya sekarang secara manual via Task Scheduler:
echo schtasks /Run /TN "BPS-PC-Guardian-Agent"
echo ========================================================
pause
