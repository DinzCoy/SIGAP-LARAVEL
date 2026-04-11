#!/bin/bash
# Mock script to test the Laravel API endpoint on Linux since PowerShell might not be fully featured for network/WMI here

API_URL="http://127.0.0.1:8000/api/pc-report"
API_KEY="BPS-SULSEL-SECRET-2026"

HOSTNAME=$(hostname)
IP_ADDRESS=$(hostname -I | awk '{print $1}')
MAC_ADDRESS="00:11:22:33:44:55"
OS_NAME=$(cat /etc/os-release | grep PRETTY_NAME | cut -d '"' -f 2)
OS_BUILD=2204
RAM_FREE_KB=$(free -k | grep Mem | awk '{print $4}')
DISK_FREE_B=$(df -B1 / | tail -1 | awk '{print $4}')

PAYLOAD=$(cat <<EOF
{
  "hostname": "$HOSTNAME",
  "ip_address": "$IP_ADDRESS",
  "mac_address": "$MAC_ADDRESS",
  "os_name": "$OS_NAME",
  "os_build": $OS_BUILD,
  "ram_free_kb": $RAM_FREE_KB,
  "disk_free_b": $DISK_FREE_B
}
EOF
)

echo "Payload:"
echo "$PAYLOAD"

curl -X POST "$API_URL" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -H "X-API-KEY: $API_KEY" \
     -d "$PAYLOAD"
