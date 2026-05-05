<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR BMN - {{ $asset->bmn_number ?? 'Aset' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .sticker-container {
            background: white;
            border: 2px solid #000;
            padding: 15px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .sub-header {
            font-size: 10px;
            color: #555;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        #qrcode {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        #qrcode img {
            border: 1px solid #eee;
            padding: 5px;
        }
        .bmn-number {
            font-family: monospace;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .device-info {
            font-size: 11px;
            color: #333;
            margin-top: 3px;
        }
        
        @media print {
            body {
                background: none;
                display: block;
                padding: 0;
                margin: 0;
            }
            .sticker-container {
                box-shadow: none;
                margin: 0;
                page-break-inside: avoid;
            }
            /* Hide print button when printing */
            .no-print {
                display: none !important;
            }
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .print-btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">Terapkan Stiker / Print</button>

    <div class="sticker-container">
        <div class="header">Aset BMN BPS</div>
        <div class="sub-header">Pindai QR ini untuk Info & Inspeksi</div>
        
        <div id="qrcode">
            @php
                $qrCode = SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(160)
                    ->errorCorrection('H')
                    ->generate(route('assets.scan', $asset->id));
                
                $logoPath = public_path('images/logo_qr.png');
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoSize = 48; // 30% dari 160
                    $logoPos = (160 - $logoSize) / 2;
                    $logoTag = '<image href="data:image/png;base64,' . $logoData . '" x="' . $logoPos . '" y="' . $logoPos . '" width="' . $logoSize . '" height="' . $logoSize . '" />';
                    $qrCode = str_replace('</svg>', $logoTag . '</svg>', $qrCode);
                }
            @endphp
            {!! $qrCode !!}
        </div>
        
        <div class="bmn-number">{{ $asset->bmn_number ?? 'N/A' }}</div>
        <div class="device-info">
            {{ $asset->deviceName->brand ?? 'PC' }} {{ $asset->deviceName->name ?? 'Device' }}
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Auto open print dialog after brief delay
            setTimeout(() => {
                // window.print(); 
            }, 500);
        });
    </script>
</body>
</html>
