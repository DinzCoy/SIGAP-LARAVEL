
param(
    [string]$PhpPath    = "",
    [string]$ProjectPath = ""
)

$TaskName   = "SIGAP Queue Worker"
$LogFile    = "$env:TEMP\sigap_queue_worker.log"

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "   SIGAP Queue Worker - Installer" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

if ([string]::IsNullOrWhiteSpace($PhpPath)) {

    $PhpPath = (Get-Command php -ErrorAction SilentlyContinue)?.Source

    if (-not $PhpPath) {
        $Candidates = @(
            "C:\laragon\bin\php\php-8.3*\php.exe",
            "C:\laragon\bin\php\php-8.2*\php.exe",
            "C:\laragon\bin\php\php-8.1*\php.exe",
            "C:\xampp\php\php.exe",
            "C:\wamp64\bin\php\php8*\php.exe"
        )
        foreach ($Pattern in $Candidates) {
            $Found = Get-Item $Pattern -ErrorAction SilentlyContinue | Select-Object -Last 1
            if ($Found) { $PhpPath = $Found.FullName; break }
        }
    }
}

if (-not (Test-Path $PhpPath -ErrorAction SilentlyContinue)) {
    Write-Host "[ERROR] PHP tidak ditemukan di: $PhpPath" -ForegroundColor Red
    Write-Host "  Gunakan: .\install-queue-worker.ps1 -PhpPath 'C:\path\to\php.exe'" -ForegroundColor Yellow
    pause; exit 1
}
Write-Host "[OK] PHP ditemukan   : $PhpPath" -ForegroundColor Green

if ([string]::IsNullOrWhiteSpace($ProjectPath)) {

    $ProjectPath = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
}

$ArtisanPath = Join-Path $ProjectPath "artisan"
if (-not (Test-Path $ArtisanPath)) {
    Write-Host "[ERROR] File artisan tidak ditemukan di: $ProjectPath" -ForegroundColor Red
    Write-Host "  Gunakan: .\install-queue-worker.ps1 -ProjectPath 'D:\path\ke\projek'" -ForegroundColor Yellow
    pause; exit 1
}
Write-Host "[OK] Project path    : $ProjectPath" -ForegroundColor Green
Write-Host "[OK] Log output      : $LogFile" -ForegroundColor Green
Write-Host ""

Write-Host "[1/3] Membersihkan task lama (jika ada)..." -ForegroundColor Yellow
Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false -ErrorAction SilentlyContinue
Write-Host "      [OK] Siap mendaftarkan task baru." -ForegroundColor Green

Write-Host "[2/3] Membuat wrapper script auto-restart..." -ForegroundColor Yellow

$WrapperPath = "$env:TEMP\sigap_queue_wrapper.ps1"
$WrapperContent = @"
`$PhpExe      = "$($PhpPath -replace '\\', '\\')"
`$ProjectDir  = "$($ProjectPath -replace '\\', '\\')"
`$LogFile     = "$($LogFile -replace '\\', '\\')"
`$MaxRestarts = 9999

function Write-QLog {
    param([string]`$Msg)
    `$ts = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "``[`$ts``] `$Msg" | Out-File -FilePath `$LogFile -Append
    Write-Host "``[`$ts``] `$Msg"
}

`$RestartCount = 0

while (`$RestartCount -lt `$MaxRestarts) {
    Write-QLog "Queue worker dimulai (restart ke-`$RestartCount)..."

    try {
        `$proc = Start-Process -FilePath `$PhpExe ``
            -ArgumentList "artisan", "queue:work", "--tries=3", "--timeout=120", "--sleep=3", "--max-jobs=500" ``
            -WorkingDirectory `$ProjectDir ``
            -Wait -PassThru -NoNewWindow

        Write-QLog "Queue worker berhenti (exit code: `$(`$proc.ExitCode)). Restart dalam 5 detik..."
    } catch {
        Write-QLog "Error menjalankan queue: `$(`$_.Exception.Message). Retry dalam 5 detik..."
    }

    `$RestartCount++
    Start-Sleep -Seconds 5
}

Write-QLog "Queue worker mencapai batas restart. Berhenti."
"@

$WrapperContent | Set-Content -Path $WrapperPath -Encoding UTF8 -Force
Write-Host "      [OK] Wrapper disimpan di: $WrapperPath" -ForegroundColor Green

Write-Host "[3/3] Mendaftarkan ke Windows Task Scheduler..." -ForegroundColor Yellow

try {

    $Action = New-ScheduledTaskAction `
        -Execute "powershell.exe" `
        -Argument "-ExecutionPolicy Bypass -WindowStyle Hidden -File `"$WrapperPath`"" `
        -WorkingDirectory $ProjectPath

    $Trigger = New-ScheduledTaskTrigger -AtStartup

    $Settings = New-ScheduledTaskSettingsSet `
        -AllowStartIfOnBatteries `
        -DontStopIfGoingOnBatteries `
        -StartWhenAvailable `
        -ExecutionTimeLimit ([TimeSpan]::Zero) `
        -RestartCount 5 `
        -RestartInterval (New-TimeSpan -Minutes 1)

    $Principal = New-ScheduledTaskPrincipal `
        -UserId "SYSTEM" `
        -RunLevel Highest `
        -LogonType ServiceAccount

    Register-ScheduledTask `
        -TaskName $TaskName `
        -Action $Action `
        -Trigger $Trigger `
        -Settings $Settings `
        -Principal $Principal `
        -Description "SIGAP: Memproses antrian job (kompres foto, dll) secara background" `
        -Force | Out-Null

    Write-Host "      [OK] Task '$TaskName' berhasil didaftarkan!" -ForegroundColor Green

} catch {
    Write-Host "      [ERROR] Gagal mendaftarkan task: $($_.Exception.Message)" -ForegroundColor Red
    pause; exit 1
}

Write-Host ""
Write-Host "Menjalankan queue worker sekarang..." -ForegroundColor Cyan
Start-ScheduledTask -TaskName $TaskName

Start-Sleep -Seconds 3

$Status = (Get-ScheduledTask -TaskName $TaskName).State
Write-Host "Status task saat ini : $Status" -ForegroundColor $(if ($Status -eq "Running") { "Green" } else { "Yellow" })

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "   INSTALASI SELESAI!" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Queue worker sekarang:" -ForegroundColor White
Write-Host "    - Berjalan di background (tanpa jendela)" -ForegroundColor DarkGray
Write-Host "    - Otomatis start saat Windows boot" -ForegroundColor DarkGray
Write-Host "    - Auto-restart jika crash (maks 5x via Scheduler)" -ForegroundColor DarkGray
Write-Host "    - Log tersimpan di: $LogFile" -ForegroundColor DarkGray
Write-Host ""
Write-Host "  Perintah berguna:" -ForegroundColor White
Write-Host "    Cek status:" -ForegroundColor DarkGray
Write-Host "    Get-ScheduledTask -TaskName '$TaskName'" -ForegroundColor Cyan

Write-Host "    Hentikan sementara:" -ForegroundColor DarkGray
Write-Host "    Stop-ScheduledTask -TaskName '$TaskName'" -ForegroundColor Cyan

Write-Host "    Jalankan ulang:" -ForegroundColor DarkGray
Write-Host "    Start-ScheduledTask -TaskName '$TaskName'" -ForegroundColor Cyan

Write-Host "    Uninstall total:" -ForegroundColor DarkGray
Write-Host "    Unregister-ScheduledTask -TaskName '$TaskName' -Confirm:`$false" -ForegroundColor Cyan
Write-Host ""
