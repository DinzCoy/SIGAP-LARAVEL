<#
.SYNOPSIS
    BPS-PC Guardian Agent Script - Pro Version
.DESCRIPTION
    Fitur Baru: Cek Kesehatan Disk (SMART), Total Kapasitas Hardware, 
    Patch Update Terakhir, dan Deteksi Anomali RAM.
#>

# CONFIGURATION
$ApiUrl = "http://127.0.0.1:8000/api/pc-report"
$ApiKey = "BPS-SULSEL-SECRET-2026"
$RoomName = "Ruangan Server BPS"

try {
    Write-Host "--- Memulai Diagnostik Pada PC ---" -ForegroundColor Cyan

    # Identitas Dasar
    $Hostname = $env:COMPUTERNAME
    $IpAddress = (Get-NetIPAddress | Where-Object { 
        $_.AddressFamily -eq 'IPv4' -and $_.IPAddress -notmatch '^169\.254\.' -and $_.IPAddress -ne '127.0.0.1' 
    } | Select-Object -First 1).IPAddress
    $Adapter = Get-NetAdapter | Where-Object { $_.Status -eq 'Up' } | Select-Object -First 1
    $MacAddress = ($Adapter.MacAddress -replace "-", ":") 

    # Info OS & Patch Update
    $OsInfo = Get-CimInstance Win32_OperatingSystem
    $OsName = $OsInfo.Caption
    $OsBuild = $OsInfo.BuildNumber
    $LastPatch = Get-HotFix | Sort-Object InstalledOn -Descending | Select-Object -First 1
    $LastPatchDate = if ($LastPatch) { $LastPatch.InstalledOn.ToString("yyyy-MM-dd") } else { "Unknown" }

    # RAM: Total & Usage Analysis
    $TotalRamKb = $OsInfo.TotalVisibleMemorySize
    $FreeRamKb = $OsInfo.FreePhysicalMemory
    $UsedRamPercent = [Math]::Round((($TotalRamKb - $FreeRamKb) / $TotalRamKb) * 100, 2)

    # DISK C: Total & Health S.M.A.R.T 
    $DiskInfo = Get-CimInstance Win32_LogicalDisk | Where-Object { $_.DeviceID -eq "C:" }
    $TotalDiskB = $DiskInfo.Size
    $FreeDiskB = $DiskInfo.FreeSpace
    # Cek Kesehatan via S.M.A.R.T (Failure Predict)
    $DiskHealthObj = Get-CimInstance -Namespace root\wmi -ClassName MSStorageDriver_FailurePredictStatus -ErrorAction SilentlyContinue
    $DiskStatus = if ($DiskHealthObj.PredictFailure) { "CRITICAL (Replace Soon)" } else { "HEALTHY" }

    # SMART TROUBLESHOOTING LOGIC 
    # Deteksi Anomali: RAM > 90% atau Disk Terancam Rusak
    $IsTrouble = $false
    $TroubleNote = "Normal"

    if ($UsedRamPercent -gt 90) {
        $IsTrouble = $true
        $TroubleNote = "High RAM Usage Anomaly detected ($UsedRamPercent%)"
    }
    if ($DiskHealthObj.PredictFailure) {
        $IsTrouble = $true
        $TroubleNote = "Hardware Alert: Disk C Health Failure Predicted!"
    }

        # INVENTARISASI SOFTWARE (Poin Tambahan)
    $SoftwareList = @()
    $SeenApps = @{}

    # Daftar kata kunci untuk mendeteksi software Antivirus/Keamanan
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
                
                # Cek apakah nama software mengandung kata kunci antivirus
                $IsAv = $false
                foreach ($Keyword in $AvKeywords) {
                    if ($AppName -match "(?i)$Keyword") { # (?i) berarti tidak case-sensitive
                        $IsAv = $true
                        break
                    }
                }

                # Tentukan nama final dan prioritas sorting
                $FinalName = $AppName
                $SortPriority = 1 # Angka 1 untuk software biasa

                if ($IsAv) {
                    # Beri tambahan tanda pembeda pada nama (gunakan karakter standar seperti * agar tidak error '???' dan otomatis berada di atas secara alfabet)
                    $FinalName = "(SECURITY/Anti Virus)" + $AppName
                    $SortPriority = 0 # Angka 0 agar ditaruh di paling atas
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

    # Urutkan berdasarkan Priority (Antivirus di atas), baru kemudian berdasarkan nama agar rapi
    $SoftwareList = @($SoftwareList | Sort-Object Priority, name)

    # (Opsional) Kembalikan agar berisikan [name, version, publisher] saja, menyembunyikan kolom Priority
    $SoftwareList = $SoftwareList | Select-Object name, version, publisher


    # MENYUSUN PAYLOAD
    $Payload = @{
        hostname      = $Hostname
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
        software_list = $SoftwareList
    } | ConvertTo-Json -Depth 3

    # LOGIC PENGIRIMAN: Rutin Mingguan vs On-Trouble
    $Headers = @{
        "Accept"       = "application/json"
        "Content-Type" = "application/json"
        "X-API-KEY"    = $ApiKey
    }

    Write-Host "Mengirim laporan ke server..." -ForegroundColor Yellow
    $Response = Invoke-RestMethod -Uri $ApiUrl -Method Post -Headers $Headers -Body $Payload -ErrorAction Stop
    Write-Host "Status: $($Response.status) - $($Response.message)" -ForegroundColor Green

} catch {
    $ErrorMessage = "$(Get-Date): Error: $($_.Exception.Message)"
    $LogPath = "$env:TEMP\bps_guardian_v2.log"
    $ErrorMessage | Out-File -FilePath $LogPath -Append
    Write-Host "Gagal mengirim data. Cek log di $LogPath" -ForegroundColor Red
}