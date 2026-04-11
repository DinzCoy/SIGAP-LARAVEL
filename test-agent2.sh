#!/bin/bash

API_URL="http://10.58.173.2/api/pc-report"
API_KEY="BPS-SULSEL-SECRET-2026"

# Create simulated JSON payload
PAYLOAD=$(cat <<JSON
{
    "hostname": "BPS-SIMULATOR",
    "ip_address": "192.168.1.100",
    "mac_address": "00:11:22:33:44:55",
    "room_name": "Ruangan Server BPS",
    "os_name": "Microsoft Windows 11 Enterprise",
    "os_build": 22631,
    "last_patch": "2026-03-01",
    "total_ram_kb": 16777216,
    "ram_free_kb": 4194304,
    "total_disk_b": 512110190592,
    "disk_free_b": 102400000000,
    "disk_status": "HEALTHY",
    "is_trouble": false,
    "trouble_note": "Normal",
    "software_list": [
        {
            "name": "SPSS Statistics 28",
            "version": "28.0.0.0",
            "publisher": "IBM Corp"
        },
        {
            "name": "Mozilla Firefox",
            "version": "123.0",
            "publisher": "Mozilla"
        },
        {
            "name": "Valorant",
            "version": "1.0.0",
            "publisher": "Riot Games"
        }
    ]
}
JSON
)

echo "Sending data to \$API_URL..."
curl -X POST "\$API_URL" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -H "X-API-KEY: \$API_KEY" \
     -d "\$PAYLOAD" \
     -i
