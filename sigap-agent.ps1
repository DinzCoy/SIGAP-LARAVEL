
param(
    [ValidateSet("startup", "scheduled")]
    [string]$Mode = "startup"
)

$ApiUrl      = "http://192.168.20.69/api/pc-report"
$ConfigUrl   = "http://192.168.20.69/api/agent-config"
$ApiKey      = "BPS-SULSEL-SECRET-2026"
$RoomName    = "Ruangan Server BPS"
$LogPath     = "$env:TEMP\bps_guardian_v2.log"

function Write-Log {
    param([string]$Message, [string]$Color = "White")
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logEntry = "[$timestamp] $Message"
    Write-Host $logEntry -ForegroundColor $Color
    $logEntry | Out-File -FilePath $LogPath -Append
}

if ($Mode -eq "scheduled") {
    Write-Log "--- Mode SCHEDULED: Mengecek jadwal dari server ---" "Cyan"

    try {
        $Headers = @{
            "Accept"    = "application/json"
            "X-API-KEY" = $ApiKey
        }
        $ConfigResponse = Invoke-RestMethod -Uri "$ConfigUrl`?room_name=$([uri]::EscapeDataString($RoomName))" `
                                            -Method Get -Headers $Headers -ErrorAction Stop

        $CurrentHour = (Get-Date).Hour
        $ScheduledHours = $ConfigResponse.scheduled_hours

        if ($ScheduledHours -notcontains $CurrentHour) {
            Write-Log "Jam $CurrentHour bukan jadwal pengiriman (Jadwal: $($ScheduledHours -join ', ')). Skip." "DarkGray"
            exit 0
        }

        $DelaySeconds = [int]$ConfigResponse.delay_seconds
        if ($DelaySeconds -gt 0) {
            Write-Log "Ruangan '$RoomName' (urut ke-$($ConfigResponse.room_order)). Menunggu $DelaySeconds detik ($([Math]::Round($DelaySeconds / 60, 1)) menit)..." "Yellow"
            Start-Sleep -Seconds $DelaySeconds
        }

        Write-Log "Delay selesai. Memulai pengiriman data..." "Green"

    } catch {
        Write-Log "Gagal mengambil konfigurasi dari server: $($_.Exception.Message). Melanjutkan tanpa delay..." "DarkYellow"

    }
} else {
    Write-Log "--- Mode STARTUP: Kirim data segera (tanpa delay) ---" "Cyan"
}

try {
    Write-Log "--- Memulai Diagnostik Pada PC ---" "Cyan"

    $Hostname = $env:COMPUTERNAME
    $IpAddress = (Get-NetIPAddress | Where-Object {
        $_.AddressFamily -eq 'IPv4' -and $_.IPAddress -notmatch '^169\.254\.' -and $_.IPAddress -ne '127.0.0.1'
    } | Select-Object -First 1).IPAddress
    $Adapter = Get-NetAdapter | Where-Object { $_.Status -eq 'Up' } | Select-Object -First 1
    $MacAddress = ($Adapter.MacAddress -replace "-", ":")

    $OsInfo = Get-CimInstance Win32_OperatingSystem
    $OsName = $OsInfo.Caption
    $OsBuild = $OsInfo.BuildNumber
    $LastPatch = Get-HotFix | Sort-Object InstalledOn -Descending | Select-Object -First 1
    $LastPatchDate = if ($LastPatch) { $LastPatch.InstalledOn.ToString("yyyy-MM-dd") } else { "Unknown" }

    $TotalRamKb = $OsInfo.TotalVisibleMemorySize
    $FreeRamKb = $OsInfo.FreePhysicalMemory
    $UsedRamPercent = [Math]::Round((($TotalRamKb - $FreeRamKb) / $TotalRamKb) * 100, 2)

    $DiskInfo = Get-CimInstance Win32_LogicalDisk | Where-Object { $_.DeviceID -eq "C:" }
    $TotalDiskB = $DiskInfo.Size
    $FreeDiskB = $DiskInfo.FreeSpace

    $DiskHealthObj = Get-CimInstance -Namespace root\wmi -ClassName MSStorageDriver_FailurePredictStatus -ErrorAction SilentlyContinue
    $IsDiskCritical = @($DiskHealthObj.PredictFailure) -contains $true
    $DiskStatus = if ($IsDiskCritical) { "CRITICAL (Replace Soon)" } else { "HEALTHY" }

    $IsTrouble = $false
    $TroubleNote = "Normal"

    if ($UsedRamPercent -gt 90) {
        $IsTrouble = $true
        $TroubleNote = "High RAM Usage Anomaly detected ($UsedRamPercent%)"
    }
    if ($IsDiskCritical) {
        $IsTrouble = $true
        $TroubleNote = "Hardware Alert: Disk C Health Failure Predicted!"
    }

    $SoftwareList = @()
    $SeenApps = @{}

    $AvKeywords = @("defender", "antivirus", "security", "kaspersky", "mcafee", "norton", "bitdefender", "avast", "eset", "symantec", "smadav", "malwarebytes", "avira", "sophos", "trellix", "sentinel")

    $UninstallKeys = @(
        "HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\*",
        "HKLM:\SOFTWARE\WOW6432Node\Microsoft\Windows\CurrentVersion\Uninstall\*",
        "HKCU:\SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\*"
    )

    foreach ($Path in $UninstallKeys) {
        $Apps = Get-ItemProperty $Path -ErrorAction SilentlyContinue |
                Where-Object { $_.DisplayName -and $_.SystemComponent -ne 1 -and $_.ParentKeyName -eq $null }

        foreach ($App in $Apps) {
            $AppName = [string]$App.DisplayName
            if (-not [string]::IsNullOrWhiteSpace($AppName) -and -not $SeenApps.ContainsKey($AppName)) {
                $SeenApps[$AppName] = $true

                $IsAv = $false
                foreach ($Keyword in $AvKeywords) {
                    if ($AppName -match "(?i)$Keyword") {
                        $IsAv = $true
                        break
                    }
                }

                $FinalName = $AppName
                $SortPriority = 1

                if ($IsAv) {

                    $FinalName = "(SECURITY/Anti Virus)" + $AppName
                    $SortPriority = 0
                }

                $SoftwareList += [PSCustomObject]@{
                    Priority  = $SortPriority
                    name      = $FinalName
                    version   = [string]$App.DisplayVersion
                    publisher = [string]$App.Publisher
                }
            }
        }
    }

    $SoftwareList = @($SoftwareList | Sort-Object Priority, name)

    $SoftwareList = $SoftwareList | Select-Object name, version, publisher

    $PayloadHashtable = @{
        hostname      = $Hostname
        username      = $env:USERNAME
        ip_address    = $IpAddress
        mac_address   = $MacAddress
        room_name     = $RoomName
        os_name       = $OsName
        os_build      = [int]$OsBuild
        last_patch    = $LastPatchDate
        total_ram_kb  = [long]$TotalRamKb
        ram_free_kb   = [long]$FreeRamKb
        total_disk_b  = [long]$TotalDiskB
        disk_free_b   = [long]$FreeDiskB
        disk_status   = $DiskStatus
        is_trouble    = $IsTrouble
        trouble_note  = $TroubleNote
        software_list = @($SoftwareList)
    }

    $PayloadJson = $PayloadHashtable | ConvertTo-Json -Depth 3

    $PayloadBytes = [System.Text.Encoding]::UTF8.GetBytes($PayloadJson)

    $Headers = @{
        "Accept"       = "application/json"
        "X-API-KEY"    = $ApiKey
    }

    Write-Log "Mengirim laporan ke server ($Mode mode)..." "Yellow"
    $Response = Invoke-RestMethod -Uri $ApiUrl -Method Post -Headers $Headers -Body $PayloadBytes -ContentType "application/json; charset=utf-8" -ErrorAction Stop
    Write-Log "Status: $($Response.status) - $($Response.message)" "Green"

} catch {
    Write-Log "Error: $($_.Exception.Message)" "Red"
    Write-Host "Gagal mengirim data. Cek log di $LogPath" -ForegroundColor Red
}